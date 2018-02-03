<?php
#consultas Generales como el nombre de la empresa etc.

#Consulta MYSQLI
if(isset($_SESSION['IDUser'])){
  $bdM = new ConexionM();
  $bdM->__constructM();
  $sql = $bdM->query("SELECT IDEmpresa, centro, DB, UserDB, PassDB, PC, TN, PP, PA, POR, correo, server, DoS, FactorA, supervisor FROM config WHERE IDUser = '".$_SESSION['IDUser']."' LIMIT 1;");
  if($bdM->rows($sql) > 0){
    $datos = $bdM->recorrer($sql);
    $IDEmpresa = $datos[0];
    $centro = $datos[1];
    $DB = $datos[2];
    $UserDB = $datos[3];
    $PassDB = $datos[4];
    $PC = $datos[5];
    $TN = $datos[6];
    $PP = $datos[7];
    $PA = $datos[8];
    $POR = $datos[9];
    $correo = $datos[10];
    $ServerS = $datos[11];
    $DepOsub = $datos[12];
    $FactorA = $datos[13];
    $supervisor = $datos[14];    
  }else {
    echo "No se han encontrado datos MYSQL(Usuario)";
  }

  $bdM->liberar($sql);
  $bdM->close();

  #Consulta SQL
  $objBDSQL = new ConexionSRV();
  $objBDSQL->conectarBD();
  $query = "SELECT LTRIM ( RTRIM ( nombre_empresa ) ) AS nombre_empresa,
                   LTRIM ( RTRIM ( rfc_empresa ) ) AS rfc_empresa,
                   LTRIM ( RTRIM ( registro_patronal ) ) AS registro_patronal,
                   direccion_empresa,
                   poblacion_empresa

            FROM empresas WHERE empresa = $IDEmpresa;";
  $objBDSQL->consultaBD($query);
  $datos = $objBDSQL->obtenResult();
  if($datos == 'NULL'){
    echo "No se encontro resultado SQL(DATOS EMPRESA)";
  }else if($datos === false){
    die(print_r(sqlsrv_errors(), true));
  }else {
    $NombreEmpresa = $datos['nombre_empresa'];
    $RFC = $datos['rfc_empresa'];
    $RegisEmpresa = $datos['registro_patronal'];
    $DirecEmpresa = $datos['direccion_empresa'];
    $PoblacionEmpresa = $datos['poblacion_empresa'];
  }

  $objBDSQL->liberarC();

  $query = "SELECT LTRIM ( RTRIM ( nomdepto ) ) AS nomdepto FROM centros WHERE centro = '$centro' AND empresa = '$IDEmpresa';";
  $NomDep="";
  $objBDSQL->consultaBD($query);
  $datos = $objBDSQL->obtenResult();
  if($datos == 'NULL'){
    echo "No se encontro resultado SQL(Nombre DEPARTAMENTO)";
  }else if($datos === false){
    die(print_r(sqlsrv_errors(), true));
  }else {
    $NomDep = $datos['nomdepto'];
  }

  $objBDSQL->liberarC();

  $query = "SELECT LEN (LEFT (mascara, charindex(' ', mascara) -1)) AS mascara FROM empresas WHERE empresa = '$IDEmpresa';";
  $MascaraEm = "";
  $objBDSQL->consultaBD($query);
  $datos = $objBDSQL->obtenResult();
  if($datos == 'NULL'){
    echo "No se encontro la empresa";
  }else if($datos === false){
    die(print_r(sqlsrv_errors(), true));
  }else {
    $MascaraEm = $datos['mascara'];
  }

  $objBDSQL->liberarC();

  $query = "SELECT LTRIM(RTRIM(nombre)) AS nombre FROM supervisores WHERE supervisor = '".$supervisor."' AND empresa = '".$IDEmpresa."'";
  $NombreSupervisor = "";
  $objBDSQL->consultaBD($query);
  $datos = $objBDSQL->obtenResult();
  if($datos == 'NULL'){
    $NombreSupervisor = "Supervisor";
  }else if($datos === false){
    die(print_r(sqlsrv_errors(), true));
  }else {
    $NombreSupervisor = $datos['nombre'];
  }

  $objBDSQL->liberarC();
  ####################################################

  $queryH = "DECLARE @return_value int
             EXEC    @return_value = [dbo].[roldehorario]
                     @empresa = '$IDEmpresa'
             ";

  $Lun = array();
  $Mar = array();
  $Mie = array();
  $Jue = array();
  $Vie = array();
  $Sab = array();
  $Dom = array();
  $DesHora = array();
  $iDH = 0;

  $objBDSQL->consultaBD($queryH);

  while($datos = $objBDSQL->obtenResult()){
    $iDH++;
    $DesHora[$iDH] = $datos["Nombre"];
    $Lun[$iDH] = $datos["LUN"];
    $Mar[$iDH] = $datos["MAR"];
    $Mie[$iDH] = $datos["MIE"];
    $Jue[$iDH] = $datos["JUE"];
    $Vie[$iDH] = $datos["VIE"];
    $Sab[$iDH] = $datos["SAB"];
    $Dom[$iDH] = $datos["DOM"];
  }

  $objBDSQL->liberarC();

  try {
    $objBDSQL->cerrarBD();
  } catch (Exception $e) {
    echo 'ERROR al cerrar la conexion con SQL SERVER', $e->getMessage(), "\n";
  }
}else {
  $NombreEmpresa = "PRENOMINA";
}



?>
