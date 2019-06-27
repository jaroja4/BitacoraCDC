class Usuario {
    // Constructor
    constructor(id, usuario, passwd, cedula, nombre, empresa, correo, fechaCreacion) {
        this.id = id || null;
        this.usuario = usuario || "";
        this.passwd = passwd || "";
        this.cedula = cedula || "";
        this.nombre = nombre || "";
        this.empresa = empresa || "";
        this.correo = correo || null;
        this.fechaCreacion = fechaCreacion || [];
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
                    targets: 7
                },
                {
                    title: "Eliminar",
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
                    targets: 7
                },
                {
                    title: "Eliminar",
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
                    targets: 7
                },
                {
                    title: "Eliminar",
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
                    targets: 7
                },
                {
                    title: "Eliminar",
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