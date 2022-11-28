var url;
var t = null;
var mouseX;
var mouseY;
var idUsuario;
var url = '';

$(document).ready(function () {

    //Validator.js
    var validator = new FormValidator({ "events": ['blur', 'input', 'change'] }, document.forms[0]);
    $('#frmLogin').submit(function (e) {
        e.preventDefault();
        var validatorResult = validator.checkAll(this);
        if (validatorResult.valid)
            Login();
        return false;
    });


    $(document).mousemove(function (e) {
        mouseX = e.pageX;
        mouseY = e.pageY;
    });

    $('#btnCambiarPasswd').click(function () {
      if ($('#newPasswd').val().length >= 8 &&
          $('#newPasswd').val() == $('#re-newPasswd').val() ){

        $('#newPasswd').val($('#newPasswd').val().trim());

        $('#btnCambiarPasswd').attr("disabled", "disabled");

        $.ajax({
            type: "POST",
            url: "class/Usuario.php",
            data: {
                action: 'UpdatePasswd',
                id: idUsuario,
                password: $('#newPasswd').val(),
                beforeSend: function () {
                    $("#error").fadeOut();
                }
            }
        })
        .done(function (e) {
            var data = JSON.parse(e);
            if (data.status == 'passwdOK') {
                if (data.url)
                    location.href = data.url;
            }
        })
        .fail(function (e) {
            showError(e);
        })
        .always(function () {
            $("#btnLogin").removeAttr("disabled");
        });
      }
      else{
        alert("Error al cambiar la contraseña");
      }
    });

    $('#btnKeyboar').click(function () {
        $('#numPad').css({ 'top': mouseY, 'left': mouseX + 50 }).fadeIn('slow');
        selector = $('#username');
    });

    // on form "reset" event
    document.forms[0].onreset = function (e) {
        validator.reset();
    }
    //


});

function Login() {
    $('#btnLogin').attr("disabled", "disabled");
    $.ajax({
        type: "POST",
        url: "class/Usuario.php",
        data: {
            action: 'Login',
            correo: $("#correo").val(),
            password: $("#password").val(),
            beforeSend: function () {
                $("#error").fadeOut();
            }
        }
    })
        .done(function (e) {
            var data = JSON.parse(e);
            if (data.status == 'login') {
                $("#frmLogin").hide();
                $("#frmChangePSWD").hide();
                $("#frmCheckToken").show();
                if (data.url)
                    url = data.url;


                idUsuario = data.id;

                $('#btnCheckToken').click(function (e) {
                  e.preventDefault()
                  $('#token').val($('#token').val().trim());
                  if ($('#token').val().length == 4){

                    $('#token').attr("disabled", "disabled");

                    $.ajax({
                        type: "POST",
                        url: "class/Usuario.php",
                        data: {
                            action: 'CheckToken',
                            id: idUsuario,
                            token: $('#token').val(),
                            beforeSend: function () {
                                $("#error").fadeOut();
                            }
                        }
                    })
                    .done(function (e) {
                        var data = JSON.parse(e);
                        if (data.status == 'TokenOK') {
                            if (url)
                                location.href = url;
                        }
                        if (data.status == false) {
                          Swal.fire({
                            position: "top-end",
                            type: 'error',
                            title: 'Token Invalido',
                            showConfirmButton: false,
                            timer: 1500,
                          });
                        }
                    })
                    .fail(function (e) {
                        showError(e);
                    })
                    .always(function () {
                        $("#btnLogin").removeAttr("disabled");
                        $('#token').removeAttr("disabled");

                    });
                  }
                  else{
                    alert("Error TOKEN");
                  }
                });
            }
            else if (data.status == 'inactivo')
                $("#error").fadeIn(500, function () {
                    $("#error").html(`
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        Usuario <strong>INACTIVO</strong>.
                    </div>
                `);
                });
            else if (data.status == "cambiarPWD"){
                idUsuario = data.id;
                $('#frmLogin').hide();
                $('#frmChangePSWD').show();
            }
            else if (data.status == 'noexiste')
                $("#error").fadeIn(500, function () {
                    $("#error").html(`
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        Usuario <strong>NO EXISTE</strong>, favor registrarse.
                    </div>
                `);
                });
            else
                $("#error").fadeIn(500, function () {
                    $("#error").html(`
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        Usuario / Contraseña <strong>Inválidos</strong>.
                    </div>
                `);
                });
        })
        .fail(function (e) {
            showError(e);
        })
        .always(function () {
            $("#btnLogin").removeAttr("disabled");
        });
};

function showError(e) {
    //$(".modal").css({ display: "none" });
    var data = JSON.parse(e.responseText);
    swal({
        type: 'error',
        title: 'Oops...',
        text: 'Algo no está bien (' + data.code + '): ' + data.msg,
        footer: '<a href>Contacte a Soporte Técnico</a>',
    })
};


