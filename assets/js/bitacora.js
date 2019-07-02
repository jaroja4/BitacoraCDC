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
            columns: [
                {
                    title: "id",
                    data: "id",
                    visible: false
                },
                {
                    title: "Formulario",
                    data: "idFormulario",
                    visible: false
                },
                {
                    title: "Visitante",
                    data: "idVisitante",
                    visible: false
                },
                {
                    title: "Tarjeta",
                    data: "idTarjeta",
                    visible: false
                },
                {
                    title: "Fecha Entrada",
                    data: "entrada",
                    width: "10%"
                },
                {
                    title: "Fecha Salida",
                    data: "salida",
                    width: "10%"
                },
                {
                    title: "Formulario",
                    data: "consecutivoFormulario",
                    width: "5%"
                },
                {
                    title: "Visitante",
                    data: "nombre",
                    width: "20%"
                },
                {
                    title: "Empresa",
                    data: "empresa",
                    width: "10%"
                },
                {
                    title: "Motivo Visita",
                    data: "motivoVisita",
                    width: "20%"
                },
                {
                    title: "Detalles Visita",
                    data: "otrosDetalles",
                    width: "20%",
                    visible: false
                },
                {
                    title: "Ubicación",
                    data: "sala",
                    width: "20%"
                },
                {
                    title: "Tarjeta Asignada",
                    data: "consecutivoTarjeta",
                    width: "5%"
                }
            ],
            "order": [
                [4, "desc"]
            ],
        });

    }
}

let bitacora = new Bitacora();  
let tbl_bitacora = [];