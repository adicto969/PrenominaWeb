<?php

$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();

$Periodo = $_POST["periodo"];
$Tn = $_POST["TN"];
$codigo = $_POST["codigo"];
$numr = "";
if($Periodo <= 24){
$_fechas = periodo($Periodo);
list($fecha1, $fecha2, $fecha3, $fecha4) = explode(',', $_fechas);
}

if($Periodo > 24 || $Tn == 1){
  $_queryFechas = "SELECT CONVERT (VARCHAR (10), inicio, 103) AS 'FECHA1', CONVERT (VARCHAR (10), cierre, 103) AS 'FECHA2' FROM Periodos WHERE tiponom = 1 AND periodo = $Periodo AND ayo_operacion = $ayoA AND empresa = $IDEmpresa ;";
  $_resultados = $objBDSQL->consultaBD($_queryFechas);
  if($_resultados === false)
  {
      die(print_r(sqlsrv_errors(), true));
      exit();
  }else {
      $_datos = $objBDSQL->obtenResult();
  }
  $fecha1 = $_datos['FECHA1'];
  $fecha2 = $_datos['FECHA2'];
  $fecha3 = $_datos['FECHA1'];
  $fecha4 = $_datos['FECHA2'];
  $objBDSQL->liberarC();
}

if($DepOsub == 1)
{
	$ComSql = "LEFT (Centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
}else {
	$ComSql = "Centro = '".$centro."'";
}

$fechaSuma = "";
$ff = str_replace('/', '-', $fecha1);

$ConsultaConfirm = "SELECT codigo FROM Llaves WHERE codigo = '".$codigo."' AND tiponom = ".$Tn." AND ".$ComSql."";
$objBDSQL->consultaBD($ConsultaConfirm);
$codigoConfirm = $objBDSQL->obtenResult();
$objBDSQL->liberarC();
if(empty($codigoConfirm['codigo'])){
  echo "El empleado $codigoConfirm no exite con los datos ingresados";
}else {
  for ($i=0; $i <= 40; $i++) {
  	if($fechaSuma != $fecha2){
  		$fechaSuma = date("d/m/Y", strtotime($ff." +".$i." day"));
      $ff2 = str_replace('/', '-', $fechaSuma);

  		$nombre = "fecha".$ff2;

  		if(empty($_POST[$nombre])){

  		}else {

  			$queryM = "SELECT valor FROM datosanti WHERE codigo = '".$codigo."' AND nombre = 'fecha".$ff2."' AND periodoP = '".$Periodo."' AND tipoN = '".$Tn."' and IDEmpresa = '".$IDEmpresa."' and ".$ComSql.";";

  			$numr = $objBDSQL->obtenfilas($queryM);

  	  		if($numr <= 0){
            if(strtoupper($_POST[$nombre]) == "PG"){
              $Minsert = "INSERT INTO datosanti VALUES ('".$codigo."', 'fecha".$ff2."', '".strtoupper($_POST[$nombre])."', '".$Periodo."', '".$Tn."', '".$IDEmpresa."', '".$centro."', 0, 0, 0);";
                $objBDSQL->consultaBD($Minsert);
            }else {
              $Minsert = "INSERT INTO datosanti VALUES ('".$codigo."', 'fecha".$ff2."', '".strtoupper($_POST[$nombre])."', '".$Periodo."', '".$Tn."', '".$IDEmpresa."', '".$centro."', 1, 1, 1);";
    	        	$objBDSQL->consultaBD($Minsert);
            }

  	  		}else{

  	  			$Minsert = "UPDATE datosanti SET valor = '".strtoupper($_POST[$nombre])."' WHERE codigo = '".$codigo."' and nombre = 'fecha".$ff2."' and periodoP = '".$Periodo."' and tipoN = '".$Tn."' and IDEmpresa = '".$IDEmpresa."' and Centro = '".$centro."';";
  	        	$objBDSQL->consultaBD($Minsert);
  	  		}



  		}

  	}else {

  	}
  }
  if($numr > 0)
  {
  	echo "1";
  }
  else {
  	echo "2";
  }

}


?>
