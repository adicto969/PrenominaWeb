<?php
$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();

$objBDSQL2 = new ConexionSRV();
$objBDSQL2->conectarBD();

$modo = $_POST['modo'];

if($DepOsub == 1)
{
	$ComSql = "LEFT (Llaves.centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
}else {
	$ComSql = "Llaves.centro = '".$centro."'";
}

$query = "
select empleados.codigo, empleados.ap_paterno+' '+empleados.ap_materno+' '+empleados.nombre as Nombre, tabulador.actividad

from empleados

INNER JOIN Llaves on  Llaves.codigo = empleados.codigo
INNER JOIN tabulador on  tabulador.ocupacion = Llaves.ocupacion and  tabulador.empresa = Llaves.empresa

where  ".$ComSql." AND Llaves.empresa = '".$IDEmpresa."' and empleados.activo = 'S'

order by ap_paterno, ap_materno, nombre
";

$objBDSQL->consultaBD($query);

if($modo == "G"){

	while($row = $objBDSQL->obtenResult()){

		$nombreA = $row["codigo"];

		if(empty($_POST[$nombreA])){
			$Minsert = "INSERT INTO ajusteempleado values ('".$nombreA."', '0', '0', '0', '0', '".$centro."', ".$IDEmpresa." );";
			$objBDSQL2->consultaBD2($Minsert);
		}else {
			$array = array($_POST[$nombreA]);

			if(empty($array[0][0])){
				$A1 = 0;
			}else {
				$A1 = $array[0][0];
			}

			if(empty($array[0][1])){
				$A2 = 0;
			}else {
				$A2 = $array[0][1];
			}

			if(empty($array[0][2])){
				$A3 = 0;
			}else {
				$A3 = $array[0][2];
			}

			if(empty($array[0][3])){
				$A4 = 0;
			}else {
				$A4 = $array[0][3];
			}

			$Minsert = "INSERT INTO ajusteempleado values ('".$nombreA."', '".$A1."', '".$A2."', '".$A3."', '".$A4."', '".$centro."', ".$IDEmpresa." );";
			$objBDSQL2->consultaBD2($Minsert);
		}
	}

echo "1";

}else {

	while ($row = $objBDSQL->obtenResult()) {

		$nombreA = $row["codigo"];

		if(empty($_POST[$nombreA])){
			$Minsert = "UPDATE ajusteempleado SET PDOM = 0, DLaborados = 0, PA = 0, PP = 0, centro = '".$centro."', IDEmpresa = ".$IDEmpresa." WHERE IDEmpleado = '".$nombreA."' AND centro = '".$centro."' AND IDEmpresa = ".$IDEmpresa.";";
			$objBDSQL2->consultaBD2($Minsert);
		}else {
			$array = array($_POST[$nombreA]);

			if(empty($array[0][0])){
				$A1 = 0;
			}else {
				$A1 = $array[0][0];
			}

			if(empty($array[0][1])){
				$A2 = 0;
			}else {
				$A2 = $array[0][1];
			}

			if(empty($array[0][2])){
				$A3 = 0;
			}else {
				$A3 = $array[0][2];
			}

			if(empty($array[0][3])){
				$A4 = 0;
			}else {
				$A4 = $array[0][3];
			}

			$Minsert = "UPDATE ajusteempleado SET PDOM = '".$A1."', DLaborados = '".$A2."', PA = '".$A3."', PP = '".$A4."', centro = '".$centro."', IDEmpresa = ".$IDEmpresa." WHERE IDEmpleado = '".$nombreA."' AND centro = '".$centro."' AND IDEmpresa = ".$IDEmpresa.";";
			$objBDSQL2->consultaBD2($Minsert);

		}
	}

	echo "1";

}
$objBDSQL->cerrarBD();
$objBDSQL2->cerrarBD();

?>
