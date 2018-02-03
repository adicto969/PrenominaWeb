$(document).ready(function() {
  verContratos();
});

function verContratos(){
  var Dimensiones = AHD();
  var conexion, resultado, variable, ancho;
  ancho = Dimensiones[0];
  variable = "valor="+ancho;
  conexion = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
  conexion.onreadystatechange = function() {
    if(conexion.readyState == 4 && conexion.status == 200){
      document.getElementById('estado_consulta_ajax').innerHTML = conexion.responseText;
      var Dimensiones = AHD();      
      if(Dimensiones[3] > Dimensiones[1]){
        $('#pie').css("position", "inherit");
      }else {
        $('#pie').css("position", "absolute");
      }

    }else if(conexion.readyState != 4){
      resultado = '<div class="progress">';
      resultado += '<div class="indeterminate"></div>';
      resultado += '</div>';
      document.getElementById('estado_consulta_ajax').innerHTML = resultado;
    }
  }
  conexion.open('POST', 'ajax.php?modo=contratosVence', true);
  conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  conexion.send(variable);
}
