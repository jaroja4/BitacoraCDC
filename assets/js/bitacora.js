class Bitacora {
    // Constructor
    constructor(id, nombre) {
        this.id = id || null;
        this.nombre = nombre || "";
    }

    //Getter
    get ReadbyRange() {
        var miAccion = 'ReadbyRange';
        $.ajax({
            type: "POST",
            url: "class/Bitacora.php",
            data: {
                action: miAccion,
                obj: JSON.stringify(bitacora)
            }
        })
            .done(function (e) {
                bitacora.drawBitacorabyRange(e);              
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }
    
    drawBitacorabyRange(e) {
        var data_bitacora = JSON.parse(e);

        tbl_bitacora = $('#tbl_bitacora').DataTable({
            data: data_bitacora,
            destroy: true,
            autoWidth: false,
            language: {
                "infoEmpty": "Sin Elementos que Mostrar",
                "emptyTable": "Sin Elementos que Mostrar",
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
                    title: "Formulario",
                    data: "idFormulario",
                    width: "20%",
                },
                {
                    title: "Visitante",
                    data: "idVisitante",
                    width: "20%",
                },
                {
                    title: "Tarjeta",
                    data: "idTarjeta",
                    width: "20%",
                },
                {
                    title: "Fecha Entrada",
                    data: "entrada",
                    width: "20%",
                },
                {
                    title: "Fecha Salida",
                    data: "salida",
                    width: "20%",
                }
            ]
        });

    }
}

let bitacora = new Bitacora();  
let tbl_bitacora = [];