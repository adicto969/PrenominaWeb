<?php
$BDS = constructS();
$codigo = $_POST['codigo'];


$consulta = "SELECT TOP(5) L.empresa AS empresa,
               L.codigo AS codigo,
               E.nombre + ' ' + E.ap_paterno + ' ' + E.ap_materno AS nombre,
               D.entra1 AS entra1,
               D.sale1 AS sale1
            FROM Llaves AS L
            INNER JOIN detalle_horarios AS D ON D.horario = L.horario AND D.empresa = L.empresa
            INNER JOIN empleados AS E ON L.codigo = E.codigo
            WHERE L.codigo LIKE '".$codigo."%'
            AND (D.dia_Semana = 1 AND D.entra1 <> '')
            AND E.activo = 'S'
";

$rs = odbc_exec($BDS, $consulta);

while (odbc_fetch_row($rs)) {
  echo '
  <div class="chip" style="cursor: pointer;" onclick="agregartap('.odbc_result($rs,"codigo").', '.odbc_result($rs,"empresa").', \''.odbc_result($rs,"entra1").'\', \''.odbc_result($rs,"sale1").'\')">
      '.odbc_result($rs,"nombre").'
      <i class="close material-icons">close</i>
  </div>
  ';
}

?>
