<?php
$periodo = $_POST['periodo'];
$TN = $_POST['TN'];
$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();
$fecha1 = "";
$fecha2 = "";
$fecha3 = "";
$fecha4 = "";
if($PC <= 24){
	$_fechas = periodo($periodo);
	list($fecha1, $fecha2, $fecha3, $fecha4) = explode(',', $_fechas);
	$fecha3 = $fecha1;
	$fecha4 = $fecha2;
}


if($TN == 1 || $PC > 24)
{
  $_queryFechas = "SELECT CONVERT (VARCHAR (10), inicio, 103) AS 'FECHA1', CONVERT (VARCHAR (10), cierre, 103) AS 'FECHA2' FROM Periodos WHERE tiponom = 1 AND periodo = $periodo AND ayo_operacion = $ayoA AND empresa = $IDEmpresa ;"; 
  $_resultados = $objBDSQL->consultaBD($_queryFechas);
  if($_resultados === false)
  {
      die(print_r(sqlsrv_errors(), true));
      break;
  }
  $_datos = $objBDSQL->obtenResult();
  $fecha1 = $_datos['FECHA1'];
  $fecha2 = $_datos['FECHA2'];
  $fecha3 = $fecha1;
  $fecha4 = $fecha2;
  $objBDSQL->liberarC();
}


if(empty($fecha3)){
    echo "Error";
}else {

    $fechas = array(
      "fecha1" => $fecha1,
      "fecha2" => $fecha2,
      "fecha3" => $fecha3,
      "fecha4" => $fecha4
    );

    echo json_encode($fechas);

    try {
      $objBDSQL->cerrarBD();
    } catch (Exception $e) {
      echo 'ERROR al cerrar la conexion con SQL SERVER', $e->getMessage(), "\n";
    }
}


?>
