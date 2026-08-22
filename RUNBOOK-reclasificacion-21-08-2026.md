# Runbook — Reclasificación de tamizajes históricos (21/08/2026)

**Servidor:** `mfl@cp42-ga:~/www` · **Rama:** `qa-server` · **Commit destino:** `444a091`

---

## Contexto: qué se está arreglando

Angélica reportó que en la tabla **Casos en seguimiento** (panel empresa) la columna
*"Ideación y riesgo suicida"* sigue mostrando `Riesgo Agudo` en varios renglones.

**No es un bug de código.** Esa columna lee directo la columna `nivel_suicidio` del
tamizaje (`app/Filament/Empresa/Resources/CasoSeguimientos/Tables/Columnas.php:101`),
no la recalcula. Los renglones afectados son tamizajes **históricos**: se contestaron
antes de que existiera la pregunta 5 del ASQ, tienen el valor viejo guardado en la
base, y ahí se quedan hasta que algo los reescriba.

Lo que los reescribe es `php artisan tamizajes:reclasificar`, que hace exactamente lo
que pide su lámina:

| Situación | `nivel_suicidio` queda en | `nivel_riesgo_general` queda en |
|---|---|---|
| ASQ positivo, sin respuesta a la pregunta 5 | `Positivo: requiere valoración posterior` | `Agudeza pendiente de confirmar` |
| ASQ negativo | `Negativo` | según ansiedad/depresión |

**Ninguna pasada eleva un histórico a riesgo agudo** — esa determinación corresponde a
la valoración clínica, no a un recálculo.

### Lo que este runbook NO hace

No toca `nivel_riesgo_detectado` de los casos de seguimiento (la columna
"Prioridad de atención" de esa misma tabla). Fue decisión deliberada: ahí ya hay
criterio capturado por empresas y gestores, y sobrescribirlo lo borraría. La
migración solo renombra el vocabulario. Si Angélica pide que también se recalculen,
es un cambio aparte.

---

## Notas de entorno

- Si el `php` del PATH de cPanel es viejo, usa `/usr/local/bin/ea-php84 artisan …`
  en lugar de `php artisan …` (producción corre 8.4; el proyecto pide `^8.3`).
- **No hace falta `composer install`** — `composer.lock` no cambió.
- **No hace falta `npm run build`** — `public/build/` está versionado y el commit
  `4d32555` ya trae los assets compilados.

---

## 0. Ubícate y confirma la rama

```bash
cd ~/www
git branch --show-current      # → qa-server
git log --oneline -1           # antes del pull: 7bcc135 o anterior
```

## 1. Respalda la base ANTES de tocar nada

El paso 4 reescribe ~1,700 registros. **No brincar este paso.**

```bash
mysqldump -u USUARIO -p NOMBRE_BD > ~/backup_masfeliz_$(date +%F_%H%M).sql
ls -lh ~/backup_masfeliz_*.sql        # confirma que no pesa 0 bytes
```

## 2. Trae el código

```bash
git pull
git log --oneline -1                  # debe decir: 444a091 Quita el cuarto nivel fantasma...
```

### ⚠️ Si el pull marca conflictos `add/add`

El commit `7bcc135` fue reescrito con `amend` el 21/08 cuando el servidor ya lo tenía
desplegado como `c28d551`. Si el servidor no ha hecho pull desde entonces, el merge
marca conflictos en archivos que son idénticos línea por línea.

```bash
git merge --abort
git log --oneline origin/qa-server..HEAD    # DEBE salir vacío (servidor sin commits propios)
git status --short                          # DEBE salir vacío (nada editado a mano)
git reset --hard origin/qa-server
```

Haz el `reset --hard` **solo** si esos dos comandos salen vacíos.

## 3. Migraciones

```bash
php artisan migrate:status            # revisa cuántas salen en "Pending"
php artisan migrate --force
php artisan optimize:clear
```

Las que pueden estar pendientes:

- `2026_08_17_000001_add_resultados_tamizaje_visibles_to_settings`
- `2026_08_18_170000_add_respuestas_to_tamizajes_table`
- `2026_08_21_120000_renombra_riesgo_general_a_prioridad_atencion` ← **la de la escala nueva**

La última ensancha tres columnas a `VARCHAR(60)` (porque *"Agudeza pendiente de
confirmar"* mide exactamente 30 caracteres y no cabía) y renombra `Moderado` →
`Moderada`. **Si esta no corre, el paso 4 trunca valores.**

## 4. Simula la reclasificación (no escribe nada)

```bash
php artisan tamizajes:reclasificar
```

Imprime una tabla con el conteo real, del estilo:

```
Cambio                                                                        Registros
Riesgo Agudo → Positivo: requiere valoración posterior | Alto → Agudeza...    933
```

**Guarda esa salida y mándasela a Angélica antes de aplicar.** Es su decisión de
negocio, y ese conteo es el que va a querer ver reflejado en las estadísticas
generales que menciona en su lámina.

## 5. Aplica

```bash
php artisan tamizajes:reclasificar --aplicar
```

Es idempotente: marca cada registro en el JSON `respuestas` con
`prioridad_atencion_21_08_2026` y **guarda el nivel anterior** ahí mismo para
auditoría. Si se corre dos veces, la segunda reporta todo como ya reclasificado.

## 6. Verifica

```bash
php artisan tamizajes:reclasificar     # debe salir en ceros / "ya estaban reclasificados"
php artisan optimize:clear
```

```sql
SELECT nivel_suicidio, nivel_riesgo_general, COUNT(*)
FROM tamizajes
GROUP BY nivel_suicidio, nivel_riesgo_general;
```

**Esperado:** ningún `Riesgo Agudo` que no tenga respuesta a la pregunta 5. Los
`Riesgo Agudo` que sobrevivan deben ser solo tamizajes contestados después del
despliegue del 18/08.

## 7. Refresca la vista de Angélica

Pídele que recargue con **Ctrl+Shift+R** la tabla de Casos en seguimiento — es la
pantalla exacta de su captura.

---

## Si algo sale mal

```bash
mysql -u USUARIO -p NOMBRE_BD < ~/backup_masfeliz_FECHA.sql
```

El comando también tiene reversa lógica: cada registro tocado conserva
`nivel_suicidio_anterior` y `nivel_riesgo_general_anterior` dentro de su JSON
`respuestas`, así que se puede reconstruir sin el dump si hiciera falta.

---

# Anexo — Resultado del ASQ: solo "Positivo" / "Negativo" (22/08/2026)

Angélica reportó que la columna **"Ideación y riesgo suicida"** no se parecía a su
recuadro **"PARA LA REVISIÓN"**. Tenía razón, y no era un problema de datos
pendientes de reclasificar: era la etiqueta.

Su recuadro define **dos** resultados, y el nombre es una sola palabra —lo que va
después de los dos puntos es la acción, no parte del nombre:

- **Negativo** → Prevención / Promoción / Psicoeducación
- **Positivo** → Valoración psicológica adicional para confirmar o descartar riesgo/agudeza

La plataforma guardaba tres valores y ninguno se llamaba así. `Riesgo Agudo` fue
invención nuestra para que la pregunta 5 se notara en esa columna.

## Qué cambió

| Antes | Ahora |
|---|---|
| `Riesgo Agudo` | `Positivo` |
| `Positivo: requiere valoración posterior` | `Positivo` |
| `Evaluación Adicional` (heredado) | `Positivo` |
| `Negativo` | `Negativo` (sin cambio) |

**La agudeza no se pierde, cambia de columna.** La pregunta 5 en "Sí" sigue subiendo
**Prioridad de atención → Urgente**, que es donde su propia escala la pide. Esa
columna no se tocó.

⚠️ Efecto visual que conviene avisarle: en la columna del ASQ, quien contestó "Sí" a
la pregunta 5 ya no se ve en rojo sino en ámbar como cualquier positivo. El rojo
sigue estando, pero en la columna de al lado (Prioridad = Urgente).

## Despliegue

**No hay paso manual nuevo.** El renombre de los datos va en la migración
`2026_08_22_120000_renombra_resultado_asq_a_positivo_negativo`, así que viaja con el
`php artisan migrate --force` del paso 3. Basta con:

```bash
cd ~/www
git pull
php artisan migrate --force
php artisan optimize:clear
```

## Verificación

```sql
SELECT nivel_suicidio, COUNT(*) FROM tamizajes GROUP BY nivel_suicidio;
```

Debe devolver **exactamente dos filas**: `Negativo` y `Positivo`. Cualquier otra cosa
—`Riesgo Agudo`, `Positivo: requiere valoración posterior`, `Evaluación Adicional`—
significa que la migración no corrió.

Y para ver que la urgencia sigue registrada donde debe:

```sql
SELECT nivel_suicidio, nivel_riesgo_general, COUNT(*)
FROM tamizajes GROUP BY nivel_suicidio, nivel_riesgo_general;
```
