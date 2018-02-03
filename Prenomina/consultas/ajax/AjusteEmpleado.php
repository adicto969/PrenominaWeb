<?php
$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();
$objBDSQL2 = new ConexionSRV();
$objBDSQL2->conectarBD();
$bdM = new ConexionM();
$bdM->__constructM();
$BTN = '<button class="btn" id="btnAjusEmp" onclick="GajusteEmple()" style="margin: 20px;">GUARDAR</button>';

if($DepOsub == 1)
{
	$ComSql = "LEFT (Llaves.centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
	$ComSql2 = "LEFT (centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
}else {
	$ComSql = "Llaves.centro = '".$centro."'";
	$ComSql2 = "centro = '".$centro."'";
}

$queryS = "
select empleados.codigo, empleados.ap_paterno+' '+empleados.ap_materno+' '+empleados.nombre as Nombre, tabulador.actividad

from empleados

INNER JOIN Llaves on  Llaves.codigo = empleados.codigo
INNER JOIN tabulador on  tabulador.ocupacion = Llaves.ocupacion and  tabulador.empresa = Llaves.empresa

where  ".$ComSql." AND Llaves.empresa = '".$IDEmpresa."' and empleados.activo = 'S'

order by ap_paterno, ap_materno, nombre
";

$numCol = $objBDSQL->obtenfilas($queryS);
if($numCol > 0){


	$queryM = "SELECT * FROM ajusteempleado WHERE ".$ComSql2.";";

	$numr = $objBDSQL->obtenfilas($queryM);

	if($numr >= 1){
		$BTN = '<button class="btn" id="btnAjusEmp" onclick="MajusteEmple()" style="margin: 20px;">ACTUALIZAR</button>';
	}


	echo '
	<form method="POST" id="frmAjusEmp">
		<table class="responsive-table striped highlight centered">
			<thead style="background-color: #00b0ff;">
				<tr id="CdMas">
					<th colspan="3" style="background-color: white;"></th>
					<th colspan="4" >Omitir Siempre En </th>
				</tr>
				<tr>
					<th>Codigo</th>
					<th>Nombre</th>
					<th>Actividad</th>
					<th class="alTH">PDOM</th>
					<th class="alTH">DLaborados</th>
					<th class="alTH">P.A</th>
					<th class="alTH">P.P</th
				</tr>
			</thead>
			<tbody>';
	$objBDSQL->consultaBD($queryS);
	while ($row = $objBDSQL->obtenResult()){
		echo '
			<tr>
				<td>'.$row["codigo"].'</td>
				<td>'.utf8_decode($row["Nombre"]).'</td>
				<td>'.$row["actividad"].'</td>';

		$consultM = "SELECT PDOM, DLaborados, PA, PP FROM ajusteempleado WHERE IDEmpleado = '".$row["codigo"]."';";

		$c1 = "";
		$c2 = "";
		$c3 = "";
		$c4 = "";
		$objBDSQL2->consultaBD2($consultM);
		while ($valorM = $objBDSQL2->obtenResult2()) {
			if($valorM['PDOM'] == 1){
				$c1 = 'checked="checked"';
			} else {
				$c1 = "";
			}

			if($valorM['DLaborados'] == 1){
				$c2 = 'checked="checked"';
			}else {
				$c2 = "";
			}

			if($valorM['PA'] == 1){
				$c3 = 'checked="checked"';
			}else {
				$c3 = "";
			}

			if($valorM['PP'] == 1){
				$c4 = 'checked="checked"';
			}else {
				$c4 = "";
			}


		}

		echo '
				<td><p style="text-align: center;"><input type="checkbox" name="'.$row["codigo"].'[0]" value="1" '.$c1.' id="Q1'.$row["codigo"].'"><label for="Q1'.$row["codigo"].'"></label></p></td>
				<td><p style="text-align: center;"><input type="checkbox" name="'.$row["codigo"].'[1]" value="1" '.$c2.' id="Q2'.$row["codigo"].'"><label for="Q2'.$row["codigo"].'"></label></p></td>
				<td><p style="text-align: center;"><input type="checkbox" name="'.$row["codigo"].'[2]" value="1" '.$c3.' id="Q3'.$row["codigo"].'"><label for="Q3'.$row["codigo"].'"></label></p></td>
				<td><p style="text-align: center;"><input type="checkbox" name="'.$row["codigo"].'[3]" value="1" '.$c4.' id="Q4'.$row["codigo"].'"><label for="Q4'.$row["codigo"].'"></label></p></td>
			</tr>
		';
	}


	echo'
			</tbody>
		</table>
	</form>
	'.$BTN;

}else {
	//echo "NO SE ENCONTRATON RESULTADOS";
	echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No se encotro resultado !</h6></div>';
}
$objBDSQL->liberarC();
$objBDSQL2->liberarC2();
$objBDSQL->cerrarBD();
$objBDSQL2->cerrarBD();

?>
