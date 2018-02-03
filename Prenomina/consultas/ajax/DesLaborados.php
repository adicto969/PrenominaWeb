<?php
$codigo = $_POST['codigo'];
$fecha = $_POST['fecha'];
$CenT = $_POST['centro'];
$Peri = $_POST['periodo'];
$TiN = $_POST['TN'];
$IDEmp = $_POST['IDEmp'];

$bd1 = new ConexionM();
$bd1->__constructM();

$CsDescansoL = "SELECT valor FROM deslaborado WHERE codigo = ".$codigo." AND fecha = '".$fecha."' AND periodo = ".$Peri." AND tipoN = ".$TiN." AND IDEmpresa = $IDEmpresa AND Centro = '".$CenT."' LIMIT 1;";

$sql1 = $bd1->query($CsDescansoL);
if($bd1->rows($sql1) > 0){
  $datos = $bd1->recorrer($sql1);
  $CsValor = $datos[0];

  if($CsValor == 0){
      $Update = "UPDATE deslaborado SET valor = 1 WHERE codigo = $codigo AND fecha = '".$fecha."' AND periodo = $Peri AND tipoN = $TiN AND IDEmpresa = $IDEmp AND Centro = '".$CenT."';";
  }else {
      $Update = "UPDATE deslaborado SET valor = 0 WHERE codigo = $codigo AND fecha = '".$fecha."' AND periodo = $Peri AND tipoN = $TiN AND IDEmpresa = $IDEmp AND Centro = '".$CenT."';";
  }

  if($bd1->query($Update)){

  }else {
    echo $bd1->errno. '  '.$bd1->error;
  }


}else {
  $CSInsert = "INSERT INTO deslaborado VALUES(NULL, ".$codigo.", '".$fecha."', 1, ".$Peri.", ".$TiN.", ".$IDEmp.", '".$CenT."')";
  if($bd1->query($CSInsert)){

  }else {
    echo $bd1->errno. '  '.$bd1->error;
  }

}

?>
