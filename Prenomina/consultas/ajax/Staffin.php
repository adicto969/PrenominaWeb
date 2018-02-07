<?php
$bdSQL = new ConexionSRV();
$bdSQL->conectarBD();
$bdM = new ConexionM();
$bdM->__constructM();
$BTN = "";
$exito = "";
$valor = "";

if($DepOsub == 1)
{
  $ComSql = "LEFT (b.centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
  $ComSql2 = "LEFT (centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
}else {
  $ComSql = "b.centro = '".$centro."'";
  $ComSql2 = "centro = ".$centro."";
}

$query = "
select distinct a.ocupacion,
    a.actividad

from tabulador as a

inner join Llaves as b on b.empresa = a.empresa and b.ocupacion = a.ocupacion
inner join empleados as c on c.empresa = b.empresa and c.codigo = b.codigo

where c.activo = 'S' and
".$ComSql."
";

$query2 = "
SELECT empresa, centro, ocupacion, v5, v10, v15, v20, v25, v30, v35, v40, v45, v50, v55, v60, v65, v70, v75, v80, v85, v90, v95, v100  FROM staff_porcentaje WHERE empresa = ".$IDEmpresa." and ".$ComSql2.";
";


echo '
	<div>
		<div>
';
$bdSQL->consultaBD($query2);

$row = $bdSQL->obtenResult();

if($bdSQL->obtenfilas($query2) > 0){
	$BTN = '<button class="btn" id="btnStaf" onclick="AStaffin()" style="margin: 20px;">ACTUALIZAR</button>';
	$exito = 1;

	$queryM = $bdM->prepare("SELECT v5 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$bdSQL->obtenResult()['ocupacion']."';");
	$queryM->execute();
	$queryM->store_result();

	$num = $queryM->num_rows;

}else {
	$BTN = '<button class="btn" id="btnStaf" onclick="GStaffin()" style="margin: 20px;">GUARDAR</button>';
}

if($exito == 1 && $num == 0)
{	
	while ($row = $bdSQL->consultaBD($query2))
	{
		$Minsert = "INSERT INTO staffing VALUES (NULL, ".$row['empresa'].", '".$row["centro"]."', ".$row["ocupacion"].", ".$row["v5"].", ".$row["v10"].", ".$row["v15"].", ".$row["v20"].", ".$row["v25"].", ".$row["v30"].", ".$row["v35"].", ".$row["v40"].", ".$row["v45"].", ".$row["v50"].", ".$row["v55"].", ".$row["v60"].", ".$row["v65"].", ".$row["v70"].", ".$row["v75"].", ".$row["v80"].", ".$row["v85"].", ".$row["v90"].", ".$row["v95"].", ".$row["v100"].");";

		$bdM->query($Minsert);
	}
}
echo '
	</div>
</div>

<form id="frmStaf">
<table class="responsive-table striped highlight centered">
	<thead>
		<tr>
			<th colspan="2" id="CdMas" style="background-color: white;"></th>
			<th colspan="20" id="CdMas">Cantidad Por Porcentaje</th>
		</tr>
		<tr>
			<th>Codigo</th>
			<th>Puesto</th>
			<th class="LineSta">5 %</th>
			<th class="LineSta">10 %</th>
			<th class="LineSta">15 %</th>
			<th class="LineSta">20 %</th>
			<th class="LineSta">25 %</th>
			<th class="LineSta">30 %</th>
			<th class="LineSta">35 %</th>
			<th class="LineSta">40 %</th>
			<th class="LineSta">45 %</th>
			<th class="LineSta">50 %</th>
			<th class="LineSta">55 %</th>
			<th class="LineSta">60 %</th>
			<th class="LineSta">65 %</th>
			<th class="LineSta">70 %</th>
			<th class="LineSta">75 %</th>
			<th class="LineSta">80 %</th>
			<th class="LineSta">85 %</th>
			<th class="LineSta">90 %</th>
			<th class="LineSta">95 %</th>
			<th class="LineSta">100 %</th>
		</tr>
	</thead>
	<tbody>
';
$bdSQL->liberarC();

$bdSQL->consultaBD($query);
$lr = 0;

while ( $row=$bdSQL->obtenResult() )
{
	$lr++;

	echo '
		<tr>
			<input type="hidden" name="C'.$lr.'" value="'.$row["ocupacion"].'">
			<td>'.$row["ocupacion"].'</td>
			<td>'.utf8_decode($row["actividad"]).'</td>
	';

	$queryM2 = $bdM->query("SELECT v5 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");

	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A5'.$lr.'" value="'.$valor.'"></td>';

	$valor = "";
	$queryM2 = $bdM->query("SELECT v10 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A10'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v15 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A15'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v20 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A20'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v25 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A25'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v30 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A30'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v35 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A35'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v40 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A40'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v45 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A45'.$lr.'" value="'.$valor.'"></td>';


  $valor = "";
	$queryM2 = $bdM->query("SELECT v50 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A50'.$lr.'" value="'.$valor.'"></td>';

	$valor = "";
	$queryM2 = $bdM->query("SELECT v55 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A55'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v60 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A60'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v65 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A65'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v70 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A70'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v75 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A75'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v80 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A80'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v85 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A85'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v90 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A90'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v95 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A95'.$lr.'" value="'.$valor.'"></td>';


	$valor = "";
	$queryM2 = $bdM->query("SELECT v100 FROM staffing WHERE IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2." AND ocupacion = '".$row["ocupacion"]."';");
	if($bdM->rows($queryM2) > 0){
    	$datos = $bdM->recorrer($queryM2);
    	$valor = $datos[0];
	}
	echo '<td><input type="number" min="0" name="A100'.$lr.'" value="'.$valor.'"></td>
	</tr>
	';

}

echo '
	</tbody>
</table>
<input type="hidden" name="canMS" value="'.$lr.'" />
</form>
'.$BTN;



?>
