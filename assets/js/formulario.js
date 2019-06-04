class Formulario {
    // Constructor
    constructor(id, idEstado, idDataCenter, idSala, idTramitante, idAutorizador, idResponsable, consecutivo, fechaSolicitud, fechaIngreso, fechaSalida, detalles, arrayVisitantes, motivoVisita) {
        this.id = id || null;
        this.idEstado = idEstado || null;
        this.idDataCenter = idDataCenter || null;
        this.idSala = idSala || null;
        this.idTramitante = idTramitante || null;
        this.idAutorizador = idAutorizador || null;
        this.idResponsable = idResponsable || null;
        this.consecutivo = consecutivo || "";
        this.fechaSolicitud = fechaSolicitud || "";
        this.fechaIngreso = fechaIngreso || "";
        this.fechaSalida = fechaSalida || "";
        this.motivoVisita = motivoVisita || "";
        this.otrosDetalles = otrosDetalles || "";
        this.arrayVisitantes = arrayVisitantes || [];

    }

    //Getter
    get validar() {
        var estadoFormulario = true;
        var inp_descripcion = $("#inp_descripcion").val().length;
        var table = $('#tb_visitante_seccionado').DataTable();
        var tb_visitante_seccionado = table.rows().count()
        var sel_responsable = $("#sel_responsable").val();
        if (inp_descripcion < 5)
            estadoFormulario = false;
        if (tb_visitante_seccionado < 1)
            estadoFormulario = false;
        if (sel_responsable < 1)
            estadoFormulario = false;

        if (estadoFormulario) {
            formulario.create;
        } else {
            alert("Faltan Datos");
        }
    }

    get create() {
        var miAccion = 'Create';

        formulario.idDataCenter = $("#sel_centro_datos").val();
        formulario.idSala = $("#sel_sala").val();;
        formulario.idResponsable = $("#sel_responsable").val();
        formulario.idAutorizador = $("#sel_autorizador").val();
        formulario.otrosDetalles = $("#otrosDetalles").val();
        formulario.motivoVisita = $("#inp_descripcion").val();
        (typeof (formulario.fechaIngreso) == "object") ? formulario.fechaIngreso = formulario.fechaIngreso.format("YYYY-MM-DD HH:mm:ss") : true;
        // formulario.fechaIngreso = formulario.fechaIngreso.format("YYYY-MM-DD HH:mm:ss");
        formulario.fechaSalida = formulario.fechaSalida.format("YYYY-MM-DD HH:mm:ss");
        formulario.arrayVisitantes = $('#tb_visitante_seccionado').DataTable().columns(0).data()[0];
        formulario.idEstado = $('#sel_estado').val();
        formulario.fechaSolicitud = moment().format("YYYY-MM-DD HH:mm:ss");

        $.ajax({
            type: "POST",
            url: "class/Formulario.php",
            data: {
                action: miAccion,
                obj: JSON.stringify(formulario)
            }
        })
            .done(function (e) {
                // formulario.drawFormulariosbyRange(e);
                alert("LISTO");
            })
            .fail(function (e) {
                // formulario.showError(e);
            });

    }

    get ReadAllbyRange() {
        var miAccion = 'ReadAllbyRange';

        $.ajax({
            type: "POST",
            url: "class/Formulario.php",
            data: {
                action: miAccion,
                obj: JSON.stringify(formulario)
            }
        })
            .done(function (e) {
                formulario.drawFormulariosbyRange(e);
            })
            .fail(function (e) {
                // formulario.showError(e);
            });
    }

    drawFormulariosbyRange(e) {
        var data_formularios = JSON.parse(e);

        tbl_formularios = $('#tbl_formularios').DataTable({
            data: data_formularios,
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
                [1, "desc"]
            ],
            columns: [
                {
                    title: "id",
                    data: "id",
                    visible: false
                },
                {
                    title: "Consecutivo",
                    data: "consecutivo",
                    width: "10%",
                },
                {
                    title: "Fecha Ingreso",
                    data: "fechaIngreso",
                    width: "10%",
                },
                {
                    title: "Fecha Solicitud",
                    data: "fechaSolicitud",
                    width: "10%",
                },
                {
                    title: "Motivo",
                    data: "motivoVisita",
                    width: "50%",
                },
                {
                    title: "Estado",
                    data: "Estado",
                    width: "10%",
                },
                {
                    title: "Otros Detalles",
                    data: "otrosDetalles",
                    width: "10%",
                }
            ]
        });

    }

    get carga_DataCenters() {
        var miAccion = 'ReadAll';
        var sel_centro_datos = $('#sel_centro_datos');
        $.ajax({
            type: "POST",
            url: "class/DataCenter.php",
            data: {
                action: miAccion,
                obj: JSON.stringify(formulario)
            }
        })
            .done(function (e) {
                var data = JSON.parse(e);
                sel_centro_datos.html('');
                $.each(data,
                    function (key, val) {
                        sel_centro_datos.append('<option value="' + val.id + '">' + val.nombre + '</option>');
                    })
                formulario.carga_SalabyDC;
            })
            .fail(function (e) {
                sel_centro_datos.html('<option id="-1">Cargando...</option>');
            });
    }

    get carga_SalabyDC() {
        var miAccion = 'ReadSalabyDC';
        var sel_sala = $('#sel_sala');
        $.ajax({
            type: "POST",
            url: "class/Sala.php",
            data: {
                action: miAccion,
                idDataCenter: $('#sel_centro_datos').val()
            }
        })
            .done(function (e) {
                var data = JSON.parse(e);
                sel_sala.html('');
                $.each(data,
                    function (key, val) {
                        sel_sala.append('<option value="' + val.id + '">' + val.nombre + '</option>');
                    })
            })
            .fail(function (e) {
                $sel_sala.html('<option id="-1">Cargando...</option>');
            });
    }


    get carga_Autorizador() {
    }

    get cargar_datos_formulario() {
        var miAccion = 'ReadbyID';
        $.ajax({
            type: "POST",
            url: "class/Formulario.php",
            data: {
                action: miAccion,
                obj: JSON.stringify(formulario)
            }
        })
            .done(function (e) {
                formulario.drawFormulario_id(e);
            })
            .fail(function (e) {
                // formulario.showError(e);
            });
    }

    drawFormulario_id(e) {
        var data_formulario = JSON.parse(e);

        $("#sel_centro_datos").val(data_formulario.idDataCenter);        
        formulario.carga_SalabyDC;
        $("#sel_sala").val(data_formulario.idSala);

        usuario.id = data_formulario.idResponsable;
        usuario.responsable_ReadbyID;
        // $("#sel_responsable").select2({"data": [{"id":"2127","text":"Henry Ford"},{"id":"2199","text":"Tom Phillips"}]});
        $("#sel_responsable").select2({"data": usuario.responsable_ReadbyID });
        $('#sel_responsable').select2().trigger('change');



        $("#sel_responsable").val("2127");
        $("#sel_autorizador").val(data_formulario.idAutorizador);
        $('#sel_estado').val(data_formulario.idEstado);
        // $("#sel_tramitante").val(data_formulario.idTramitante);
        $("#otrosDetalles").val(data_formulario.otrosDetalles);
        //Rango de fecha
        // data_formulario.fechaIngreso
        // data_formulario.fechaSalida
        $("#inp_descripcion").val(data_formulario.motivoVisita);

        $('#tb_visitante_seccionado').DataTable().columns(0).data()[0];
        (typeof (formulario.fechaIngreso) == "object") ?
            formulario.fechaIngreso = formulario.fechaIngreso.format("YYYY-MM-DD HH:mm:ss") :
            true;
    }



}

let formulario = new Formulario();
let tbl_formularios = [];