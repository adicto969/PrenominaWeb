<?php
$Mupdate = "";
$Select = "";
$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();
$objBDSQL2 = new ConexionSRV();
$objBDSQL2->conectarBD();
$bdM = new ConexionM();
$bdM->__constructM();

if($DepOsub == 1)
{
  $ComSql = "LEFT (llaves.centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
  $ComSql2 = "LEFT (centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
}else {
  $ComSql = "llaves.centro = '".$centro."'";
  $ComSql2 = "centro = '".$centro."'";
}

$query = "
        select all (empleados.codigo),
        ltrim (empleados.ap_paterno)+' '+ltrim (empleados.ap_materno)+' '+ltrim (empleados.nombre) ,
        llaves.ocupacion,
        tabulador.actividad,
        llaves.horario,
        MAx (convert(varchar(10),empleados.fchantigua,103)),
        max(convert(varchar(10),contratos.fchAlta ,103)) ,
        max(convert(varchar(10),contratos.fchterm ,103)) ,
        SUM(contratos.dias)

        from empleados

        LEFT  join contratos  on contratos.empresa = empleados.empresa and contratos.codigo = empleados.codigo
        INNER JOIN llaves on llaves.empresa = empleados.empresa and llaves.codigo = empleados.codigo
        INNER JOIN tabulador on tabulador.empresa = llaves.empresa and tabulador.ocupacion = llaves.ocupacion

        where empleados.activo = 'S' AND
          ".$ComSql." and llaves.empresa = '".$IDEmpresa."' AND
	llaves.supervisor = '".$supervisor."'

        group by  empleados.codigo,
              empleados.ap_paterno,
              empleados.ap_materno,
              empleados.nombre,
              empleados.fchantigua,
              llaves.ocupacion,
              tabulador.actividad,
              llaves.horario
    ";

$objBDSQL->consultaBD($query);

while ($row = $objBDSQL->obtenResult()) {

  $consultaI = "SELECT ID FROM contrato WHERE IDEmpleado = '".$row["codigo"]."' AND IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2.";";

  $objBDSQL2->consultaBD2($consultaI);
  $valorM = $objBDSQL2->obtenResult2();
  $objBDSQL2->liberarC2();
  if(empty($valorM)){
    $name = $row["codigo"];
    $option = "NC".$row["codigo"];

    if(empty($_POST[$option])){
      $Select = "INSERT INTO contrato VALUES ('".$row["codigo"]."', '".$_POST[$name]."', 'vacio', ".$IDEmpresa.", '".$centro."');";
    }else {
      $Select = "INSERT INTO contrato VALUES ('".$row["codigo"]."', '".$_POST[$name]."', '".$_POST[$option]."', ".$IDEmpresa.", '".$centro."');";
    }

    $objBDSQL2->consultaBD2($Select);

  }else {
    $consultaII = "SELECT ID FROM contrato WHERE IDEmpleado = '".$row["codigo"]."' AND IDEmpresa = '".$IDEmpresa."' AND ".$ComSql2.";";

    $objBDSQL2->consultaBD2($consultaII);
    while ($valorM = $objBDSQL2->obtenResult2()) {
      $name = $row["codigo"];
      $option = "NC".$row["codigo"];
      if($valorM['ID']){
        if(empty($_POST[$option])){
          $Mupdate = "UPDATE contrato SET IDEmpleado = '".$row["codigo"]."', Observacion = '".$_POST[$name]."', Contrato = 'vacio', IDEmpresa = ".$IDEmpresa.", centro = '".$centro."' WHERE ID = ".$valorM['ID']." AND IDEmpleado = '".$row["codigo"]."' AND IDEmpresa = '".$IDEmpresa."' AND centro = '".$centro."';";
        }else {
          $Mupdate = "UPDATE contrato SET IDEmpleado = '".$row["codigo"]."', Observacion = '".$_POST[$name]."', Contrato = '".$_POST[$option]."', IDEmpresa = ".$IDEmpresa.", centro = '".$centro."' WHERE ID = ".$valorM['ID']." AND IDEmpleado = '".$row["codigo"]."' AND IDEmpresa = '".$IDEmpresa."' AND centro = '".$centro."';";
        }

        $objBDSQL2->consultaBD2($Mupdate);
      }
    }
  }
}

$Minsert = "UPDATE config SET correo = '".$_POST["correo"]."' WHERE IDUser = '".$_SESSION['IDUser']."'";
$bdM->query($Minsert);
$objBDSQL->cerrarBD();
$objBDSQL2->cerrarBD();

echo "1";

?>
