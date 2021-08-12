<?php
namespace tool_inscripcion\task;

// use tool_inscripcion\models;
use tool_inscripcion\models\correo;
// use tool_inscripcion\models\report;
class inscripcion extends \core\task\scheduled_task
{
    /**
     * return name of task for admin panel.
     *inscripcion
     * @return string name
     */
    public function get_name()
    {
        return get_string('cronenroll', 'tool_inscripcion');
    }

    /**
     * method to execute by cron task.
     */
    public function execute()
    {
      // mtrace("Hola mundo");
      global $CFG;
      // Funcion para el recordatorio
      $correo_envio = new correo();
      $correo_envio->correo_envio();


    }
}
