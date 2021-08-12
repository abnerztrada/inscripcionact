<?php
namespace tool_inscripcion\models;

// require(dirname(dirname(__FILE__)).'/config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/moodlelib.php');

/**
 *
 */
class correo
{

  public function __construct()
  {
    // code...
  }

  public function correo_envio(){
      //Query de curso y fechas
      $query = "SELECT c.id, c.shortname, c.fullname, 
      DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(c.enddate, '%Y-%m-%d'), INTERVAL -5 HOUR),'%d/%m/%Y') AS fecha_fin,
      DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(c.startdate, '%Y-%m-%d'), INTERVAL -5 HOUR),'%d/%m/%Y') AS fecha_inicio,
      DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(cd.value, '%Y-%m-%d %H:%i'), INTERVAL -5 HOUR),'%d/%m/%Y %H:%i') AS fecha_inscripcion
      FROM mdl_course c
      INNER JOIN mdl_customfield_data cd ON cd.instanceid = c.id
      where c.visible = 1 and cd.fieldid = 31  and c.id = 241"; 


      global $DB;
      $result = $DB->get_records_sql($query);

      $url = 'https://calidad.laucmi.telefonicaed.pe/course/view.php?id=';

      foreach ($result as $it) {
        $urltemp = $url.$it->id;
        $id = $it->id;
        $fecha = $it->fecha_inscripcion;
        $fechaInicio = $it->fecha_inicio; 
        $fechaFinal = $it->fecha_fin;         

        //Query de estudiantes. 
        $query3 = "SELECT   @s:=@s + 1 id_auto, c.id, c.fullname, CONCAT(u.firstname ,' ', u.lastname) as nombre, c.shortname, c.fullname, 
                          u.email
                          FROM
                          (select @s:=0) as s,
                          mdl_user u
                          INNER JOIN mdl_role_assignments as asg on asg.userid = u.id
                          INNER JOIN mdl_context as con on asg.contextid = con.id
                          INNER JOIN mdl_course c on con.instanceid = c.id
                          INNER JOIN mdl_role r on asg.roleid = r.id
                          where c.id = $id and r.shortname = 'student'";

        $result3 = $DB->get_records_sql($query3);


          echo '<pre>';
            print_r($query3);
          echo '</pre>';

          echo '<pre>';
          print_r($result3);
          echo '</pre>';

        
          echo '<pre>';
          print_r($result);
          echo '</pre>';

          echo $fecha;

          foreach ($result3 as $it3) {
            $nombre = $it3->nombre;
            $subject = $it3->fullname;
            $body = $urltemp;

            $emailuser->email = $it3->email;
            $emailuser->id = -99;
            $emailuser->maildisplay = true;
            $emailuser->mailformat = 1;

            date_default_timezone_set("America/Guatemala");
            $fechaAct = date("d/m/Y H:i"); //w para los dias de la semana

            //Imagen 
            $String ="<img src='http://54.161.158.96/local/img/banner.jpg'";  

            //Texto para el recordatorio
            $string1 = ""; 
            $string1 .= $String."\n";
            $string1 .= "<br>"; 
            $string1 .= "<br>"; 
            $string1 .= "<div style='color: orange; font-size: 18px; font-family: Century Gothic;'> $nombre </div>";
            $string1 .= "<br>"; 
            $string1 .= "<div style='color: black; font-size: 16px; font-family: Century Gothic;'> laUcmi te da la mas cordial bienvenida al curso <span style= 'color: orange; font-size: 16px; font-family: Century Gothic;'> $subject, </span> el cual estará habilitado desde el <span style= 'color: orange; font-size: 16px; font-family: Century Gothic;'> $fechaInicio </span> hasta <span style= 'color: orange; font-size: 16px; font-family: Century Gothic;'> $fechaFinal. </span> </div>";
            $string1 .= "<div style='color: black; font-size: 16px; font-family: Century Gothic;'> Haz click en el siguiente enlace para ingresar al curso $body </div>";
            $string1 .= "<br>"; 
            $string1 .= "<div style='color: black; font-size: 16px; font-family: Century Gothic;'> Para que tu asistencia al curso sea tomada en cuenta, recuerda que debes responder la encuesta de satisfacción. \n </div>";
            $string1 .= "<div style='color: black; font-size: 16px; font-family: Century Gothic;'> Cualquier duda o comentario puedes escribirnos a cmi-laucmi@somoscmi.com \n </div>";
            $string1 .= "<br>"; 
            $string1 .= "<div style='color: black; font-size: 16px; font-family: Century Gothic;'> Atentamente, \n </div>";
            $string1 .= "<div style='color: black; font-size: 16px; font-family: Century Gothic;'> laUcmi \n </div>";

            if($fechaAct == $fecha){
              $email = email_to_user($emailuser,'laUcmi',' laUcmi te da la Bienvenida al curso '.$subject,$string1);
              echo "Correo enviado";
            }else{
              echo "Correo no enviado";
            }
          }
        }
      }
    }
?>
