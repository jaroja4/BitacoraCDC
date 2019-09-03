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

    get tb_readAll() {
        var miAccion = "ReadAll";
        $.ajax({
            type: "POST",
            url: "class/Roles.php",
            data: {
                action: miAccion
            }
        })
            .done(function (e) {
                rol.draw_tblRoles(e);
                
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

    draw_tblRoles(e){
        let dataRol = JSON.parse(e);
        $('#tb_roles').DataTable({
            data: dataRol,
            destroy: true,
            autoWidth: false,
            language: {
                "infoEmpty": "Sin Roles Creados",
                "emptyTable": "Sin Roles Creados",
                "search": "Buscar",
                "zeroRecords": "No hay resultados",
                "lengthMenu": "Mostar _MENU_ registros",
                "paginate": {
                    "first": "Primera",
                    "last": "Ultima",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "order": [
                [1, "asc"]
            ],
            columns: [
                {
                    title: "id",
                    data: "id",
                    targets: 0,
                    visible: false
                },
                {
                    title: "Nombre",
                    data: "text",
                    width: "90%",
                    targets: 1
                },
                {
                    title: "Eliminar",
                    targets: 8,
                    visible: true,
                    mRender: function (e) {
                        return '<button class=btnEliminarRol onclick="deleteRol(this)" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> </button>';
                    }
                }
            ]
        });

        $('#tb_roles tbody').on('click', 'tr', function () {
            dataCenter.clear;
            dataCenter.id = $('#tb_CDC').DataTable().row(this).data().id;
            // dataCenter.cargarResponsablebyID;
        });
    }
}

let rol = new Rol();  