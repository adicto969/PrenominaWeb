 <?php
 set_time_limit(6000);
//////////////// VARIABLES ///////////////
$_periodo = $_POST['periodo'];
$_tipoNom = $_POST['tipoNom'];
$diasAnteO = false;
if(isset($_POST['obtenDiasAnt'])){
	if(!empty($_POST['obtenDiasAnt']) && $_POST['obtenDiasAnt'] == 'true'){
	    $diasAnteO = true;
	}
}


$_permisoT = $_SESSION["Permiso"];
$_dias = array('', 'LUN', 'MAR', 'MIE', 'JUE', 'VIE', 'SAB', 'DOM');
$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();

$objBDSQL2 = new ConexionSRV();
$objBDSQL2->conectarBD();

$BDM = new ConexionM();
$BDM->__constructM();
$_HTML = "";
$_BTNS = '';
$_nResultados = 1;

$_cabecera = "<tr>";
$_cabeceraD = "<tr><th></th><th></th><th></th><th></th>";
$_cabeceraD = '<tr>
<th colspan="4" id="CdMas" style="background-color: white;
border-top: 1px solid transparent; border-left: 1px solid transparent;"></th>';
$_cuerpo = "";
$_Fecha0 = "";
$_FechaPar = "";
$_DiaNumero = "";
$_FechaND = "";
$_FechaCol = "";
$_date = "";
$_FechaNDQ = "";
$_queryDatos = "";
$_NumColumnas = 0;
$_NumResultado = 0;
$_tmp_E_valor = "";
$_valorC = "";
$_colorF = "";
$_arrayCabeceraD = "";
$_arrayCabeceraF = "";
$_PPPA = explode('|', "0|0");
$_PPPAempleado = explode('|', "0|0");
/////////////////////////////////////////
/////////////// PERIODOS ///////////////
if($_periodo <= 24){
	$_fechas = periodo($_periodo);
	list($_fecha1, $_fecha2, $_fecha3, $_fecha4) = explode(',', $_fechas);
}

if($_tipoNom == 1 || $_periodo > 24)
{
  $_queryFechas = "SELECT CONVERT (VARCHAR (10), inicio, 103) AS 'FECHA1', CONVERT (VARCHAR (10), cierre, 103) AS 'FECHA2' FROM Periodos WHERE tiponom = 1 AND periodo = $_periodo AND ayo_operacion = $ayoA AND empresa = $IDEmpresa ;";
  $_resultados = $objBDSQL->consultaBD($_queryFechas);
  if($_resultados === false)
  {
      die(print_r(sqlsrv_errors(), true));
      exit();
  }else {
      $_datos = $objBDSQL->obtenResult();
  }
  $_fecha1 = $_datos['FECHA1'];
  $_fecha2 = $_datos['FECHA2'];
  $objBDSQL->liberarC();
}

///////////////CONSULTA GENERAL ////////
list($_dia, $_mes, $_ayo) = explode('/', $_fecha1);
list($_diaB, $_mesB, $_ayoB) = explode('/', $_fecha2);

$_fecha1 = $_ayo.$_mes.$_dia;
$_fecha2 = $_ayoB.$_mesB.$_diaB;

///////////////INCRUSTRAR CHECADAS//////////
$Jcodigos = file_get_contents("datos/empleados.json");
$datosJSON = json_decode($Jcodigos, true);
$contarJSON = count($datosJSON["empleados"]);
$dateInc = $_ayo.'/'.$_mes.'/'.$_dia;

$varificarIns = true;
for($j = 0; $j <= $contarJSON - 1; $j++){
  if($datosJSON["empleados"][$j]["estado"] == 1){
    $randon = rand(0, 15);
    $hora = strtotime($datosJSON["empleados"][$j]["hora"]);
    $horaParse = (date('H:i:s', $hora));
    $hora2 = strtotime($datosJSON["empleados"][$j]["hora2"]);
    $hora2Parse = (date('H:i:s', $hora2));
    //RECORRER DOS FECHAS
    for($i=0; $i<=31; $i++){
      $fechaS = date("d/m/Y", strtotime($dateInc." + ".$i." day"));
      list($dia, $mes, $ayo) = explode('/', $fechaS);
      $fConsulta = $ayo.$mes.$dia;
      $fComp = explode('/', $fechaS);
      $FMK = mktime(0,0,0,$fComp[1],$fComp[0],$fComp[2]);
      $FMK2 = mktime(0,0,0,$_mesB, $_diaB, $_ayoB);
      if($FMK <= $FMK2){
        //CONFIRMAR QUE NO EXISTE ENTRADA
        $consultaChecada = "SELECT R.checada
                            FROM relch_registro AS R
                            WHERE R.fecha = '".$fConsulta."'
                            AND R.codigo = ".$datosJSON["empleados"][$j]["codigo"]."
                            AND R.empresa = ".$datosJSON["empleados"][$j]["empresa"]."
                            AND R.checada <> '00:00:00'
                            AND (R.EoS = '1' OR R.EoS = 'E' OR R.EoS IS NULL);";

        $_resultados = $objBDSQL->consultaBD($consultaChecada);
        if($_resultados === false)
        {
            die(print_r(sqlsrv_errors(), true));
            break;
        }else {
            $objBDSQL->liberarC();
            $_datos = $objBDSQL->obtenResult();
            if(empty($_datos)){
              $fechaInst = date("Ymd", $FMK);
              $fchora = strtotime('+'.rand(0, 15).' minute', strtotime($horaParse));
              $horainsert = date('H:s:i', $fchora);
              $checadaEntrada = "ObtenRelojDatosEmps ".$datosJSON["empleados"][$j]["empresa"].", ".$datosJSON["empleados"][$j]["codigo"].", '".$fechaInst."', '".$horainsert."', 'N', ' ', ' '";
              try {
                $insertChecada = $objBDSQL->consultaBD($checadaEntrada);
                if($insertChecada){
                  $file = fopen("datos/insertSS.txt", "w");
                  fwrite($file, 1);
                  fclose($file);
                }else {
                  $verificacion = false;
                }
              }catch(Exception $e){
                var_dump($e->getMessage());
              }
              //$objBDSQL->liberarC();
            }
        }

        ////////////////////CONFIRMAR QUE NO EXISTE UNA SALIDA
        $consultaChecada0 = "SELECT R.checada
                            FROM relch_registro AS R
                            WHERE R.fecha = '".$fConsulta."'
                            AND R.codigo = ".$datosJSON["empleados"][$j]["codigo"]."
                            AND R.empresa = ".$datosJSON["empleados"][$j]["empresa"]."
                            AND R.checada <> '00:00:00'
                            AND (R.EoS = '2' OR R.EoS = 'S' OR R.EoS IS NULL);";
        $_resultados = $objBDSQL->consultaBD($consultaChecada);
        if($_resultados === false)
        {
            die(print_r(sqlsrv_errors(), true));
            break;
        }else {
            $objBDSQL->liberarC();
            $_datos = $objBDSQL->obtenResult();
            if(empty($_datos)){
              $fechaInst = date("Ymd", $FMK2);
              $fchora = strtotime('+'.rand(0, 15).' minute', strtotime($hora2Parse));
              $horainsert = date('H:s:i', $fchora);
              $checadaEntrada = "ObtenRelojDatosEmps ".$datosJSON["empleados"][$j]["empresa"].", ".$datosJSON["empleados"][$j]["codigo"].", '".$fechaInst."', '".$horainsert."', 'N', ' ', ' '";
              try {
                $insertChecada = $objBDSQL->consultaBD($checadaEntrada);
                if($insertChecada){
                  $file = fopen("datos/insertSS.txt", "w");
                  fwrite($file, 1);
                  fclose($file);
                }else {
                  $verificacion = false;
                }
              }catch(Exception $e){
                var_dump($e->getMessage());
              }
              //$objBDSQL->liberarC();
            }
        }

      }
    }
  }
}

if($varificarIns == false){
  echo "<script >alert ('ERROR - Verifica los parametros de los empleados(cargarChecadas)');</script>";
}
///////////////////////////////////////////

if($DepOsub == 1)
{
  $queryGeneral = "
  [dbo].[reporte_checadas_excel_ctro]
  '".$_fecha1."',
  '".$_fecha2."',
  '".$centro."',
  '".$supervisor."',
  '".$IDEmpresa."',
  '".$_tipoNom."',
  'LEFT (Llaves.centro, 10) = LEFT (''".$centro."'', ".$MascaraEm.")', '1'";
  $ComSql = "LEFT (Centro, ".$MascaraEm.") = LEFT ('".$centro."', ".$MascaraEm.")";
}else {
  $queryGeneral = "
  [dbo].[reporte_checadas_excel_ctro]
  '".$_fecha1."',
  '".$_fecha2."',
  '".$centro."',
  '".$supervisor."',
  '".$IDEmpresa."',
  '".$_tipoNom."',
  'Llaves.centro = ''".$centro."''',
  '0'";
  $ComSql = "Centro = '".$centro."'";
}


/////////////Periodo y TipoNomina/////////
$_UPDATEPYT = "UPDATE config SET PC = $_periodo, TN = $_tipoNom WHERE IDUser = '".$_SESSION['IDUser']."';";
try{
  if($BDM->query($_UPDATEPYT)){

  }else {
    echo $BDM->errno.' '.$BDM->error;
  }
}catch (\Exception $e){
  echo $e;
}
//////////ACTUALIZAR FRENTES ////////////////

if(date($_fecha1) >= date("Ymd")){
    $SELECTJUEVES = "SELECT Codigo, JUE FROM relacionempfrente";
    $queryJueves = $objBDSQL->consultaBD($SELECTJUEVES);
    while ($row = $objBDSQL->obtenResult()) {
      $codigoJue = $row["Codigo"];
      $Juev = ltrim(rtrim($row["JUE"]));
      $UPDATEFRENTES = "UPDATE relacionempfrente SET LUN = '".$Juev."', MAR = '".$Juev."', MIE = '".$Juev."', VIE = '".$Juev."', SAB = '".$Juev."', DOM = '".$Juev."' WHERE Codigo = '".$codigoJue."';";
      $result = $objBDSQL->consultaBD($UPDATEFRENTES);
      if($result === false){
        die(print_r(sqlsrv_errors(), true));
        break;
      }
    }
    //$objBDSQL->liberarC();
}

////////////BOTONES DE GUARDAR EDITAR GENERAR ETC.//////////////
$consultaEstatus = "SELECT estado FROM estatusPeriodo WHERE periodo = $_periodo AND tipoNom = $_tipoNom";
$bloquear = '';
$objBDSQL->consultaBD($consultaEstatus);
	if(!empty(sqlsrv_errors()[0]['message'])){
		echo "Error Capturado: ".sqlsrv_errors()[0]['message'];
	}
	$estadoP = $objBDSQL->obtenResult();
	$objBDSQL->liberarC();
	if($estadoP['estado'] == 1){
  		$bloquear = 'disabled="disabled"';
		$_BTNS = '<button name="generar" id="btnGenerar" class="waves-effect waves-light btn" onclick="GTasistencia()" style="margin: 20px;">GENERAR</button> <button name="cierre" class="waves-effect waves-light btn" style="margin: 20px;" onclick="CerrarT(0)">HABILITAR</button>';
		if($_permisoT == 0){
			$_BTNS = '<button name="generar" id="btnGenerar" class="waves-effect waves-light btn" onclick="GTasistencia()" style="margin: 20px;">GENERAR</button> <button name="cierre" disabled="disabled" class="waves-effect waves-light btn" style="margin: 20px;">HABILITAR</button>';
		}
	}else if($estadoP['estado'] == 0 || empty($estadoP['estado'])){
		$_BTNS = '<button name="generar" id="btnGenerar" class="waves-effect waves-light btn" onclick="GTasistencia()" style="margin: 20px;">GENERAR</button> <button name="cierre" class="waves-effect waves-light btn" style="margin: 20px;" onclick="CerrarT(1)">CERRAR</button>';
  		$bloquear = '';
	}



/*
if($_permisoT == 0){
    $_BTNS = '<button name="generar" id="btnGenerar" class="waves-effect waves-light btn" onclick="GTasistencia()" style="margin: 20px;">GENERAR</button>';
}*/
/*
$_queryNumeroR = "SELECT valor FROM datos WHERE periodoP = $_periodo AND tipoN = $_tipoNom AND ".$ComSql." ;";
$_nResultados = $BDS->nFilas($_conn, $_queryNumeroR);
if($_nResultados > 0){
  if($_permisoT == 0){
    $BTNT = '';
  }else if($_permisoT == 1)
  {

  }
}*/

///////////////////////////////////////////////////////////////

$_HTML = '<form method="POST" id="frmTasis">
      <div id="Sugerencias" style="position: fixed; left: 0; top: -3px; padding-top: 15px; margin-bottom: 0; z-index: 998;"></div>
      <table id="t01" class="responsive-table striped highlight centered" >
      <thead id="Thfija">';



/*$serverName = "DESKTOP-2POHOQ5\\JUAN";
$connectionInfo = array( "Database"=>"VISTA", "UID"=>"sa", "PWD"=>"Enterprice9");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn === false ) {
   die( print_r( sqlsrv_errors(), true));
}
$_consultaS = sqlsrv_query($conn, $queryGeneral);*/

$objBDSQL->consultaBD($queryGeneral);
while ($row=$objBDSQL->obtenResult()) {
  $_NumResultado++;
  $_cuerpo .= "<tr>";
    foreach (array_keys($row) as $value) {
      if($_nResultados==1){
        if($value == 'codigo' ||	$value == 'Nombre' ||	$value == 'Sueldo' ||	$value == 'Tpo'){

        }else {
          $_date = str_ireplace('/', '-', $value);
          $_Fecha0 = date('Y-m-d', strtotime($_date));
          $_FechaND = $_dias[date('N', strtotime($_Fecha0))];
          $_cabeceraD .= "<th>".$_FechaND."</th>";
          $_NumColumnas++;
          $_arrayCabeceraD .= '<input type = "hidden" name="CabeceraD[]" value="'.$_FechaND.'">';
        }
        $_cabecera .= "<th>".$value."</th>";
        $_arrayCabeceraF .= '<input type = "hidden" name="Cabecera[]" value="'.$value.'">';
      }

      if($value == 'codigo' ||	$value == 'Nombre' ||	$value == 'Sueldo' ||	$value == 'Tpo'){
        $_cuerpo .= "<td>".$row[$value]."</td>";
      }else {
        $tmp_valorC = "";
        $_FechaCol = str_replace("/", "-", $value);
        $_FechaPar = date('Y-m-d', strtotime($_FechaCol));
        $_DiaNumero = date('N', strtotime($_FechaPar));
        if($_DiaNumero == 1){
            $_FechaNDQ = $_dias[6];
        }else {
            $_FechaNDQ = $_dias[$_DiaNumero-1];
        }

        $_queryDatos = "
          SELECT
            (SELECT TOP(1) valor FROM datosanti WHERE codigo = '".$row['codigo']."' AND nombre = 'fecha".str_replace("/", "-", $value)."' and periodoP = '".$_periodo."' and tipoN = '".$_tipoNom."' and IDEmpresa = '".$IDEmpresa."' and ".$ComSql.") AS 'A',
            (SELECT TOP(1) valor FROM datos WHERE codigo = '".$row['codigo']."' AND nombre = '".str_replace("/", "-", $value)."' and periodoP = '".$_periodo."' and tipoN = '".$_tipoNom."' and IDEmpresa = '".$IDEmpresa."' and ".$ComSql.") AS 'B',
            (SELECT TOP(1) valor FROM datosanti WHERE codigo = '".$row['codigo']."' AND nombre = 'fecha".str_replace("/", "-", $value)."' and periodoP = '".$_periodo."' and tipoN = '".$_tipoNom."' and IDEmpresa = '".$IDEmpresa."' and ".$ComSql." and Autorizo1 = 1) AS 'C',
            (SELECT TOP(1) ".$_FechaNDQ." FROM relacionempfrente WHERE Codigo = '".$row['codigo']."' AND ".$ComSql." AND IDEmpresa = '".$IDEmpresa."') AS 'D',
            (SELECT TOP(1) valor FROM deslaborado WHERE codigo = ".$row['codigo']." AND fecha = '".str_replace("/", "-", $value)."' AND periodo = $_periodo AND tipoN = $_tipoNom AND IDEmpresa = '".$IDEmpresa."' AND ".$ComSql.") AS 'E',
            (SELECT TOP(1) (Convert(varchar(5), PP)+'|'+Convert(varchar(5), PA)) as 'B' FROM premio WHERE codigo = '".$row['codigo']."' and Periodo = '".$_periodo."' and TN = '".$_tipoNom."' and ".$ComSql." and IDEmpresa = '".$IDEmpresa."') AS 'F',
            (SELECT TOP(1) (Convert(varchar(5), PP)+'|'+Convert(varchar(5), PA)) as 'B' FROM ajusteempleado WHERE IDEmpleado = '".$row['codigo']."' and ".$ComSql." and IDEmpresa = '".$IDEmpresa."') AS 'G'
        ";

        $consultaMedi = $objBDSQL2->consultaBD2($_queryDatos);
        if($consultaMedi === false){
          die(print_r(sqlsrv_errors(), true));
          exit();
        }else {
          $row2=$objBDSQL2->obtenResult2();
          $objBDSQL2->liberarC2();
        }

        ##################################################
        //VERIFICAR LOS DATOS EN LAS TABLAS EXTRAS
        ##################################################
        $_colorF = 0;
        $_valorC = "";
        $_PPPAempleado = explode('|', '0|0');
        $_PPPA = explode('|', '0|0');
        if(!empty($row2['A'])){
          $_colorF = 1;
        }
        if(!empty($row2['B'])){
          if($row2['B'] == '-n' || $row2['B'] == '-N'){

          }else {
              $_valorC = $row2['B'];
          }
        }
        if(!empty($row2['C'])){
          if($row2['C'] == '-n' || $row2['C'] == '-N'){

          }else {
            $_valorC = $row2['C'];
          }
        }
        if(!empty($row2['F'])){
            $_PPPA = explode('|', $row2['F']);
        }
        if(!empty($row2['G'])){
            $_PPPAempleado = explode('|', $row2['G']);
        }
        ##################################################
        //Condiciones para insertar datos encuanto a fecha
        ##################################################
	if($diasAnteO == true){
        if(date($_FechaCol) == date("d-m-Y")){
          if($_valorC != ""){
            if($row2['D'] != 'F'){
              $UPDATERELACIONDT = "UPDATE relacionempfrente SET ".$_dias[$_DiaNumero]." = '".$row2['D']."' WHERE Codigo = '".$row['codigo']."' AND ".$ComSql." AND IDEmpresa = '".$IDEmpresa."';";
              $updaRelaFrente = $objBDSQL2->consultaBD2($UPDATERELACIONDT);
              if($updaRelaFrente === false){
                die(print_r(sqlsrv_errors(), true));
                break;
              }
              //$objBDSQL2->liberarC2();
            }
          }
        }

        if(empty($_valorC)){
          $fechaHOY = date("d-m-Y");
          $_valorC = $row2['D'];
          if(!empty($_valorC) && (date($_FechaCol) <= $fechaHOY)){
            if($_valorC != 'F'){
              $INSERTRELACIONDT = "INSERT INTO datos (codigo, nombre, valor, periodoP, tipoN, IDEmpresa, Centro) VALUES ('".$row['codigo']."', '".$_FechaCol."', '".$_valorC."', '".$_periodo."', '".$_tipoNom."', '".$IDEmpresa."', '".$centro."');";
              $InsertD = $objBDSQL2->consultaBD2($INSERTRELACIONDT);
              if($InsertD === false){
                die(print_r(sqlsrv_errors(), true));
                break;
              }
              //$objBDSQL2->liberarC2();
            }
          }
        }
	}
        ##################################################
        ##################################################
        if(empty($row[$value])){
          $_cuerpo .= '<td style="height: 74px;">';
          if($_colorF == 1){
              $_cuerpo .= '<input type="text" '.$bloquear.' style="background-color: #f57c7c;" id="'.$row['codigo'].$_FechaCol.'" value="'.$_valorC.'" onkeyup="ConsultaFrente('.$row["codigo"].', \''.$_FechaCol.'\', \''.$row["codigo"].$_FechaCol.'\', )" >';
          }else {
              $_cuerpo .= '<input type="text" '.$bloquear.' id="'.$row['codigo'].$_FechaCol.'" value="'.$_valorC.'" onkeyup="ConsultaFrente('.$row['codigo'].', \''.$_FechaCol.'\', \''.$row['codigo'].$_FechaCol.'\', )" >';
          }
          $_cuerpo .= '</td>';

        }else {
          if($row2['E'] == 1){
            $_cuerpo .= '<td class="Aline" style="height: 74px;">
                          <p style="padding: 0; margin: 0; text-align: center;">
                            <input type="checkbox" '.$bloquear.' checked="checked" id="'.$row['codigo'].$_FechaCol.'DL" />
                            <label for="'.$row['codigo'].$_FechaCol.'DL" onclick="DLaborados(\''.$row['codigo'].'\', \''.$_FechaCol.'\', \''.$centro.'\', \''.$_periodo.'\', \''.$_tipoNom.'\', \''.$IDEmpresa.'\')" title="Descanso Laborado" style="padding: 6px; margin-left: 17px; position: absolute; margin-top: -25px;"></label>
                          </p>
                          <input type="text" '.$bloquear.' value="'.$_valorC.'" id="'.$row['codigo'].$_FechaCol.'" onkeyup="ConsultaFrente('.$row['codigo'].', \''.$_FechaCol.'\', \''.$row['codigo'].$_FechaCol.'\')">
                        </td>
                       ';
          }else{
            $_cuerpo .= '<td class="Aline" style="height: 74px;">
                          <p style="padding: 0; margin: 0; text-align: center;">
                            <input type="checkbox" '.$bloquear.' id="'.$row['codigo'].$_FechaCol.'DL" />
                            <label for="'.$row['codigo'].$_FechaCol.'DL" onclick="DLaborados(\''.$row['codigo'].'\', \''.$_FechaCol.'\', \''.$centro.'\', \''.$_periodo.'\', \''.$_tipoNom.'\', \''.$IDEmpresa.'\')" title="Descanso Laborado" style="padding: 6px; margin-left: 17px; position: absolute; margin-top: -25px;"></label>
                          </p>
                          <input type="text" '.$bloquear.' value="'.$_valorC.'" id="'.$row['codigo'].$_FechaCol.'" onkeyup="ConsultaFrente('.$row['codigo'].', \''.$_FechaCol.'\', \''.$row['codigo'].$_FechaCol.'\')">
                        </td>
                       ';
          }
        }

      }

    }
    if($row['Tpo'] == "E"){
      $tmp_PPC = "";
      $tmp_PAC = "";
      $tmp_APPC = "";
      $tmp_APAC = "";
      if($_PPPAempleado[0] == 0){
        if($_PPPA[0] == 0){
          $_cuerpo .= '<td><input type="number" '.$bloquear.' style="width: 70px;" min="0" name="pp'.$row['codigo'].'" placeholder="P.P" step="0.01" value=""></td>';
        }else {
          $_cuerpo .= '<td><input type="number" '.$bloquear.' style="width: 70px;" min="0" name="pp'.$row['codigo'].'" placeholder="P.P" step="0.01" value="'.$_PPPA[0].'"></td>';
        }
      }else {
        $_cuerpo .= '<td class="Aline"></td>';
      }

      if($_PPPAempleado[1] == 0){
        if($_PPPA[1] == 0){
          $_cuerpo .= '<td><input type="number" '.$bloquear.' style="width: 70px; min="0" name="pa'.$row['codigo'].'" placeholder="P.A" step="0.01" value=""></td>';
        }else {
          $_cuerpo .= '<td><input type="number" '.$bloquear.' style="width: 70px; min="0" name="pa'.$row['codigo'].'" placeholder="P.A" step="0.01" value="'.$_PPPA[1].'"></td>';
        }
      }else {
        $_cuerpo .= '<td class="Aline"></td>';
      }
    }else {
      $_cuerpo .= '<td class="Aline"></td>';
      $_cuerpo .= '<td class="Aline"></td>';
    }

    $_nResultados++;
    $_cuerpo .= '<td></td></tr>';
}

$_cabecera .= "<th>P.P</th><th>P.A</th><th>Firma</th></tr>";
$_cabeceraD .= "</tr>";

if(strlen($_cabeceraD) == 148){
  echo '<div style="width: 100%;" class="deep-orange accent-4"><h4 class="center-align" style="padding: 10px; color: white;">No se encontraron resultados !</h4></div>';
}else {
  echo $_HTML.$_cabeceraD.'
  '.$_cabecera.'
  </thead>
  <tbody>
  '.$_cuerpo.'
  </tbody>
  </table>';

  $Fd = substr($_fecha1, 6, 2);
  $Fm = substr($_fecha1, 4, 2);
  $Fa = substr($_fecha1, 0, 4);

  $F2d = substr($_fecha2, 6, 2);
  $F2m = substr($_fecha2, 4, 2);
  $F2a = substr($_fecha2, 0, 4);

  $fecha1 = $Fd."/".$Fm."/".$Fa;
  $fecha2 = $F2d."/".$F2m."/".$F2a;
  
  echo '<input type="hidden" name="Nresul" value="'.$_NumResultado.'"/>';
  echo '<input type="hidden" name="f1" value="'.$fecha1.'"/>';
  echo '<input type="hidden" name="f2" value="'.$fecha2.'"/>';
  echo '<input type="hidden" name="f3" value="'.$fecha1.'"/>';
  echo '<input type="hidden" name="f4" value="'.$fecha2.'"/>';
  echo '<input type="hidden" name="Ncabecera" value="'.$_NumColumnas.'"/>';
  echo '<input type="hidden" name="periodo" value="'.$_periodo.'" id="periodo"/>';
  echo '<input type="hidden" name="multiplo" value="'.$FactorA.'"/>';
  echo '<input type="hidden" name="pp" value="'.$_PPPA[0].'" id="hPP"/>';
  echo '<input type="hidden" name="pa" value="'.$_PPPA[1].'" id="hPA"/>';
  echo '<input type="hidden" name="tipoNom" value="'.$_tipoNom.'"/>';
  echo $_arrayCabeceraD;
  echo $_arrayCabeceraF;
  echo '</form>';
  echo $_BTNS;
  echo '<a class="modal-trigger waves-effect waves-light btn"  onclick="modal()" style="margin: 20px;">Conceptos Extras</a>';
}

try{
  $objBDSQL->liberarC();
  $objBDSQL2->cerrarBD();
  $objBDSQL->cerrarBD();
}catch(Exception $e){
  echo "Error con la BD: ".$e;
}


?>
