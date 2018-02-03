<?php
require_once('ClassImpFormatos.php');
$bd1 = new ConexionM();
$bd1->__constructM();
$bdS = constructS();
$Carpeta = "quincenal";

if($TN == 1){
  $Carpeta = "semanal";
}

$ID = $_POST['ID'];
$CodEmp = $_POST['codEmp'];
$fechhh = $_POST['FF'];
if(!empty($_POST['AU'])){
    $AU = $_POST['AU'];
    if($AU == 2){
      $AU = 0;
    }
    $InsertarTNT = "UPDATE datosanti SET Autorizo1 = $AU WHERE ID = $ID ;";
}

if(!empty($_POST['AU2'])){
    $AU2 = $_POST['AU2'];
    if($AU2 == 2){
      $AU2 = 0;
    }
    $InsertarTNT = "UPDATE datosanti SET Autorizo2 = $AU2 WHERE ID = $ID ;";
}


if($bd1->query($InsertarTNT)){
    $consultaMs = "SELECT valor FROM datosanti WHERE Autorizo1 = 1 AND Autorizo2 = 1 AND valor = 'PG' AND ID = $ID ;";
    if($resulll = $bd1->query($consultaMs)){
      $contar = $resulll->num_rows;
      if($contar == 1){

        $querySS = "SELECT E.nombre + ' ' + E.ap_paterno + ' ' + E.ap_materno AS Nombre, E.codigo, C.nomdepto
                    FROM empleados AS E
                    INNER JOIN Llaves AS L ON L.codigo = E.codigo
                    INNER JOIN centros AS C ON C.centro = L.centro AND C.empresa = L.empresa
                    WHERE E.codigo = '".$CodEmp."' AND E.empresa = $IDEmpresa;";

        $rs = odbc_exec($bdS, $querySS);

        //$consultaFechas = "SELECT nombre FROM datosanti WHERE Autorizo1 = 1 AND Autorizo2 = 1 AND valor = 'PG' AND codigo = $CodEmp AND ;";

        $pdfImp = new PDF('P', 'mm', 'Letter');
        $pdfImp->AliasNbPages();
        $pdfImp->SetFont('Arial', '', 8);
        $pdfImp->AddPage();
        $pdfImp->tablePermiso($NombreEmpresa, utf8_encode(odbc_result($rs,"Nombre")), odbc_result($rs,"codigo"), utf8_encode(odbc_result($rs,"nomdepto")), $fechhh);
        //$pdfImp->Output('F', Unidad.'E'.$IDEmpresa.'\\'.$Carpeta.'\pdf\PruebaPermiso.pdf');
        $pdfImp->Output('F', "Temp/tempPP.pdf");
        echo "1";
      }
    }
}else {
  echo $bd1->errno. '  '.$bd1->error;
}

?>
