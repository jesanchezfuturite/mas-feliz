{{--
    Franja que identifica al servidor de PRUEBA. Existe porque Angélica pasó
    días revisando el ambiente de prueba creyendo que era producción (25/08/2026)
    y de ahí salieron reportes de datos "incorrectos" que eran de la otra base.

    Se enciende con AMBIENTE_PRUEBA=true en el .env — solo el del servidor de
    prueba la trae. La vista se guarda a sí misma: en producción y en local
    renderiza vacío, así que los providers y layouts la incluyen sin condición.

    Estilos inline a propósito: se dibuja igual en los paneles de Filament y en
    las vistas públicas de Tailwind, sin depender de ninguna hoja compilada.
--}}
@if (config('app.ambiente_prueba'))
    <div style="
        background: repeating-linear-gradient(135deg, #facc15 0 16px, #1f2937 16px 32px);
        padding: 3px;
        position: sticky;
        top: 0;
        z-index: 9999;
    ">
        <div style="
            background-color: #1f2937;
            color: #facc15;
            text-align: center;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            padding: 0.3rem 1rem;
        ">
            ⚠️ AMBIENTE DE PRUEBA — los datos de este servidor no son los de producción
        </div>
    </div>
@endif
