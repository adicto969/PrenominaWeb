<?php
//-----------------------------------------------------
//-----------------------------------------------------

/**
 * CONEXION MYSQL
 */

class ConexionM extends mysqli
{
  public $conexionInfo="";

  public $enlace="";
  public $resultado="";
  public $consulta="";


  public function __constructM()
  {
    parent::__construct(M_DB_SERVER,M_DB_USER,M_DB_PASS,M_DB_NOMBRE);
    $this->connect_errno ? die('Error en la Conexión a la base de datos') : null;
    $this->set_charset("utf8");
  }

  public function rows($query) {
    return mysqli_num_rows($query);
  }

  public function liberar($query){
    return mysqli_free_result($query);
  }

  public function recorrer($query){
    return mysqli_fetch_array($query);
  }

}

//-----------------------------------------------------
//-----------------------------------------------------

/**
 * CONEXION SQL LIBRERIA SRV
 */

class ConexionSRV
{
 public $conexionInfo="";

 public $enlace="";
 public $resultado="";
 public $resultado2="";
 public $consulta="";
 public $consulta2="";

 public function ConexionSRV()
 {   
     $this->conexionInfo = array("Database" => S_DB_NOMBRE, "UID" => S_DB_USER, "PWD" => S_DB_PASS, "CharacterSet" => "UTF-8");
 }

 public function conectarBD()
 {
   $this->enlace = sqlsrv_connect( S_DB_SERVER, $this->conexionInfo);
     if( $this->enlace === false ) {
       foreach(sqlsrv_errors() as $error){
         echo "<h1 style='text-align: center'>OCURRIO UN ERROR EN LA CONEXION CON LA BD.</h1><br/>";
         echo "SQLSTATE: ".$error['SQLSTATE']."<br/>";
         echo "CODIGO: ".$error['code']."<br/>";
         echo "MENSAJE: ".$error['message']."<br/>";
       }
        //die( print_r( sqlsrv_errors(), true));
        exit();
     }
 }

 public function consultaBD($sentenciaSQL)
 {
    $this->consulta = sqlsrv_query($this->enlace, $sentenciaSQL);

    if($this->consulta === false || empty($this->consulta))
    {
      if(($errors = sqlsrv_errors()) != null)
      {

        $file = fopen("log/log".date("d-m-Y").".txt", "a");
        fwrite($file, ":::::::::::::::::::::::CONSULTA BD:::::::::::::::::::::::".PHP_EOL);

        foreach($errors as $error){
          fwrite($file, '['.date('d/m/Y h:i:s A').']'.' SQLSTATE: '.$error['SQLSTATE'].PHP_EOL);
          fwrite($file, '['.date('d/m/Y h:i:s A').']'.' CODIGO: '.$error['code'].PHP_EOL);
          fwrite($file, '['.date('d/m/Y h:i:s A').']'.' MENSAJE: '.$error['message'].PHP_EOL);          
        }
        fwrite($file, '['.date('d/m/Y h:i:s A').']'.' CONSULTA: '.$sentenciaSQL.PHP_EOL);
        fclose($file);
        return 1;
      }
    }  
    return 0;
 }

 public function consultaBD2($sentenciaSQL){
   $this->consulta2 = sqlsrv_query($this->enlace, $sentenciaSQL);

   if($this->consulta2 === false || empty($this->consulta2))
   {
     if(($errors = sqlsrv_errors()) != null)
     {

       $file = fopen("log/log".date("d-m-Y").".txt", "a");
       fwrite($file, ":::::::::::::::::::::::CONSULTA BD:::::::::::::::::::::::".PHP_EOL);

       foreach($errors as $error){
         fwrite($file, '['.date('d/m/Y h:i:s A').']'.' SQLSTATE: '.$error['SQLSTATE'].PHP_EOL);
         fwrite($file, '['.date('d/m/Y h:i:s A').']'.' CODIGO: '.$error['code'].PHP_EOL);
         fwrite($file, '['.date('d/m/Y h:i:s A').']'.' MENSAJE: '.$error['message'].PHP_EOL);
       }
       fwrite($file, '['.date('d/m/Y h:i:s A').']'.' CONSULTA: '.$sentenciaSQL.PHP_EOL);
       fclose($file);
       return 1;
     }
   }

   return 0;
 }

 public function returnConsulta(){
   return $this->consulta;
 }

 public function returnConsulta2(){
   return $this->consulta2;
 }

 public function obtenResult(){
   $this->resultado=sqlsrv_fetch_array($this->consulta, SQLSRV_FETCH_ASSOC);
   return $this->resultado;
 }

 public function obtenResultNum(){
   $this->resultado=sqlsrv_fetch_array($this->consulta, SQLSRV_FETCH_NUMERIC);
   return $this->resultado;
 }

 public function obtenResult2(){
   $this->resultado2=sqlsrv_fetch_array($this->consulta2, SQLSRV_FETCH_ASSOC);
   return $this->resultado2;
 }

 public function obtenfilas($sentenciaSQL) {
   $this->resultado=sqlsrv_num_rows(sqlsrv_query($this->enlace, $sentenciaSQL, array(), array("Scrollable" => "buffered")));
   return $this->resultado;
 }

 public function liberarC()
 {
   sqlsrv_free_stmt($this->consulta);
 }

 public function liberarC2(){
   sqlsrv_free_stmt($this->consulta2);
 }

 public function cerrarBD()
 {
     sqlsrv_close( $this->enlace );
 }

}

?>
