class Rol {
    // Constructor
    constructor(id, nombre, idUsuario) {
        this.id = id || null;
        this.nombre = nombre || "";
        this.idUsuario = idUsuario || "";
    }
    //GetterloadIdUser
    get readAll() {
        var miAccion = "ReadAll";
        var sel_rol = $('#inp_rol');
        $.ajax({
            type: "POST",
            url: "class/Roles.php",
            data: {
                action: miAccion
            }
        })
            .done(function (e) {
                var data = JSON.parse(e);
                sel_rol.html('');
                $.each(data, function (key, val) {
                    var newOption = new Option(val.text, val.id, false, false);
                    $('#inp_rol').append(newOption).trigger('change');
                })
                
            })
            .fail(function (e) {
                $sel_rol.html('<option id="-1">Cargando...</option>');
            });
    }

    get loadbyUser() {
        var miAccion = "LoadbyUser";
        var sel_rol = $('#inp_rol');
        $.ajax({
            type: "POST",
            url: "class/Roles.php",
            data: {
                action: miAccion,
                idUsuario: this.idUsuario 
            }
        })
            .done(function (e) {
                var data = JSON.parse(e);
                var arrayRol = [];
                $.each( data, function( key, value ) {
                    arrayRol.push(value.idRol);
                })
                $("#inp_rol").val(arrayRol).trigger("change");       
            })
            .fail(function (e) {
                $sel_rol.html('<option id="-1">Cargando...</option>');
            });
    }
}

let rol = new Rol();  