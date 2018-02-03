$(document).ready(function() {
  AjusteEmpleados();
});

function AjusteEmpleados(){
  var conexion, resultado, variable;
  variable = "valor=X"
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
  conexion.open('POST', 'ajax.php?modo=AjusteEmpleado', true);
  conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  conexion.send(variable);
}


function GajusteEmple() {
  var conexion, variables;
  variables = $('#frmAjusEmp').serialize();
  variables += "&modo=G";
  conexion = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
  conexion.onreadystatechange = function() {
    if(conexion.readyState == 4 && conexion.status == 200){
      if(conexion.responseText == 1){
        document.getElementById('textCargado').innerHTML = "DATOS GUARDADOS";
        setTimeout(function() {
          $('#modal1').modal('close');
          document.getElementById('textCargado').innerHTML = "Procesando..";
          $("#btnAjusEmp").attr("onclick","MajusteEmple()");
          $("#btnAjusEmp").html("ACTUALIZAR");
          $('#btnAjusEmp').blur();
        }, 1500);
      }else{
        document.getElementById('textCargado').innerHTML = conexion.responseText;
        setTimeout(function() {
          $('#modal1').modal('close');
          document.getElementById('textCargado').innerHTML = "Procesando..";
        }, 2500);
      }
    }else if(conexion.readyState != 4){
      $('#modal1').modal('open');
    }
  }
  conexion.open('POST', 'ajax.php?modo=MajusteEmple', true);
  conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  conexion.send(variables);
}

function MajusteEmple(){
  var conexion, variables;
  variables = $('#frmAjusEmp').serialize();
  variables += "&modo=M";
  conexion = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
  conexion.onreadystatechange = function() {
    if(conexion.readyState == 4 && conexion.status == 200){
      if(conexion.responseText == 1){
        document.getElementById('textCargado').innerHTML = "DATOS ACTUALIZADOS";
        setTimeout(function(){
          $('#modal1').modal('close');
          document.getElementById('textCargado').innerHTML = "Procesando...";
        }, 1500);
      }else {
        document.getElementById('textCargado').innerHTML = conexion.responseText;
        setTimeout(function(){
          $('#modal1').modal('close');
          document.getElementById('textCargado').innerHTML = "Procesando...";
        }, 2500);
      }
    }else if(conexion.readyState != 4){
      $('#modal1').modal('open');
    }
  }
  conexion.open('POST', 'ajax.php?modo=MajusteEmple', true);
  conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  conexion.send(variables);
}
