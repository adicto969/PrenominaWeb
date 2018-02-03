<?php
$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();


$codigo = $_POST["codigo"];
$valor = $_POST["valor"];
$fecha = $_POST["fecha"];
$Tnn = $_POST["Tn"];
$P = $_POST["Pe"];

$consultaE = "SELECT valor FROM TiempoExtra WHERE Codigo = '$codigo' AND Fecha = '$fecha' AND Periodo = '$P' AND Tn = '$Tnn' AND IDEmpresa = '$IDEmpresa' AND Centro = '$centro';";
$result = $objBDSQL->obtenfilas($consultaE);
if($result == 0){
  $INSERT = "INSERT INTO TiempoExtra VALUES ('$codigo', '$fecha', '$valor', '$P', '$Tnn', '$IDEmpresa', '$centro');";
}else {
  $INSERT = "UPDATE TiempoExtra SET Valor = '$valor' WHERE Codigo = '$codigo' AND Fecha = '$fecha' AND Periodo = '$P' AND Tn = '$Tnn' AND IDEmpresa = '$IDEmpresa' AND Centro = '$centro';";
}

$objBDSQL->consultaBD($INSERT);
$objBDSQL->cerrarBD();

?>
