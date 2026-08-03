# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Contexto

Plataforma **"+Feliz"** (Distintivo +Feliz): programa estatal de salud mental laboral. Empresas se registran, aplican un tamizaje anónimo a sus colaboradores, llenan una autoevaluación de criterios, y un evaluador/gobierno dictamina y emite el distintivo en PDF.

Todo el dominio, la UI y los mensajes están **en español**. `AppServiceProvider` fuerza `app()->setLocale('es')`. Nombres de modelos, columnas y rutas son en español (`Empresa`, `Tamizaje`, `Autoevaluacion`, `correo`, `/tablero`, `/evaluador`).

[estado_actual_plataforma.md](estado_actual_plataforma.md) es una auditoría funcional detallada del sistema — útil para entender flujos de negocio, aunque puede quedar desfasada respecto al código.

## Stack (verificar antes de asumir)

Laravel **13**, Filament **v5**, Livewire **v4**, PHP **8.3**, Tailwind **v4** (vía `@tailwindcss/vite`), Vite 8, MariaDB/MySQL.

⚠️ El [README.md](README.md) dice "Filament v3" — **está desactualizado**, `composer.lock` tiene `filament/filament v5.6`. Las APIs de v5 difieren de v3/v4: `Filament\Schemas\Components\Section`, `Filament\Actions\*`, resources con carpetas `Schemas/` + `Tables/`, `form(Schema $schema)`. No copiar snippets de v3 sin adaptarlos.

Livewire 4 usa componentes de archivo único con prefijo ⚡: [resources/views/components/⚡registro-empresa-form.blade.php](resources/views/components/⚡registro-empresa-form.blade.php). `User` usa atributos PHP de Laravel 13 (`#[Fillable]`, `#[Hidden]`) en vez de propiedades.

## Comandos

```bash
composer dev          # server + queue:listen + pail (logs) + vite, todo en paralelo
composer test         # config:clear && artisan test
composer setup        # install + key:generate + migrate + npm install + build

php artisan test --filter=ResponderTamizajeTest              # una clase
php artisan test --filter=test_nombre_del_metodo             # un test
php artisan test tests/Feature/DictamenTest.php              # un archivo

vendor/bin/pint       # formateo (sin pint.json, usa preset Laravel)
npm run dev / build   # Vite

php artisan migrate:fresh --seed   # admin@inspira.gob.mx / password + empresa demo
docker compose up -d               # app en :8080, mariadb en :3306
```

Los tests corren sobre **sqlite en memoria** (ver [phpunit.xml](phpunit.xml)) con `RefreshDatabase`; la app real corre sobre MySQL/MariaDB.

**Gotcha de entorno:** `.env` tiene `DB_HOST=db` (nombre del servicio Docker). Para correr `artisan` desde el host contra la BD del contenedor hay que sobrescribirlo:
`DB_HOST=127.0.0.1 DB_PORT=3306 php artisan tinker --execute="..."`.

## Arquitectura

### Cinco superficies, cuatro paneles Filament

| Superficie | Ruta | Guard / modelo | Provider |
|---|---|---|---|
| Gobierno (admin) | `/admin` | `web` → `User` con `role='admin'` | [AdminPanelProvider.php](app/Providers/Filament/AdminPanelProvider.php) |
| Empresas | `/tablero` | `empresa` → `Empresa` | [EmpresaPanelProvider.php](app/Providers/Filament/EmpresaPanelProvider.php) |
| Evaluadores | `/evaluador` | `web` → `User` con `role='evaluador'` + `CheckRole:evaluador` | [EvaluadorPanelProvider.php](app/Providers/Filament/EvaluadorPanelProvider.php) |
| Gestores (trabajo social) | `/gestor` | `web` → `User` con `role='gestor'` + `CheckRole:gestor` | [GestorPanelProvider.php](app/Providers/Filament/GestorPanelProvider.php) |
| Colaboradores (público) | `/diagnostico/{token}` | ninguno (token) | [ResponderTamizaje.php](app/Livewire/ResponderTamizaje.php) |

Cada panel descubre su propio namespace: `App\Filament\Resources` (admin), `App\Filament\Empresa\*`, `App\Filament\Evaluador\*`. Al agregar un recurso, ponerlo en la carpeta del panel correcto.

`Empresa` es a la vez modelo de negocio **y** Authenticatable. Como Filament espera `email` y la columna es `correo`, `Empresa::newEloquentBuilder()` traduce `where('email', …)` → `where('correo', …)`, y [Filament/Pages/Auth/Login.php](app/Filament/Pages/Auth/Login.php) reemplaza el campo de login.

`Evaluador` es una **subclase de `User`** sobre la misma tabla `users`, con global scope `role='evaluador'` y accesores `nombres`/`correo` → `name`/`email`. Por eso `User::empresas()` fija explícitamente el pivote `empresa_user`/`user_id` (si no, `Evaluador` inferiría `empresa_evaluador`).

### Aislamiento de datos — es manual, no multitenancy de Filament

No se usa `->tenant()`. El scoping se hace a mano en cada recurso y **olvidarlo filtra datos entre empresas**:

- Panel empresa: `getEloquentQuery()->where('empresa_id', auth()->id())` y, al crear, `mutateFormDataBeforeCreate()` con `$data['empresa_id'] = auth()->id()`. Nota: `auth()->id()` aquí es el id de la **Empresa**, no de un usuario.
- Panel evaluador: `getEloquentQuery()->whereHas('evaluadores', fn ($q) => $q->where('user_id', auth()->id()))` (o `empresa.evaluadores` para recursos hijos), vía pivote `empresa_user`.
- `canAccessPanel()` en `User` y `Empresa` decide qué panel puede abrir cada actor (`Empresa` solo `empresa`; `User` exige además `estatus === true`).

### Interruptor global de herramientas

Todo el módulo de herramientas del panel empresa (Autoevaluación, Tamizajes, Casos, Prevención, Crisis, Capacitación) se enciende/apaga con un único flag: `Setting::where('key','global_config')->first()?->herramientas_empresa_activas`, editable desde [ConfiguradorLanding.php](app/Filament/Pages/ConfiguradorLanding.php). Cada Resource/Page lo consulta en `canAccess()`/`shouldRegisterNavigation()`. **Todo recurso nuevo del panel empresa debe respetar este flag.**

### Flujo de tamizaje (público → clínico)

`Empresa::booted()` genera al crear: `folio` correlativo `MF-2026-NNNN` (con `lockForUpdate()`) y `token_tamizaje` (32 chars). El token es la única credencial de `/diagnostico/{token}`.

[ResponderTamizaje](app/Livewire/ResponderTamizaje.php) es un wizard de 3 pasos (`consentimiento` → `demograficos` → `cuestionario`) y calcula en `submit()`:

- **GAD-7** (7 ítems 0–3): ≥15 Grave, ≥10 Moderada, ≥5 Leve, si no Mínima.
- **PHQ-9** (9 ítems 0–3): ≥20 Grave, ≥15 Moderadamente grave, ≥10 Moderada, ≥5 Leve, si no Mínima.
- **Conducta suicida** (4 ítems 0/1): ítem 4 = `Riesgo Agudo`; cualquiera de 1–3 = `Evaluación Adicional`; si no `Negativo`.
- `nivel_riesgo_general`: `Urgente` si hay riesgo suicida o depresión Grave/Mod-grave o ansiedad Grave; `Moderado` si depresión o ansiedad Moderada; si no `Leve`.

Quien **declina** participar genera igualmente un `Tamizaje` con `consentimiento_otorgado=false`, scores 0 y `nivel_riesgo_general='No participó'`, para que cuente en el % de avance sin contaminar la gráfica de riesgos. Cualquier consulta de estadísticas debe excluir `'No participó'` de los gráficos de riesgo pero incluirlo en el denominador de participación.

### Flujo de canalización a Secretaría de Salud

Cuando una empresa marca un `CasoSeguimiento` como `estatus_atencion = 'Canalizado'` y activa `referencia_secretaria_salud`, puede llenar el **formato de referencia** desde el listado de casos. Eso crea una `SolicitudReferencia` (tabla `solicitudes_referencia`, folio correlativo `REF-2026-NNNN` generado en `booted()` igual que el de `Empresa`).

El reparto de responsabilidades lo definió la contraparte de gobierno y no es negociable a la ligera:

- **La empresa** captura la solicitud (datos de la persona, CURP, INE, domicilio, derechohabiencia, informe de valoración).
- **El Gestor y el admin** son los únicos que asignan `fecha_cita`, `unidad_atencion` y `estatus_somos`; la empresa los ve en modo lectura desde su propio tablero.

El formato vive en un solo lugar, [SolicitudReferenciaForm.php](app/Filament/Empresa/Resources/CasoSeguimientos/Schemas/SolicitudReferenciaForm.php), con dos banderas (`puedeAgendar`, `soloLectura`) porque lo consumen tres paneles. Ese esquema recibe indistintamente un `CasoSeguimiento` (desde la empresa) o una `SolicitudReferencia` (desde Gestor/admin), así que los datos de cabecera se resuelven de forma defensiva.

Gestor y admin comparten además las tablas de [referencias](app/Filament/Gestor/Resources/SolicitudReferencias/Tables/SolicitudReferenciasTable.php) y [casos canalizados](app/Filament/Gestor/Resources/CasosCanalizados/Tables/CasosCanalizadosTable.php): los recursos del panel admin son cascarones que delegan en las clases del namespace `Gestor`.

### Flujo de dictamen y distintivo

Autoevaluación (`autoevaluacions.estatus`): `Borrador` → `En revisión` (la empresa envía) → `Validado` (admin aprueba, se genera PDF) o vuelta a `Borrador` (devuelta, dispara `AutoevaluacionDevueltaMail`).

Al validar, [ViewAutoevaluacion.php](app/Filament/Resources/Autoevaluacions/Pages/ViewAutoevaluacion.php) renderiza `pdf.distintivo` con dompdf, guarda la ruta dentro del JSON `respuestas['pdf_distintivo']` (no en columna propia — `Empresa::getRutaPdfAttribute()` lo lee de la última autoevaluación) y pone `empresas.estatus_distintivo = 'Validado'`.

`Empresa::getEstatusAttribute()` mapea `Validado`/`Aprobado` → `Dictaminado` para la UI. `paso_certificacion` (int) alimenta [CertificationTimelineWidget](app/Filament/Empresa/Widgets/CertificationTimelineWidget.php) y lo mueve el admin desde la tabla de empresas.

Para poder enviar a revisión, la empresa debe haber contestado los **96 elementos** de los 20 criterios (`'0'` y `'NA'` cuentan como respuesta; `null` y `''` no). El conteo por criterio vive en `AutoevaluacionForm::ELEMENTOS_POR_CRITERIO`, junto a las definiciones que lo alimentan: **si se agrega o quita un elemento a un criterio hay que actualizar ese mapa**, porque es lo que valida `criteriosIncompletos()`.

La autoevaluación es un formulario grande con tabs anidados (Criterios Indispensables / Necesarios → Fortalecimiento, Prevención, Cuidado y Atención / Deseables) construido programáticamente en [AutoevaluacionForm.php](app/Filament/Empresa/Resources/Autoevaluacions/Schemas/AutoevaluacionForm.php) (~800 líneas); las respuestas viven en la columna JSON `respuestas`.

### Correo

Transporte **Brevo** registrado a mano en `AppServiceProvider::boot()` (`Mail::extend('brevo', …)` con `config('services.brevo.dsn')`). Los envíos se disparan desde acciones de Filament, no desde eventos de modelo: accesos al tablero, bienvenida de evaluador, autoevaluación devuelta, distintivo aprobado, visita agendada ([app/Mail/](app/Mail/)). `/preview-email` en [routes/web.php](routes/web.php) previsualiza `AccesosTableroEmpresa` en el navegador.

### Estado del registro público

La landing tiene el registro **cerrado** ("la convocatoria de esta edición concluyó") — el formulario está reemplazado por un aviso en [welcome.blade.php](resources/views/welcome.blade.php). El componente `⚡registro-empresa-form` sigue existiendo y sus tests (`RegistroEmpresaTest`) siguen corriendo.

## Trampas conocidas

- **`.htaccess` de la raíz y `/storage/`:** el despliegue es cPanel con el document root en la raíz del proyecto, así que [.htaccess](.htaccess) reescribe todo hacia `public/`. La regla `RedirectMatch 404 ^/(app|bootstrap|…)/` **no debe incluir `storage`**: `/storage/` es el symlink `public/storage` por donde se sirven evidencias, logos, flujograma de crisis y materiales de apoyo, y `RedirectMatch` (mod_alias) se evalúa antes que el `RewriteRule`, así que devuelve 404 antes de llegar al archivo. Ya ocurrió una vez y dejó a las empresas sin poder ver sus evidencias.
- **Modales de la autoevaluación:** las acciones de la modal de detalles solo hacían `$set()` sobre el estado del formulario. Como la autoevaluación es un formulario gigante que el usuario puede abandonar sin guardar, cualquier dato capturado en una modal debe además persistirse con `$record->update()` sobre el JSON `respuestas`.
- **La suite de tests está desfasada:** 13 de 35 tests fallan en `main`/`qa-server` por cambios de dominio que nunca se reflejaron en los tests (etiquetas `Rechazado` → `no_cumple`, umbrales de nivel de madurez, `telefono` que pasó a ser obligatorio en el tamizaje, `canAccessPanel` que ahora exige `role` + `estatus`). Al tocar código, compara contra ese baseline en vez de asumir que la suite estaba verde.

## Convenciones a respetar

- **Estilos de los paneles:** cada panel inyecta su CSS vía `renderHook(PanelsRenderHook::HEAD_END, …)` como bloque `<style>` inline dentro del PanelProvider, con mucho `!important` (tema oscuro de sidebar `#2a3042`, primario `#556ee6`, teal para evaluador). No hay hoja de estilos de Filament aparte; cambios visuales de panel se hacen ahí. Los tres bloques están duplicados — al ajustar uno, revisar si aplica a los otros.
- `Schema::defaultStringLength(191)` está fijado en `AppServiceProvider` (compatibilidad MySQL con índices utf8mb4).
- Los modelos de datos usan `protected $guarded = []` (`Tamizaje`, `Autoevaluacion`, `CasoSeguimiento`); `Empresa`, `User` y `MaterialApoyo` sí declaran campos.
- Las migraciones son incrementales (`add_x_to_y_table`), no se editan las existentes.
- Los tests son PHPUnit clásico (`tests/TestCase.php`, `RefreshDatabase`), con `Livewire::test()` para componentes y creación directa de modelos en `setUp()` — no hay factories propias más allá de `UserFactory`.
