<?php

namespace App\Filament\Empresa\Resources\Autoevaluacions\Schemas;

use Filament\Forms\Components\Select;
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

        $categorias = [
            1 => '+FORTALECIMIENTO',
            2 => '+FORTALECIMIENTO',
            3 => '+PREVENCIÓN',
            4 => '+PREVENCIÓN',
            5 => '+PREVENCIÓN',
            6 => '+FORTALECIMIENTO',
            7 => '+CUIDADO/ATENCIÓN',
            8 => '+CUIDADO/ATENCIÓN',
            9 => '+PREVENCIÓN',
            10 => '+FORTALECIMIENTO',
            11 => '+FORTALECIMIENTO',
            12 => '+FORTALECIMIENTO',
            13 => '+CUIDADO/ATENCIÓN',
            14 => '+CUIDADO/ATENCIÓN',
            15 => '+CUIDADO/ATENCIÓN',
            16 => '+PREVENCIÓN',
            17 => '+PREVENCIÓN',
            18 => '+FORTALECIMIENTO',
            19 => '+FORTALECIMIENTO',
            20 => '+FORTALECIMIENTO',
        ];

        $requisitos = [
            1 => 'INDISPENSABLE',
            2 => 'NECESARIO',
            3 => 'NECESARIO',
            4 => 'INDISPENSABLE',
            5 => 'NECESARIO',
            6 => 'NECESARIO',
            7 => 'NECESARIO',
            8 => 'NECESARIO',
            9 => 'INDISPENSABLE',
            10 => 'NECESARIO',
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
                $mainText = 'Criterio 1.- Política documentada y participación';
                $elementos = [
                    [
                        'short' => 'Política documentada',
                        'desc' => 'La organización cuenta con una política/declaración interna documentada para prevenir, cuidar atender y fortalecer la Salud Mental.'
                    ],
                    [
                        'short' => 'Participación de áreas',
                        'desc' => 'La política fue elaborada con la participación de distintas áreas de la organización'
                    ],
                    [
                        'short' => 'Aprobación directiva',
                        'desc' => 'La política está aprobada por la alta dirección y forma parte de los planes y programas institucionales'
                    ],
                    [
                        'short' => 'Roles y responsabilidades',
                        'desc' => 'La política define claramente roles, funciones y responsabilidades para su implementación, seguimiento y evaluación'
                    ],
                    [
                        'short' => 'Difusión al personal',
                        'desc' => 'La política ha sido difundida a todo el personal de las diferentes áreas y turnos'
                    ],
                    [
                        'short' => 'Mecanismos de evaluación',
                        'desc' => 'La organización estableció mecanismos de evaluación para garantizar su comprensión'
                    ],
                    [
                        'short' => 'Conocimiento y comprensión',
                        'desc' => 'El personal conoce la política, sabe dónde consultarla y comprende sus alcances'
                    ]
                ];
            } elseif ($i === 2) {
                $mainText = 'Criterio 2.- La organización conforma un Comité de Salud Mental';
                $elementos = [
                    [
                        'short' => 'Comité constituido',
                        'desc' => 'La organización cuenta con un Comité de Salud Mental formalmente constituido y aprobado por las máximas autoridades'
                    ],
                    [
                        'short' => 'Integración representativa',
                        'desc' => 'El Comité está integrado de manera representativa por distintas áreas y niveles jerárquicos, incluyendo personal operativo'
                    ],
                    [
                        'short' => 'Funciones definidas',
                        'desc' => 'El Comité tiene funciones, responsabilidades y atribuciones claramente definidas'
                    ],
                    [
                        'short' => 'Programa vigente',
                        'desc' => 'El Comité cuenta con un programa de trabajo vigente y un calendario de sesiones alineados al plan de salud mental'
                    ],
                    [
                        'short' => 'Reuniones y evidencia',
                        'desc' => 'El Comité realiza reuniones periódicas y genera evidencia documental de sus actividades, acuerdos y seguimiento'
                    ]
                ];
            } elseif ($i === 3) {
                $mainText = 'Criterio 3.- Plan de prevención y fortalecimiento de la Salud Mental';
                $elementos = [
                    [
                        'short' => 'Plan establecido',
                        'desc' => 'La organización cuenta con un plan de salud mental formalmente establecido y vinculado a la política organizacional de salud mental.'
                    ],
                    [
                        'short' => 'Acciones según diagnóstico',
                        'desc' => 'El plan considera acciones planificadas de prevención, cuidado/atención y fortalecimiento de la salud mental derivadas del diagnóstico de salud socio emocional de los trabajadores'
                    ],
                    [
                        'short' => 'Acciones planificadas',
                        'desc' => 'El plan incluye acciones planificadas de prevención, cuidado/atención y fortalecimiento de la salud mental.'
                    ],
                    [
                        'short' => 'Atención de riesgos',
                        'desc' => 'El plan considera acciones para la atención de riesgos psicosociales basado en la identificación de riesgos psicosociales del entorno laboral y el clima organizacional'
                    ],
                    [
                        'short' => 'Seguimiento y evaluación',
                        'desc' => 'La organización cuenta con mecanismos documentados de seguimiento, evaluación y mejora continua del plan, incluyendo monitorización del impacto de las acciones a través de indicadores'
                    ],
                    [
                        'short' => 'Capacitación y difusión',
                        'desc' => 'Se implementan acciones de capacitación, sensibilización y difusión del plan de salud mental'
                    ],
                    [
                        'short' => 'Evidencia de implementación',
                        'desc' => 'Existe evidencia documental que demuestra la implementación, operación y seguimiento del plan'
                    ]
                ];
            } elseif ($i === 4) {
                $mainText = 'Criterio 4.- Diagnóstico de principales riesgos en la salud mental de los trabajadores';
                $elementos = [
                    [
                        'short' => 'Herramientas de tamizaje validadas',
                        'desc' => 'La organización realiza un diagnóstico de salud mental mediante herramientas de tamizaje validadas para la detección de ansiedad, depresión y conducta suicida.'
                    ],
                    [
                        'short' => 'Confidencialidad y protección',
                        'desc' => 'La organización garantiza la confidencialidad y protección de los datos obtenidos durante el proceso de evaluación.'
                    ],
                    [
                        'short' => 'Cobertura representativa',
                        'desc' => 'El diagnóstico alcanza una cobertura representativa del 90% de la población trabajadora.'
                    ],
                    [
                        'short' => 'Resultados documentados',
                        'desc' => 'Los resultados se encuentran documentados y permiten identificar factores de riesgo, factores protectores y necesidades prioritarias de intervención.'
                    ],
                    [
                        'short' => 'Mecanismos de seguimiento',
                        'desc' => 'La organización cuenta con mecanismos definidos de orientación, canalización y seguimiento para personas identificadas con riesgo moderado o alto.'
                    ],
                    [
                        'short' => 'Actualización del Plan',
                        'desc' => 'Los resultados del diagnóstico son utilizados para el diseño o actualización del Plan de prevención, cuidado/atención y fortalecimiento de la Salud Mental.'
                    ],
                    [
                        'short' => 'Evidencia documental',
                        'desc' => 'Existe evidencia documental de la aplicación, análisis de resultados y seguimiento de las acciones derivadas del diagnóstico.'
                    ]
                ];
            } elseif ($i === 5) {
                $mainText = 'Criterio 5.- Gestión de riesgos psicosociales del entorno laboral';
                $elementos = [
                    [
                        'short' => 'Identificación y evaluación de riesgos',
                        'desc' => 'La organización identifica y evalúa los riesgos psicosociales mediante herramientas validadas y de conformidad con la legislación aplicable vigente'
                    ],
                    [
                        'short' => 'Muestra representativa y confidencialidad',
                        'desc' => 'La evaluación se realiza en una muestra representativa y garantiza la confidencialidad de la información obtenida'
                    ],
                    [
                        'short' => 'Riesgos documentados y priorizados',
                        'desc' => 'Los riesgos identificados se encuentran documentados y priorizados'
                    ],
                    [
                        'short' => 'Programa de intervención',
                        'desc' => 'La organización cuenta con un programa de intervención basado en los resultados obtenidos, que incluye: acciones de mejora, responsables, plazos e indicadores de seguimiento'
                    ],
                    [
                        'short' => 'Evidencia de acciones',
                        'desc' => 'Existe evidencia de la implementación, seguimiento y evaluación de las acciones establecidas'
                    ],
                    [
                        'short' => 'Reincorporación laboral',
                        'desc' => 'Cuando aplique, la organización cuenta con mecanismos de seguimiento y reincorporación laboral para trabajadores que hayan presentado afectaciones relacionadas con la salud mental'
                    ],
                    [
                        'short' => 'Programa integrado al Plan',
                        'desc' => 'El programa de atención de riesgos psicosociales del entorno laboral se encuentra integrado al Plan de Prevención, Cuidado/Atención y Fortalecimiento de la Salud Mental de la organización'
                    ]
                ];
            } elseif ($i === 6) {
                $mainText = 'Criterio 6.- La organización evalúa anualmente el clima laboral y la cultura organizacional';
                $elementos = [
                    [
                        'short' => 'Proceso de evaluación',
                        'desc' => 'Se cuenta con un proceso para evaluar el Clima Organizacional y la Cultura Organizacional'
                    ],
                    [
                        'short' => 'Medición anual',
                        'desc' => 'La organización realiza al menos una medición anual del clima laboral y cultura organizacional'
                    ],
                    [
                        'short' => 'Instrumento definido',
                        'desc' => 'Se mide el clima organizacional mediante un instrumento definido'
                    ],
                    [
                        'short' => 'Identificación de oportunidades',
                        'desc' => 'Se identifican áreas de oportunidad o riesgos a partir de los resultados'
                    ],
                    [
                        'short' => 'Plan de acción alineado',
                        'desc' => 'La organización cuenta con un plan de acción de salud mental alineado a los resultados'
                    ],
                    [
                        'short' => 'Acciones de mejora',
                        'desc' => 'La organización desarrolla acciones de mejora basado en los resultados y los incluye en el Plan de prevención, cuidado/atención y fortalecimiento de la Salud Mental'
                    ],
                    [
                        'short' => 'Seguimiento a acciones',
                        'desc' => 'Se da seguimiento a las acciones implementadas'
                    ]
                ];
            } elseif ($i === 7) {
                $mainText = 'Criterio 7.- Estrategias para promover condiciones básicas del ambiente físico';
                $elementos = [
                    [
                        'short' => 'Estrategias de orden y limpieza',
                        'desc' => 'La organización cuenta con estrategias o lineamientos para promover el orden y la limpieza'
                    ],
                    [
                        'short' => 'Condiciones de seguridad',
                        'desc' => 'Los espacios de trabajo cumplen con condiciones básicas de seguridad'
                    ],
                    [
                        'short' => 'Limpieza en centros de trabajo',
                        'desc' => 'Se mantienen condiciones adecuadas de limpieza en los centros de trabajo'
                    ],
                    [
                        'short' => 'Iluminación adecuada',
                        'desc' => 'Los espacios cuentan con iluminación adecuada para el desarrollo de actividades'
                    ],
                    [
                        'short' => 'Ventilación adecuada',
                        'desc' => 'Existe ventilación adecuada en las áreas de trabajo'
                    ],
                    [
                        'short' => 'Evaluaciones periódicas',
                        'desc' => 'La organización realiza evaluaciones periódicas del ambiente físico laboral'
                    ],
                    [
                        'short' => 'Acciones correctivas',
                        'desc' => 'Se implementan acciones correctivas derivadas de las evaluaciones'
                    ],
                    [
                        'short' => 'Participación del personal',
                        'desc' => 'El personal participa en el mantenimiento del orden y limpieza'
                    ],
                    [
                        'short' => 'Evidencia documental',
                        'desc' => 'Se cuenta con evidencia documental de las evaluaciones y acciones realizadas'
                    ]
                ];
            } elseif ($i === 8) {
                $mainText = 'Criterio 8.- Programa de entornos saludables';
                $elementos = [
                    [
                        'short' => 'Programa de Entornos Saludables',
                        'desc' => 'La organización cuenta con un Programa de Entornos Saludables'
                    ],
                    [
                        'short' => 'Aprobación de la alta dirección',
                        'desc' => 'El programa está aprobado formalmente por la alta dirección'
                    ],
                    [
                        'short' => 'Estrategias completas',
                        'desc' => 'Las estrategias incluyen al menos los incisos de la a) a la e)'
                    ],
                    [
                        'short' => 'Difusión del Programa',
                        'desc' => 'La organización difunde el Programa de Entornos Saludables entre el personal'
                    ],
                    [
                        'short' => 'Implementación de estrategias',
                        'desc' => 'La organización implementa las estrategias del Programa'
                    ],
                    [
                        'short' => 'Seguimiento a quejas',
                        'desc' => 'La organización da seguimiento a las quejas recibidas'
                    ],
                    [
                        'short' => 'Evaluación y actualización',
                        'desc' => 'El programa es evaluado y actualizado periódicamente'
                    ]
                ];
            } elseif ($i === 9) {
                $mainText = 'Criterio 9.- Programa de promoción de la salud mental';
                $elementos = [
                    [
                        'short' => 'Programa documentado y vigente',
                        'desc' => 'La organización cuenta con un programa de promoción de la salud mental documentado y vigente.'
                    ],
                    [
                        'short' => 'Alineación con planes estatales',
                        'desc' => 'El programa se encuentra alineado con el plan de salud mental organizacional y del “Gran Programa Estatal de Salud Mental”.'
                    ],
                    [
                        'short' => 'Objetivos y responsables',
                        'desc' => 'El programa define objetivos, acciones, responsables y periodicidad de implementación'
                    ],
                    [
                        'short' => 'Componentes mínimos',
                        'desc' => 'El programa incluye al menos los componentes enunciados del inciso a) a la e'
                    ],
                    [
                        'short' => 'Implementación periódica',
                        'desc' => 'La organización implementa acciones del programa de manera periódica'
                    ],
                    [
                        'short' => 'Evidencia de ejecución',
                        'desc' => 'Existe evidencia documental de la ejecución de las actividades (listas de asistencia, materiales, convocatorias, reportes)'
                    ],
                    [
                        'short' => 'Difusión y accesibilidad',
                        'desc' => 'El programa es difundido al personal y es accesible para diferentes áreas, niveles y turnos'
                    ]
                ];
            } elseif ($i === 10) {
                $mainText = 'Criterio 10.- Capacitación a directivos y líderes en señales de alerta de salud mental';
                $elementos = [
                    [
                        'short' => 'Programa de capacitación directiva',
                        'desc' => 'La organización cuenta con un programa de capacitación en salud mental dirigido a directivos, líderes y gerentes'
                    ],
                    [
                        'short' => 'Cobertura garantizada',
                        'desc' => 'La organización garantiza la cobertura de capacitación a directivos, líderes y gerentes'
                    ],
                    [
                        'short' => 'Contenidos de capacitación',
                        'desc' => 'La organización establece contenidos de capacitación en salud mental tomando en cuenta al menos del inciso a) a la h)'
                    ],
                    [
                        'short' => 'Habilidades prácticas',
                        'desc' => 'La organización desarrolla habilidades prácticas en los líderes para el abordaje de situaciones relacionadas con salud mental'
                    ],
                    [
                        'short' => 'Evaluación de efectividad',
                        'desc' => 'La organización evalúa la efectividad de la capacitación'
                    ]
                ];
            } elseif ($i === 11) {
                $mainText = 'Criterio 11.- Capacitación en alfabetización y concientización en salud mental';
                $elementos = [
                    ['short' => 'Capacitación periódica', 'desc' => 'La organización realiza acciones de capacitación en salud mental dirigidas al personal, al menos dos veces al año y al personal de nuevo ingreso'],
                    ['short' => 'Contenidos mínimos', 'desc' => 'La organización cuenta con al menos los temas de la a) a la f)'],
                    ['short' => 'Cobertura del 80%', 'desc' => 'La organización capacita al menos al 80% de su personal, considerando las diferentes áreas, niveles y turnos.']
                ];
            } elseif ($i === 12) {
                $mainText = 'Criterio 12.- Capacitación a “gatekeepers” para actuación ante crisis';
                $elementos = [
                    ['short' => 'Designación de gatekeepers', 'desc' => 'La organización identifica y designa personal clave que funge como “gatekeepers”'],
                    ['short' => 'Capacitación formal', 'desc' => 'El personal asignado cuenta con capacitación formal para la detección oportuna, atención inicial y canalización del personal en riesgo, acorde a lo solicitado en el numeral de la a) a la g).'],
                    ['short' => 'Funciones delimitadas', 'desc' => 'Se delimitan claramente las funciones, alcances del personal capacitado y mecanismos de confidencialidad'],
                    ['short' => 'Evaluación de desempeño', 'desc' => 'La organización evalúa el desempeño y efectividad de las actividades de los gatekeepers mediante simulacros, encuestas de usuarios, análisis y seguimiento de casos'],
                    ['short' => 'Manejo ético', 'desc' => 'Se garantiza la confidencialidad y el manejo ético de la información relacionada a la atención de casos']
                ];
            } elseif ($i === 13) {
                $mainText = 'Criterio 13.- Espacios y actividades para el desarrollo de habilidades socioemocionales';
                $elementos = [
                    ['short' => 'Programa de reconocimiento', 'desc' => 'La organización cuenta con un programa formal de reconocimiento a los trabajadores documentado y vigente avalado por el Comité de Salud Mental.'],
                    ['short' => 'Criterios claros', 'desc' => 'Se cuenta criterios claros de participación, selección y exclusión.'],
                    ['short' => 'Difusión general', 'desc' => 'El programa es difundido a todo el personal (categorías, áreas y turnos).'],
                    ['short' => 'Transparencia y equidad', 'desc' => 'Contar con mecanismos que garanticen la transparencia, equidad y accesibilidad.'],
                    ['short' => 'Difusión al personal', 'desc' => 'El programa es difundido entre el personal.'],
                    ['short' => 'Programa implementado', 'desc' => 'El programa de reconocimiento se encuentra implementado.'],
                    ['short' => 'Evaluación de impacto', 'desc' => 'La organización evalúa periódicamente el impacto del programa.']
                ];
            } elseif ($i === 14) {
                $mainText = 'Criterio 14.- Acceso a la información sobre servicios de apoyo en salud mental';
                $elementos = [
                    ['short' => 'Información visible', 'desc' => 'La organización cuenta con información visible y de fácil acceso relacionada a servicios de apoyo internos y/o externos en salud mental disponibles, en espacios estratégicos del centro de trabajo'],
                    ['short' => 'Línea de Vida estatal', 'desc' => 'La organización integra en la información visible, la Línea de Vida estatal.'],
                    ['short' => 'Directorio actualizado', 'desc' => 'La organización cuenta con un directorio actualizado con servicios de apoyo internos y externos vigentes.'],
                    ['short' => 'Directorio accesible', 'desc' => 'El directorio está disponible y accesible para todo el personal, sin distinción de área, turno o nivel jerárquico.'],
                    ['short' => 'Conocimiento del directorio', 'desc' => 'El personal conoce la existencia del directorio y sabe cómo acceder a los recursos.'],
                    ['short' => 'Actualización periódica', 'desc' => 'La organización actualiza periódicamente el directorio para garantizar su vigencia']
                ];
            } elseif ($i === 15) {
                $mainText = 'Criterio 15.- Acceso a servicios de apoyo psicológico internos o externos';
                $elementos = [
                    ['short' => 'Servicios establecidos', 'desc' => 'La organización cuenta con servicios de apoyo psicológico internos o externos formalmente establecidos.'],
                    ['short' => 'Mecanismos de solicitud', 'desc' => 'Existen mecanismos definidos y accesibles para que el personal pueda solicitar los servicios.'],
                    ['short' => 'Garantía de confidencialidad', 'desc' => 'Se garantiza la confidencialidad en la atención y manejo de la información'],
                    ['short' => 'Difusión de servicios', 'desc' => 'Los servicios son difundidos entre el personal y son conocidos por las diferentes áreas, niveles y turnos'],
                    ['short' => 'Evidencia de disponibilidad', 'desc' => 'Se cuenta con evidencia de la disponibilidad o utilización de los servicios'],
                    ['short' => 'Evaluación de servicios', 'desc' => 'La organización evalúa periódicamente la utilización y efectividad de los servicios']
                ];
            } elseif ($i === 16) {
                $mainText = 'Criterio 16.- Protocolo integral de actuación ante crisis de salud mental';
                $elementos = [
                    ['short' => 'Designación de gatekeepers', 'desc' => 'La organización identifica y designa personal clave que funge como “gatekeepers”.'],
                    ['short' => 'Capacitación formal', 'desc' => 'El personal asignado cuenta con capacitación formal para la detección oportuna, atención inicial y canalización del personal en riesgo, acorde a lo solicitado en el numeral de la a) a la g).'],
                    ['short' => 'Funciones delimitadas', 'desc' => 'Se delimitan claramente las funciones, alcances del personal capacitado y mecanismos de confidencialidad.'],
                    ['short' => 'Evaluación de desempeño', 'desc' => 'La organización evalúa el desempeño y efectividad de las actividades de los gatekeepers mediante simulacros, encuestas de usuarios, análisis y seguimiento de casos.'],
                    ['short' => 'Manejo ético', 'desc' => 'Se garantiza la confidencialidad y el manejo ético de la información relacionada a la atención de casos.']
                ];
            } elseif ($i === 17) {
                $mainText = 'Criterio 17.- Prueba anual del Protocolo integral ante crisis';
                $elementos = [
                    ['short' => 'Simulacro anual', 'desc' => 'La organización realiza al menos un simulacro por año para probar todo el protocolo integral de atención ante crisis de salud mental, o parte del mismo'],
                    ['short' => 'Participación de gatekeepers', 'desc' => 'Todo el personal responsable (gatekeepers) participa en los simulacros'],
                    ['short' => 'Algoritmos de atención', 'desc' => 'El simulacro se basa en los algoritmos de atención ante crisis de salud mental'],
                    ['short' => 'Evaluación documentada', 'desc' => 'Se evalúan y documentan las pruebas del simulacro'],
                    ['short' => 'Áreas de mejora', 'desc' => 'Se identifican y corrigen las áreas de mejora']
                ];
            } elseif ($i === 18) {
                $mainText = 'Criterio 18.- Programa de Reconocimiento a los trabajadores';
                $elementos = [
                    ['short' => 'Programa de reconocimiento', 'desc' => 'La organización cuenta con un programa formal de reconocimiento a los trabajadores documentado y vigente avalado por el Comité de Salud Mental.'],
                    ['short' => 'Criterios claros', 'desc' => 'Se cuenta criterios claros de participación, selección y exclusión'],
                    ['short' => 'Difusión general', 'desc' => 'El programa es difundido a todo el personal (categorías, áreas y turnos).'],
                    ['short' => 'Transparencia y equidad', 'desc' => 'Contar con mecanismos que garanticen la transparencia, equidad y accesibilidad.'],
                    ['short' => 'Difusión al personal', 'desc' => 'El programa es difundido entre el personal'],
                    ['short' => 'Programa implementado', 'desc' => 'El programa de reconocimiento se encuentra implementado'],
                    ['short' => 'Evaluación de impacto', 'desc' => 'La organización evalúa periódicamente el impacto del programa']
                ];
            } elseif ($i === 19) {
                $mainText = 'Criterio 19.- Fortalecimiento de competencias parentales y apoyo familiar';
                $elementos = [
                    ['short' => 'Programa de fortalecimiento', 'desc' => 'La organización cuenta con un programa o estrategia de fortalecimiento parental y apoyo familiar.'],
                    ['short' => 'Actividades periódicas', 'desc' => 'Se desarrollan actividades periódicas dirigidas a madres, padres o cuidadores'],
                    ['short' => 'Componentes mínimos', 'desc' => 'Las actividades incluyen al menos los componentes a) a g).'],
                    ['short' => 'Evidencia documental', 'desc' => 'Existe evidencia documental de implementación'],
                    ['short' => 'Evaluación de participación', 'desc' => 'La organización evalúa periódicamente la participación y satisfacción de los asistentes']
                ];
            } elseif ($i === 20) {
                $mainText = 'Criterio 20.- Sistema de gestión de la salud mental';
                $elementos = [
                    ['short' => 'Sistema de mejora continua', 'desc' => 'La organización cuenta con un sistema de mejora continua en salud mental.'],
                    ['short' => 'Fuentes confiables', 'desc' => 'La información utilizada proviene de fuentes confiables y verificables.'],
                    ['short' => 'Indicadores cuantitativos', 'desc' => 'Se han definido indicadores cuantitativos (rotación, ausentismo, incidentes, entre otros).'],
                    ['short' => 'Indicadores cualitativos', 'desc' => 'Se han definido indicadores cualitativos (percepción de apoyo, clima laboral, cultura organizacional).'],
                    ['short' => 'Monitoreo periódico', 'desc' => 'La organización realiza monitoreo periódico de los indicadores.'],
                    ['short' => 'Toma de decisiones', 'desc' => 'Los resultados son utilizados para la toma de decisiones y mejora de estrategias.'],
                    ['short' => 'Mecanismos de retroalimentación', 'desc' => 'Se consideran mecanismos para incorporar la retroalimentación del personal.'],
                    ['short' => 'Seguimiento a acciones', 'desc' => 'Se da seguimiento a las acciones implementadas.']
                ];
            } else {
                $mainText = "Criterio {$i}.- [Título corto por definir]";
                $elementos = [
                    [
                        'short' => "Punto {$i}.1",
                        'desc' => "Descripción detallada del elemento medible {$i}.1"
                    ]
                ];
            }

            $isAdmin = filament()->getCurrentPanel()->getId() === 'admin';
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
                                    $url = \Illuminate\Support\Facades\Storage::url($archivo);
                                    $badges[] = "<a href=\"{$url}\" target=\"_blank\" style=\"background-color: #f0fdf4; color: #15803d; font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px; font-weight: 500; border: 1px solid #bbf7d0; text-decoration: none; cursor: pointer; display: inline-block;\">📎 Evidencia</a>";
                                }
                                if ($calificacion === 'Aprobado') {
                                    $badges[] = "<span{$tooltipAttr} style=\"background-color: #f0fdf4; color: #166534; font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px; font-weight: 600; border: 1px solid #bbf7d0; cursor: help;\">✅ Aprobado</span>";
                                } elseif ($calificacion === 'Rechazado') {
                                    $badges[] = "<span{$tooltipAttr} style=\"background-color: #fef2f2; color: #991b1b; font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px; font-weight: 600; border: 1px solid #fecaca; cursor: help;\">❌ Rechazado</span>";
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
                                $opts = [
                                    '10' => 'Cumple (10 puntos)',
                                    '5' => 'En proceso (5 puntos)',
                                    '0' => 'No cumple (0 puntos)',
                                    'NA' => 'No aplica',
                                ];
                                if ($isAdmin) {
                                    $opts[''] = 'Selecciona...';
                                }
                                return $opts;
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
                                        
                                        Placeholder::make('archivo_link')
                                            ->label('Ver Evidencia')
                                            ->content(function ($get) {
                                                $archivo = $get('archivo');
                                                if (is_array($archivo)) {
                                                    $archivo = array_values($archivo)[0] ?? null;
                                                }
                                                if (! $archivo) return 'No hay evidencia subida.';
                                                $url = \Illuminate\Support\Facades\Storage::url($archivo);
                                                return new \Illuminate\Support\HtmlString("<a href=\"{$url}\" target=\"_blank\" style=\"color: #2563eb; text-decoration: underline; font-weight: 500;\">Ver/Descargar Archivo</a>");
                                            })
                                            ->visible(fn ($record) => $isAdmin || ($record && in_array($record->estatus, ['En revisión', 'Validado']))),
                                            
                                        FileUpload::make('archivo')
                                            ->label('Subir Evidencia')
                                            ->disk('public')
                                            ->directory('autoevaluaciones_evidencia')
                                            ->visible(fn ($record) => ! ($isAdmin || ($record && in_array($record->estatus, ['En revisión', 'Validado'])))),

                                        Select::make('calificacion_politica')
                                            ->label('Calificación de la política')
                                            ->options([
                                                'Aprobado' => 'Aprobar',
                                                'Rechazado' => 'Rechazar',
                                            ])
                                            ->required(fn () => $isAdmin)
                                            ->live()
                                            ->disabled(fn ($record) => ! $isAdmin || ($record && $record->estatus === 'Validado')),
                                            
                                        Textarea::make('feedback')
                                            ->label('Retroalimentación del evaluador')
                                            ->rows(2)
                                            ->required(fn ($get) => $get('calificacion_politica') === 'Rechazado')
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
                                        $set("respuestas.criterio_{$i}.elemento_{$elemId}.archivo", $data['archivo']);
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
                : "Criterio {$i}.- {$mainText}";

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
                                'Aprobado', 'Validado' => "<span{$tooltipAttr} style=\"background-color: #ecfdf5; color: #065f46; font-size: 0.875rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; border: 1px solid #a7f3d0; cursor: help;\">Aprobado</span>",
                                'En Revisión' => "<span{$tooltipAttr} style=\"background-color: #fffbeb; color: #92400e; font-size: 0.875rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; border: 1px solid #fde68a; cursor: help;\">En Revisión</span>",
                                'Rechazado' => "<span{$tooltipAttr} style=\"background-color: #fef2f2; color: #991b1b; font-size: 0.875rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; border: 1px solid #fecaca; cursor: help;\">Rechazado</span>",
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
                            ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin')
                            ->disabled(fn ($record) => $record && ($record->respuestas["criterio_{$i}"]['status'] ?? null) === 'Aprobado')
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
                                    if ($calif !== 'Aprobado') {
                                        $allApproved = false;
                                        break;
                                    }
                                }

                                $status = $allApproved ? 'Aprobado' : 'Rechazado';

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
                Tabs::make('Tipos de Criterios')
                    ->tabs([
                        Tab::make('Criterios Indispensables')
                            ->icon('heroicon-o-shield-check')
                            ->schema($indispensablesComponents),
                        
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


