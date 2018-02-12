<?php   
$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();

$BDM = new ConexionM();
$BDM->__constructM();
$codigo = $_POST["codigo"];
$valor = $_POST["valor"];
$fecha = $_POST["fecha"];
$PRio = $_POST["periodo"];
$Ttn = $_POST["tn"];
$centroE = $centro;

$valor = strtoupper($valor);
$result = array();
$result['error'] = 0;
 
if($DepOsub == 1){
    $ComSql = "LEFT (Centro, ".$MascaraEm.") = LEFT('".$centro."', ".$MascaraEm.")";
}else {
    $ComSql = "Centro = '".$centro."'";
}

$consulta = "SELECT valor
            FROM datos
            WHERE codigo = '$codigo'
            AND nombre = '$fecha'
            AND periodoP = '$PRio'
            AND tipoN = '$Ttn'
            AND IDEmpresa = '$IDEmpresa'
            AND $ComSql;";

if($objBDSQL->consultaBD($consulta) == 1){
    $result['error'] = 1;
    $objBDSQL->cerrarBD();
    echo json_encode($result);    
    exit();
}

$row = $objBDSQL->obtenResult();
$objBDSQL->liberarC();

/**
 * Consultar centro del empleado
 */

$consultaCentroEmp = "SELECT TOP 1 LTRIM(RTRIM(centro)) AS centro
                    FROM Llaves
                    WHERE codigo = '$codigo'
                    AND empresa = '$IDEmpresa'
                    AND tiponom = '$Ttn'";

if($objBDSQL->consultaBD($consultaCentroEmp) == 1){
    $result['error'] = 1;
    $objBDSQL->cerrarBD();
    echo json_encode($result);    
    exit();
}

$rowCentro = $objBDSQL->obtenResult();
if(!empty($rowCentro)){
    $centroE = $rowCentro['centro'];
}
$objBDSQL->liberarC();

/**
 * Verificar si existen datos en la consulta
 */
if(!empty($row)){
    if($row['valor'] == "F"){
        $consultaUser = "SELECT ID FROM usuarios WHERE User = '".Autoriza1."';";
        $queryUser = $BDM->query($consultaUser);
        $resultUser = $BDM->recorrer($queryUser);

        /**
         * Insertar registro en notificacion, se ha modificado una falta
         */
        $inserAlert = "INSERT INTO notificaciones VALUES (NULL, '".$_SESSION['IDUser']."', '$resultUser[0]', 'SE ELIMINO FALTA DE ".$codigo."', '', '0');";
        if(!$BDM->query($inserAlert)){
            $file = fopen("log/log".date("d-m-Y").".txt", "a");
            fwrite($file, ":::::::::::::::::ERROR MYSQL:::::::::::::::::::::".PHP_EOL);
            fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$BDM->errno. '  '.$BDM->error.PHP_EOL);
            fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - CONSULTA: '.$INSERALERTA);
            fclose($file);
        }

    }

    /**
     * Actualizar Datos en la Tabla Datos
     */

    $update = "UPDATE datos 
                SET valor = '$valor' 
                WHERE codigo = '$codigo'
                AND nombre = '$fecha'
                AND periodoP = '$PRio'
                AND tipoN = '$Ttn'
                AND IDEmpresa = '$IDEmpresa'
                AND Centro = '$centroE';";
    
    $consulta = $objBDSQL->consultaBD($update);
    if($consulta == 1){
        $result['error'] = 1;
        $objBDSQL->cerrarBD();
        echo json_encode($result);    
        exit();
    }
    
}else {

    /**
     * Insertar registro en la tabla Datos
     */

    $insert = "INSERT INTO datos VALUES 
                ('$codigo', 
                '$fecha', 
                '$valor', 
                '$PRio',
                '$Ttn',
                '$IDEmpresa',
                '$centroE');";

    if($objBDSQL->consultaBD($insert) == 1){
        $result['error'] = 1;
        $objBDSQL->cerrarBD();
        echo json_encode($result);    
        exit();
    }

}

try{
    $BDM->close();
    $objBDSQL->cerrarBD();
}catch(Exception $e){
    $resultV['error'] = 1;
    $file = fopen("log/log".date("d-m-Y").".txt", "a");
    fwrite($file, ":::::::::::::::::::::::ERROR BD:::::::::::::::::::::::".PHP_EOL);
    fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - ERROR al cerrar la conexion con SQL SERVER'.PHP_EOL);
    fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$e->getMessage().PHP_EOL);
    fclose($file);
}

echo json_encode($result);
exit();
?>