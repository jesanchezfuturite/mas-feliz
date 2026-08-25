# Runbook — Publicar el lote del tamizaje en PRODUCCIÓN (25/08/2026)

**Destino:** el host que sirve `masfeliz.coahuila.gob.mx` · **Rama:** `main` · **Commit destino:** el merge del PR #14

> No confundir con [RUNBOOK-reclasificacion-21-08-2026.md](RUNBOOK-reclasificacion-21-08-2026.md):
> ese se ejecutó el 22/08 sobre **`mfl@cp42-ga:~/www`, rama `qa-server`**, que es el
> servidor de **prueba** —el que Angélica revisó—, con **otra base de datos**.
> Producción no ha recibido nada de este lote: ni el código ni la reclasificación.

---

## Contexto: qué se está publicando

El 25/08 a las 9:19 Angélica pidió, por WhatsApp: *"mientras confirmamos esto,
podrías ya poner en el servidor lo del tamizaje"*, con la captura de la pregunta 5
funcionando **en el de prueba**. Es un adelanto deliberado: no espera las cuatro
decisiones abiertas del tablero de sintomatología para publicar el cuestionario.

### Evidencia de que producción está atrás

El sitio público sirve el build de `main` y el de `qa-server` no existe ahí:

```
https://masfeliz.coahuila.gob.mx/build/assets/app-DIgRKZQH.css  → 200  (main)
https://masfeliz.coahuila.gob.mx/build/assets/app-DznlpfUf.css  → 404  (el nuevo)
```

Sin CDN de por medio (LiteSpeed, HTML `no-cache`), así que es el estado real del
servidor y no caché. Es la verificación más rápida para repetir después del pull:
**si el CSS nuevo sigue dando 404, el código no llegó.**

### Lo que cambia para el colaborador que contesta

- Aparece la **pregunta 5** ("¿Estás pensando en quitarte la vida en este momento?")
  cuando hubo algún "Sí" en el ASQ, y la derivada de la 4 ("¿cuándo fue el último
  intento?").
- El resultado del ASQ pasa a ser **`Positivo` / `Negativo`** y nada más.
- "Riesgo general" pasa a llamarse **Prioridad de atención**, con Leve / Moderada /
  Alta / Urgente y `Agudeza pendiente de confirmar` para los históricos.

---

## Notas de entorno

- PHP: producción corre 8.4. Si el `php` del PATH de cPanel es viejo, usa
  `/usr/local/bin/ea-php84 artisan …`.
- **No hace falta `composer install`** — `composer.lock` no cambió.
- **No hace falta `npm run build`** — `public/build/` está versionado.
- El `.htaccess` de la raíz **no debe** listar `storage` en su `RedirectMatch 404`:
  ahí se sirven las evidencias. Si el pull lo toca, revísalo.

---

## 0. Reconocimiento — antes de tocar nada

Producción es un host distinto al de prueba y no está documentado en el repo.
Confirma sobre qué estás parado:

```bash
pwd                            # la raíz del proyecto (document root de cPanel)
git remote -v                  # debe ser el repo mas-feliz por HTTPS
git branch --show-current      # → main
git log --oneline -1           # → 781e204 Merge pull request #13  (o anterior)
git status --short             # DEBE salir vacío: nada editado a mano en el servidor
grep -E "APP_ENV|APP_URL|DB_DATABASE" .env
```

**Si `git status --short` NO sale vacío**, para aquí: alguien editó archivos
directamente en el servidor y el pull los va a pisar. Guarda copia antes.

**Si `git branch --show-current` no dice `main`**, para aquí: la suposición de este
runbook es que producción sigue `main`. Averigua qué rama sirve antes de continuar.

## 1. Respalda la base ANTES de tocar nada

El paso 5 reescribe ~1,700 registros de personas reales. **No brincar este paso.**

```bash
mysqldump -u USUARIO -p NOMBRE_BD > ~/backup_masfeliz_prod_$(date +%F_%H%M).sql
ls -lh ~/backup_masfeliz_prod_*.sql     # confirma que no pesa 0 bytes
```

## 2. Fotografía del "antes" (para poder comparar y para Angélica)

```sql
SELECT nivel_suicidio, COUNT(*) FROM tamizajes GROUP BY nivel_suicidio;
SELECT nivel_riesgo_general, COUNT(*) FROM tamizajes GROUP BY nivel_riesgo_general;
```

Anota los números. Aquí es donde deben aparecer las **~1,771 alertas urgentes** y las
etiquetas viejas (`Riesgo Agudo`, `Evaluación Adicional`, `Alto`, `Moderado`).
Si ya salieran las etiquetas nuevas, **detente**: significa que la base no es la que
crees o que alguien ya corrió esto, y el paso 5 no aplica.

## 3. Trae el código

```bash
git pull
git log --oneline -1        # debe ser el merge del PR #14
```

Verificación inmediata desde tu máquina, sin entrar al panel:

```bash
curl -s -o /dev/null -w "%{http_code}\n" \
  https://masfeliz.coahuila.gob.mx/build/assets/app-DznlpfUf.css   # ahora debe dar 200
```

## 4. Migraciones

```bash
php artisan migrate:status      # revisa TODAS las Pending, no solo las tres nuevas
php artisan migrate --force
php artisan optimize:clear
```

Las tres del lote:

- `2026_08_18_170000_add_respuestas_to_tamizajes_table`
- `2026_08_21_120000_renombra_riesgo_general_a_prioridad_atencion` ← **la de la escala**
- `2026_08_22_120000_renombra_resultado_asq_a_positivo_negativo`

⚠️ Producción se atrasa varias migraciones, no solo las del último despliegue: el
17/08 Configuración General tiró un 500 por `Unknown column
'resultados_tamizaje_visibles'` justo por esto. Lee la salida completa de
`migrate:status`, no asumas que solo faltan tres.

⚠️ La segunda ensancha tres columnas a `VARCHAR(60)` porque *"Agudeza pendiente de
confirmar"* mide exactamente 30 caracteres y no cabía. **Si esa no corre, el paso 5
trunca valores.**

## 5. Reclasifica los históricos

Primero en seco — no escribe nada:

```bash
php artisan tamizajes:reclasificar
```

Imprime la tabla de cambios con el conteo real. **Guarda esa salida y mándasela a
Angélica antes de aplicar**: es su decisión de negocio y ese conteo es el que va a
ver reflejado en las estadísticas. En el servidor de prueba fueron ~1,373 registros
a `Agudeza pendiente de confirmar`.

```bash
php artisan tamizajes:reclasificar --aplicar
```

Idempotente: marca cada registro en el JSON `respuestas` con
`prioridad_atencion_21_08_2026` y **guarda el nivel anterior** ahí mismo para
auditoría. Corrido dos veces, la segunda reporta todo como ya reclasificado.

**Ninguna pasada eleva un histórico a riesgo agudo** — eso lo determina la valoración
clínica, no un recálculo.

## 6. Verifica

```bash
php artisan tamizajes:reclasificar     # debe salir en ceros / "ya estaban reclasificados"
php artisan optimize:clear
```

```sql
SELECT nivel_suicidio, COUNT(*) FROM tamizajes GROUP BY nivel_suicidio;
```

Debe devolver **exactamente dos filas**: `Negativo` y `Positivo`. Cualquier otra cosa
—`Riesgo Agudo`, `Positivo: requiere valoración posterior`, `Evaluación Adicional`—
significa que la migración del paso 4 no corrió.

```sql
SELECT nivel_suicidio, nivel_riesgo_general, COUNT(*)
FROM tamizajes GROUP BY nivel_suicidio, nivel_riesgo_general;
```

Referencia de cómo quedó el servidor de prueba el 22/08 (producción tiene su propia
base, los números **no** tienen que coincidir, pero la forma sí):

| | |
|---|---|
| Tamizajes totales | 13,902 |
| Declinaron participar | 744 |
| ASQ Negativo | 11,778 (Leve 6,306 · Moderada 5,071 · Alta 401) |
| ASQ Positivo | 1,380 (Agudeza pendiente 1,373 · Alta 3 · Urgente 4) |
| Etiquetas viejas sobrevivientes | 0 |

Y una prueba de humo del cuestionario en vivo, que es lo que Angélica va a mirar:
abre `/diagnostico/{token}` de una empresa, contesta "Sí" a la 4 y confirma que
salgan **"¿Cuándo fue el último intento?"** y la **pregunta 5**.

## 7. Avísale a Angélica

Pídele recargar con **Ctrl+Shift+R**. Dos cosas que conviene decirle de entrada:

- En la columna del ASQ, quien contestó "Sí" a la pregunta 5 **ya no se ve en rojo**
  sino en ámbar como cualquier positivo. El rojo no se perdió: está en la columna de
  al lado, Prioridad de atención = `Urgente`.
- Los que quedan en `Agudeza pendiente de confirmar` no son un error: son los
  tamizajes contestados antes de que existiera la pregunta 5, y solo bajan conforme
  Salud los vaya valorando.

Las **cuatro decisiones** del tablero de sintomatología siguen abiertas y este
despliegue no las toca — es justo lo que ella llamó *"mientras confirmamos esto"*.

---

## Si algo sale mal

```bash
mysql -u USUARIO -p NOMBRE_BD < ~/backup_masfeliz_prod_FECHA.sql
```

Para volver solo el código:

```bash
git log --oneline -3
git reset --hard 781e204      # el merge del PR #13, lo que había antes
php artisan optimize:clear
```

El comando también tiene reversa lógica: cada registro tocado conserva
`nivel_suicidio_anterior` y `nivel_riesgo_general_anterior` dentro de su JSON
`respuestas`, así que se puede reconstruir sin el dump si hiciera falta.
