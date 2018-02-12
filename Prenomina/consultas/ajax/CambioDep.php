<?php
$Estado = 0;
$Dep = $_POST['centro'];
$Tipo = $_POST['Tipo']; 
$idEmp = $_POST['idEmp'];
/*$file = fopen("datos/DepOsup.txt", "w");
if(fwrite($file, $Tipo)){
  $Estado++;
}
fclose($file);*/
$bd1 = new ConexionM();
$bd1->__constructM();

if($Tipo == 9){
  $InsertarCP = "UPDATE config SET IDEmpresa = '$idEmp' WHERE IDUser = '".$_SESSION['IDUser']."';";

  $_SESSION['IDEmpresa'] = $idEmp;
  if($bd1->query($InsertarCP)){
    $Estado = 2;
  }else {
    echo $bd1->errno. '  '.$bd1->error;
  }
}else {

  $InsertarCP = "UPDATE config SET centro = '$Dep', IDEmpresa = '$idEmp', DoS = '$Tipo' WHERE IDUser = '".$_SESSION['IDUser']."';";
  $_SESSION['centro'] = $Dep;
  $_SESSION['DepOsub'] = $Tipo;
  $_SESSION['IDEmpresa'] = $idEmp;
  if($bd1->query($InsertarCP)){
    $Estado = 2;
  }else {
    echo $bd1->errno. '  '.$bd1->error;
  }
}


$bd1->close();

echo $Estado;

?>
