<?php

$ancho = $_POST['valor'];

if($ancho <= 495)
{
	$estilo = "col s12";
}else {
	$estilo = "col s4 offset-s4";
}

$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();

if($DepOsub == 1)
{
	$comSql = "LEFT (centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
}else {
	$comSql = "centro = '".$centro."'";
}
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////verificar contratos vencidos a 5 dia /////////////////////////////////

$fecha56 = date('d-m-Y');
$nuevafecha56 = strtotime ( '+6 day' , strtotime ( $fecha56 ) ) ;
$nuevafecha56 = date ( 'Ymd' , $nuevafecha56 );

$nuevafecha5612 = strtotime ( '+1 day' , strtotime ( $fecha56 ) ) ;
$nuevafecha5612 = date ( 'Ymd' , $nuevafecha5612 );

$sql56 = "SELECT con.codigo, em.ap_paterno+' '+em.ap_materno+' '+em.nombre AS nombre,convert (varchar(10), fchterm, 103) AS fchterm FROM contratos AS con INNER JOIN empleados AS em ON em.codigo = con.codigo WHERE fchterm BETWEEN '".$nuevafecha5612."' AND '".$nuevafecha56."' AND vencido = 'F' AND ".$comSql.";";

$num1 = 0;
$num1 = $objBDSQL->obtenfilas($sql56);

/////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////verificar contratos vencidos a 1 dia /////////////////////////////////
$nuevafecha561 = strtotime ( '+1 day' , strtotime ( $fecha56 ) ) ;
$nuevafecha561 = date ( 'Ymd' , $nuevafecha561 );

$nuevafecha5612 = strtotime ( '-4 day' , strtotime ( $fecha56 ) ) ;
$nuevafecha5612 = date ( 'Ymd' , $nuevafecha5612 );

$sql561 = "SELECT con.codigo, em.ap_paterno+' '+em.ap_materno+' '+em.nombre AS nombre,convert (varchar(10), fchterm, 103) AS fchterm FROM contratos AS con INNER JOIN empleados AS em ON em.codigo = con.codigo WHERE fchterm = '".date("d-m-Y")."' AND vencido = 'F' AND ".$comSql.";";

$num2 = 0;
$num2 = $objBDSQL->obtenfilas($sql56);

/*$numCol = $bdSQL->recorrer($sql56);
$numCol2 = count($bdSQL->recorrer($sql561));*/

if($num1 == 0 && $num2 == 0){
	echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No se encotro resultado !</h6></div>';
}else {
	echo '<div class="row">';
	echo "<div class='col s12'><h5 class='center'>Alerta Naranja (entre 5 dias para vencer)</h5>";
	if($num1 > 0){

		echo '
		<table id="TconV" class="'.$estilo.' striped highlight">
			<thead class="orange">
			<tr>
				<th style="text-align: center;">Codigo</th>
				<th style="text-align: center;">Nombre</th>
				<th style="text-align: center;">Fecha de Terminación</th>
			</tr>
			</thead>
			<tbody>
		';

		$objBDSQL->consultaBD($sql56);
		while ($row = $objBDSQL->obtenResult())
		{
			echo '<tr>
	                <td>'.$row["codigo"].'</td>
	                <td>'.utf8_encode($row["nombre"]).'</td>
	                <td style="text-align: center;">'.utf8_encode($row["fchterm"]).'</td>
	              </tr>';
		}
		$objBDSQL->liberarC();
		echo '
			</tbody>
		</table>';
    echo "<br>";
	}else {

		echo "<p class='center'>No Se Encontraron Resultados</p>";
	}
  echo "</div>";
	echo "<div class='col s12'><h5 class='center'>Alerta Roja (a 1 dia o ya vencidos)</h5>";

	if($num2 > 0){
		echo '
		<table id="TconV2" class="'.$estilo.' striped highlight">
			<thead class="red" style="text-align: center;">
			<tr>
				<th style="text-align: center;">Codigo</th>
				<th style="text-align: center;">Nombre</th>
				<th style="text-align: center;">Fecha de Terminación</th>
			</tr>
			</thead>
			<tbody>
		';
		$objBDSQL2->consultaBD($sql561);
		while ($row = $objBDSQL->obtenResult())
		{
			echo '<tr>
	                <td>'.$row["codigo"].'</td>
	                <td>'.utf8_encode($row["nombre"]).'</td>
	                <td style="text-align: center;">'.utf8_encode($row["fchterm"]).'</td>
	              </tr>';
		}

		echo '
			</tbody>
		</table>';
	}else {

		echo "<p class='center'>No Se Encontraron Resultados</p>";
	}
	echo "</div>";
	echo '</div>';
}

 ?>
