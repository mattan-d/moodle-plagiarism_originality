<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * plagiarism_originality ES
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Originalidad - Detección de Copias';
$string['originality'] = 'Componente de Originalidad - Detección de Copias en Documentos';
$string['originality_help'] =
        'Configuración para la detección de copias en trabajos de texto. No debe utilizarse para trabajos en otros idiomas o trabajos de ingeniería, ya que el mecanismo no está diseñado para eso.';
$string['originality_shortname'] = 'Originalidad';
$string['plugin_server_type'] = 'Servidor de Originalidad';
$string['plugin_settings'] = 'Configuración de Originalidad';
$string['plugin_enabled'] = 'Habilitar el complemento';
$string['plugin_connected'] = 'Clave secreta válida, conectado al sistema de Originalidad';
$string['student_disclosure'] =
        "Debes marcar √ en el lugar correspondiente para enviar la tarea para su verificación de originalidad. Sin marcar esto, no se podrá enviar esta tarea.<br>Esta presentación es original, es de mi propiedad, fue realizada por mí y al presentarla asumo la responsabilidad de la originalidad del contenido.<br><br>Excepto en los lugares donde indiqué que el trabajo fue realizado por otros y se encuentra el enlace correspondiente en la bibliografía o en el lugar requerido.<br><br>Soy consciente y acepto que esta tarea será revisada para detectar plagio por parte de la empresa Originalidad y acepto los <a rel='external' href='https://originality.world/termsOfUseSP.html' target='_blank' style='text-decoration:underline'>Términos de uso</a>.";
$string['secret'] = 'Clave de uso';
$string['key'] = 'Clave de uso';
$string['key_help'] = 'Debe tener una clave de uso para utilizar el complemento.';
$string['saved_failed'] = 'La clave de uso ingresada es incorrecta, el complemento no está activado.';
$string['checking_inprocessmsg'] = 'En proceso de revisión';
$string['checking_unprocessable'] = 'No se puede procesar';
$string['submitted_before_activation'] = 'Presentado antes de activar el complemento';
$string['service_is_inactive'] = 'El complemento de Originalidad no está activo. Por favor, contacte al administrador de Moodle.';
$string['warning_message'] = "Debe marcar el botón de aceptación ('Soy consciente y acepto') para habilitar el botón de envío.";
$string['previous_submissions'] =
        'Existen entregas anteriores. Estos estudiantes deben volver a entregar su trabajo para que se verifique su originalidad.';
$string['production_endpoint'] =
        '<b>Servidor de Originalidad</b>&nbsp;&nbsp;<span style="font-size:14px;">Enviar trabajos al servidor de producción de Originalidad.</span>';
$string['test_endpoint'] =
        '<b>Servidor de Prueba</b>&nbsp;&nbsp;<span style="font-size:14px;">Enviar trabajos al servidor de prueba de Originalidad. Debe seleccionar esto solo después de coordinar con Originalidad.</span>';
$string['check_ghostwriter'] = 'Componente de Detección de Escritores Fantasma para Trabajos Grandes';
$string['check_ghostwriter_help'] = 'Ghostwriter check realiza un servicio adicional que calcula la probabilidad de que el estudiante haya escrito la tarea enviada. Debe verificar con Originality su elegibilidad para utilizar el servicio. Simplemente habilitarlo sin la aprobación previa de Originality no habilitará el servicio, pero seguramente frustrará a sus profesores.';
$string['check_ghostwriter_label'] = 'Escritores Fantasma';
$string['ghostwriter_enabled'] = 'Habilitar la verificación del escritor fantasma';
$string['ghostwriter_failed_message'] = 'No se puede realizar la verificación de escritores fantasma para texto en línea';
$string['pdf:filename'] = 'Ver informe de originalidad';
$string['default_settings_assignments'] = 'Habilitar la verificación para nuevas tareas';
$string['merge_reports'] = 'Combinar archivos de informes antiguos en una nueva estructura';
$string['stuck_submissions'] = 'Intentando volver a enviar envíos que están bloqueados';
