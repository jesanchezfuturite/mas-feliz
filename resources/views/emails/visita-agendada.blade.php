<x-mail::message>
# Hola, {{ $empresa->nombre_empresa }}

Te informamos que se ha agendado la **Visita de Supervisión Presencial** para el proceso de certificación del programa **(+FELIZ)**.

<x-mail::panel>
**Detalles de la Cita:**
- **Fecha y Hora:** {{ $empresa->fecha_visita_presencial->format('d/m/Y h:i A') }}
- **Lugar:** Domicilio registrado de la organización.
</x-mail::panel>

Esta visita es un paso fundamental para validar las acciones implementadas en tu organización y avanzar hacia la obtención del distintivo. Un evaluador oficial se presentará en la fecha y hora indicadas.

<x-mail::button :url="config('app.url') . '/tablero'">
Ir al Tablero de la Empresa
</x-mail::button>

Si tienes alguna duda o necesitas reprogramar la visita, por favor ponte en contacto con tu evaluador asignado.

Gracias,<br>
Equipo de Certificación (+FELIZ)
</x-mail::message>
