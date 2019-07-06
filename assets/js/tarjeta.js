class Tarjeta {
    // Constructor
    constructor(id, idSala, estado, consecutivo) {
        this.id = id || null;
        this.idSala = idSala || null;
        this.estado = estado || null;
        this.consecutivo = consecutivo || null;
    }

    //Gette
    get carga_pool_tarjetas() {
        var miAccion = 'ReadAll';

        $.ajax({
            type: "POST",
            url: "class/Tarjeta.php",
            data: {
                action: miAccion,
                obj: JSON.stringify(tarjeta)
            }
        })
            .done(function (e) {
                tarjeta.drawTarjetas(e);
            })
            .fail(function (e) {
                // tarjeta.showError(e);
            });
    }

    drawTarjetas(e) {
        var data_tajeta = JSON.parse(e);

        var tb_pool_tarjetas = $('#tb_pool_tarjetas').DataTable({
            // responsive: true,
            data: data_tajeta,
            destroy: true,
            // paging: false,
            ordering: true,
            responsive: true,
            // info: false,
            // searching: false,
            // scrollX: false,
            // scrollY: false,
            // scrollCollapse: true,
            language: {
                "infoEmpty": "Sin Tarjetas Registradas",
                "emptyTable": "Sin Tarjetas Registradas",
                "search": "Buscar",
                "zeroRecords": "No hay resultados",
                "lengthMenu": "Mostrar _MENU_ registros",
                "paginate": {
                    "first": "Primera",
                    "last": "Ultima",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            columns: [
                {
                    title: "idTarjeta",
                    data: "id",
                    visible: false
                },
                {
                    title: "Consecutivo",
                    data: "consecutivo"
                },
                {
                    title: "Centro de Datos",
                    data: "nombreDC"
                },
                {
                    title: "idSala",
                    data: "idSala",
                    visible: false
                },
                {
                    title: "Sala",
                    data: "nombreSala"
                },
                {
                    title: "Estado",
                    data: "estado",
                    mRender: function (e) {
                        return (e == 0) ? 'Disponible' : (e != 1) ? 'Otro' : 'Asiganada'
                    }
                },
                {
                    title: "Eliminar",
                    visible: true,
                    mRender: function (e) {
                        return '<button class=btnEliminarTarjeta onclick="deleteTarjeta(this)" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> </button>';
                    }
                }
            ]
        });

    }

    get Entregar() {
        var miAccion = 'Entregar';

        $.ajax({
            type: "POST",
            url: "class/Tarjeta.php",
            data: {
                action: miAccion,
                value: $('#modalVisitanteConsecutivoTarjeta').data("id"),
                id: tarjeta.id,
                obj: JSON.stringify(formulario)
            }
        })
            .done(function (e) {
                var result = JSON.parse(e);
                if(result){
                    Swal.fire({
                        position: 'top-end',
                        type: 'success',
                        title: 'Tarjeta Registrada',
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
                else{
                    Swal.fire({
                        type: 'error',
                        title: 'ERROR',
                        text: 'Ocurrio un problema al registrar la tarjeta!',
                        timer: 3000
                    })
                }
                $("#inp_identificacion").val("");
            })
            .fail(function (e) {
                Swal.fire({
                    type: 'error',
                    title: 'ERROR',
                    text: 'Ocurrio un problema contacte al 2002-4040!',
                    timer: 3000
                })
            });
    }
    get Recibir() {
        var miAccion = 'Recibir';

        $.ajax({
            type: "POST",
            url: "class/Tarjeta.php",
            data: {
                action: miAccion,
                value: $('#inp_identificacion').val()
            }
        })
            .done(function (e) {
                var result = JSON.parse(e);
                if(result){
                    Swal.fire({
                        position: 'top-end',
                        type: 'success',
                        title: 'Tarjeta Registrada',
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
                else{
                    Swal.fire({
                        type: 'error',
                        title: 'ERROR',
                        text: 'Ocurrio un problema al registrar la tarjeta!',
                        timer: 3000
                    })
                }
                $("#inp_identificacion").val("");
            })
            .fail(function (e) {
                Swal.fire({
                    type: 'error',
                    title: 'ERROR',
                    text: 'Ocurrio un problema contacte al 2002-4040!',
                    timer: 3000
                })
            });
    }
}

let tarjeta = new Tarjeta();