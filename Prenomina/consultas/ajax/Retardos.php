<?php

function contarValores($array)
{
    $repetidos = array();
    $faltasChecadas = array();
    $x = 0;
    foreach($array as $value)
    {
        if(isset($repetidos[$value]))
        {
            // si ya existe, le añadimos uno
            $repetidos[$value]+=1;
            if($repetidos[$value] == 3 || $repetidos[$value] == 6 || $repetidos[$value] == 9 || $repetidos[$value] == 12 || $repetidos[$value] == 15){
                $faltasChecadas[$x]=$value;
                $x++;
            }
        }else {
            // si no existe lo añadimos al array
            $repetidos[$value]=1;
        }
    }
    return $faltasChecadas;
}

$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();

$objBDSQL2 = new ConexionSRV();
$objBDSQL2->conectarBD();

$bd1 = new ConexionM();
$bd1->__constructM();


$Periodo = $_POST['Pr'];
$Tn = $_POST['Tn'];
$lista = array();
$cabecera = "";

if($Periodo <= 24){
$_fechas = periodo($Periodo);
list($fecha1, $fecha2, $fecha3, $fecha4) = explode(',', $_fechas);
}


if($Periodo > 24 || $Tn == 1){
  $_queryFechas = "SELECT CONVERT (VARCHAR (10), inicio, 103) AS 'FECHA1', CONVERT (VARCHAR (10), cierre, 103) AS 'FECHA2' FROM Periodos WHERE tiponom = 1 AND periodo = $Periodo AND ayo_operacion = $ayoA AND empresa = $IDEmpresa ;";
  $_resultados = $objBDSQL->consultaBD($_queryFechas);
  if($_resultados === false)
  {
      die(print_r(sqlsrv_errors(), true));
      exit();
  }else {
      $_datos = $objBDSQL->obtenResult();
  }
  $fecha1 = $_datos['FECHA1'];
  $fecha2 = $_datos['FECHA2'];
  $fecha3 = $_datos['FECHA1'];
  $fecha4 = $_datos['FECHA2'];
  $objBDSQL->liberarC();
}

list($dia, $mes, $ayo) = explode('/', $fecha3);
list($diaB, $mesB, $ayoB) = explode('/', $fecha4);

$fecha3 = $ayo."/".$mes."/".$dia;
$fechaF3 = $ayo."/".$mes."/".$dia;
$fechaF4 = $ayoB."/".$mesB."/".$diaB;


list($dia, $mes, $ayo) = explode('/', $fecha1);
list($diaB, $mesB, $ayoB) = explode('/', $fecha2);
$fecha1 = $ayo.$mes.$dia;
$fecha2 = $ayoB.$mesB.$diaB;


if($DepOsub == 1)
{
  $query = "[dbo].[proc_retardos]
          '".$fecha1."',
          '".$fecha2."',
          '".$centro."',
          '0',
          '".$IDEmpresa."',
          '".$Tn."',
          'E',
          'min',
          '1'";
  $ComSql = "LEFT (Dep, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
}else {
  $query = "[dbo].[proc_retardos]
          '".$fecha1."',
          '".$fecha2."',
          '".$centro."',
          '0',
          '".$IDEmpresa."',
          '".$Tn."',
          'E',
          'min',
          '0'";
  $ComSql = "Dep = '".$centro."'";
}

$num = $objBDSQL->obtenfilas($query);
$numCH = 0;
if($num > 0){

	echo '
		<form id="frmRetardos">
			<table class="responsive-table striped highlight centered">
				<thead>
					<tr>
						<th id="CdMas" colspan="4" style="background-color: white;"></th>
		';
  $objBDSQL->consultaBD($query);
  $row = $objBDSQL->obtenResult();
  foreach (array_keys($row) as $value) {
    if($value == "codigo" || $value == "Nombre" || $value == "sueldo" || $value == "Tpo"){

    }else {
      $date = $value;
      $date = str_replace('/', '-', $date);
      $fecha0 = date('Y-m-d', strtotime($date));

      $dias = array('', 'LUN', 'MAR', 'MIE', 'JUE', 'VIE', 'SAB', 'DOM');
      $fecha = $dias[date('N', strtotime($fecha0))];

      echo '<th colspan="2" id="CdMas">'.$fecha.'</th>';
      $cabecera .= '<input type = "hidden" name="CabeceraD[]" value="'.$fecha.'">';
    }

  }
	echo '</tr><tr>';


  foreach (array_keys($row) as $value) {
    if($value == "codigo" || $value == "Nombre" || $value == "sueldo" || $value == "Tpo"){
      echo '<th>'.$value.'</th>';
    }else {
      echo '<th colspan="2" id="ThMas">'.$value.'</th>';
    }
    	$cabecera .= '<input type = "hidden" name="Cabecera[]" value="'.$value.'">';
      $numCH++;
  }

	$cabecera .= '<input type = "hidden" name="Ncabecera" value="'.$numCH.'">';
	echo '<th>Firma</th>
					</tr>
				</thead>
				<tbody>
	';

  $objBDSQL->liberarC();
  $objBDSQL->consultaBD($query);
	$lr = 0;
	while ($row = $objBDSQL->obtenResult())
	{
		$lr++;
    echo '<tr>';
    foreach (array_keys($row) as $value) {
      if($value == "codigo" || $value == "Nombre" || $value == "sueldo" || $value == "Tpo"){
        if($value == "Nombre"){
            echo '<td style="text-align: left;">'.utf8_encode($row[$value]).'</td>';
        }else {
            echo '<td>'.$row[$value].'</td>';
        }
      }else {
          if(!empty($row[$value]) && $row["Tpo"] == "E" ){
            $fechaTitulo = str_replace("/", "-", $value);
            $nombre = "fecha".$row['codigo'].$fechaTitulo;

            $hr = str_replace(".", ":", $row[$value]);
            echo '<td>'.$hr.'</td>';
            $valorC = "";

            $sql1 = "SELECT TOP(1) valor FROM retardo WHERE codigo ='".$row["codigo"]."' AND fecha = '".$fechaTitulo."' AND TN = '".$Tn."' AND periodo = '".$Periodo."' and IDEmpresa = '".$IDEmpresa."' and ".$ComSql.";";
            $objBDSQL2->consultaBD2($sql1);
            $valorS = $objBDSQL2->obtenResult2();
            if(empty($valorS['valor'])){
                $valorC = "R";
            }else {
              $valorC = $valorS['valor'];
            }


            $cabecera .= '<input type="hidden" name="'.$nombre.'[]" value="'.$row["codigo"].'"/>';
    				$cabecera .= '<input type="hidden" name="'.$nombre.'[]" value="120"/>';
    				$cabecera .= '<input type="hidden" name="'.$nombre.'[]" value="'.$row["sueldo"].'"/>';
    				$cabecera .= '<input type="hidden" name="'.$nombre.'[]" value="'.date("d/m/Y", strtotime($fechaF3." + ".$lr." day")).'"/>';
    				echo '<td style="padding: 0px;"> <input type="text" name="'.$nombre.'[]" size="7"  autofocus pattern="[r,j,R,J]{1}" title="Solo se permite ( R y J)" value="'.$valorC.'" onkeyup="javascript:this.value=this.value.toUpperCase();"> </td>';
    				$cabecera .= '<input type="hidden" name="'.$nombre.'[]" value="'.$row[$value].'"/>';

    				$ahcef = date("d/m/Y", strtotime($fecha3." + ".$lr." day"));

    				$etad = new \DateTime($fechaF4);

    				$FG = $etad->format("d/m/Y");

    				$lista[$nombre] = $row["codigo"];
    				$lista[$nombre] .= '-';
    				$lista[$nombre] .= $row["sueldo"];
    				$lista[$nombre] .= '-';
    				$lista[$nombre] .= $FG;
          }else {
            echo '<td style="height: 32px;">'.$row[$value].'</td>';
    				echo '<td style="height: 37px;"></td>';
          }
      }
    }
		echo '<td></td>';
		echo '</tr>';
	}

  $fD = substr($fecha1, 6, 2);
  $fM = substr($fecha1, 4, 2);
  $fA = substr($fecha1, 0, 4);

  $f2D = substr($fecha2, 6, 2);
  $f2M = substr($fecha2, 4, 2);
  $f2A = substr($fecha2, 0, 4);

  $f3D = substr($fecha3, 8, 2);
  $f3M = substr($fecha3, 5, 2);
  $f3A = substr($fecha3, 0, 4);

  $fecha1 = $fD."/".$fM."/".$fA;
  $fecha2 = $f2D."/".$f2M."/".$f2A;
  $fecha3 = $f3D."/".$f3M."/".$f3A;

	echo '
				</tbody>
			</table>
			<input type="hidden" name="Nresultado" value="'.$num.'" />
      <input type="hidden" name="F1" value="'.$fecha1.'" />
      <input type="hidden" name="F2" value="'.$fecha2.'" />
      <input type="hidden" name="F3" value="'.$fecha3.'" />
      <input type="hidden" name="F4" value="'.$fecha4.'" />
	    <input type="hidden" name="Ncol" value="'.($numCH-4).'" />
	    <input type="hidden" name="faltasR" value='.serialize(contarValores($lista)).' />
	';

	echo $cabecera;

	echo '
		</form>
        <button class="btn" style="margin: 20px;" onclick="GRetardos()">GENERAR</button>
	';

}else {
	//echo "No Se Encontraron Resultados";
  echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No se encotro resultado !</h6></div>';
}
$objBDSQL->cerrarBD();
$objBDSQL2->cerrarBD();

?>
