# Auditoría de Software: Estado Actual de la Plataforma "+Feliz"

Este documento contiene un reporte técnico exhaustivo sobre la arquitectura, funcionalidades, lógica de negocio y configuraciones actuales implementadas en la plataforma de salud mental **"+Feliz"**. Toda la información detallada aquí se basa estrictamente en el código fuente actual.

---

## 1. Actores, Accesos y Seguridad

El sistema cuenta con tres actores principales que interactúan mediante rutas dedicadas y capas de seguridad específicas:

### A. Gobierno (Administradores)
* **Acceso:** Panel de administración de Filament.
* **URL:** `/admin` (Inicio de sesión estándar de Filament mediante el guard `web`).
* **Credenciales por defecto:** `admin@inspira.gob.mx` (asociado al modelo `App\Models\User`).
* **Seguridad:** Acceso completo a la visualización y gestión de todas las empresas incorporadas al programa estatal.

### B. Empresas (Organizaciones)
* **Acceso:** Panel privado multitenant de Filament.
* **URL:** `/tablero`
  * **Login:** `/tablero/login` (Mapeado mediante una página de Login personalizada en `App\Filament\Pages\Auth\Login` que sustituye el campo nativo de email de Filament por `correo` para coincidir con el campo de la base de datos).
* **Seguridad:** Aislamiento estricto de datos (Tenant Scoping). Las empresas no pueden visualizar los datos de otras organizaciones. Se autentican mediante el guard `empresa` (proveedor Eloquent sobre el modelo `App\Models\Empresa`).

### C. Empleados / Colaboradores (Público)
* **Acceso:** Interfaz móvil responsiva de diagnóstico.
* **URL:** `/diagnostico/{token}`
* **Seguridad:** Acceso de lectura y escritura anónimo regulado por un token único alfanumérico (`token_tamizaje`). No se solicita ningún dato personal (nombre, nómina o correo) en esta ruta pública para asegurar el anonimato clínico inicial.

---

## 2. Flujos Principales de Negocio

### A. Prerregistro y Generación de Credenciales
1. **Formulario Público:** Disponible en la Landing Page principal (`/`).
2. **Componente Livewire:** Controlado por el componente Volt/Livewire `registro-empresa-form`.
3. **Validación:** Se exigen campos obligatorios (`nombre_empresa`, `municipio`, `dias_horario_servicio`, `nombre_director`, `nombre_responsable`, `correo` único, `telefono`, `rubro`, `numero_trabajadores`).
4. **Generación automática:**
   * **Folio único:** Se calcula en el evento `creating` del modelo `Empresa` con el formato `MF-2026-XXXX` (bloqueando la fila en base de datos para evitar colisiones concurrentes).
   * **Contraseña temporal:** Se genera un string aleatorio de 8 caracteres.
   * **Token de Tamizaje:** Se genera un string único de 32 caracteres (`Str::random(32)`) para la liga de diagnóstico de sus empleados.
5. **Notificación:** Se envía un correo electrónico al destinatario (`correo`) con sus credenciales de acceso temporal usando el mailable `AccesosTableroEmpresa`.

### B. Cédula de Autoevaluación
* **Formulario (`AutoevaluacionResource`):** Exclusivo para el panel de la empresa. Consta de 25 criterios evaluativos de selección obligatoria agrupados visualmente en 4 Enfoques:
  1. *Liderazgo, Política y Prevención de Riesgos (+Prevención)* [Criterios 1 al 6]
  2. *Cuidado, Salud Emocional y Promoción del Bienestar (+Salud)* [Criterios 7 al 12]
  3. *Desarrollo Humano, Formación y Comunicación (+Desarrollo)* [Criterios 13 al 18]
  4. *Condiciones de trabajo, bienestar y entorno psicosocial (+Bienestar)* [Criterios 19 al 25]
* **Escala de Opciones:** `['10' => 'Sí (10)', '5' => 'En proceso (5)', '0' => 'No (0)', 'NA' => 'No aplica']`.
* **Cálculo de Puntaje:**
  * Se ignoran los valores no numéricos (`NA`).
  * Se suman los puntajes numéricos de las preguntas respondidas (`10`, `5`, `0`).
* **Semaforización en Tabla:**
  * **Verde (success):** Puntaje >= 200.
  * **Amarillo (warning):** Puntaje entre 100 y 199.
  * **Rojo (danger):** Puntaje < 100.
* **Nivel de Madurez (Maturity Levels):**
  * `>= 200` $\rightarrow$ **Nivel 3: Sobresaliente**
  * `>= 100` $\rightarrow$ **Nivel 2: En proceso**
  * `< 100`  $\rightarrow$ **Nivel 1: Inicial**

### C. Diagnóstico de Tamizaje
* **Componente (`ResponderTamizaje`):** Interfaz pública optimizada para dispositivos móviles.
* **Estructura:** 9 preguntas de opción múltiple puntuadas en escala de `0` (Nunca), `1` (A veces) y `2` (Casi siempre).
  * **Módulo Ansiedad:** Preguntas 1, 2, 3 (Puntaje máx: 6).
  * **Módulo Depresión:** Preguntas 4, 5, 6 (Puntaje máx: 6).
  * **Módulo Riesgo Suicida:** Preguntas 7, 8, 9 (Puntaje máx: 6).
* **Heurística Clínica de Puntuación:**
  * **Urgente:** Si el subtotal del Módulo de Riesgo Suicida es $\ge 2$ (lo que indica que se respondió al menos a una pregunta con "A veces" o "Casi siempre"), **O** si la suma total de los tres módulos es $\ge 12$ puntos (sobre 18 posibles).
  * **Moderado:** Si la suma de los tres módulos es $\ge 6$ puntos (y no cumple con la regla de Urgente).
  * **Leve:** Si la suma de los tres módulos es $< 6$ puntos (y no tiene ideación suicida).

### D. Bitácora de Seguimiento Clínico
* **Recurso (`CasoSeguimientoResource`):** Permite al departamento interno de psicología de la empresa llevar el registro manual e independiente de casos en atención.
* **Reactividad Formulario:** El campo `institucion_canalizacion` es dinámico: solo aparece en la pantalla y se vuelve requerido cuando el `estatus_atencion` seleccionado es `"Canalizado"`.
* **Semaforización de Badges:**
  * **Riesgo Detectado:** Verde (Leve), Amarillo (Moderado), Rojo (Urgente).
  * **Estatus de la Atención:** Azul (En seguimiento), Amarillo (Canalizado), Verde (Cerrado satisfactorio), Gris (Abandonó).
* **Tenant Isolation:** Cada empresa solo visualiza los casos que ha dado de alta. Al crear un registro, el `empresa_id` se asigna automáticamente en segundo plano.

---

## 3. Dashboards, Indicadores y Reportes

La plataforma tiene configurados los siguientes elementos visuales y de descarga en sus paneles:

### A. Panel de Gobierno
* **Indicadores Estadísticos (`EmpresaStats`):**
  * *Total de Empresas Registradas:* Conteo total del modelo `Empresa`.
  * *Total de Trabajadores Impactados:* Suma agregada de la columna `numero_trabajadores` de todas las empresas.

### B. Panel de la Empresa
* **Indicadores Clave (`DashboardStatsOverview`):**
  * *Liga de Diagnóstico:* Muestra el enlace completo para copiar y compartir con los empleados (`route('tamizaje.publico', ...)`).
  * *Progreso de Participación:* Porcentaje de tamizajes completados sobre el total de trabajadores de la empresa. Aplica semaforización verde (success) si se alcanza la meta del $\ge 90\%$, o amarillo (warning) si es menor.
  * *Total Evaluados:* Conteo absoluto de tamizajes guardados en la empresa.
* **Gráfica de Tendencia (`RiesgosGeneralesChart`):**
  * Gráfica de tipo **Dona** (`doughnut`) que muestra la distribución porcentual de los tamizajes completados de la empresa según el riesgo general determinado (Leve, Moderado, Urgente).
  * Semaforización de la gráfica: Verde (`#10b981`), Amarillo (`#f59e0b`) y Rojo (`#ef4444`).
* **Reportes Descargables en PDF (`descargar_acuse`):**
  * Acción de fila en el listado de autoevaluaciones.
  * Utiliza `barryvdh/laravel-dompdf` para generar de manera síncrona el PDF.
  * Nombre del archivo descargado: `Acuse_Autoevaluacion_[Folio].pdf`.
  * Contenido del PDF: Cabecera institucional con 3 logotipos, resumen de datos fiscales de la empresa, tabla detallada con las 25 respuestas de criterios por enfoques, puntaje final acumulado y badge con el Nivel de Madurez correspondiente.
