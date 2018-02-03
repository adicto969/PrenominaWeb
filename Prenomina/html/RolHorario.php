<?php
$periodo = $PC;
$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();
if($periodo <= 24){
$_fechas = periodo($periodo);
list($fecha1, $fecha2, $fecha3, $fecha4) = explode(',', $_fechas);
}
if($TN == 1 || $periodo > 24){
  $_queryFechas = "SELECT CONVERT (VARCHAR (10), inicio, 103) AS 'FECHA1', CONVERT (VARCHAR (10), cierre, 103) AS 'FECHA2' FROM Periodos WHERE tiponom = 1 AND periodo = $periodo AND ayo_operacion = $ayoA AND empresa = $IDEmpresa ;";
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
?>
<h4 style="text-align: center;"><?php echo $NomDep; ?></h4>
<h5 style="text-align: center;">ROL DE HORARIO</h5>
<div>
  <center>
    <?php
      $numMESS = substr($fecha2, 3, 2);
      if($numMESS < 9){
        $numMESS = substr($fecha2, 4, 1);
      }
      $ayoF = substr($fecha2, 6, 4);
      $mesF = substr($fecha2, 3, 2);
      $diaF = substr($fecha2, 0, 2);

    ?>
      <p>DEL <input style="width: 64px; background-color: white; text-align: center;" id="Dia" type="number" min="1" max="31" value="<?php echo substr($fecha1, 0, 2);?>" /> AL <input style="width: 64px; background-color: white; text-align: center;" id="Dia2" type="number" min="1" max="31" value="<?php echo substr($fecha2, 0, 2); ?>" /> DE
      <select style="width: auto;" id="Mes">
        <option value="<?php echo $MESES[$numMESS];?>"><?php echo $MESES[$numMESS];?></option>
        <option value="<?php $diamas = date($ayoF."/".$mesF."/".$diaF); echo $MESES[date("n", strtotime($diamas." + 1 month"))]; ?>"><?php echo $MESES[date("n", strtotime($diamas." + 1 month"))]; ?></option>
        <option value="<?php echo $MESES[date("n", strtotime($diamas." + 2 month"))]; ?>"><?php echo $MESES[date("n", strtotime($diamas." + 2 month"))]; ?></option>
        <option value="<?php echo $MESES[date("n", strtotime($diamas." + 3 month"))]; ?>"><?php echo $MESES[date("n", strtotime($diamas." + 3 month"))]; ?></option>
        <option value="<?php echo $MESES[date("n", strtotime($diamas." + 4 month"))]; ?>"><?php echo $MESES[date("n", strtotime($diamas." + 4 month"))]; ?></option>
        <option value="<?php echo $MESES[date("n", strtotime($diamas." + 5 month"))]; ?>"><?php echo $MESES[date("n", strtotime($diamas." + 5 month"))]; ?></option>
        <option value="<?php echo $MESES[date("n", strtotime($diamas." + 6 month"))]; ?>"><?php echo $MESES[date("n", strtotime($diamas." + 6 month"))]; ?></option>
        <option value="<?php echo $MESES[date("n", strtotime($diamas." + 7 month"))]; ?>"><?php echo $MESES[date("n", strtotime($diamas." + 7 month"))]; ?></option>
        <option value="<?php echo $MESES[date("n", strtotime($diamas." + 8 month"))]; ?>"><?php echo $MESES[date("n", strtotime($diamas." + 8 month"))]; ?></option>
        <option value="<?php echo $MESES[date("n", strtotime($diamas." + 9 month"))]; ?>"><?php echo $MESES[date("n", strtotime($diamas." + 9 month"))]; ?></option>
        <option value="<?php echo $MESES[date("n", strtotime($diamas." + 10 month"))]; ?>"><?php echo $MESES[date("n", strtotime($diamas." + 10 month"))]; ?></option>
        <option value="<?php echo $MESES[date("n", strtotime($diamas." + 11 month"))]; ?>"><?php echo $MESES[date("n", strtotime($diamas." + 11 month"))]; ?></option>
      </select>
      DEL
      <input style="width: 64px; background-color: white; text-align: center;" id="Ayo" type="number" value="<?php echo $ayoA;?>" />
      </p>
  </center>
</div>
<div class="modal " id="modal1" style="text-align: center; padding-top: 10px;">
  <h4 id="textCargado">Procesando...</h4>
  <div class="progress">
    <div class="indeterminate"></div>
  </div>
</div>
<div id="estado_consulta_ajax">
  <?php
  $ihidden = "";
  if($DepOsub == 1)
  {
    $querySQL = "[dbo].[roldehorarioEmp]
                '".$IDEmpresa."',
                '".$centro."',
                '1'";

  }else {
    $querySQL = "[dbo].[roldehorarioEmp]
                '".$IDEmpresa."',
                '".$centro."',
                '0'";

  }

  $numCol = $objBDSQL->obtenfilas($querySQL);
  if($numCol > 0){

    echo ' <form id="frmRol" method="POST">
        <input type="hidden" name="centro" value="'.$centro.'" >
        <table id="tRH" class="responsive-table striped highlight centered" style="border: 1px solid #000080;">
        <thead>
          <tr>
               <th style="border-right: 1px solid black; background-color: #0091ea; color: white; border-bottom: 1px solid #696666;">Codigo</th>
               <th style="border-right: 1px solid black; background-color: #0091ea; color: white; border-bottom: 1px solid #696666;">Nombre</th>
               <th id="TRolH" style="border-right: 1px solid black; background-color: #0091ea; color: white; border-bottom: 1px solid #696666;">Horario</th>
               <th style="border-right: 1px solid black; background-color: #0091ea; color: white; border-bottom: 1px solid #696666;">Descripcion</th>
               <th style="border-right: 1px solid black; background-color: #0091ea; color: white; border-bottom: 1px solid #696666;">Lunes</th>
               <th style="border-right: 1px solid black; background-color: #0091ea; color: white; border-bottom: 1px solid #696666;">Martes</th>
               <th style="border-right: 1px solid black; background-color: #0091ea; color: white; border-bottom: 1px solid #696666;">Miercoles</th>
               <th style="border-right: 1px solid black; background-color: #0091ea; color: white; border-bottom: 1px solid #696666;">Jueves</th>
               <th style="border-right: 1px solid black; background-color: #0091ea; color: white; border-bottom: 1px solid #696666;">Viernes</th>
               <th style="border-right: 1px solid black; background-color: #0091ea; color: white; border-bottom: 1px solid #696666;">Sabado</th>
               <th style="background-color: #0091ea; color: white;">Domingo</th>
           </tr>
         </thead><tbody>';

    $objBDSQL->consultaBD($querySQL);

    $lr = 0;
    while ( $row = $objBDSQL->obtenResult() )
    {
      $lr++;
      $ihidden .= '<input type="hidden" name="c'.$lr.'" value="'.$row["codigo"].'">';
      echo '<tr>
            <td style="border-left: 1px solid #696666; border-bottom: 1px solid #696666;" >'.$row["codigo"].'</td>
            <td style="border-left: 1px solid #696666; border-bottom: 1px solid #696666;" >'.utf8_encode($row["nomE"]).'</td>
            <td style="text-align: center; border-left: 1px solid #696666; border-bottom: 1px solid #696666;"><div onclick="cambiar'.$lr.'()" class="controlgroup"><input style="width: 100%; margin: 0;"  min="1" class="ui-spinner-input" value ="'.$row["Horario"].'" name="inp'.$lr.'" id="'.$lr.'" onkeyup="cambiar'.$lr.'()" onclick="cambiar'.$lr.'()"></div></td>
            <td style="border-left: 1px solid #696666; border-bottom: 1px solid #696666;"  id="des'.$lr.'">'.utf8_encode($row["Nombre"]).'</td>
            ';
            if($row["LUN"] == "DESCANSO"){
                echo '<td style="background-color: #f9ff00; border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="l'.$lr.'">'.$row["LUN"].'</td>';
            }else {
                echo '<td style="border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="l'.$lr.'">'.$row["LUN"].'</td>';
            }

            if($row["MAR"] == "DESCANSO"){
                echo '<td style="background-color: #f9ff00; border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="m'.$lr.'">'.$row["LUN"].'</td>';
            }else {
                echo '<td style="border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="m'.$lr.'">'.$row["MAR"].'</td>';
            }

            if($row["MIE"] == "DESCANSO"){
                echo '<td style="background-color: #f9ff00; border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="i'.$lr.'">'.$row["LUN"].'</td>';
            }else {
                echo '<td style="border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="i'.$lr.'">'.$row["MIE"].'</td>';
            }

            if($row["JUE"] == "DESCANSO"){
                echo '<td style="background-color: #f9ff00; border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="j'.$lr.'">'.$row["LUN"].'</td>';
            }else {
                echo '<td style="border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="j'.$lr.'">'.$row["JUE"].'</td>';
            }

            if($row["VIE"] == "DESCANSO"){
                echo '<td style="background-color: #f9ff00; border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="v'.$lr.'">'.$row["LUN"].'</td>';
            }else {
                echo '<td style="border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="v'.$lr.'">'.$row["VIE"].'</td>';
            }

            if($row["SAB"] == "DESCANSO"){
                echo '<td style="background-color: #f9ff00; border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="s'.$lr.'">'.$row["LUN"].'</td>';
            }else {
                echo '<td style="border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="s'.$lr.'">'.$row["SAB"].'</td>';
            }

            if($row["DOM"] == "DESCANSO"){
                echo '<td style="background-color: #f9ff00; border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="d'.$lr.'">'.$row["LUN"].'</td>';
            }else {
                echo '<td style="border-left: 1px solid #696666; border-bottom: 1px solid #696666;" id="d'.$lr.'">'.$row["DOM"].'</td>';
            }

            echo '</tr>';
      }
    echo '</tbody></table><input type="hidden" name="cantidad" value="'.$lr.'">'.$ihidden.'</form>';
    echo '<button class="waves-effect waves-light btn" style="margin: 10px;" onclick="Rol()">GUARDAR</button>';
    echo '<script type="text/javascript">';


    for($jh=1; $jh<=$lr; $jh++){

    echo 'function cambiar'.$jh.'() {
    var numero;
    numero = document.getElementById("'.$jh.'").value;

    numero = Number(numero);

    switch (numero) {
    ';
    for ($j=1; $j<=$iDH; $j++){


      echo'
      case '.$j.':
          document.getElementById("des'.$jh.'").innerHTML = "'.$DesHora[$j].'";
          document.getElementById("l'.$jh.'").innerHTML = "'.$Lun[$j].'";';
          if($Lun[$j] == "DESCANSO"){
              echo 'document.getElementById("l'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("l'.$jh.'").style.background = \'#ffffff\';';
          }

      echo 'document.getElementById("m'.$jh.'").innerHTML = "'.$Mar[$j].'";';

          if($Mar[$j] == "DESCANSO"){
              echo 'document.getElementById("m'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("m'.$jh.'").style.background = \'#ffffff\';';
          }

      echo 'document.getElementById("i'.$jh.'").innerHTML = "'.$Mie[$j].'";';

          if($Mie[$j] == "DESCANSO"){
              echo 'document.getElementById("i'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("i'.$jh.'").style.background = \'#ffffff\';';
          }

      echo 'document.getElementById("j'.$jh.'").innerHTML = "'.$Jue[$j].'";';

          if($Jue[$j] == "DESCANSO"){
              echo 'document.getElementById("j'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("j'.$jh.'").style.background = \'#ffffff\';';
          }
      echo 'document.getElementById("v'.$jh.'").innerHTML = "'.$Vie[$j].'";';

          if($Vie[$j] == "DESCANSO"){
              echo 'document.getElementById("v'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("v'.$jh.'").style.background = \'#ffffff\';';
          }
      echo 'document.getElementById("s'.$jh.'").innerHTML = "'.$Sab[$j].'";';

          if($Sab[$j] == "DESCANSO"){
              echo 'document.getElementById("s'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("s'.$jh.'").style.background = \'#ffffff\';';
          }

      echo 'document.getElementById("d'.$jh.'").innerHTML = "'.$Dom[$j].'";';

          if($Dom[$j] == "DESCANSO"){
              echo 'document.getElementById("d'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("d'.$jh.'").style.background = \'#ffffff\';';
          }

      echo ' break;';


      }
      echo '
      default: alert ("Horario no valido");
          document.getElementById("des'.$jh.'").innerHTML = "'.$DesHora[1].'";
          document.getElementById("l'.$jh.'").innerHTML = "'.$Lun[1].'";';

          if($Lun[1] == "DESCANSO"){
              echo 'document.getElementById("l'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("l'.$jh.'").style.background = \'#ffffff\';';
          }
          echo 'document.getElementById("m'.$jh.'").innerHTML = "'.$Mar[1].'";';

          if($Mar[1] == "DESCANSO"){
              echo 'document.getElementById("m'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("m'.$jh.'").style.background = \'#ffffff\';';
          }

      echo 'document.getElementById("i'.$jh.'").innerHTML = "'.$Mie[1].'";';

          if($Mie[1] == "DESCANSO"){
              echo 'document.getElementById("i'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("i'.$jh.'").style.background = \'#ffffff\';';
          }

      echo 'document.getElementById("j'.$jh.'").innerHTML = "'.$Jue[1].'";';

          if($Jue[1] == "DESCANSO"){
              echo 'document.getElementById("j'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("j'.$jh.'").style.background = \'#ffffff\';';
          }
      echo 'document.getElementById("v'.$jh.'").innerHTML = "'.$Vie[1].'";';

          if($Vie[1] == "DESCANSO"){
              echo 'document.getElementById("v'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("v'.$jh.'").style.background = \'#ffffff\';';
          }
      echo 'document.getElementById("s'.$jh.'").innerHTML = "'.$Sab[1].'";';

          if($Sab[1] == "DESCANSO"){
              echo 'document.getElementById("s'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("s'.$jh.'").style.background = \'#ffffff\';';
          }

      echo 'document.getElementById("d'.$jh.'").innerHTML = "'.$Dom[1].'";';

          if($Dom[1] == "DESCANSO"){
              echo 'document.getElementById("d'.$jh.'").style.background = \'#f9ff00\';';
          }else {
              echo 'document.getElementById("d'.$jh.'").style.background = \'#ffffff\';';
          }


      echo '
      document.getElementById("'.$jh.'").value = "1";
      break;

      }
      };
      ';

    }
    echo '</script>';
  }else {
      //echo 'No se encontraron resultados';
      echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No se encotro resultado !</h6></div>';
  }
  $objBDSQL->liberarC();
  $objBDSQL->cerrarBD();
  ?>
</div>
<script src="js/procesos/Rol.js"></script>
