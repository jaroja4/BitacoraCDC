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
            if ($("#btn_guardar_formulario").hasClass("actualizarFormulario"))
                var miAccion = "Update";
            else
                var miAccion = "Create";
            formulario.create(miAccion);
        } else {
            alert("Faltan Datos");
        }
    }

    create(miAccion) {
        // var miAccion = 'Create';

        formulario.idDataCenter = $("#sel_centro_datos").val();
        formulario.idSala = $("#sel_sala").val();;
        formulario.idResponsable = $("#sel_responsable").val();
        formulario.idAutorizador = $("#sel_autorizador").val();
        formulario.otrosDetalles = $("#otrosDetalles").val();
        formulario.motivoVisita = $("#inp_descripcion").val();
        // (typeof (formulario.fechaIngreso) == "object") ? formulario.fechaIngreso = formulario.fechaIngreso.format("YYYY-MM-DD HH:mm:ss") : true;
        formulario.fechaIngreso = moment(formulario.fechaIngreso).format("YYYY-MM-DD HH:mm:ss");
        // (typeof (formulario.fechaSalida) == "object") ? formulario.fechaSalida = formulario.fechaSalida.format("YYYY-MM-DD HH:mm:ss") : true;
        formulario.fechaSalida = moment(formulario.fechaSalida).format("YYYY-MM-DD HH:mm:ss");
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
                formulario.ReadAllbyRange;
                // formulario = new Formulario();
                $("#modal_new_form").modal("hide");
                Swal.fire({
                    position: 'top-end',
                    type: 'success',
                    title: 'Formulario Enviado',
                    showConfirmButton: false,
                    timer: 1500
                })
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
                    width: "5%",
                },
                {
                    title: "Fecha Solicitud",
                    data: "fechaSolicitud",
                    width: "10%",
                },
                {
                    title: "Fecha Ingreso",
                    data: "fechaIngreso",
                    width: "10%",
                },
                {
                    title: "Fecha Salida",
                    data: "fechaSalida",
                    width: "10%",
                },
                {
                    title: "Motivo",
                    data: "motivoVisita",
                    width: "45%",
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

    get carga_Autorizador() {
    }

    get cargarFormulariobyID() {
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
        formulario.id = data_formulario.id;
        formulario.idEstado = data_formulario.idEstado;
        formulario.idDataCenter = data_formulario.idDataCenter
        formulario.idSala = data_formulario.idSala;
        formulario.idTramitante = data_formulario.idTramitante;
        formulario.idAutorizador = data_formulario.idAutorizador;
        formulario.idResponsable = data_formulario.idResponsable;
        formulario.consecutivo = data_formulario.consecutivo;
        formulario.fechaSolicitud = data_formulario.fechaSolicitud;
        formulario.fechaIngreso = data_formulario.fechaIngreso;
        formulario.fechaSalida = data_formulario.fechaSalida
        formulario.motivoVisita = data_formulario.motivoVisita;
        formulario.otrosDetalles = data_formulario.otrosDetalles;
        formulario.arrayVisitantes = data_formulario.arrayVisitantes;

        dataCenter.id = formulario.idDataCenter;
        sala.id = formulario.idSala;
        usuario.id = formulario.idResponsable;

        dataCenter.ReadAll;

        usuario.responsable_ReadbyID;

        $("#sel_autorizador").val(formulario.idAutorizador);
        $("#sel_estado").val(formulario.idEstado);
        // // $("#sel_tramitante").val(data_formulario.idTramitante);
        $("#otrosDetalles").val(formulario.otrosDetalles);

        $("#dp_rangoFechaFormulario").data('daterangepicker').setStartDate(moment(formulario.fechaIngreso));
        $("#dp_rangoFechaFormulario").data('daterangepicker').setEndDate(moment(formulario.fechaSalida));
        $('#dp_rangoFechaFormulario span').html(moment(formulario.fechaIngreso).format('D MMMM YY - hh:mm A') + ' - ' + moment(formulario.fechaSalida).format('D MMMM YY - hh:mm A'));
        $("#inp_descripcion").val(formulario.motivoVisita);

        $(formulario.arrayVisitantes).each(function (index, value) {
            $('#tb_visitante_seccionado').DataTable().row.add(value).draw();
        });
        $("#btn_guardar_formulario").text("Actualizar");
        $("#btn_guardar_formulario").addClass("actualizarFormulario");
        $("#modal_new_form").modal("show");
    }

    get clear() {
        dataCenter.ReadAll;

        usuario.id = "-1";
        usuario.responsable_ReadAll;

        $("#sel_autorizador").val("d3b95439-89c2-11e7-8f4b-005056a81613");
        $("#sel_estado").val(0);
        // // $("#sel_tramitante").val(data_formulario.idTramitante);
        $("#otrosDetalles").val("");
        var fechaIngreso = moment().locale("es");
        var fechaSalida = moment().add(5, 'hour').locale("es");
        $("#dp_rangoFechaFormulario").data('daterangepicker').setStartDate(fechaIngreso);
        $("#dp_rangoFechaFormulario").data('daterangepicker').setEndDatefechaSalida
        $('#dp_rangoFechaFormulario span').html(moment(fechaIngreso).format('D MMMM YY - hh:mm A') + ' - ' + moment(fechaSalida).format('D MMMM YY - hh:mm A'));
        $("#inp_descripcion").val("");
        $('#tb_visitante_seccionado').DataTable().clear().draw();
        // $('#accordion').collapse('hide');
        // $('.panel-collapse').collapse({
        //     toggle: false
        //   });
        // $(".panel-collapse").collapse("hide");
        // $(".panel-collapse").collapse("show");
        // $('.panel-collapse').collapse('toggle');
        $("#btn_guardar_formulario").removeClass("actualizarFormulario");
        $("#btn_guardar_formulario").text("Enviar");


        if ( !$("#collapseOne").hasClass("in") ){
            $("#collapseOne").collapse('toggle');
        }
        if ( $("#collapseTwo").hasClass("in") ){
            $("#collapseTwo").collapse('toggle');
        }
        if ( $("#collapseThree").hasClass("in") ){
            $("#collapseThree").collapse('toggle');
        }
    }

}

let formulario = new Formulario();
let tbl_formularios = [];