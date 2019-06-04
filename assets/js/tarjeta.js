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
            columnDefs: [
                {
                    title: "idTarjeta",
                    data: "id",
                    targets: 0,
                    visible: false
                },
                {
                    title: "Consecutivo",
                    data: "consecutivo",
                    targets: 1,
                    orderData: [0, 3]
                },
                {
                    title: "idSala",
                    data: "idSala",
                    targets: 2,
                    visible: false
                },
                {
                    title: "Sala",
                    data: "nombreSala",
                    targets: 3
                },
                {
                    title: "Estado",
                    data: "estado",
                    targets: 4,
                    mRender: function (e) {
                        return (e == 0) ? 'Disponible' : 'Asiganada'
                    }
                },
                {
                    title: "Eliminar",
                    targets: 5,
                    visible: true,
                    mRender: function (e) {
                        return '<button class=btnEliminarTarjeta onclick="deleteTarjeta(this)" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> </button>';
                    }
                }
            ]
        });

    }
}

let tarjeta = new Tarjeta();