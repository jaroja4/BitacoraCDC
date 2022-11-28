class Formulario {
    // Constructor
    constructor(id, idEstado, idDataCenter, idSala, idTramitante, idAutorizador, idResponsable, consecutivo, fechaSolicitud, fechaIngreso, fechaSalida, otrosDetalles, arrayVisitantes, motivoVisita) {
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
        this.img_userDefault = "data:image/png;base64,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";

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
        formulario.idTramitante =  $("#sel_tramitante :selected").val();
        formulario.otrosDetalles = $("#otrosDetalles").val();
        formulario.motivoVisita = $("#inp_descripcion").val();
        formulario.fechaIngreso = moment(formulario.fechaIngreso).format("YYYY-MM-DD HH:mm:ss");
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
            sScrollX: "100%",
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
                    width: "15%",
                    mRender: function (e) {
                        var event = new Date(e);
                        var f_entrada = moment(event).format('DD-MMM-YYYY hh:mm a', 'es');
                        return f_entrada;
                    }
                },
                {
                    title: "Fecha Ingreso",
                    data: "fechaIngreso",
                    width: "15%",
                    mRender: function (e) {
                        var event = new Date(e);
                        var f_entrada = moment(event).format('DD-MMM-YYYY hh:mm a', 'es');
                        return f_entrada;
                    }
                },
                {
                    title: "Fecha Salida",
                    data: "fechaSalida",
                    width: "15%",
                    mRender: function (e) {
                        var event = new Date(e);
                        var f_entrada = moment(event).format('DD-MMM-YYYY hh:mm a', 'es');
                        return f_entrada;
                    }
                },
                {
                    title: "Motivo",
                    data: "motivoVisita",
                    width: "30%",
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
                },
                {
                    title: "nombreVisitante",
                    data: "nombreVisitante",
                    visible: false,
                    searchable: true
                },
                {
                    title: "cedulaVisitante",
                    data: "cedulaVisitante",
                    visible: false,
                    searchable: true
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
        formulario.tramitante_nombre = data_formulario.tramitante_nombre;
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

        // $("#sel_autorizador").val(formulario.idAutorizador);
        $("#sel_estado").val(formulario.idEstado);
        $('#sel_tramitante')
          .empty()
          .append(`<option selected="selected" value="${formulario.idTramitante}">${formulario.tramitante_nombre}</option>`);
        $("#sel_tramitante").val(formulario.idTramitante).change();
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

        $("#sel_estado").val(0);
        $('#sel_autorizador').val(Session.id);
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


        if (!$("#collapseOne").hasClass("in")) {
            $("#collapseOne").collapse('toggle');
        }
        if ($("#collapseTwo").hasClass("in")) {
            $("#collapseTwo").collapse('toggle');
        }
        if ($("#collapseThree").hasClass("in")) {
            $("#collapseThree").collapse('toggle');
        }
    }

    get buscar() {
        var miAccion = 'Buscar';
        $.ajax({
            type: "POST",
            url: "class/Formulario.php",
            data: {
                action: miAccion,
                value: $("#inp_identificacion").val(),
                idDataCenter: dataCenter.id
            }
        })
            .done(function (e) {
                formulario.modalVisitante(e);
            })
            .fail(function (e) {
                // formulario.showError(e);
            });
    }

    modalVisitante(e) {
        var dataVisitante = JSON.parse(e);
        dataVisitante = dataVisitante[0];

        if (dataVisitante == "noformulario") {
            Swal.fire({
                type: 'error',
                title: 'SIN REGISTRO',
                text: 'No exite un resgistro para la cédula ingresada!',
                timer: 2000
            })
            return false;
        }
        if (dataVisitante == "notarjeta") {
            Swal.fire({
                type: 'error',
                title: 'SIN TARJETA',
                text: 'No exite una tarjeta diponible para esta recinto!',
                timer: 2000
            })
            return false;
        }

        dataVisitante.fechaIngreso = new Date(dataVisitante.fechaIngreso);
        dataVisitante.fechaIngreso = moment(dataVisitante.fechaIngreso).format('DD-MMM-YYYY hh:mm a', 'es');
        dataVisitante.fechaSalida = new Date(dataVisitante.fechaSalida);
        dataVisitante.fechaSalida = moment(dataVisitante.fechaSalida).format('DD-MMM-YYYY hh:mm a', 'es');

        if ( typeof dataVisitante.idFormulario != "undefined" ? true : false ) {

            this.dataCenter = dataVisitante.nombreDataCenter;
            this.consecutivo = dataVisitante.consecutivo;
            this.cedula = dataVisitante.cedula;
            this.nombre = dataVisitante.nombre;
            this.empresa = dataVisitante.empresa;
            this.autorizador = dataVisitante.autorizador;
            this.fechaIngreso = dataVisitante.fechaIngreso;
            this.fechaSalida = dataVisitante.fechaSalida;
            tarjeta.consecutivo = dataVisitante.consecutivoTarjeta;;
            tarjeta.id = dataVisitante.idTarjeta;;
            this.sala = dataVisitante.nombreSala;
            this.otrosDetalles = dataVisitante.otrosDetalles;

            $("#img_visitante").attr("src", dataVisitante.imagen?dataVisitante.imagen:this.img_userDefault);
            $("#modalVisitanteTitulo").text(this.dataCenter);
            $("#modalVisitanteNoFormulario").text(this.consecutivo);
            $("#modalVisitanteCedula").val(this.cedula);
            $("#modalVisitanteNombre").val(this.nombre);
            $("#modalVisitanteEmpresa").val(this.empresa);
            $("#modalVisitanteAutoriza").val(this.autorizador);
            $("#modalVisitanteFechaEntrada").val(this.fechaIngreso);
            $("#modalVisitanteFechaSalida").val(this.fechaSalida);
            $("#modalVisitanteConsecutivoTarjeta").val(tarjeta.consecutivo);
            $('#modalVisitanteConsecutivoTarjeta').data("id", tarjeta.id);
            $("#modalVisitaSala").val(this.sala);
            $("#modalVisitanteOtrosDetalles").val(this.otrosDetalles);
            $("#btn_entrega_tarjeta").text("Recibir Tarjeta");
            $('#btn_entrega_tarjeta').data("accion", "recibir");
            $("#modalTituloVisitante").contents()[0].textContent="Formulario de SALIDA # ";
            $("#modalVisitante").modal("toggle");
            return false;
        }

        this.id = dataVisitante.id;
        this.idEstado = dataVisitante.idEstado;
        // this.idDataCenter = dataVisitante.idDataCenter;
        this.idSala = dataVisitante.idSala;
        // this.idTramitante = dataVisitante.idTramitante;
        // this.idAutorizador = dataVisitante.idAutorizador;
        // this.idResponsable = dataVisitante.idResponsable;
        this.consecutivo = dataVisitante.consecutivo;
        this.fechaSolicitud = dataVisitante.fechaSolicitud;
        this.fechaIngreso = dataVisitante.fechaIngreso;
        this.fechaSalida = dataVisitante.fechaSalida;
        // this.motivoVisita = dataVisitante.motivoVisita;
        this.otrosDetalles = dataVisitante.otrosDetalles;
        this.idVisitante = dataVisitante.idVisitante;
        tarjeta.consecutivo = dataVisitante.tarjeta.consecutivo;
        tarjeta.id = dataVisitante.tarjeta.id;

        $("#img_visitante").attr("src", dataVisitante.imagen?dataVisitante.imagen:this.img_userDefault);
        $("#modalVisitanteTitulo").text(dataVisitante.dataCenter);
        $("#modalVisitanteNoFormulario").text(dataVisitante.consecutivo);
        $("#modalVisitanteCedula").val(dataVisitante.cedula);
        $("#modalVisitanteNombre").val(dataVisitante.nombre);
        $("#modalVisitanteEmpresa").val(dataVisitante.empresa);
        $("#modalVisitanteAutoriza").val(dataVisitante.autorizador);
        $("#modalVisitanteFechaEntrada").val(dataVisitante.fechaIngreso);
        $("#modalVisitanteFechaSalida").val(dataVisitante.fechaSalida);
        $("#modalVisitanteConsecutivoTarjeta").val(dataVisitante.tarjeta.consecutivo);
        $('#modalVisitanteConsecutivoTarjeta').data("id", dataVisitante.tarjeta.id);
        $("#modalVisitaSala").val(dataVisitante.sala);
        $("#modalVisitanteOtrosDetalles").val(dataVisitante.otrosDetalles);
        $("#btn_entrega_tarjeta").text("Entregar Tarjeta");
        $('#btn_entrega_tarjeta').data("accion", "entregar");
        $("#modalTituloVisitante").contents()[0].textContent="Formulario de ENTRADA # ";
        $("#modalVisitante").modal("toggle");
    }

    rolCheck() {
        Session.rol.map((rol) => { formulario.loadRol(rol)});
    }

    loadRol(rol){
      switch (rol.nombre) {
        case "Tramitante":
          formulario.setRolTramitante();
          break;
        case "Autorizador":
          formulario.setRolAutorizador();
          break;
        default:
          // alert("default: "+rol.nombre)
      }
    }

    setRolTramitante(){
      $('#btn_abrirModal').css('display','inline-block');
    }

    setRolAutorizador(){
      $('#sel_autorizador').empty();
      $('#sel_autorizador').append(`<option value="${Session.id}">${Session.name}</option>`);
      $('#sel_autorizador').val(Session.id);
      $('#sel_estado').prop("disabled", false);
    }


}

let formulario = new Formulario();
let tbl_formularios = [];
