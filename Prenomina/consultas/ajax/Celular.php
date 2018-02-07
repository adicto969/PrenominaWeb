<?php
$bdSQL = new ConexionSRV();
$bdSQL->conectarBD();

$bdM = new ConexionM();
$bdM->__constructM();

$fcH = $_POST['fcH'];
$Hrs = $_POST['Hrs'];
$Dep = $_POST['Dep'];

list($dia, $mes, $ayo) = explode('/', $fcH);

$fcH = $ayo.$mes.$dia;

if($Dep == "TODO" || $Dep == "TODOS" || $Dep == "todo" || $Dep == "todos")
{

	$query =  "
    SELECT empleados.codigo, ap_paterno+' '+ap_materno+' '+nombre AS Nombre,
        Tabulador.Actividad, convert(VARCHAR(10), relch_registro.fecha, 103) fecha, relch_registro.checada

	FROM Empleados
	   INNER JOIN Llaves ON Llaves.Codigo = Empleados.Codigo AND Llaves.Empresa = Empleados.Empresa
	   INNER JOIN relch_registro on relch_registro.codigo = Empleados.Codigo
	   INNER JOIN Tabulador ON Tabulador.Ocupacion = Llaves.Ocupacion AND Tabulador.Empresa = Llaves.Empresa

	WHERE  Empleados.Activo = 'S' and
	  relch_registro.fecha = '".$fcH."' and
      relch_registro.horario = '".$Hrs."' and
	  relch_registro.checada <> '00:00:00' and
	  relch_registro.empresa = '".$IDEmpresa."'

    ORDER BY Tabulador.Actividad ASC
    ";

}else {

	$query =  "
    SELECT empleados.codigo, ap_paterno+' '+ap_materno+' '+nombre AS Nombre,
        Tabulador.Actividad, convert(VARCHAR(10), relch_registro.fecha, 103) fecha, relch_registro.checada

	FROM Empleados
	   INNER JOIN Llaves ON Llaves.Codigo = Empleados.Codigo AND Llaves.Empresa = Empleados.Empresa
	   INNER JOIN relch_registro on relch_registro.codigo = Empleados.Codigo
	   INNER JOIN Tabulador ON Tabulador.Ocupacion = Llaves.Ocupacion AND Tabulador.Empresa = Llaves.Empresa

	WHERE  Empleados.Activo = 'S' and
	  relch_registro.fecha = '".$fcH."' and
      relch_registro.horario = '".$Hrs."' and
	  relch_registro.checada <> '00:00:00' and
	  relch_registro.empresa = '".$IDEmpresa."' and
	  Llaves.centro = '".$Dep."'

    ORDER BY codigo ASC
    ";

}

$num = $bdSQL->obtenfilas($query);

if($num > 1)
{	
	$bdSQL->consultaBD($query);

	echo '
		<form>
			<table>
				<thead>
					<tr>
						<th>Codigo</th>
						<th>Nombre</th>
						<th>Actividad</th>
						<th>Fecha</th>
						<th>Checada</th>
						<th>Modelo</th>
						<th>Celular</th>
					</tr>
				</thead>
				<tbody>
	';

	$lr = 0;

	while ($row=$bdSQL->obtenResult())
	{
		$lr++;
		echo '
			<tr>
				<td>'.$row["codigo"].'</td>
				<td>'.utf8_decode($row["Nombre"]).'</td>
				<td>'.$row["Actividad"].'</td>
				<td>'.$row["fecha"].'</td>
				<td>'.$row["checada"].'</td>
		';

		$resulM = $bdM->query("SELECT Modelo FROM cel WHERE IDEmpleado = '".$row["codigo"]."' AND Empresa = '".$IDEmpresa."';");
		$valor = "";
		$check = "";

		$datos = $bdM->recorrer($resulM);

		if($datos[0]){
			$valor = $datos[0];
			$check = 'checked="checked"';
		}else{
			$valor = "";
			$check = "";
		}

		echo '
			<td>'.$valor.'</td>
			<td><p style="text-align: center;"><input type="checkbox" name="'.$row["codigo"].'"  id="'.$row["codigo"].$lr.'" value="SI" '.$check.'><label for="'.$row["codigo"].$lr.'"></label></p></td>
		</tr>
		';
	}


	echo '
				</tbody>
			</table>
		</form>
	';

}else {
	//echo "No Se Encontraron Resultados";
	echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No se encotro resultado !</h6></div>';
}



?>
