<?php
#consultas Generales como el nombre de la empresa etc.

#Consulta MYSQLI
if(isset($_SESSION['IDUser'])){
  if(isset($_SESSION['IDEmpresa'])){
        
    $IDEmpresa = $_SESSION['IDEmpresa'];
    $centro = $_SESSION['centro'];
    $DB = $_SESSION['DB'];
    $UserDB = $_SESSION['UserDB'];
    $PassDB = $_SESSION['PassDB'];
    $PC = $_SESSION['PC'];
    $TN = $_SESSION['TN'];
    $PP = $_SESSION['PP'];
    $PA = $_SESSION['PA'];
    $POR = $_SESSION['POR'];
    $correo = $_SESSION['correo'];
    $ServerS = $_SESSION['ServerS'];
    $DepOsub = $_SESSION['DepOsub'];
    $FactorA = $_SESSION['FactorA'];
    $supervisor = $_SESSION['supervisor'];
  }else {
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

      $_SESSION['IDEmpresa'] = $IDEmpresa;
      $_SESSION['centro'] = $centro;
      $_SESSION['DB'] = $DB;
      $_SESSION['UserDB'] = $UserDB;
      $_SESSION['PassDB'] = $PassDB;
      $_SESSION['PC'] = $PC;
      $_SESSION['TN'] = $TN;
      $_SESSION['PP'] = $PP;
      $_SESSION['PA'] = $PA;
      $_SESSION['POR'] = $POR;
      $_SESSION['correo'] = $correo;
      $_SESSION['ServerS'] = $ServerS;
      $_SESSION['DepOsub'] = $DepOsub;
      $_SESSION['FactorA'] = $FactorA;
      $_SESSION['supervisor'] = $supervisor;
    }else {
      echo "No se han encontrado datos MYSQL(Usuario)";
    }

    $bdM->liberar($sql);
    $bdM->close();
  }
  
  #Consulta SQL
  $objBDSQL = new ConexionSRV();
  $objBDSQL->conectarBD();

  if(isset($_SESSION['NombreEmpresa'])){

    $NombreEmpresa = $_SESSION['NombreEmpresa'];
    $RFC = $_SESSION['RFC'];
    $RegisEmpresa = $_SESSION['RegisEmpresa'];
    $DirecEmpresa = $_SESSION['DirecEmpresa'];
    $PoblacionEmpresa = $_SESSION['PoblacionEmpresa'];

  }else {
    
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

      $_SESSION['NombreEmpresa'] = $NombreEmpresa;
      $_SESSION['RFC'] = $RFC;
      $_SESSION['RegisEmpresa'] = $RegisEmpresa;
      $_SESSION['DirecEmpresa'] = $DirecEmpresa;
      $_SESSION['PoblacionEmpresa'] = $PoblacionEmpresa;
    }

    $objBDSQL->liberarC();
  }


  if(isset($_SESSION['NomDep'])){
    $NomDep = $_SESSION['NomDep'];
  }else {
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
      $_SESSION['NomDep'] = $NomDep;
    }

    $objBDSQL->liberarC();

  }
  
  if(isset($_SESSION['MascaraEm'])){
    $MascaraEm = $_SESSION['MascaraEm'];
  }else {
    $query = "SELECT LEN (LEFT (mascara, charindex(' ', mascara) -1)) AS mascara FROM empresas WHERE empresa = '$IDEmpresa';";
    $MascaraEm = "";
    if($objBDSQL->consultaBD($query) == 1){
      $_SESSION['MascaraEm'] = 0;
    }else {
      $datos = $objBDSQL->obtenResult();
      if($datos == 'NULL'){
        echo "No se encontro la empresa";
      }else if($datos === false){
        die(print_r(sqlsrv_errors(), true));
      }else {
        $MascaraEm = $datos['mascara'];
        $_SESSION['MascaraEm'] = $MascaraEm;
      }

      $objBDSQL->liberarC();
    }
    
  }
  
  if(isset($_SESSION['NombreSupervisor'])){
    $NombreSupervisor = $_SESSION['NombreSupervisor'];
  }else {
    $query = "SELECT LTRIM(RTRIM(nombre)) AS nombre FROM supervisores WHERE supervisor = '".$supervisor."' AND empresa = '".$IDEmpresa."'";
    $NombreSupervisor = "";
    if($objBDSQL->consultaBD($query) == 1){
      $NombreSupervisor = "Supervisor";
    }else {
      $datos = $objBDSQL->obtenResult();
      if($datos == 'NULL'){
        $NombreSupervisor = "Supervisor";
      }else if($datos === false){
        die(print_r(sqlsrv_errors(), true));
      }else {
        $NombreSupervisor = $datos['nombre'];
      } 

      $objBDSQL->liberarC();
    }
    $_SESSION['NombreSupervisor'] = $NombreSupervisor;
    
  }
  
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
