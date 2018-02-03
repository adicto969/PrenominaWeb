
$( function() {
    $( ".controlgroup" ).controlgroup()
    $( ".controlgroup-vertical" ).controlgroup({
      "direction": "vertical"
    });
} );


$(document).ready(function(){
   //VerRol();   
  $("form").keypress(function(e){
    if(e.which == 13){
      return false;
    }
  });

  $('#pie').css("position", "inherit");    
});

// # verificar en tiempo real cada 1seg.
/*
function notiReal(){
  var noti = $.ajax({
    url : 'notificaciones.php',
    dataType : 'text',
    async : false
  }).responseText;

  document.getElementById('noti').innerHTML = noti;
}

setInterval(notiReal, 1000);

*/

function Rol(){
  var conexion, variables, Dia, Dia2, Mes, Ayo;
  Dia = $('#Dia').val();
  Dia2 = $('#Dia2').val();
  Mes = $('#Mes').val();
  Ayo = $('#Ayo').val();

  if(Dia != "" && Dia2 != "" && Mes != "" && Ayo != ""){
    variables = $("#frmRol").serialize();
    variables += "&Dia="+Dia+"&Dia2="+Dia2+"&Mes="+Mes+"&Ayo="+Ayo;
    conexion = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    conexion.onreadystatechange = function() {
      if(conexion.readyState == 4 && conexion.status == 200){
        conexion.responseText = conexion.responseText.replace("\ufeff", "").replace("\ufeff\ufeff", "").replace("\ufeff\ufeff\ufeff", "");
        if(conexion.responseText == 1){
          document.getElementById('textCargado').innerHTML = "ROL ACTUALIZADOS";
          setTimeout(function(){
            $("#modal1").modal('close');
            document.getElementById('textCargado').innerHTML = "Procesando...";

          }, 1500);
        }else {
          document.getElementById('textCargado').innerHTML = conexion.responseText;
          setTimeout(function(){
            $("#modal1").modal('close');
          }, 2000);

        }
      }else if(conexion.readyState != 4){
        document.getElementById('textCargado').innerHTML = "Procesando...";
        $("#modal1").modal('open');
      }
    }
    conexion.open('POST', 'ajax.php?modo=ActRol', true);
    conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    conexion.send(variables);
  }else {
    if(Dia == ""){
      $('#Dia').focus();
    }else if(Dia2 == ""){
      $('#Dia2').focus();
    }else if(Mes == ""){
      $('#Mes').focus();
    }else {
      $('#Ayo').focus();
    }
  }


}
