
$(document).ready(function(){
  Checadas();
  $("form").keypress(function(e){
    if(e.which == 13){
      return false;
    }
  });
});

$("#frm1").on("submit", function(e){
  e.preventDefault();
  var frm = $("#frm1").serialize();
  $.ajax({
    method: "POST",
    url: "codigos.php",
    data: frm
  }).done(function(info){
    recargo();
  });

  var recargo = function() {
    $.ajax({
      url: 'codigos.json',
      type: 'POST',
      dataType: 'JSON',
      success: function (data) {        
        var infodata = "<input type='hidden' name='cantidadC' value='"+data['codigos'].length+"'/><thead><tr><th>Codigo</th><th>Descripcion</th></tr></thead><tbody>";
        for (var c = 0; c <= data['codigos'].length-1; c++){
          infodata += "<tr><td>"+data['codigos'][c].codigo;
          infodata += "</td><td>"+data['codigos'][c].descripcion+"</td></tr>";
        }
        infodata += "<tr><td><input type='text' name='codigo' size='7' pattern='[a-zA-Z]{3}|[a-zA-Z]{2}|[a-zA-Z]{1}'></td><td><input type='text' name='descripcion' size='7'></td></tr></tbody><input type='hidden' name='opcion' value='nuevo'/> ";
        $("#Tcodigos").html(infodata);
      }
    });
  };
});

$(function () {
  $.ajax({
    url: 'codigos.json',
    type: 'POST',
    dataType: 'JSON',
    success: function (data){            
      var infojson = "<input type='hidden' name='cantidadC' value='"+data['codigos'].length+"'/><thead><tr><th>Codigos</th><th>Descripcion</th></tr></thead><tbody>";
      for (var c = 0; c <= data['codigos'].length-1; c++){
        infojson += "<tr><td>"+data['codigos'][c].codigo;
        infojson += "</td><td>"+data['codigos'][c].descripcion+"</td></tr>";
      }
      infojson += "<tr><td><input type='text' name='codigo' size='7' pattern='[a-zA-Z]{3}|[a-zA-Z]{2}|[a-zA-Z]{1}'></td><td><input type='text' name='descripcion' size='7'></td></tr></tbody><input type='hidden' name='opcion' value='nuevo'>";
      $("#Tcodigos").html(infojson);
    }
  });
});

function cambiarPeriodo(){
  var conexion, variables, responder, resultado, Periodo, tn;
  Periodo = document.getElementById('periodo').value;
  tn = document.getElementById('tiponom').value;
  if(Periodo != ''){
      variables = 'periodo='+Periodo+'&TN='+tn;
      conexion = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
      conexion.onreadystatechange = function() {
        if(conexion.readyState == 4 && conexion.status == 200){
          conexion.responseText = conexion.responseText.replace(/\ufeff/g, '');
          if(conexion.responseText == 'Error'){
            document.getElementById('estado_consulta_ajax').innerHTML = '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No hay fecha de este periodo !</h6></div>';

          } else {

            var fechaJSON = JSON.parse(conexion.responseText);

            document.getElementById('fchI').value = fechaJSON.fecha1;
            document.getElementById('fchF').value = fechaJSON.fecha2;
            document.getElementById('fchP_I').value = fechaJSON.fecha3;
            document.getElementById('fchP_F').value = fechaJSON.fecha4;
            document.getElementById('btnT').disabled  = false;
          }
        }else if(conexion.readyState != 4){
          document.getElementById('btnT').disabled  = true;
        }
      }
      conexion.open('POST', 'ajax.php?modo=periodo', true);
      conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      conexion.send(variables);
    }else {
      document.getElementById('estado_consulta_ajax').innerHTML = '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">Todos los datos deben estar llenos !</h6></div>';
    }
}


////////////////////////////////////////////CONSULTA AJAX A SQL/////////////////////////////////////////////
function Checadas(){
    cambiarPeriodo();
    var conexion, variables, responder, resultado, Periodo, fecha1, fecha2, tipoNom;
    fecha1 = document.getElementById('fchI').value;
    fecha2 = document.getElementById('fchF').value;
    Periodo = document.getElementById('periodo').value;
    tipoNom = document.getElementById('tiponom').value;
    if(fecha1 != '' && fecha2 != '' && Periodo != '' && tipoNom != ''){
        variables = 'periodo='+Periodo+'&fecha1='+fecha1+'&fecha2='+fecha2+'&tipoNom='+tipoNom+'&obtenDiasAnt=false';;
        conexion = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        conexion.onreadystatechange = function() {
          if(conexion.readyState == 4 && conexion.status == 200){
            conexion.responseText = conexion.responseText.replace(/\ufeff/g, '');
            if(conexion.responseText == 1){

            } else {

              if(conexion.responseText == '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No se encotro resultado !</h6></div>'){
                document.getElementById('pie').style.position = 'absolute';
              }else {
                document.getElementById('pie').style.position = 'inherit';
              }
              document.getElementById('estado_consulta_ajax').innerHTML = conexion.responseText;
              Cfausentismo();
              var Dimensiones = AHD();

              if(Dimensiones[3] > Dimensiones[1]){
                $('#pie').css("position", "inherit");
              }else {
                $('#pie').css("position", "absolute");
              }
            }
          }else if(conexion.readyState != 4){
            resultado = '<div class="progress">';
            resultado += '<div class="indeterminate"></div>';
            resultado += '</div>';
            document.getElementById('estado_consulta_ajax').innerHTML = resultado;
          }
        }
        conexion.open('POST', 'ajax.php?modo=Tasistencia', true);
        conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        conexion.send(variables);
      }else {
        document.getElementById('estado_consulta_ajax').innerHTML = '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">Todos los datos deben estar llenos !</h6></div>';
      }
}

function scriptChecadas(e){
  if(e.keyCode == 13){
    Checadas();
  }
}

function Cfausentismo(){
  var conexion, variable, fa, Tn;
  setTimeout(function(){
    fa = $("input[name=FA]:checked").val();
    Tn = document.getElementById('tiponom').value;
    variable = "FA="+fa+"&Tn="+Tn;
    conexion = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    conexion.onreadystatechange = function() {
      if(conexion.readyState == 4 && conexion.status == 200){
        conexion.responseText = conexion.responseText.replace(/\ufeff/g, '');
        if(conexion.responseText != 1){
          console.log(conexion.responseText);
        }
      }
    }
    conexion.open('POST', 'ajax.php?modo=FA', true);
    conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    conexion.send(variable);
  }, 500);

}

function DLaborados(codigo, fecha, centro, periodo, tn, IDEmp) {

  $.ajax({
    method: "POST",
    url: "ajax.php?modo=DesLaborados",
    data: "codigo="+codigo+"&fecha="+fecha+"&centro="+centro+"&periodo="+periodo+"&TN="+tn+"&IDEmp="+IDEmp
  }).done(function(info){
    console.log(info);
  });
}

function DobleTurno(codigo, fecha, centro, periodo, tn, IDEmp) {
  $.ajax({
    method: "POST",
    url: "ajax.php?modo=DobleTurno",
    data: "codigo="+codigo+"&fecha="+fecha+"&centro="+centro+"&periodo="+periodo+"&TN="+tn+"&IDEmp="+IDEmp
  }).done(function(info){
    console.log(info);
  });
}

function GuardarTR(codigo, frente, fecha, periodo, tn, id){
  $("#"+id).val(frente);
  $("#Sugerencias").html("");
  $.ajax({
    method: "POST",
    url: "ajax.php?modo=GuardarTR",
    data: "codigo="+codigo+"&frente="+frente+"&fecha="+fecha+"&periodo="+periodo+"&tn="+tn
  }).done(function(info){
    console.log(info);
  });
}


function ConsultaFrente(codigo, fecha, input){
  var perido, tn, valor;
  valor = $("#"+input).val();
  periodo = $("#periodo").val();
  tn = $("#tiponom").val();
  if(valor == "f"){
      $("#"+input).val(valor.toUpperCase());
      GuardarTR(codigo, valor.toUpperCase(), fecha, periodo, tn, input);
  }

  $.ajax({
    method: "POST",
    url: "ajax.php?modo=ConsultaFrente",
    data: "codigo="+codigo+"&fecha="+fecha+"&periodo="+periodo+"&tn="+tn+"&valor="+valor
  }).done(function(info){
    console.log(info);
    $("#Sugerencias").html(info);
  });
}

function modal(){
  if($("#OCV").length > 0){

  }else {
    REcargoColum();
  }
  $('#modalB').modal('open');
}

function InserConExt(codigo, columna, id) {
  var valor = $("#"+id).val();
  $.ajax({
    method: "POST",
    url: "ajax.php?modo=InserConExt",
    data: "codigo="+codigo+"&columna="+columna+"&valor="+valor
  }).done(function(info){
    console.log(info);
  });
}

function verificarespacio(id){
  var Verificar = $("#"+id).val();
  Verificar = Verificar.replace(/\s/g,"");
  $("#"+id).val(Verificar);
}

function AgregarColumna(){
  var codigo = 0, nombre = "";
  codigo = $("#codigoNCC").val();
  nombre = $("#nombreNCC").val();

  $.ajax({
    method: "POST",
    url: "ajax.php?modo=AgregarColumna",
    data: "nombre="+nombre+"&codigo="+codigo
  }).done(function(info){
    console.log(info);
  });

  $("#codigoNCC").val("");
  $("#nombreNCC").val("");

  /////////////RECARGAR DATOS
  REcargoColum();
}

function REcargoColum(){
  var resultado="";
  resultado = '<div class="progress">';
  resultado += '<div class="indeterminate"></div>';
  resultado += '</div>';
  $("#porcentaje").html(resultado);
  $.ajax({
    method: "POST",
    url: "ajax.php?modo=ConseptosExt",
    data: "ukl=11"
  }).done(function(datosC){
    $("#TConsepExtras").html(datosC);
    $("#porcentaje").html("");
  });
}


function eliminarColumna(nombre){
  $.ajax({
    method: "POST",
    url: "ajax.php?modo=EliminarCExtra",
    data: "nombre="+nombre
  }).done(function(datosC){
    console.log(datosC);
  });

///////////////recargo
REcargoColum();

}

function CargDiasANT() {
   cambiarPeriodo();
    var conexion, variables, responder, resultado, Periodo, fecha1, fecha2, tipoNom;
    fecha1 = document.getElementById('fchI').value;
    fecha2 = document.getElementById('fchF').value;
    Periodo = document.getElementById('periodo').value;
    tipoNom = document.getElementById('tiponom').value;
    if(fecha1 != '' && fecha2 != '' && Periodo != '' && tipoNom != ''){
        variables = 'periodo='+Periodo+'&fecha1='+fecha1+'&fecha2='+fecha2+'&tipoNom='+tipoNom+'&obtenDiasAnt=true';
        conexion = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        conexion.onreadystatechange = function() {
          if(conexion.readyState == 4 && conexion.status == 200){
            conexion.responseText = conexion.responseText.replace(/\ufeff/g, '');
            if(conexion.responseText == 1){

            } else {

              if(conexion.responseText == '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No se encotro resultado !</h6></div>'){
                document.getElementById('pie').style.position = 'absolute';
              }else {
                document.getElementById('pie').style.position = 'inherit';
              }
              document.getElementById('estado_consulta_ajax').innerHTML = conexion.responseText;
              Cfausentismo();
              var Dimensiones = AHD();

              if(Dimensiones[3] > Dimensiones[1]){
                $('#pie').css("position", "inherit");
              }else {
                $('#pie').css("position", "absolute");
              }
            }
          }else if(conexion.readyState != 4){
            resultado = '<div class="progress">';
            resultado += '<div class="indeterminate"></div>';
            resultado += '</div>';
            document.getElementById('estado_consulta_ajax').innerHTML = resultado;
          }
        }
        conexion.open('POST', 'ajax.php?modo=Tasistencia', true);
        conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        conexion.send(variables);
      }else {
        document.getElementById('estado_consulta_ajax').innerHTML = '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">Todos los datos deben estar llenos !</h6></div>';
      }
}

function GenerarExcel(){
  var fecha1 = document.getElementById('fchI').value;
  var fecha2 = document.getElementById('fchF').value;
  $.ajax({
    method: 'POST',
    url: 'ajax.php?modo=GenerarExcel',
    data: "tipo=tasistencia&"+"fecha1="+fecha1+"&fecha2="+fecha2,
    beforeSend: function(){
      $('#textCargado').html("Procesando...");
      $('#modal1').modal('open');
    }
  }).done(function(datosC){
    datosC = datosC.replace(/\ufeff/g, '');
    if(datosC == '1'){
        $('#textCargado').html("ARCHIVO GENERADO");
    }else{
        $('#textCargado').html("ERROR AL GENERAR EL ARCHIVO");
    }
  }).fail(function(retorno){
    $('#textCargado').html(retorno);
  }).always(function(){
    setTimeout(function(){
      $('#textCargado').html("Procesando...");
      $('#modal1').modal('close');
    }, 1500);
  });
}

function GuardarRegistro(codigo, fecha){
  var Valor = $("#"+codigo+fecha).val();
  $("#"+codigo+fecha).val(Valor.toUpperCase());
  var periodo = $("#periodo").val();
  var tn = $("#tiponom").val();
  if(Valor != ""){
    $.ajax({
      method: "POST",
      url: "ajax.php?modo=GuardarRegistro",
      data: "codigo="+codigo+"&valor="+Valor+"&fecha="+fecha+"&periodo="+periodo+"&tn="+tn
    }).done(function(datos){
      try {
          var jsonDatos = JSON.parse(datos.replace(/\ufeff/g, ''));
          if(jsonDatos.error == 1){
            $("#"+codigo+fecha).css("background-color", "#ff4646");
          }else {
            $("#"+codigo+fecha).css("background-color", "rgb(78, 212, 78)");
          }
      } catch (e) {
        $("#"+codigo+fecha).css("background-color", "#ff4646");
      }
    });
  }
}
