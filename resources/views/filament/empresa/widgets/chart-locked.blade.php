<div class="relative rounded-xl bg-white p-6 shadow-sm border border-gray-100" style="width: 100%; min-height: 280px; display: flex; flex-direction: column;">
    <div style="font-size: 1rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem;">Distribución de Niveles de Riesgo</div>
    
    <!-- Contenido difuminado (mock chart) -->
    <div style="filter: blur(5px); opacity: 0.35; pointer-events: none; user-select: none; flex: 1; display: flex; align-items: center; justify-content: center; gap: 3rem; flex-wrap: wrap; padding: 1rem 0;">
        <!-- Circular Donut SVG -->
        <svg viewBox="0 0 36 36" style="width: 8rem; height: 8rem;">
            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e2e8f0" stroke-width="4" />
            <path stroke-dasharray="60, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10b981" stroke-width="4" />
            <path stroke-dasharray="25, 100" stroke-dashoffset="-60" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#f59e0b" stroke-width="4" />
            <path stroke-dasharray="15, 100" stroke-dashoffset="-85" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#ef4444" stroke-width="4" />
        </svg>

        <!-- Leyenda mock -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem; text-align: left;">
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #475569;">
                <span style="height: 0.75rem; width: 0.75rem; background-color: #10b981; border-radius: 50%; display: inline-block;"></span>
                <span>Leve (60%)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #475569;">
                <span style="height: 0.75rem; width: 0.75rem; background-color: #f59e0b; border-radius: 50%; display: inline-block;"></span>
                <span>Moderado (25%)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #475569;">
                <span style="height: 0.75rem; width: 0.75rem; background-color: #ef4444; border-radius: 50%; display: inline-block;"></span>
                <span>Urgente (15%)</span>
            </div>
        </div>
    </div>

    <!-- Overlay de Bloqueo -->
    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 10;">
        <div style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(4px); border: 1px solid #f1f5f9; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); border-radius: 1rem; padding: 1.25rem 1.75rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; max-width: 26rem;">
            <div style="height: 2.5rem; width: 2.5rem; background-color: #fef2f2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; font-weight: bold; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);">
                🔒
            </div>
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0;">Métricas de Riesgo Bloqueadas</h3>
            <p style="color: #475569; font-size: 0.825rem; line-height: 1.4; margin: 0;">
                El gráfico de distribución de riesgos clínicos se habilitará una vez que completes y envíes tu <strong>Autoevaluación (Paso 2)</strong>.
            </p>
        </div>
    </div>
</div>
