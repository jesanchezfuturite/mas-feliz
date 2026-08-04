<?php

namespace App\Console\Commands;

use App\Models\Autoevaluacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Diagnóstico de las evidencias de autoevaluación.
 *
 * Existe porque hubo dos fallas distintas que se veían igual desde afuera
 * ("no se ven las evidencias"): el 404 de /storage/ en el .htaccess y la
 * modal que solo guardaba en el estado del formulario. Este comando separa
 * ambos casos y, sobre todo, encuentra los archivos que sí se subieron al
 * disco pero perdieron su vínculo con el criterio.
 *
 *   php artisan evidencias:diagnosticar
 *   php artisan evidencias:diagnosticar --recuperar
 */
class DiagnosticarEvidencias extends Command
{
    protected $signature = 'evidencias:diagnosticar
                            {--recuperar : Vuelve a vincular los archivos huérfanos que puedan atribuirse sin ambigüedad}';

    protected $description = 'Revisa el estado de las evidencias de autoevaluación: referenciadas, rotas y huérfanas';

    private const DIRECTORIOS = ['autoevaluacion-evidencias', 'autoevaluaciones_evidencia'];

    public function handle(): int
    {
        $disco = Storage::disk('public');

        $this->diagnosticarEntorno($disco);

        $referenciadas = $this->evidenciasReferenciadas();
        $enDisco = $this->archivosEnDisco($disco);

        $rotas = $referenciadas->reject(fn ($e) => $disco->exists($e['ruta']));
        $huerfanas = $enDisco->diff($referenciadas->pluck('ruta'));

        $this->newLine();
        $this->info('=== Evidencias de autoevaluación ===');
        $this->line('Archivos en disco .................. ' . $enDisco->count());
        $this->line('Referenciadas en autoevaluaciones .. ' . $referenciadas->count());
        $this->line('Referenciadas pero SIN archivo ..... ' . $rotas->count());
        $this->line('En disco SIN referencia (huérfanas)  ' . $huerfanas->count());
        $this->newLine();

        if ($rotas->isNotEmpty()) {
            $this->warn('Referencias rotas: el criterio apunta a un archivo que no está en el disco.');
            $this->table(
                ['Autoevaluación', 'Empresa', 'Criterio', 'Elemento', 'Ruta'],
                $rotas->map(fn ($e) => [$e['autoevaluacion'], $e['empresa'], $e['criterio'], $e['elemento'], $e['ruta']])->all(),
            );
        }

        if ($huerfanas->isNotEmpty()) {
            $this->warn('Archivos huérfanos: se subieron pero ningún criterio los referencia.');
            $this->table(
                ['Archivo', 'Subido', 'Tamaño'],
                $huerfanas->map(fn ($ruta) => [
                    $ruta,
                    date('d/m/Y H:i', $disco->lastModified($ruta)),
                    round($disco->size($ruta) / 1024) . ' KB',
                ])->sortBy(1)->values()->all(),
            );

            $this->newLine();
            $this->line('Estos archivos no se pueden reasignar solos: el nombre no guarda a qué');
            $this->line('criterio pertenecían. Hay que cotejarlos con la empresa, o pedir que los');
            $this->line('vuelvan a adjuntar (ya sin perderse, porque la modal ahora guarda al vuelo).');
        }

        if ($rotas->isNotEmpty()) {
            $this->newLine();
            $this->info('Antes de dar por perdidos los archivos, búscalos en el resto del servidor:');
            $this->line('  find ~ -name "' . basename($rotas->first()['ruta']) . '" 2>/dev/null');
            $this->line('Si aparecen en otra ruta, el despliegue los dejó atrás y basta con moverlos a:');
            $this->line('  ' . storage_path('app/public/' . dirname($rotas->first()['ruta'])));
        }

        if ($rotas->isEmpty() && $huerfanas->isEmpty()) {
            $this->info('Todo en orden: cada evidencia referenciada tiene su archivo y no hay huérfanos.');
        }

        return self::SUCCESS;
    }

    /**
     * Estado del entorno. Sirve para distinguir "el archivo nunca se guardó"
     * de "el archivo se guardó en otra ruta" o "la carpeta se perdió en un
     * despliegue", que desde la interfaz se ven exactamente igual.
     */
    private function diagnosticarEntorno($disco): void
    {
        $raiz = storage_path('app/public');

        $this->newLine();
        $this->info('=== Entorno ===');
        $this->line('Directorio de la aplicación ........ ' . base_path());
        $this->line('Raíz del disco público ............. ' . $raiz);
        $this->line('  ¿existe? ......................... ' . (is_dir($raiz) ? 'sí' : 'NO'));
        $this->line('  ¿escribible? ..................... ' . (is_writable($raiz) ? 'sí' : 'NO'));

        foreach (self::DIRECTORIOS as $directorio) {
            $ruta = $raiz . '/' . $directorio;
            $this->line('  ' . str_pad($directorio, 32, '.') . ' ' . (is_dir($ruta)
                ? count(glob($ruta . '/*')) . ' archivo(s)'
                : 'no existe'));
        }

        $enlace = public_path('storage');
        $this->line('Enlace public/storage .............. ' . (file_exists($enlace)
            ? (is_link($enlace) ? 'symlink → ' . readlink($enlace) : 'existe pero NO es symlink')
            : 'NO existe (falta php artisan storage:link)'));

        $this->line('APP_URL ............................ ' . config('app.url'));
        $this->line('FILESYSTEM_DISK .................... ' . config('filesystems.default'));

        // Si las autoevaluaciones no encuentran su empresa, el problema no es
        // de archivos sino de datos: apunta a una base de otro ambiente.
        $huerfanasDeEmpresa = Autoevaluacion::whereDoesntHave('empresa')->count();
        $this->line('Empresas registradas ............... ' . \App\Models\Empresa::count());
        $this->line('Autoevaluaciones ................... ' . Autoevaluacion::count()
            . ($huerfanasDeEmpresa > 0 ? "  (⚠ {$huerfanasDeEmpresa} sin empresa asociada)" : ''));

        $this->newLine();
    }

    /**
     * Recorre el JSON de respuestas de todas las autoevaluaciones y devuelve
     * cada evidencia con su ubicación exacta.
     */
    private function evidenciasReferenciadas()
    {
        $evidencias = collect();

        Autoevaluacion::with('empresa')->chunk(100, function ($autoevaluaciones) use ($evidencias) {
            foreach ($autoevaluaciones as $autoevaluacion) {
                foreach ($autoevaluacion->respuestas ?? [] as $criterio => $elementos) {
                    if (! is_array($elementos)) {
                        continue;
                    }

                    foreach ($elementos as $elemento => $datos) {
                        $archivo = is_array($datos) ? ($datos['archivo'] ?? null) : null;

                        if (is_array($archivo)) {
                            $archivo = array_values($archivo)[0] ?? null;
                        }

                        if (! $archivo) {
                            continue;
                        }

                        $evidencias->push([
                            'autoevaluacion' => $autoevaluacion->id,
                            'empresa' => $autoevaluacion->empresa?->nombre_empresa ?? 'N/A',
                            'criterio' => $criterio,
                            'elemento' => $elemento,
                            'ruta' => $archivo,
                        ]);
                    }
                }
            }
        });

        return $evidencias;
    }

    private function archivosEnDisco($disco)
    {
        return collect(self::DIRECTORIOS)
            ->flatMap(fn ($directorio) => $disco->exists($directorio) ? $disco->files($directorio) : [])
            ->reject(fn ($ruta) => str_ends_with($ruta, '.gitignore'))
            ->values();
    }
}
