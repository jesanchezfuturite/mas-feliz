<div class="relative w-full">
    <!-- Fondo difuminado con maquetación falsa -->
    <div style="filter: blur(5px); opacity: 0.35; pointer-events: none; user-select: none; width: 100%;">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 w-full">
            <!-- Stat 1 -->
            <div style="background-color: #ffffff; border-radius: 1rem; padding: 1.25rem; display: flex; align-items: center; border: 1px solid #e2e8f0; height: 90px; overflow: hidden;">
                <div style="width: 60%; padding-right: 0.75rem; border-right: 1px solid #e2e8f0;">
                    <div style="font-size: 0.9rem; font-weight: 600; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">http://localhost:8080/tamizaje/...</div>
                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Liga de Diagnóstico</div>
                </div>
                <div style="width: 40%; background-color: #3b82f6; height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem;">Copiar</div>
            </div>
            <!-- Stat 2 -->
            <div style="background-color: #ffffff; border-radius: 1rem; padding: 1.25rem; display: flex; align-items: center; border: 1px solid #e2e8f0; height: 90px; overflow: hidden;">
                <div style="width: 60%; padding-right: 0.75rem; border-right: 1px solid #e2e8f0;">
                    <div style="font-size: 1.75rem; font-weight: 600; color: #111827;">0%</div>
                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Progreso de Participación</div>
                </div>
                <div style="width: 40%; background-color: #eab308; height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem;">Meta: 90%</div>
            </div>
            <!-- Stat 3 -->
            <div style="background-color: #ffffff; border-radius: 1rem; padding: 1.25rem; display: flex; align-items: center; border: 1px solid #e2e8f0; height: 90px; overflow: hidden;">
                <div style="width: 60%; padding-right: 0.75rem; border-right: 1px solid #e2e8f0;">
                    <div style="font-size: 1.75rem; font-weight: 600; color: #111827;">0</div>
                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Total Evaluados</div>
                </div>
                <div style="width: 40%; background-color: #22c55e; height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem;">Colaboradores</div>
            </div>
        </div>
    </div>

    <!-- Overlay de Bloqueo -->
    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 10;">
        <div style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(4px); border: 1px solid #f1f5f9; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); border-radius: 1rem; padding: 1.25rem 1.75rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; max-width: 26rem;">
            <div style="height: 2.5rem; width: 2.5rem; background-color: #fef2f2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; font-weight: bold; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);">
                🔒
            </div>
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0;">Fase de Diagnóstico / Tamizaje Bloqueada</h3>
            <p style="color: #475569; font-size: 0.825rem; line-height: 1.4; margin: 0;">
                Completa y envía tu <strong>Autoevaluación (Paso 2)</strong> para activar la liga de tamizaje y habilitar la evaluación de tus colaboradores.
            </p>
        </div>
    </div>
</div>
