<?php

namespace App\Filament\Empresa\Resources\Autoevaluacions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class AutoevaluacionForm
{
    public static function configure(Schema $schema): Schema
    {
        $tabs = [];
        $isAdmin = in_array(filament()->getCurrentPanel()?->getId(), ['admin', 'evaluador']);

        $categorias = [
            1 => '+FORTALECIMIENTO',
            2 => '+FORTALECIMIENTO',
            3 => '+PREVENCIÓN',
            4 => '+FORTALECIMIENTO',
            5 => '+PREVENCIÓN',
            6 => '+CUIDADO/ATENCIÓN',
            7 => '+CUIDADO/ATENCIÓN',
            8 => '+PREVENCIÓN',
            9 => '+FORTALECIMIENTO',
            10 => '+FORTALECIMIENTO',
            11 => '+FORTALECIMIENTO',
            12 => '+CUIDADO/ATENCIÓN',
            13 => '+FORTALECIMIENTO',
            14 => '+CUIDADO/ATENCIÓN',
            15 => '+CUIDADO/ATENCIÓN',
            16 => '+FORTALECIMIENTO',
            17 => '+PREVENCIÓN',
            18 => '+PREVENCIÓN',
            19 => '+CUIDADO/ATENCIÓN',
            20 => '+FORTALECIMIENTO',
        ];

        $requisitos = [
            1 => 'NECESARIO',
            2 => 'NECESARIO',
            3 => 'NECESARIO',
            4 => 'INDISPENSABLE',
            5 => 'NECESARIO',
            6 => 'NECESARIO',
            7 => 'NECESARIO',
            8 => 'NECESARIO',
            9 => 'INDISPENSABLE',
            10 => 'INDISPENSABLE',
            11 => 'NECESARIO',
            12 => 'NECESARIO',
            13 => 'NECESARIO',
            14 => 'NECESARIO',
            15 => 'INDISPENSABLE',
            16 => 'INDISPENSABLE',
            17 => 'NECESARIO',
            18 => 'NECESARIO',
            19 => 'NECESARIO',
            20 => 'DESEABLE',
        ];

        $indispensablesComponents = [];
        $necesariosFortalecimiento = [];
        $necesariosPrevencion = [];
        $necesariosCuidado = [];
        $deseablesComponents = [];

        for ($i = 1; $i <= 20; $i++) {
            if ($i === 1) {
                $mainText = 'Criterio 1.- Existencia de una política/declaración interna para promover la Salud Mental en la organización';
                $elementos = [
                    ['short' => 'Política documentada', 'desc' => 'La organización cuenta con una política/declaración interna documentada para prevenir, cuidar atender y fortalecer la Salud Mental.'],
                    ['short' => 'Participación de áreas', 'desc' => 'La política fue elaborada con la participación de distintas áreas de la organización'],
                    ['short' => 'Aprobación directiva', 'desc' => 'La política está aprobada por la alta dirección y forma parte de los planes y programas institucionales'],
                    ['short' => 'Roles y responsabilidades', 'desc' => 'La política define claramente roles, funciones y responsabilidades para su implementación, seguimiento y evaluación'],
                    ['short' => 'Difusión al personal', 'desc' => 'La política ha sido difundida a todo el personal de las diferentes áreas y turnos'],
                    ['short' => 'Mecanismos de evaluación', 'desc' => 'La organización estableció mecanismos de evaluación para garantizar su comprensión'],
                    ['short' => 'Conocimiento y comprensión', 'desc' => 'El personal conoce la política, sabe dónde consultarla y comprende sus alcances'],
                ];
            }
            elseif ($i === 2) {
                $mainText = 'Criterio 2: La organización conforma un Comité de Salud Mental';
                $elementos = [
                    ['short' => 'Comité constituido', 'desc' => 'La organización cuenta con un Comité de Salud Mental formalmente constituido y aprobado por las máximas autoridades'],
                    ['short' => 'Integración representativa', 'desc' => 'El Comité está integrado de manera representativa por distintas áreas y niveles jerárquicos, incluyendo personal operativo'],
                    ['short' => 'Funciones definidas', 'desc' => 'El Comité tiene funciones, responsabilidades y atribuciones claramente definidas'],
                    ['short' => 'Reuniones y evidencia', 'desc' => 'El Comité cuenta con calendario de sesiones, realiza reuniones periódicas y genera evidencia documental de sus actividades, acuerdos y seguimiento.'],
                    ['short' => 'Alineación con el plan', 'desc' => 'El Comité está alineado con el plan de prevención, cuidado, atención y fortalecimiento de salud mental.'],
                ];
            }
            elseif ($i === 3) {
                $mainText = 'Criterio 3: Existencia de un Plan de prevención, cuidado/atención y fortalecimiento de la Salud Mental vinculado a la política organizacional';
                $elementos = [
                    ['short' => 'Plan establecido', 'desc' => 'La organización cuenta con un plan de prevención, cuidado, atención y fortalecimiento de salud mental formalmente establecido y vinculado a la política organizacional de salud mental.'],
                    ['short' => 'Acciones según diagnóstico', 'desc' => 'El plan considera acciones planificadas de prevención, cuidado/atención y fortalecimiento de la salud mental derivadas del diagnóstico de salud mental de los trabajadores.'],
                    ['short' => 'Atención de riesgos', 'desc' => 'El plan considera acciones para la atención de riesgos psicosociales basado en la identificación de riesgos del entorno laboral y el clima organizacional.'],
                    ['short' => 'Seguimiento y evaluación', 'desc' => 'La organización cuenta con mecanismos documentados de seguimiento, evaluación y mejora continua del plan, incluyendo monitorización del impacto de las acciones a través de indicadores.'],
                    ['short' => 'Capacitación y difusión', 'desc' => 'Se implementan acciones de capacitación, sensibilización y difusión del Plan de prevención, cuidado, atención y fortalecimiento de salud mental.'],
                    ['short' => 'Evidencia de implementación', 'desc' => 'Existe evidencia documental que demuestra la implementación, operación y seguimiento del plan.'],
                ];
            }
            elseif ($i === 4) {
                $mainText = 'Criterio 4: La organización desarrolla un programa de salud mental, basado en el diagnóstico de los principales riesgos relacionados a la salud mental de los trabajadores.';
                $elementos = [
                    ['short' => 'Herramientas de tamizaje', 'desc' => 'La organización realiza un diagnóstico de salud mental mediante herramientas de tamizaje validadas para la detección de ansiedad, depresión y conducta suicida.'],
                    ['short' => 'Identificación de riesgos', 'desc' => 'La identificación de riesgos cumple con los incisos de la a) a la e)'],
                    ['short' => 'Diseño y vinculación del programa', 'desc' => 'La organización diseña un programa de salud mental y cuenta con evidencia documental de la aplicación de tamizaje, análisis de resultados y la implementación de acciones prevención, cuidado y fortalecimiento derivadas del diagnóstico y lo vincula al plan de prevención, cuidado, atención y fortalecimiento de salud mental de la organización.'],
                ];
            }
            elseif ($i === 5) {
                $mainText = 'Criterio 5: La organización diseña un programa de atención de los riesgos psicosociales del entorno laboral.';
                $elementos = [
                    ['short' => 'Identificación y evaluación de riesgos', 'desc' => 'La organización identifica y evalúa los riesgos psicosociales mediante herramientas validadas y de conformidad con la legislación aplicable vigente.'],
                    ['short' => 'Muestra representativa y confidencialidad', 'desc' => 'La evaluación se realiza en una muestra representativa y garantiza la confidencialidad de la información obtenida.'],
                    ['short' => 'Riesgos documentados y priorizados', 'desc' => 'Los riesgos identificados se encuentran documentados y priorizados.'],
                    ['short' => 'Programa de intervención', 'desc' => 'La organización cuenta con un programa de intervención basado en los resultados obtenidos, que incluye: acciones de mejora, responsables, plazos e indicadores de seguimiento.'],
                    ['short' => 'Evidencia de acciones', 'desc' => 'Existe evidencia de la implementación, seguimiento y evaluación de las acciones establecidas.'],
                    ['short' => 'Reincorporación laboral', 'desc' => 'Cuando aplique, la organización cuenta con mecanismos de seguimiento y reincorporación laboral para trabajadores que hayan presentado afectaciones relacionadas con la salud mental'],
                    ['short' => 'Programa integrado al Plan', 'desc' => 'El programa de atención de riesgos psicosociales del entorno laboral se encuentra integrado al Plan de prevención, cuidado, atención y fortalecimiento de salud mental de la organización.'],
                ];
            }
            elseif ($i === 6) {
                $mainText = 'Criterio 6: La organización evalúa anualmente el clima laboral y la cultura organizacional';
                $elementos = [
                    ['short' => 'Proceso estructurado', 'desc' => 'Se cuenta con un proceso estructurado para evaluar el Clima laboral y la Cultura Organizacional.'],
                    ['short' => 'Medición anual', 'desc' => 'La organización realiza al menos una medición anual del clima laboral y cultura organizacional'],
                    ['short' => 'Instrumento validado', 'desc' => 'La medición del clima laboral se lleva a cabo mediante un instrumento validado'],
                    ['short' => 'Análisis de resultados', 'desc' => 'La organización analiza e interpreta los resultados obtenidos para identificar fortalezas, áreas de oportunidad y riesgos organizacionales'],
                    ['short' => 'Acciones de mejora', 'desc' => 'La organización desarrolla acciones de mejora basado en los resultados y los incluye en el Programa de atención de los riesgos psicosociales del entorno laboral'],
                    ['short' => 'Seguimiento de acciones', 'desc' => 'La organización da seguimiento a las acciones implementadas'],
                ];
            }
            elseif ($i === 7) {
                $mainText = 'Criterio 7: La organización establece estrategias para promover condiciones básicas del ambiente físico';
                $elementos = [
                    ['short' => 'Estrategias de orden y limpieza', 'desc' => 'La organización cuenta con estrategias para promover el orden y la limpieza.'],
                    ['short' => 'Condiciones de seguridad', 'desc' => 'Los espacios de trabajo cumplen con condiciones básicas de seguridad definidas en los incisos a) a la h).'],
                    ['short' => 'Evidencia de evaluación', 'desc' => 'La organización cuenta con evidencia de la evaluación periódica del cumplimiento.'],
                ];
            }
            elseif ($i === 8) {
                $mainText = 'Criterio 8: La organización implementa una estrategia para promover entornos saludables';
                $elementos = [
                    ['short' => 'Estrategia de entornos saludables', 'desc' => 'La organización implementa una estrategia para fomentar entornos saludables que incluyan al menos los incisos de la a) a la e.'],
                    ['short' => 'Difusión de la estrategia', 'desc' => 'La organización difunde la estrategia de Entornos Saludables entre el personal operativo'],
                    ['short' => 'Seguimiento a quejas', 'desc' => 'La organización da seguimiento y resolución a las quejas recibidas, a través del comité de salud mental'],
                    ['short' => 'Evaluación periódica', 'desc' => 'La organización documenta y evalúa periódicamente la estrategia .'],
                ];
            }
            elseif ($i === 9) {
                $mainText = 'Criterio 9: La organización cuenta con una estrategia de promoción de la salud mental';
                $elementos = [
                    ['short' => 'Estrategia documentada y vigente', 'desc' => 'La organización cuenta con una estrategia de promoción de la salud mental documentada y vigente.'],
                    ['short' => 'Alineación y componentes', 'desc' => 'La estrategia se encuentra alineada con el programa de salud mental y del “Gran Programa Estatal de Salud Mental” e incluye al menos los componentes enunciados del inciso a) a la e.'],
                    ['short' => 'Evidencia de ejecución', 'desc' => 'Existe evidencia documental de la ejecución de las actividades (listas de asistencia, materiales, convocatorias, reportes).'],
                    ['short' => 'Difusión y accesibilidad', 'desc' => 'La estrategia es difundida al personal y es accesible para diferentes áreas, niveles y turnos.'],
                    ['short' => 'Evaluación y monitorización', 'desc' => 'La organización evalúa periódicamente la estrategia mediante indicadores definidos (ej. cobertura, participación, cumplimiento de actividades), y documenta resultados.'],
                ];
            }
            elseif ($i === 10) {
                $mainText = 'Criterio 10: La organización capacita a directivos, líderes y gerentes, en detección y atención temprana de señales de alerta de salud mental';
                $elementos = [
                    ['short' => 'Programa de capacitación directiva', 'desc' => 'La organización cuenta con acciones de capacitación en salud mental dirigido a directivos, líderes y gerentes'],
                    ['short' => 'Cobertura garantizada', 'desc' => 'La organización garantiza la cobertura de capacitación a directivos, líderes y gerentes'],
                    ['short' => 'Contenidos de capacitación', 'desc' => 'La organización establece contenidos de capacitación en salud mental tomando en cuenta al menos del inciso a) a la h)'],
                    ['short' => 'Habilidades prácticas', 'desc' => 'La organización desarrolla habilidades prácticas en los líderes para el abordaje de situaciones relacionadas con salud mental'],
                    ['short' => 'Evaluación de efectividad', 'desc' => 'La organización evalúa la efectividad de la capacitación'],
                ];
            }
            elseif ($i === 11) {
                $mainText = 'Criterio 11: La organización implementa acciones de capacitación en alfabetización y concientización en salud mental dirigidas al personal';
                $elementos = [
                    ['short' => 'Capacitación periódica', 'desc' => 'La organización realiza acciones de capacitación en salud mental dirigidas al personal, al menos dos veces al año y al personal de nuevo ingreso.'],
                    ['short' => 'Contenidos mínimos', 'desc' => 'La organización cuenta con al menos los temas de la a) a la f)'],
                    ['short' => 'Cobertura del 80%', 'desc' => 'La organización capacita al menos al 80% de su personal, considerando las diferentes áreas, niveles y turnos.'],
                ];
            }
            elseif ($i === 12) {
                $mainText = 'Criterio 12: La organización capacita a personal mandos medios y directivos como “gatekeepers” o líderes entrenados para la implementación del protocolo integral de actuación ante crisis en salud mental';
                $elementos = [
                    ['short' => 'Designación de gatekeepers', 'desc' => 'La organización identifica y designa personal clave que funge como “gatekeepers”'],
                    ['short' => 'Capacitación formal', 'desc' => 'El personal asignado cuenta con capacitación formal para la detección oportuna, atención inicial y canalización del personal en riesgo, acorde a lo solicitado en el numeral de la a) a la g.'],
                    ['short' => 'Funciones delimitadas', 'desc' => 'Se delimitan claramente las funciones, alcances del personal capacitado y mecanismos de confidencialidad'],
                    ['short' => 'Evaluación de desempeño', 'desc' => 'La organización evalúa el desempeño y efectividad de las actividades de los gatekeepers mediante simulacros, encuestas de usuarios, análisis y seguimiento de casos'],
                    ['short' => 'Manejo ético', 'desc' => 'Se garantiza la confidencialidad y el manejo ético de la información relacionada a la atención de casos'],
                ];
            }
            elseif ($i === 13) {
                $mainText = 'Criterio 13: La organización implementa spaces y actividades orientadas al desarrollo de habilidades socioemocionales';
                $elementos = [
                    ['short' => 'Espacios y mecanismos', 'desc' => 'La organización cuenta con espacios o mecanismos para el desarrollo de habilidades socioemocionales del personal.'],
                    ['short' => 'Componentes mínimos', 'desc' => 'Las acciones implementadas contemplan al menos los elementos señalados en los incisos a) al g).'],
                    ['short' => 'Evidencia de ejecución', 'desc' => 'Las acciones se encuentran implementadas y existe evidencia documental de su ejecución.'],
                    ['short' => 'Evaluación y mejora', 'desc' => 'La organización evalúa periódicamente los resultados de las acciones implementadas y establece acciones de mejora continua cuando corresponda'],
                ];
            }
            elseif ($i === 14) {
                $mainText = 'Criterio 14: La organización implementa mecanismos permanentes de difusión para que el personal conozca los servicios de apoyo en salud mental disponibles y la forma de acceder a ellos.';
                $elementos = [
                    ['short' => 'Información visible', 'desc' => 'La organización cuenta con información visible y de fácil acceso relacionada a servicios de apoyo internos y/o externos en salud mental disponibles, en espacios estratégicos del centro de trabajo.'],
                    ['short' => 'Línea de Vida estatal', 'desc' => 'La organización incluye la Línea de Vida Estatal dentro de las acciones de difusión de los servicios de apoyo en salud mental dirigidos al personal'],
                    ['short' => 'Directorio actualizado', 'desc' => 'La organización cuenta con un directorio actualizado con servicios de apoyo internos y externos vigentes.'],
                    ['short' => 'Directorio accesible', 'desc' => 'El directorio está disponible y accesible para todo el personal, sin distinción de área, turno o nivel jerárquico.'],
                    ['short' => 'Conocimiento del directorio', 'desc' => 'El personal conoce la existencia del directorio y sabe cómo acceder a los recursos.'],
                    ['short' => 'Actualización periódica', 'desc' => 'La organización actualiza periódicamente el directorio para garantizar su vigencia'],
                ];
            }
            elseif ($i === 15) {
                $mainText = 'Criterio 15:La organización garantiza el acceso oportuno, confidencial y efectivo a servicios de apoyo psicológico internos o externos para su personal';
                $elementos = [
                    ['short' => 'Servicios establecidos', 'desc' => 'La organización cuenta con servicios de apoyo psicológico internos o externos formalmente establecidos acorde a lo solicitado del inciso a) a la d).'],
                    ['short' => 'Evidencia de disponibilidad', 'desc' => 'Existe evidencia de la implementación y utilización de los servicios de apoyo psicológico'],
                    ['short' => 'Evaluación de servicios', 'desc' => 'La organización evalúa periódicamente la efectividad, accesibilidad y satisfacción de los servicios, documenta los resultados y establece acciones de mejora continua.'],
                ];
            }
            elseif ($i === 16) {
                $mainText = 'Criterio 16: La organización cuenta con un protocolo integral de actuación ante crisis de salud mental en el personal';
                $elementos = [
                    ['short' => 'Protocolo documentado y aprobado', 'desc' => 'La organización cuenta con un protocolo integral de actuación ante crisis de salud mental en el personal, que se encuentre documentado, vigente y formalmente aprobado por la alta dirección'],
                    ['short' => 'Elementos del protocolo', 'desc' => 'La organización incluye en el protocolo al menos los elementos señalados de la a) a la j)'],
                    ['short' => 'Difusión del protocolo', 'desc' => 'La organización cuenta con la difusión del protocolo entre el personal responsable de la actuación.'],
                    ['short' => 'Conocimiento y activación', 'desc' => 'El personal responsable conoce los mecanismos de activación y actuación establecidos en el protocolo.'],
                    ['short' => 'Revisión y seguimiento', 'desc' => 'La organización realiza la revisión del protocolo, se actualiza periódicamente y se da seguimiento de los casos.'],
                ];
            }
            elseif ($i === 17) {
                $mainText = 'Criterio 17: El Protocolo integral de actuación ante crisis de salud mental se prueba al menos una vez al año';
                $elementos = [
                    ['short' => 'Simulacro anual', 'desc' => 'La organización realiza al menos un simulacro por año para probar todo el protocolo integral de actuación ante crisis de salud mental, o parte del mismo.'],
                    ['short' => 'Participación de gatekeepers', 'desc' => 'La organización cuenta con la participación de todo el personal responsable (gatekeepers) en los simulacros.'],
                    ['short' => 'Algoritmos de atención', 'desc' => 'La organización realiza simulacros basados en los algoritmos de atención de crisis de salud mental'],
                    ['short' => 'Aspectos evaluados', 'desc' => 'Se evalúan aspectos como tiempos de respuesta, apego al protocolo, comunicación, escalamiento y canalización.'],
                    ['short' => 'Evaluación y mejora', 'desc' => 'La organización evalua los resultados de las pruebas de simulacro e implementa acciones de mejora cuando se detectan desviaciones o áreas de oportunidad'],
                ];
            }
            elseif ($i === 18) {
                $mainText = 'Criterio 18: La organización implementa una estrategia formal de reconocimiento a los trabajadores';
                $elementos = [
                    ['short' => 'Estrategia formal', 'desc' => 'La organización cuenta con una estrategia formal de reconocimiento a las personas trabajadoras, documentada, vigente e integrada al Plan de Prevención, Cuidado/Atención y Fortalecimiento de la Salud Mental.'],
                    ['short' => 'Elementos de la estrategia', 'desc' => 'La estrategia contempla al menos los elementos señalados en los incisos a) al f).'],
                    ['short' => 'Difusión y evidencia', 'desc' => 'La estrategia es difundida al personal y existen evidencias de su implementación .'],
                    ['short' => 'Evaluación e impacto', 'desc' => 'La organización evalúa periódicamente los resultados e impacto de la estrategia, documenta los hallazgos y establece acciones de mejora continua'],
                ];
            }
            elseif ($i === 19) {
                $mainText = 'Criterio 19:  La organización implementa acciones de fortalecimiento de competencias parentales y apoyo a las personas trabajadoras';
                $elementos = [
                    ['short' => 'Estrategia de fortalecimiento', 'desc' => 'La organización cuenta con acciones de fortalecimiento de competencias parentales y apoyo familiar alineado al Gran Programa Estatal de Salud Mental y la Estrategia SOMOS+ Ruta de Atención de Señales de Alerta.'],
                    ['short' => 'Componentes mínimos', 'desc' => 'Las acciones implementadas incluyen al menos los componentes señalados en los incisos de la a) a j.'],
                    ['short' => 'Difusión y accesibilidad', 'desc' => 'Las acciones son difundidas y accesibles para el personal de diferentes áreas, categorías laborales, niveles jerárquicos y turnos.'],
                    ['short' => 'Evidencia de implementación', 'desc' => 'Existe evidencia documental de la implementación de las acciones.'],
                    ['short' => 'Evaluación periódica', 'desc' => 'La organización evalúa periódicamente la participación, satisfacción y utilidad percibida de las acciones implementadas.'],
                ];
            }
            elseif ($i === 20) {
                $mainText = 'Criterio 20: La organización integra la prevención, cuidado y fortalecimiento de la salud mental de manera sostenible en su cultura y estrategia empresarial a través de un sistema de gestión de la salud mental';
                $elementos = [
                    ['short' => 'Monitoreo continuo', 'desc' => 'La organización implementa un monitoreo continuo del desempeño de su sistema de gestión de salud mental a través de tablero de indicadores, informes periódicos, análisis de tendencias entre otros.'],
                    ['short' => 'Elementos del sistema', 'desc' => 'El sistema contempla lo señalado del inciso a) a la c.'],
                    ['short' => 'Ajuste de intervenciones', 'desc' => 'La organización ajusta las intervenciones que conforman su plan de salud mental considerando la información obtenida de su sistema de monitorización.'],
                    ['short' => 'Evidencia de monitoreo', 'desc' => 'Se cuenta con evidencia objetiva del monitoreo de los resultados sostenidos durante al menos un año, las acciones de mejora implementadas y la evaluación del impacto de dichas acciones.'],
                    ['short' => 'Comunicación de resultados', 'desc' => 'Los resultados globales son comunicados a toda la organización'],
                ];
            }

            // $isAdmin is defined at the top
            $gridElementos = [];
            foreach ($elementos as $index => $item) {
                $elemId = $index + 1;
                
                $gridElementos[] = Grid::make(12)
                    ->schema([
                        Placeholder::make("titulo_elemento_{$i}_{$elemId}")
                            ->hiddenLabel()
                            ->content(function ($get) use ($item, $i, $elemId) {
                                $comentario = $get("respuestas.criterio_{$i}.elemento_{$elemId}.comentario");
                                $archivo = $get("respuestas.criterio_{$i}.elemento_{$elemId}.archivo");
                                $feedback = $get("respuestas.criterio_{$i}.elemento_{$elemId}.feedback");
                                $calificacion = $get("respuestas.criterio_{$i}.elemento_{$elemId}.calificacion_politica");
                                $evaluadorEmail = $get("respuestas.criterio_{$i}.elemento_{$elemId}.evaluador_email");

                                $tooltipAttr = $evaluadorEmail ? " title=\"Evaluado por: {$evaluadorEmail}\"" : "";

                                $badges = [];
                                if ($comentario) {
                                    $badges[] = '<span style="background-color: #eff6ff; color: #1d4ed8; font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px; font-weight: 500; border: 1px solid #bfdbfe;">📝 Comentario</span>';
                                }
                                if ($archivo) {
                                    $url = \Illuminate\Support\Facades\Storage::disk('public')->url($archivo);
                                    $badges[] = "<a href=\"{$url}\" target=\"_blank\" style=\"background-color: #f0fdf4; color: #15803d; font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px; font-weight: 500; border: 1px solid #bbf7d0; text-decoration: none; cursor: pointer; display: inline-block;\">📎 Evidencia</a>";
                                }
                                if ($calificacion === 'validado' || $calificacion === 'Aprobado') {
                                    $badges[] = "<span{$tooltipAttr} style=\"background-color: #f0fdf4; color: #166534; font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px; font-weight: 600; border: 1px solid #bbf7d0; cursor: help;\">✅ Validado</span>";
                                } elseif ($calificacion === 'revisado') {
                                    $badges[] = "<span{$tooltipAttr} style=\"background-color: #fffbeb; color: #92400e; font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px; font-weight: 600; border: 1px solid #fde68a; cursor: help;\">⚠️ Revisado</span>";
                                } elseif ($calificacion === 'no_cumple' || $calificacion === 'Rechazado') {
                                    $badges[] = "<span{$tooltipAttr} style=\"background-color: #fef2f2; color: #991b1b; font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px; font-weight: 600; border: 1px solid #fecaca; cursor: help;\">❌ No cumple</span>";
                                }
                                if ($feedback) {
                                    $badges[] = "<span{$tooltipAttr} style=\"background-color: #fdf4ff; color: #a21caf; font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px; font-weight: 500; border: 1px solid #fbcfe8; cursor: help;\">💬 Retroalimentación</span>";
                                }

                                $badgesHtml = !empty($badges) ? '<div style="margin-top: 0.75rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">' . implode('', $badges) . '</div>' : '';

                                return new HtmlString("<div style='display: flex; flex-direction: column;'>
                                    <span style='font-weight: 600; color: #1f2937;'>{$item['short']}</span>
                                    <span style='font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;'>{$item['desc']}</span>
                                    {$badgesHtml}
                                </div>");
                            })
                            ->columnSpan(8),

                        Select::make("respuestas.criterio_{$i}.elemento_{$elemId}.score")
                            ->hiddenLabel()
                            ->disabled(fn ($record) => $isAdmin || ($record && in_array($record->estatus, ['En revisión', 'Validado'])))
                            ->options(function () use ($isAdmin) {
                                if ($isAdmin) {
                                    return [
                                        '10' => 'Cumple (10 puntos)',
                                        '5' => 'En proceso (5 puntos)',
                                        '0' => 'No cumple (0 puntos)',
                                        'NA' => 'No aplica',
                                        '' => 'Selecciona...',
                                    ];
                                } else {
                                    return [
                                        '10' => 'Cumple',
                                        '5' => 'En proceso',
                                        '0' => 'No cumple',
                                        'NA' => 'No aplica',
                                    ];
                                }
                            })
                            ->afterStateHydrated(function (\Filament\Forms\Components\Select $component, $state) {
                                if ($state === null) {
                                    $component->state('');
                                }
                            })
                            ->placeholder('Seleccione una opción')
                            ->columnSpan(3)
                            ->extraAttributes(['style' => 'display: flex; flex-direction: column; justify-content: flex-start; height: 100%;'])
                            ->live()
                            ->afterStateUpdated(function ($state, $get, $livewire) {
                                $livewire->dispatch('formUpdated', respuestas: $get('respuestas') ?? []);
                            }),

                        // Hidden fields that store the data inside the main form state
                        Hidden::make("respuestas.criterio_{$i}.elemento_{$elemId}.comentario"),
                        Hidden::make("respuestas.criterio_{$i}.elemento_{$elemId}.archivo"),
                        Hidden::make("respuestas.criterio_{$i}.elemento_{$elemId}.calificacion_politica"),
                        Hidden::make("respuestas.criterio_{$i}.elemento_{$elemId}.evaluador_email"),
                        Hidden::make("respuestas.criterio_{$i}.elemento_{$elemId}.feedback"),

                        Actions::make([
                            Action::make("detalles_{$i}_{$elemId}")
                                ->label('')
                                ->icon('heroicon-m-document-plus')
                                ->color('gray')
                                ->extraAttributes(['style' => 'height: 36px !important; padding: 0 1rem !important; display: inline-flex; align-items: center; justify-content: center;'])
                                ->tooltip('Agregar Detalles o Evidencia')
                                ->modalHeading('Detalles y Evidencia')
                                ->modalSubmitActionLabel('Guardar detalles')
                                ->extraModalWindowAttributes([
                                    'style' => 'background-color: #ffffff !important; background: #ffffff !important;'
                                ])
                                ->modalSubmitAction(fn ($action) => $action->hidden(fn ($record) => ! $isAdmin && $record && in_array($record->estatus, ['En revisión', 'Validado'])))
                                ->modalCancelAction(fn ($action) => $action->hidden(fn ($record) => ! $isAdmin && $record && in_array($record->estatus, ['En revisión', 'Validado'])))
                                ->form(function () use ($isAdmin) {
                                    return [
                                        Textarea::make('comentario')
                                            ->label('Comentario')
                                            ->rows(3)
                                            ->disabled(fn ($record) => $isAdmin || ($record && in_array($record->estatus, ['En revisión', 'Validado']))),

                                        FileUpload::make('archivo')
                                            ->label('Evidencia (documento o imagen)')
                                            ->helperText('Adjunta un documento (PDF o Word) o una imagen que respalde el cumplimiento de este elemento. Tamaño máximo 50 MB.')
                                            ->disk('public')
                                            ->directory('autoevaluacion-evidencias')
                                            ->downloadable()
                                            ->openable()
                                            ->maxSize(51200)
                                            ->acceptedFileTypes([
                                                'application/pdf',
                                                'image/*',
                                                'application/msword',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                            ])
                                            ->visible(fn ($record) => ! $isAdmin && ! ($record && in_array($record->estatus, ['En revisión', 'Validado']))),

                                        Placeholder::make('archivo_link')
                                            ->label('Ver Evidencia')
                                            ->content(function ($get) {
                                                $archivo = $get('archivo');
                                                if (is_array($archivo)) {
                                                    $archivo = array_values($archivo)[0] ?? null;
                                                }
                                                if (! $archivo) return 'No hay evidencia subida.';
                                                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($archivo);
                                                return new \Illuminate\Support\HtmlString("<a href=\"{$url}\" target=\"_blank\" style=\"color: #2563eb; text-decoration: underline; font-weight: 500;\">Ver/Descargar Archivo</a>");
                                            })
                                            ->visible(fn ($record) => $isAdmin || ($record && in_array($record->estatus, ['En revisión', 'Validado']))),
                                            
                                        \Filament\Forms\Components\ToggleButtons::make('calificacion_politica')
                                            ->label('Dictamen del Evaluador')
                                            ->options([
                                                'validado' => 'Validado',
                                                'revisado' => 'Revisado',
                                                'no_cumple' => 'No cumple',
                                            ])
                                            ->colors([
                                                'validado' => 'success',
                                                'revisado' => 'warning',
                                                'no_cumple' => 'danger',
                                            ])
                                            ->icons([
                                                'validado' => 'heroicon-m-check-circle',
                                                'revisado' => 'heroicon-m-exclamation-circle',
                                                'no_cumple' => 'heroicon-m-x-circle',
                                            ])
                                            ->required(fn () => $isAdmin)
                                            ->live()
                                            ->disabled(fn ($record) => ! $isAdmin || ($record && $record->estatus === 'Validado')),
                                            
                                        Textarea::make('feedback')
                                            ->label('Retroalimentación del evaluador')
                                            ->rows(2)
                                            ->required(fn ($get) => in_array($get('calificacion_politica'), ['revisado', 'no_cumple']))
                                            ->disabled(fn ($record) => ! $isAdmin || ($record && $record->estatus === 'Validado')),
                                    ];
                                })
                                ->fillForm(fn ($get) => [
                                    'comentario' => $get("respuestas.criterio_{$i}.elemento_{$elemId}.comentario"),
                                    'archivo' => $get("respuestas.criterio_{$i}.elemento_{$elemId}.archivo"),
                                    'calificacion_politica' => $get("respuestas.criterio_{$i}.elemento_{$elemId}.calificacion_politica"),
                                    'feedback' => $get("respuestas.criterio_{$i}.elemento_{$elemId}.feedback"),
                                ])
                                ->action(function (array $data, $set, $record) use ($i, $elemId, $isAdmin) {
                                    if (array_key_exists('comentario', $data)) {
                                        $set("respuestas.criterio_{$i}.elemento_{$elemId}.comentario", $data['comentario']);
                                    }
                                    if (array_key_exists('archivo', $data)) {
                                        $archivo = $data['archivo'];
                                        if (is_array($archivo)) {
                                            $archivo = array_values($archivo)[0] ?? null;
                                        }
                                        $set("respuestas.criterio_{$i}.elemento_{$elemId}.archivo", $archivo);
                                    }
                                    if (array_key_exists('calificacion_politica', $data)) {
                                        $set("respuestas.criterio_{$i}.elemento_{$elemId}.calificacion_politica", $data['calificacion_politica']);
                                    }
                                    if (array_key_exists('feedback', $data)) {
                                        $set("respuestas.criterio_{$i}.elemento_{$elemId}.feedback", $data['feedback']);
                                    }

                                    // If admin, save feedback directly since the form is read-only
                                    if ($record && $isAdmin) {
                                        $respuestas = $record->respuestas ?? [];
                                        if (array_key_exists('calificacion_politica', $data)) {
                                            $respuestas["criterio_{$i}"]["elemento_{$elemId}"]['calificacion_politica'] = $data['calificacion_politica'];
                                        }
                                        if (array_key_exists('feedback', $data)) {
                                            $respuestas["criterio_{$i}"]["elemento_{$elemId}"]['feedback'] = $data['feedback'];
                                        }
                                        
                                        $evaluadorEmail = auth()->user()?->email;
                                        $respuestas["criterio_{$i}"]["elemento_{$elemId}"]['evaluador_email'] = $evaluadorEmail;
                                        $set("respuestas.criterio_{$i}.elemento_{$elemId}.evaluador_email", $evaluadorEmail);

                                        $record->update(['respuestas' => $respuestas]);
                                    }
                                })
                        ])
                            ->key("detalles_actions_{$i}_{$elemId}")
                            ->columnSpan(1)->extraAttributes(['style' => 'justify-content: flex-start !important; width: 100%; margin-top: 0 !important; margin-bottom: 0 !important;'])
                    ])
                    ->extraAttributes([
                        'style' => 'border: 1px solid #f3f4f6; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; align-items: center; background-color: #ffffff; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);'
                    ]);
            }

            $categoria = $categorias[$i] ?? '';
            $requisito = $requisitos[$i] ?? null;

            $badgeStyle = match($requisito) {
                'INDISPENSABLE' => "background-color: #fef2f2; color: #dc2626; border: 1px solid #fca5a5;",
                'NECESARIO' => "background-color: #fffbeb; color: #d97706; border: 1px solid #fcd34d;",
                'DESEABLE' => "background-color: #ecfdf5; color: #059669; border: 1px solid #6ee7b7;",
                default => "background-color: #f3f4f6; color: #4b5563; border: 1px solid #d1d5db;",
            };

            $requisitoHtml = $requisito 
                ? "<span style='{$badgeStyle} padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.05em;'>{$requisito}</span>" 
                : "";

            $sectionTitle = str_starts_with($mainText, "Criterio {$i}.-") 
                ? $mainText 
                : (str_starts_with($mainText, "Criterio {$i}:") ? $mainText : "Criterio {$i}.- {$mainText}");

            $criterioSection = \Filament\Schemas\Components\Section::make($sectionTitle)
                ->description(new HtmlString("
                    <div style='display: flex; justify-content: flex-end; align-items: center; gap: 0.75rem; margin-top: 0.25rem;'>
                        {$requisitoHtml}
                        <span style='color: #556ee6; font-weight: 800; font-size: 0.875rem; letter-spacing: 0.05em;'>{$categoria}</span>
                    </div>
                "))
                ->collapsible()
                ->collapsed()
                ->schema([
                    Placeholder::make("evaluacion_criterio_status_{$i}")
                        ->hiddenLabel()
                        ->content(function ($get) use ($i) {
                            $status = $get("respuestas.criterio_{$i}.status") ?? 'Borrador';
                            $feedback = $get("respuestas.criterio_{$i}.feedback");
                            $evaluadorEmail = $get("respuestas.criterio_{$i}.evaluador_email");

                            $tooltipAttr = $evaluadorEmail ? " title=\"Evaluado por: {$evaluadorEmail}\"" : "";

                            $statusBadge = match($status) {
                                'validado', 'Aprobado' => "<span{$tooltipAttr} style=\"background-color: #ecfdf5; color: #065f46; font-size: 0.875rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; border: 1px solid #a7f3d0; cursor: help;\">Validado</span>",
                                'revisado', 'Revisado' => "<span{$tooltipAttr} style=\"background-color: #fffbeb; color: #92400e; font-size: 0.875rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; border: 1px solid #fde68a; cursor: help;\">Revisado</span>",
                                'no_cumple', 'Rechazado' => "<span{$tooltipAttr} style=\"background-color: #fef2f2; color: #991b1b; font-size: 0.875rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; border: 1px solid #fecaca; cursor: help;\">No cumple</span>",
                                default => '<span style="background-color: #f3f4f6; color: #374151; font-size: 0.875rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; border: 1px solid #e5e7eb;">Pendiente de Evaluar</span>',
                            };

                            $feedbackHtml = $feedback 
                                ? "<div style='margin-top: 0.5rem; padding: 0.75rem; background-color: #f9fafb; border-left: 4px solid #3b82f6; border-radius: 0.25rem; font-size: 0.875rem; color: #4b5563;'><strong style='color: #1f2937;'>Retroalimentación del Evaluador:</strong>" . ($evaluadorEmail ? " <span style='font-size: 0.75rem; color: #6b7280; font-weight: normal;'>(Otorgado por: {$evaluadorEmail})</span>" : "") . "<br>{$feedback}</div>" 
                                : "";

                            return new HtmlString("
                                <div style='margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #f9fafb;'>
                                    <div style='display: flex; align-items: center; gap: 1rem;'>
                                        <span style='font-weight: 600; color: #374151;'>Estatus de Evaluación del Criterio:</span>
                                        {$statusBadge}
                                    </div>
                                    {$feedbackHtml}
                                </div>
                            ");
                        }),

                    Actions::make([
                        Action::make("evaluar_criterio_{$i}")
                            ->label('Evaluar Criterio')
                            ->icon('heroicon-m-check-badge')
                            ->color('primary')
                            ->visible(fn () => in_array(filament()->getCurrentPanel()->getId(), ['admin', 'evaluador']))
                            ->disabled(fn ($record) => $record && in_array($record->respuestas["criterio_{$i}"]['status'] ?? null, ['validado', 'Aprobado']))
                            ->modalHeading("Evaluar Criterio {$i}")
                            ->modalSubmitActionLabel('Guardar Evaluación')
                            ->form(fn ($record) => [
                                Textarea::make('retroalimentacion_general')
                                    ->label('Retroalimentación General')
                                    ->rows(3),
                            ])
                            ->fillForm(fn ($get) => [
                                'retroalimentacion_general' => $get("respuestas.criterio_{$i}.feedback"),
                            ])
                            ->action(function (array $data, $set, $record) use ($i, $elementos) {
                                $respuestas = $record ? ($record->respuestas ?? []) : [];
                                $numElements = count($elementos);
                                $allApproved = true;

                                for ($e = 1; $e <= $numElements; $e++) {
                                    $calif = $respuestas["criterio_{$i}"]["elemento_{$e}"]['calificacion_politica'] ?? null;
                                    if (!in_array($calif, ['validado', 'Aprobado'])) {
                                        $allApproved = false;
                                        break;
                                    }
                                }

                                $status = $allApproved ? 'validado' : 'no_cumple';

                                $set("respuestas.criterio_{$i}.status", $status);
                                $set("respuestas.criterio_{$i}.feedback", $data['retroalimentacion_general']);

                                $evaluadorEmail = auth()->user()?->email;
                                $set("respuestas.criterio_{$i}.evaluador_email", $evaluadorEmail);

                                if ($record) {
                                    $respuestas = $record->respuestas ?? [];
                                    if (!is_array($respuestas)) {
                                        $respuestas = [];
                                    }
                                    if (!isset($respuestas["criterio_{$i}"]) || !is_array($respuestas["criterio_{$i}"])) {
                                        $respuestas["criterio_{$i}"] = [];
                                    }
                                    $respuestas["criterio_{$i}"]['status'] = $status;
                                    $respuestas["criterio_{$i}"]['feedback'] = $data['retroalimentacion_general'];
                                    $respuestas["criterio_{$i}"]['evaluador_email'] = $evaluadorEmail;
                                    $record->update(['respuestas' => $respuestas]);

                                    \Filament\Notifications\Notification::make()
                                        ->title("Criterio {$i} evaluado automáticamente como {$status}")
                                        ->success()
                                        ->send();
                                }
                            })
                    ])
                    ->key("evaluar_criterio_actions_{$i}")
                    ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin')
                    ->columnSpan('full')
                    ->extraAttributes(['style' => 'margin-bottom: 1.5rem;']),

                    ...$gridElementos,
                ]);

            if ($requisito === 'INDISPENSABLE') {
                $indispensablesComponents[] = $criterioSection;
            } elseif ($requisito === 'DESEABLE') {
                $deseablesComponents[] = $criterioSection;
            } else {
                if ($categoria === '+FORTALECIMIENTO') {
                    $necesariosFortalecimiento[] = $criterioSection;
                } elseif ($categoria === '+PREVENCIÓN') {
                    $necesariosPrevencion[] = $criterioSection;
                } else {
                    $necesariosCuidado[] = $criterioSection;
                }
            }
        }

        return $schema
            ->columns(1)
            ->components([
                Grid::make(12)
                    ->schema([
                        Placeholder::make('indispensables_stat_card')
                            ->hiddenLabel()
                            ->content(function ($get) {
                                $respuestas = $get('respuestas') ?? [];
                                $indispensableIds = [4, 9, 10, 15, 16];
                                $indispensablesCount = 0;
                                $criterioElementsCount = [
                                    4 => 3,
                                    9 => 5,
                                    10 => 5,
                                    15 => 3,
                                    16 => 5,
                                ];

                                foreach ($indispensableIds as $id) {
                                    $met = true;
                                    $numElements = $criterioElementsCount[$id];
                                    for ($e = 1; $e <= $numElements; $e++) {
                                        $score = $respuestas["criterio_{$id}"]["elemento_{$e}"]['score'] ?? null;
                                        if ($score !== '10' && $score !== 'NA') {
                                            $met = false;
                                            break;
                                        }
                                    }
                                    if ($met) {
                                        $indispensablesCount++;
                                    }
                                }
                                $cumpleIndispensables = ($indispensablesCount === 5);

                                $title = "{$indispensablesCount} de 5";
                                $desc = $cumpleIndispensables ? '¡Todos los obligatorios cumplidos!' : 'Faltan indispensables por cumplir';
                                $color = $cumpleIndispensables ? '#10b981' : '#ef4444'; // Green or Red
                                $icon = $cumpleIndispensables 
                                    ? '<svg style="width: 1.75rem; height: 1.75rem; color: #ffffff; margin-top: 0.5rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>'
                                    : '<svg style="width: 1.75rem; height: 1.75rem; color: #ffffff; margin-top: 0.5rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>';

                                return new HtmlString("
                                    <div style='padding: 0 !important; display: flex; flex-direction: row; overflow: hidden; border-radius: 0.75rem; border: 1px solid #e5e7eb; background-color: #ffffff; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); height: 100%; min-height: 130px;'>
                                        <!-- Left Column: 65% -->
                                        <div style='width: 65%; padding: 1rem 1.25rem; display: flex; flex-direction: column; justify-content: center; border-right: 1px solid #e5e7eb;'>
                                            <div style='font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem; line-height: 1.1;'>
                                                {$title}
                                            </div>
                                            <div style='font-size: 0.75rem; font-weight: 500; color: #6b7280;'>
                                                Criterios Indispensables
                                            </div>
                                        </div>

                                        <!-- Right Column: 35% -->
                                        <div style='width: 35%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.75rem; text-align: center; background-color: {$color};'>
                                            <span style='font-size: 0.7rem; font-weight: 600; color: #ffffff; line-height: 1.2;'>
                                                {$desc}
                                            </span>
                                            {$icon}
                                        </div>
                                    </div>
                                ");
                            })
                            ->columnSpan([
                                'default' => 12,
                                'md' => 4,
                            ])
                            ->visible(fn() => filament()->getCurrentPanel()?->getId() === 'empresa'),

                        Placeholder::make('aviso_importante')
                            ->hiddenLabel()
                            ->content(new HtmlString('
                                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); border-radius: 0.75rem; padding: 1.25rem; height: 100%; display: flex; flex-direction: column; justify-content: center;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #1e293b; margin-bottom: 0.5rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="#3b82f6" style="width: 1.15rem; height: 1.15rem; display: inline-block;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.852l-.708 2.836a.75.75 0 001.063.852l.041-.028M12 18.75h.007v.008H12v-.008zM12 3a9 9 0 110 18 9 9 0 010-18z" />
                                        </svg>
                                        <h3 style="font-size: 0.85rem; font-weight: 700; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">Aviso importante</h3>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #475569; line-height: 1.5; display: flex; flex-direction: column; gap: 0.5rem;">
                                        <p style="margin: 0;">El puntaje y el nivel de madurez que se muestran en este apartado tienen carácter informativo y corresponden a un ejercicio de referencia calculado a partir de la revisión documental presentada por la organización, así como de la información proporcionada en su proceso de autoevaluación y autopercepción.</p>
                                        <p style="margin: 0;">El resultado definitivo será determinado por el equipo evaluador durante la visita de evaluación presencial, en la cual se verificará la evidencia documental, la implementación de las acciones reportadas y su aplicación en la práctica.</p>
                                        <p style="margin: 0;">Por lo anterior, el puntaje y el nivel de madurez visualizados en esta etapa no constituyen el resultado final ni garantizan la obtención de un nivel específico. La organización deberá esperar la conclusión del proceso de evaluación y dictaminación para conocer oficialmente el nivel de madurez alcanzado.</p>
                                    </div>
                                </div>
                            '))
                            ->columnSpan('full'),
                    ])

                    ->columnSpan('full')
                    ->extraAttributes(['style' => 'margin-bottom: 1.5rem;']),

                Tabs::make('Tipos de Criterios')
                    ->tabs([
                        Tab::make('Criterios Indispensables')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Placeholder::make('nota_indispensables')
                                    ->hiddenLabel()
                                    ->content(new HtmlString('
                                        <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-left: 4px solid #3b82f6; border-radius: 0.5rem; padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="#2563eb" style="width: 1.15rem; height: 1.15rem; display: inline-block;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.852l-.708 2.836a.75.75 0 001.063.852l.041-.028M12 18.75h.007v.008H12v-.008zM12 3a9 9 0 110 18 9 9 0 010-18z" />
                                                </svg>
                                                <h3 style="font-size: 0.8rem; font-weight: 700; color: #1e3a8a; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">Nota</h3>
                                            </div>
                                            <ul style="margin: 0 0 0.85rem; padding-left: 1.1rem; font-size: 0.85rem; color: #1e40af; line-height: 1.55; list-style: disc;">
                                                <li style="margin-bottom: 0.35rem;">Para el cumplimiento de los 5 criterios indispensables, es importante realizarlos con los recursos de apoyo proporcionados a través de la plataforma.</li>
                                                <li>Para el desarrollo de todos los criterios es importante consultar el Lineamiento y la Guía de Criterios que puede descargar a continuación.</li>
                                            </ul>
                                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                                <a href="' . asset('docs/lineamiento-operativo.pdf') . '" target="_blank" style="display: inline-flex; align-items: center; gap: 0.4rem; background-color: #2563eb; color: #ffffff; font-size: 0.8rem; font-weight: 600; padding: 0.45rem 0.9rem; border-radius: 0.5rem; text-decoration: none;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 1rem; height: 1rem;"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v6.638l1.96-2.158a.75.75 0 111.08 1.04l-3.25 3.5a.75.75 0 01-1.08 0l-3.25-3.5a.75.75 0 111.08-1.04l1.96 2.158V3.75A.75.75 0 0110 3zM3.5 13a.75.75 0 01.75.75v1.5c0 .414.336.75.75.75h10a.75.75 0 00.75-.75v-1.5a.75.75 0 011.5 0v1.5A2.25 2.25 0 0115 17.5H5A2.25 2.25 0 012.75 15.25v-1.5A.75.75 0 013.5 13z" clip-rule="evenodd"/></svg>
                                                    Lineamiento Operativo
                                                </a>
                                                <a href="' . asset('docs/guia-de-criterios-mas-feliz.pdf') . '" target="_blank" style="display: inline-flex; align-items: center; gap: 0.4rem; background-color: #ffffff; color: #1d4ed8; border: 1px solid #93c5fd; font-size: 0.8rem; font-weight: 600; padding: 0.45rem 0.9rem; border-radius: 0.5rem; text-decoration: none;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 1rem; height: 1rem;"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v6.638l1.96-2.158a.75.75 0 111.08 1.04l-3.25 3.5a.75.75 0 01-1.08 0l-3.25-3.5a.75.75 0 111.08-1.04l1.96 2.158V3.75A.75.75 0 0110 3zM3.5 13a.75.75 0 01.75.75v1.5c0 .414.336.75.75.75h10a.75.75 0 00.75-.75v-1.5a.75.75 0 011.5 0v1.5A2.25 2.25 0 0115 17.5H5A2.25 2.25 0 012.75 15.25v-1.5A.75.75 0 013.5 13z" clip-rule="evenodd"/></svg>
                                                    Guía de Criterios
                                                </a>
                                            </div>
                                        </div>
                                    '))
                                    ->columnSpanFull(),

                                ...$indispensablesComponents,
                            ]),
                        
                        Tab::make('Criterios Necesarios')
                            ->icon('heroicon-o-document-duplicate')
                            ->schema([
                                Tabs::make('Ejes')
                                    ->tabs([
                                        Tab::make('Fortalecimiento')
                                            ->schema($necesariosFortalecimiento),
                                        Tab::make('Prevención')
                                            ->schema($necesariosPrevencion),
                                        Tab::make('Cuidado y Atención')
                                            ->schema($necesariosCuidado),
                                    ])
                                    ->columnSpan('full')
                            ]),
                        
                        Tab::make('Criterios Deseables')
                            ->icon('heroicon-o-sparkles')
                            ->schema($deseablesComponents),
                    ])
                    ->columnSpan('full'),

                Placeholder::make('custom-css')
                    ->content(new HtmlString('<div style="display: none;"><style>
                        .fi-resource-create-record-page form, .fi-resource-edit-record-page form {
                            background-color: transparent !important;
                            padding: 0 !important;
                            box-shadow: none !important;
                        }
                        
                        /* Force Modal Background to be Solid White */
                        .fi-modal-window, .fi-modal-content {
                            background-color: #ffffff !important;
                            background: #ffffff !important;
                        }
                        
                        /* Align modal footer buttons to the right and swap order */
                        .fi-modal-footer-actions {
                            flex-direction: row-reverse !important;
                            justify-content: flex-start !important;
                        }
                        .fi-modal-footer {
                            text-align: right !important;
                        }
                    </style></div>'))
                    ->hiddenLabel()
                    ->columnSpan('full'),
            ]);
    }
}
