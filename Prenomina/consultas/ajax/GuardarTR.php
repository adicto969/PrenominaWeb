<?php
$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();

$BDM = new ConexionM();
$BDM->__constructM();
$codigo = $_POST["codigo"];
$frente = $_POST["frente"];
$fecha = $_POST["fecha"];
$PRio = $_POST["periodo"];
$Ttn = $_POST["tn"];

if($DepOsub == 1){
  $ComSql2 = "LEFT (CENTRO, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
  $ComSql3 = "LEFT (Centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
}else {
  $ComSql2 = "CENTRO = '".$centro."'";
  $ComSql3 = "Centro = '".$centro."'";
}

if($frente == 'F' || $frente == 'f' || empty($frente) || $frente == ' '){

}else {
  $Fecha0 = date('Y-m-d', strtotime($fecha));
  $dias = array('','LUN','MAR','MIE','JUE','VIE','SAB','DOM');
  $fechaDias = $dias[date('N', strtotime($Fecha0))];

  $RelacionEmpFrente = "SELECT ".$fechaDias." FROM relacionempfrente WHERE Codigo = '$codigo' AND CENTRO = '$centro' AND IDEmpresa = '$IDEmpresa';";
  $rsultadoM1 = $BDM->query($RelacionEmpFrente);
  $consulta = $objBDSQL->consultaBD($RelacionEmpFrente);

  if($consulta === false){
    $errorS = sqlsrv_errors();
    var_dump($errorS[0]['message']);
    exit();
  }


  if(empty($objBDSQL->obtenResult())){
    switch ($fechaDias) {
      case 'LUN':
        $INSERTRELACION = "INSERT INTO relacionempfrente VALUES ('$codigo', '$frente', '', '', '', '', '', '', '$centro', '$IDEmpresa');";
      break;
      case 'MAR':
        $INSERTRELACION = "INSERT INTO relacionempfrente VALUES ('$codigo', '', '$frente','','','','','', '$centro', '$IDEmpresa');";
      break;
      case 'MIE':
        $INSERTRELACION = "INSERT INTO relacionempfrente VALUES ('$codigo', '', '','$frente','','','','', '$centro', '$IDEmpresa');";
      break;
      case 'JUE':
        $INSERTRELACION = "INSERT INTO relacionempfrente VALUES ('$codigo', '', '','','$frente','','','', '$centro', '$IDEmpresa');";
      break;
      case 'VIE':
        $INSERTRELACION = "INSERT INTO relacionempfrente VALUES ('$codigo', '', '','','','$frente','','', '$centro', '$IDEmpresa');";
      break;
      case 'SAB':
        $INSERTRELACION = "INSERT INTO relacionempfrente VALUES ('$codigo', '', '','','','','$frente','', '$centro', '$IDEmpresa');";
      break;
      case 'DOM':
        $INSERTRELACION = "INSERT INTO relacionempfrente VALUES ('$codigo', '', '','','','','','$frente', '$centro', '$IDEmpresa');";
      break;
      default:
        # code...
        break;
    }

    $insertar = $objBDSQL->consultaBD($INSERTRELACION);
    if($insertar === false){
      die(print_r(sqlsrv_errors(), true));
      exit();
    }
    $objBDSQL->liberarC();
  }else {
    if($frente == '-n' || $frente == '-N'){
        $UPDATERELACION = "UPDATE relacionempfrente SET ".$fechaDias." = '' WHERE Codigo = '$codigo' AND CENTRO = '$centro' AND IDEmpresa = '$IDEmpresa';";
    }else {
        $UPDATERELACION = "UPDATE relacionempfrente SET ".$fechaDias." = '$frente' WHERE Codigo = '$codigo' AND CENTRO = '$centro' AND IDEmpresa = '$IDEmpresa';";
    }

    $insertar = $objBDSQL->consultaBD($UPDATERELACION);
    if($insertar === false){
      die(print_r(sqlsrv_errors(), true));
      exit();
    }
    $objBDSQL->liberarC();
  }
}

$consultaD = "SELECT valor FROM datos WHERE codigo = '$codigo' AND nombre = '$fecha' AND periodoP = '$PRio' AND tipoN = '$Ttn' AND IDEmpresa = '$IDEmpresa' AND Centro = '$centro';";
echo $consultaD;
$consulta = $objBDSQL->consultaBD($consultaD);

if($consulta === false){
  $errorS = sqlsrv_errors();
  var_dump($errorS[0]['message']);
  exit();
}
$row = $objBDSQL->obtenResult();
if(!empty($row)){
  echo $row['valor'];
  if($row['valor'] == "F"){
    $consCodigo = "SELECT ID FROM usuarios WHERE User = '".Autoriza1."';";
    echo $consCodigo;
    $RUSER = $BDM->query($consCodigo);
    $datoUser = $BDM->recorrer($RUSER);
    $INSERALERTA = "INSERT INTO notificaciones VALUES (NULL, '".$_SESSION['IDUser']."', '$datoUser[0]', 'SE ELIMINO FALTA DE ".$codigo."', '', '0');";
    if($BDM->query($INSERALERTA)){

    }else {
      echo $BDM->errno. '  '.$BDM->error;
    }

    $consultaAnti = "SELECT valor FROM datosanti WHERE codigo = '$codigo' AND nombre = 'fecha".$fecha."' AND periodoP = '$PRio' AND tipoN = '$Ttn' AND IDEmpresa = '$IDEmpresa' AND Centro = '$centro';";
    echo $consultaAnti;
    $consulta = $objBDSQL->consultaBD($consultaD);
    $resultados = $objBDSQL->obtenResult();
    $objBDSQL->liberarC();
    if(empty($resultados)){
      $insert = "INSERT INTO datosanti VALUES ('$codigo', 'fecha".$fecha."', '$frente', '$PRio', '$Ttn', '$IDEmpresa', '$centro', 0, 0, 0);";
      echo $insert;
      $consulta = $objBDSQL->consultaBD($insert);
      if($consulta === false){
        die(print_r(sqlsrv_errors(), true));
      }
      $objBDSQL->liberarC();
    }else {
      $update = "UPDATE datosanti SET valor = '$frente' WHERE codigo = '$codigo' AND nombre = 'fecha".$fecha."' AND periodoP = '$PRio' AND tipoN = '$Ttn' AND IDEmpresa = '$IDEmpresa' AND Centro = '$centro';";
      echo $update;
      $consulta = $objBDSQL->consultaBD($update);
      if($consulta === false){
        die(print_r(sqlsrv_errors(), true));
      }
      $objBDSQL->liberarC();
    }
  }else {
    if(empty($row)){
      $insert = "INSERT INTO datos VALUES ('$codigo', '$fecha', '$frente', '$PRio', '$Ttn', '$IDEmpresa', '$centro');";
       echo $insert;
      $consulta = $objBDSQL->consultaBD($insert);
      if($consulta === false){
        die(print_r(sqlsrv_errors(), true));
      }
      $objBDSQL->liberarC();
    }else {
      $update = "UPDATE datos SET valor = '$frente' WHERE codigo = '$codigo' AND nombre = '$fecha' AND periodoP = '$PRio' AND tipoN = '$Ttn' AND IDEmpresa = '$IDEmpresa' AND Centro = '$centro';";
      echo $update;
      $consulta = $objBDSQL->consultaBD($update);
      if($consulta === false){
        die(print_r(sqlsrv_errors(), true));
      }
      $objBDSQL->liberarC();
    }
  }
}

try {
  $BDM->close();
  $objBDSQL->cerrarBD();
} catch (Exception $e) {
  echo 'ERROR al cerrar la conexion con SQL SERVER', $e->getMessage(), "\n";
}

?>
