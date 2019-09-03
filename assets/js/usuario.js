class Usuario {
    // Constructor
    constructor(id, usuario, passwd, cedula, nombre, empresa, correo, fechaCreacion, rol) {
        this.id = id || null;
        this.usuario = usuario || "";
        this.passwd = passwd || "";
        this.cedula = cedula || "";
        this.nombre = nombre || "";
        this.empresa = empresa || "";
        this.correo = correo || null;
        this.fechaCreacion = fechaCreacion || [];
        this.rol = rol || "";
    }

    get clearModalNuevoUsuario(){
        $("#inp_nombre").val("");
        $("#inp_identificacion").val("");
        $("#inp_correo").val("");
        $("#inp_usuario").val("");
        $("#inp_empresa").val("");
        $("#inp_searchValue").val("");      
        $('#sel_tipoFiltro').val("mail").change();
        $('#inp_rol').val("Visitante").change();
    }
    get responsable_ReadAll() {
        var miAccion = 'ReadAll';
        $.ajax({
            type: "POST",
            url: "class/Responsable.php",
            data: {
                action: miAccion
            }
        })
            .done(function (e) {
                var data = JSON.parse(e);
                $("#sel_responsable").select2({ "data": data });
                $("#sel_responsable").val(usuario.id);
                $('#sel_responsable').select2().trigger('change');
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }

    get responsable_ReadbyID() {
        var miAccion = 'ReadbyID';
        $.ajax({
            type: "POST",
            url: "class/Responsable.php",
            data: {
                action: miAccion,
                id: usuario.id
            }
        })
            .done(function (e) {
                var data = JSON.parse(e);
                $("#sel_responsable").select2({ "data": data });
                $("#sel_responsable").val(usuario.id);
                $('#sel_responsable').select2().trigger('change');
                $("#sel_responsable").select2({
                    ajax: {
                        url: "class/Responsable.php",
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                action: "ReadAll",
                                search_value: params.term // search term
                            };
                        },
                        processResults: function (response) {
                            // result_Visitantes = response;
                            return {
                                results: response
                            };
                        },
                        cache: true
                    }
                });
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }

    get visitante_ReadbyID() {
        var miAccion = 'ReadbyID';

        $.ajax({
            type: "POST",
            url: "class/Visitante.php",
            data: {
                action: miAccion,
                id: usuario.id
            }
        })
            .done(function (e) {
                // componente.drawByDC(e);
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }

    get autorizador_ReadAll() {
        var miAccion = 'ReadAll_list';

        $.ajax({
            type: "POST",
            url: "class/Autorizador.php",
            data: {
                action: miAccion
            }
        })
            .done(function (e) {
                var data = JSON.parse(e);
                $('#sel_autorizador').html('');
                $.each(data,
                    function (key, val) {
                        $('#sel_autorizador').append('<option value="' + val.id + '">' + val.nombre + '</option>');
                    })

                $('#sel_autorizador').append('<option value="null" selected disabled hidden>Seleccione un Autorizador </option>').change();

            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }

    get responsables_ReadAll_list() {
        var miAccion = 'ReadAll_List';

        $.ajax({
            type: "POST",
            url: "class/Responsable.php",
            data: {
                action: miAccion
            }
        })
            .done(function (e) {
                usuario.drawResponsablesDataTable(e);
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }

    get usuarios_ReadAll_list() {
        var miAccion = 'ReadAll';
        $.ajax({
            type: "POST",
            url: "class/Usuario.php",
            data: {
                action: miAccion
            }
        })
            .done(function (e) {
                usuario.drawUsuariosDataTable(e);
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }

    get visitantes_ReadAll_list() {
        var miAccion = 'ReadAll_list';
        $.ajax({
            type: "POST",
            url: "class/Visitante.php",
            data: {
                action: miAccion
            }
        })
            .done(function (e) {
                usuario.drawVisitantesDataTable(e);
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }

    get autorizadores_ReadAll_list() {
        var miAccion = 'ReadAll_list';
        $.ajax({
            type: "POST",
            url: "class/Autorizador.php",
            data: {
                action: miAccion
            }
        })
            .done(function (e) {
                usuario.drawAutorizadoresDataTable(e);
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }
    
    get create() {
        var miAccion = 'Create';
        $.ajax({
            type: "POST",
            url: "class/Usuario.php",
            data: {
                action: miAccion,
                obj: JSON.stringify(usuario)
            }
        })
        .done(function (e) {
            var result = JSON.parse(e);
            usuario.clearModalNuevoUsuario;
            switch (result) {
                case "5050":
                    usuario.SwalAlert('error','Usuario Repetido');
                    break;
                case true:
                    usuario.SwalAlert('success','Usuario Agregado');
                    break; 
                default: 
                    usuario.SwalAlert('error','Error al Crear'); 
              }           
            $("#modal_NuevoUsuario").modal("toggle");
        })
        .fail(function (e) {
            
        });
    }

    get update() {
        var miAccion = 'Update';
        $.ajax({
            type: "POST",
            url: "class/Usuario.php",
            data: {
                action: miAccion,
                obj: JSON.stringify(usuario)
            }
        })
        .done(function (e) {
            var result = JSON.parse(e);
            usuario.clearModalNuevoUsuario;
            result?usuario.SwalAlert('success','Usuario Actualizado'):usuario.SwalAlert('error','Error al Actualizar');            
            $("#modal_NuevoUsuario").modal("toggle");
        })
        .fail(function (e) {
            
        });
    }

    get updateEstado() {
        var miAccion = 'UpdateEstado';
        $.ajax({
            type: "POST",
            url: "class/Usuario.php",
            data: {
                action: miAccion,
                obj: JSON.stringify(usuario)
            }
        })
        .done(function (e) {
            var result = JSON.parse(e);
            usuario.clearModalNuevoUsuario;
            result?usuario.SwalAlert('success','EStado del Usuario Actualizado'):usuario.SwalAlert('error','Error al Actualizar Estado del Usuario');            
            $("#modal_NuevoUsuario").modal("toggle");
        })
        .fail(function (e) {
            
        });
    }

    get Delete() {
        $.ajax({
            type: "POST",
            url: "class/Usuario.php",
            data: {
                action: 'Delete',
                id: this.id
            }
        })
            .done(function (e) {
                var result = JSON.parse(e);
                if (result.status==1) 
                    usuario.SwalAlert('error','Error al Eliminar Usuario, el registro se encuentra en uso');
                else
                    usuario.SwalAlert('success','El usuario fue Eliminado');
            })
            .fail(function (e) {
            
            })
    }
    
    SwalAlert(tipo, titulo){
        usuario.responsables_ReadAll_list;
        usuario.usuarios_ReadAll_list;
        usuario.visitantes_ReadAll_list;
        usuario.autorizadores_ReadAll_list;
        Swal.fire({
            position: 'top-end',
            type: tipo,
            title: titulo,
            showConfirmButton: false,
            timer: 1500
        })
    }

    drawUsuariosDataTable(e) {
        var dataUsuario = JSON.parse(e);
        var tb_usuarios = $('#tb_usuarios').DataTable({
            data: dataUsuario,
            destroy: true,
            autoWidth: false,
            language: {
                "infoEmpty": "Sin Formularios Creados",
                "emptyTable": "Sin Formularios Creados",
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
                    data: "nombre",
                    targets: 2
                },
                {
                    title: "Identificación",
                    data: "cedula",
                    targets: 3
                },
                {
                    title: "Usuario",
                    data: "usuario",
                    targets: 4
                },
                {
                    title: "Correo",
                    data: "correo",
                    targets: 5
                },
                {
                    title: "Empresa",
                    data: "empresa",
                    targets: 6
                },
                {
                    title: "Creado",
                    data: "fechaCreacion",
                    targets: 7,
                    mRender: function (e) {
                        var event = new Date(e);
                        var f_entrada = moment(event).format('DD-MM-YY hh:mma');
                        return f_entrada;                
                    }
                },
                {
                    title: "Roles",
                    data: "roles",
                    targets: 8
                },
                {
                    title: "Eliminar",
                    class: "except",
                    visible: true,
                    mRender: function (e) {
                        return '<button class=btnEliminarResponsable onclick="deleteResponsable(this)" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> </button>';
                    }
                }
            ]
        });

        $('#tb_responsables tbody').on('click', 'tr', function () {
            usuario.clear;
            usuario.id = $('#tb_responsables').DataTable().row(this).data().id;
            usuario.cargarResponsablebyID;
        });
    }

    drawResponsablesDataTable(e) {
        var dataResponsables = JSON.parse(e);
        var tb_responsables = $('#tb_responsables').DataTable({
            data: dataResponsables,
            destroy: true,
            autoWidth: false,
            language: {
                "infoEmpty": "Sin Formularios Creados",
                "emptyTable": "Sin Formularios Creados",
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
                    data: "nombre",
                    targets: 2
                },
                {
                    title: "Identificación",
                    data: "cedula",
                    targets: 3
                },
                {
                    title: "Usuario",
                    data: "usuario",
                    targets: 4
                },
                {
                    title: "Correo",
                    data: "correo",
                    targets: 5
                },
                {
                    title: "Empresa",
                    data: "empresa",
                    targets: 6
                },
                {
                    title: "Creado",
                    data: "fechaCreacion",
                    targets: 7,
                    mRender: function (e) {
                        var event = new Date(e);
                        var f_entrada = moment(event).format('DD-MM-YY hh:mma');
                        return f_entrada;                
                    }
                },
                {
                    title: "Eliminar",
                    class: "except",
                    targets: 8,
                    visible: true,
                    mRender: function (e) {
                        return '<button class=btnEliminarResponsable onclick="deleteResponsable(this)" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> </button>';
                    }
                }
            ]
        });

        $('#tb_responsables tbody').on('click', 'tr', function () {
            usuario.clear;
            usuario.id = $('#tb_responsables').DataTable().row(this).data().id;
            usuario.cargarResponsablebyID;
        });
    }

    drawVisitantesDataTable(e) {
        var dataVisitantes = JSON.parse(e);
        var tb_visitantes = $('#tb_visitantes').DataTable({
            data: dataVisitantes,
            destroy: true,
            autoWidth: false,
            language: {
                "infoEmpty": "Sin Formularios Creados",
                "emptyTable": "Sin Formularios Creados",
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
                    data: "nombre",
                    targets: 2
                },
                {
                    title: "Identificación",
                    data: "cedula",
                    targets: 3
                },
                {
                    title: "Usuario",
                    data: "usuario",
                    targets: 4
                },
                {
                    title: "Correo",
                    data: "correo",
                    targets: 5
                },
                {
                    title: "Empresa",
                    data: "empresa",
                    targets: 6
                },
                {
                    title: "Creado",
                    data: "fechaCreacion",
                    targets: 7,
                    mRender: function (e) {
                        var event = new Date(e);
                        var f_entrada = moment(event).format('DD-MM-YY hh:mma');
                        return f_entrada;                
                    }
                },
                {
                    title: "Eliminar",
                    class: "except",
                    targets: 8,
                    visible: true,
                    mRender: function (e) {
                        return '<button class=btnEliminarResponsable onclick="deleteResponsable(this)" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> </button>';
                    }
                }
            ]
        });

        $('#tb_responsables tbody').on('click', 'tr', function () {
            usuario.clear;
            usuario.id = $('#tb_responsables').DataTable().row(this).data().id;
            usuario.cargarResponsablebyID;
        });
    }

    drawAutorizadoresDataTable(e) {
        var dataAutorizadores = JSON.parse(e);
        var tb_autorizadores = $('#tb_autorizadores').DataTable({
            data: dataAutorizadores,
            destroy: true,
            autoWidth: false,
            language: {
                "infoEmpty": "Sin Formularios Creados",
                "emptyTable": "Sin Formularios Creados",
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
                    data: "nombre",
                    targets: 2
                },
                {
                    title: "Identificación",
                    data: "cedula",
                    targets: 3
                },
                {
                    title: "Usuario",
                    data: "usuario",
                    targets: 4
                },
                {
                    title: "Correo",
                    data: "correo",
                    targets: 5
                },
                {
                    title: "Empresa",
                    data: "empresa",
                    targets: 6
                },
                {
                    title: "Creado",
                    data: "fechaCreacion",
                    targets: 7,
                    mRender: function (e) {
                        var event = new Date(e);
                        var f_entrada = moment(event).format('DD-MM-YY hh:mma');
                        return f_entrada;                
                    }
                },
                {
                    title: "Eliminar",
                    class: "except",
                    targets: 8,
                    visible: true,
                    mRender: function (e) {
                        return '<button class=btnEliminarResponsable onclick="deleteResponsable(this)" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> </button>';
                    }
                }
            ]
        });

        $('#tb_responsables tbody').on('click', 'tr', function () {
            usuario.clear;
            usuario.id = $('#tb_responsables').DataTable().row(this).data().id;
            usuario.cargarResponsablebyID;
        });
    }
}

let usuario = new Usuario();