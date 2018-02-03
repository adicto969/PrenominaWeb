<?php

$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();
$objBDSQL2 = new ConexionSRV();
$objBDSQL2->conectarBD();
$script = "";

if($DepOsub == 1)
{
  $ComSql = "LEFT (llaves.centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
  $ComSql2 = "LEFT (centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
}else {
  $ComSql = "llaves.centro = '".$centro."'";
  $ComSql2 = "centro = '".$centro."'";
}

$query = "
    select all (empleados.codigo) AS 'codigo',
    ltrim (empleados.ap_paterno)+' '+ltrim (empleados.ap_materno)+' '+ltrim (empleados.nombre) AS 'nombre',
	  llaves.ocupacion AS 'ocupacion',
	  tabulador.actividad AS 'actividad',
	  llaves.horario AS 'horario',
    MAx (convert(varchar(10),empleados.fchantigua,103)) AS 'fechaAnti',
    max(convert(varchar(10),contratos.fchAlta ,103)) AS 'fechaAlta',
    max(convert(varchar(10),contratos.fchterm ,103)) AS 'fechaTer',
    SUM(contratos.dias) AS 'dias'

    from empleados

    LEFT  join contratos  on contratos.empresa = empleados.empresa and contratos.codigo = empleados.codigo
    INNER JOIN llaves on llaves.empresa = empleados.empresa and llaves.codigo = empleados.codigo
    INNER JOIN tabulador on tabulador.empresa = llaves.empresa and tabulador.ocupacion = llaves.ocupacion

    where empleados.activo = 'S' AND
	  llaves.supervisor = '".$supervisor."' AND
          ".$ComSql." and llaves.empresa = '".$IDEmpresa."'

    group by  empleados.codigo,
          empleados.ap_paterno,
          empleados.ap_materno,
          empleados.nombre,
          empleados.fchantigua,
          llaves.ocupacion,
          tabulador.actividad,
          llaves.horario
    ";

$numCol = $objBDSQL->obtenfilas($query);
if($numCol >= 1){
  echo '<form method="post" id="frmContratos">
        <input type="hidden" name="Emp9" value="'.$NombreEmpresa.'" />
        <input type="hidden" name="NomDep" value="'.$NomDep.'" />
        <input type="hidden" name="centro" value="'.$centro.'" />
        <input type="hidden" name="TNomina" value="'.$TN.'" />
        <table id="tablaContra" class="responsive-table striped highlight centered" style="border: 1px solid #000080;">
        <thead>
          <tr id="CdMas">
            <th colspan="10" style="background-color: white; border-top: hidden;"></th>
            <th colspan="2" style="text-aling: center; background-color: #0091ea;">Contrato</th>
          </tr>
          <tr style="background-color: #00b0ff; border-top: 1px solid #000">
            <th>Codigo</th>
            <th>Nombre</th>
            <th>Ocupación</th>
            <th>Actividad</th>
            <th>horario</th>
            <th>Antigüedad</th>
            <th>Inicio de Contrato</th>
            <th>Termino de Contrato</th>
            <th>Ds A</th>
            <th class="Line35">Observacion</th>
            <th class="Line45">SI</th>
            <th class="Line45">NO</th>
          </tr>
        </thead>
        <tbody>
      ';


  $objBDSQL->consultaBD($query);

  while ( $row = $objBDSQL->obtenResult() )
  {
    echo '
    <tr ondblclick="seleccion('.$row["codigo"].')" id="'.$row["codigo"].'">
    <td>'.$row["codigo"].'</td>
    <td style="text-align: left;">'.utf8_encode($row["nombre"]).'</td>
    <td>'.$row["ocupacion"].'</td>
    <td>'.$row["actividad"].'</td>
    <td>'.$row["horario"].'</td>
    <td>'.$row["fechaAnti"].'</td>
    <td>'.$row["fechaAlta"].'</td>
    <td>'.$row["fechaTer"].'</td>
    <td>'.$row["dias"].'</td>';

    $consultaI = "SELECT Observacion, Contrato FROM contrato WHERE IDEmpleado = '".$row["codigo"]."' AND IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2;

    $valorC = "";
    $valorA = "";
    $valorB = "";
    $objBDSQL2->consultaBD2($consultaI);
    $valorM = $objBDSQL2->obtenResult2();
    if(empty($valorM)){
      echo '<td><input type="text" name="'.$row["codigo"].'" id="obser" size="100%"></td>
      <td><p><input class="with-gap" type="radio" id="A'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="SI"><label for="A'.$row["codigo"].'"></label></p></td>
      <td ondblclick="quitar'.$row["codigo"].'()"><p><input class="with-gap" type="radio" id="B'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="NO"><label for="B'.$row["codigo"].'"></label></p></td>';

    }else {

      $valorC = "";
      $valorA = "";
      $valorB = "";


      if ($valorM['Observacion'] != ''){
        $valorC = $valorM['Observacion'];

        if($valorM['Contrato'] == "SI"){

          echo '<td><input type="text" name="'.$row["codigo"].'" id="obser" size="100%" value="'.$valorC.'"></td>
          <td><p><input class="with-gap" type="radio" id="A'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="SI" checked><label for="A'.$row["codigo"].'"></label></p></td>
          <td ondblclick="quitar'.$row["codigo"].'()"><p><input class="with-gap" type="radio" id="B'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="NO"><label for="B'.$row["codigo"].'"></label></p></td> ';

        }

        if($valorM['Contrato'] == "NO"){

          echo '<td><input type="text" name="'.$row["codigo"].'" id="obser" size="100%" value="'.$valorC.'"></td>
          <td><p><input class="with-gap" type="radio" id="A'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="SI"><label for="A'.$row["codigo"].'"></label></p></td>
          <td ondblclick="quitar'.$row["codigo"].'()"><p><input class="with-gap" type="radio" id="B'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="NO" checked><label for="B'.$row["codigo"].'"></label></p></p></td>';

        }

        if($valorM['Contrato'] == "vacio"){

          echo '<td><input type="text" name="'.$row["codigo"].'" id="obser" size="100%" value="'.$valorC.'"></td>
          <td><p><input class="with-gap" type="radio" id="A'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="SI"><label for="A'.$row["codigo"].'"></label></p></td>
          <td ondblclick="quitar'.$row["codigo"].'()"><p><input class="with-gap" type="radio" id="B'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="NO"><label for="B'.$row["codigo"].'"></label></p></td>';

        }

      }else {

        $valorC = "";
        if($valorM['Contrato'] == "SI"){

          echo '<td><input type="text" name="'.$row["codigo"].'" id="obser" size="100%"></td>
          <td><p><input class="with-gap" type="radio" id="A'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="SI" checked><label for="A'.$row["codigo"].'"></label></p></p></td>
          <td ondblclick="quitar'.$row["codigo"].'()"><p><input class="with-gap" type="radio" id="B'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="NO"><label for="B'.$row["codigo"].'"></label></p></p></td>';

        }

        if ($valorM['Contrato'] == "NO"){

          echo '<td><input type="text" name="'.$row["codigo"].'" id="obser" size="100%"></td>
          <td><p><input class="with-gap" type="radio" id="A'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="SI"><label for="A'.$row["codigo"].'"></label></p></p></td>
          <td ondblclick="quitar'.$row["codigo"].'()"><p><input class="with-gap" type="radio" id="B'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="NO" checked><label for="B'.$row["codigo"].'"></label></p></p></td>';

        }
        if($valorM['Contrato'] == "vacio"){

          echo '<td><input type="text" name="'.$row["codigo"].'" id="obser" size="100%"></td>
          <td><p><input class="with-gap" type="radio" id="A'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="SI"><label for="A'.$row["codigo"].'"></label></p></p></td>
          <td ondblclick="quitar'.$row["codigo"].'()"><p><input class="with-gap" type="radio" id="B'.$row["codigo"].'" name="NC'.$row["codigo"].'" value="NO"><label for="B'.$row["codigo"].'"></label></p></p></td>';

        }
      }

    }
    echo '</tr>';
  }

  echo '
  </tbody>
  </table>
  <input type="hidden" name="Centro" value="'.$centro.'">
  </form>
  <button class="waves-effect waves-light btn" onclick="Gcontratos()" id="btnGuardar">GUARDAR</button>
  <button class="waves-effect waves-light btn" onclick="EnviarContrato()" id="btnEnviar" >GENERAR</button>';
}else {
  echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No se encotro resultado !</h6></div>';
}
?>
