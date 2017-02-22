<?php

function connectDB(){

        $server = '10.132.91.134';
        $user = 'root';
        $pass = '%$M@l*#';
        $bd = 'testosticket';

    $conexion = mysqli_connect($server, $user, $pass,$bd);
/*
        if($conexion){
            echo 'La conexion de la base de datos se ha hecho satisfactoriamente
';
        }else{
            echo 'Ha sucedido un error inexperado en la conexion de la base de datos
';
        }
*/
    return $conexion;
}

function disconnectDB($conexion){

    $close = mysqli_close($conexion);

        /*if($close){
            echo 'La desconexion de la base de datos se ha hecho satisfactoriamente';
        }else{
            echo 'Ha sucedido un error inexperado en la desconexion de la base de datos
';
        } */  

    return $close;
}
 $rawdata = array(); 
function getArraySQL($sql, $color,$type){
    //Creamos la conexión con la función anterior
    $conexion = connectDB();
    global $rawdata;
    //generamos la consulta

        mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

    if(!$result = mysqli_query($conexion, $sql)) die(); //si la conexión cancelar programa

   //creamos un array

    //guardamos en un array multidimensional todos los datos de la consulta
    $i=0;

    while($row = mysqli_fetch_array($result))
    {
          $title=$row['title'];
    $start=$row['start'];
    $end=$row['end'];   

    $id=$row['id'];
    
    $rawdata[] = array('title'=> $title, 'start'=> $start, 'end'=> $end,'constraint'=> 'businessHours', 'borderColor'=>'#666', 'url'=>'http://helpdesk.erpcya.com/Test/scp/'.$type.'.php?id='.$id, 'color'=> $color
    );

     // .$type.'?id='.$id   $rawdata[$i] = $row;
        $i++;
        $end='';
    }

 
  
    disconnectDB($conexion); //desconectamos la base de datos

    return $rawdata; //devolvemos el array
}


function createFile(){
	global $rawdata;
$json_string = json_encode($rawdata);

$file = 'clientes.json';
file_put_contents($file, $json_string);
}
$staff=$_GET['staff'];
$sql="SELECT tcd.task_id as id, tcd.title as title, t.created start, COALESCE(t.duedate,t.closed) end 
		FROM ost_task t 
		INNER JOIN ost_thread th ON (t.id=th.object_id)
		INNER JOIN ost_task__cdata tcd ON (th.object_id=tcd.task_id)
		WHERE th.object_type='A' and t.staff_id=$staff
		";
        $myArray = getArraySQL($sql,'#25427e','tasks');

$sql="SELECT tc.ticket_id as id, tc.subject as title, t.created start, COALESCE(t.duedate,t.closed) end
        FROM ost_ticket t
        INNER JOIN ost_thread th ON (t.ticket_id=th.object_id)
        INNER JOIN ost_ticket__cdata tc ON (th.object_id=tc.ticket_id)
        WHERE th.object_type='T' and t.staff_id=$staff
        ";
        
$myArray = getArraySQL($sql,'#7dbf3f','tickets');
createFile();
        
// Require our Event class and datetime utilities
require dirname(__FILE__) . '/utils.php';

// Short-circuit if the client did not give us a date range.
if (!isset($_GET['start']) || !isset($_GET['end'])) {
	die("Please provide a date range.");
}

// Parse the start/end parameters.
// These are assumed to be ISO8601 strings with no time nor timezone, like "2013-12-29".
// Since no timezone will be present, they will parsed as UTC.
$range_start = parseDateTime($_GET['start']);
$range_end = parseDateTime($_GET['end']);


// Parse the timezone parameter if it is present.
$timezone = null;
if (isset($_GET['timezone'])) {
	$timezone = new DateTimeZone($_GET['timezone']);
}

// Read and parse our events JSON file into an array of event data arrays.
 $json = file_get_contents(dirname(__FILE__) . '/../php/clientes.json');
$input_arrays = json_decode($json, true);

// Accumulate an output array of event data arrays.
$output_arrays = array();
foreach ($input_arrays as $array) {

	// Convert the input array into a useful Event object
	$event = new Event($array, $timezone);

	// If the event is in-bounds, add it to the output
	if ($event->isWithinDayRange($range_start, $range_end)) {
		$output_arrays[] = $event->toArray();
	}
}

// Send JSON to the client.
echo json_encode($output_arrays);