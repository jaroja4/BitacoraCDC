class DataCenter {
    // Constructor
    constructor(id, nombre) {
        this.id = id || null;
        this.nombre = nombre || "";
    }
    //Getter
    get ReadAll() {
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
                if (dataCenter.id != null) {
                    sel_centro_datos.val(dataCenter.id);
                }
                sala.carga_SalabyDC;
            })
            .fail(function (e) {
                sel_centro_datos.html('<option id="-1">Cargando...</option>');
            });
    }

    get index_cargaSitioXIP() {
        $.ajax({
            type: "POST",
            url: "class/DataCenter.php",
            data: {
                action: "ReadByIP"
            }
        })
            .done(function (e) {
                var data = JSON.parse(e);
                dataCenter.id = data["id"];
                dataCenter.nombre = data["nombre"];
                $(".sitio").html(data["nombre"] + ' <i style="margin-left: 10px;font-size:24px;" class="fa fa-building-o"></i>');
            })
            .fail(function (e) {
                $(".sitio").html('Seleccionar un Sitio: <i style="margin-left: 10px;font-size:24px;" class="fa fa-building-o"></i>');
            });
    }

    get Registro_ReadAll() {
        var miAccion = 'ReadAll';
        var menu1 = $('#menu1');
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
                menu1.html('');
                $.each(data,
                        function (key, val) {
                            menu1.append(
                                `<li>
                                    <a onclick="dataCenterSelect('${val.id}','${val.nombre}')">
                                        <span class="image"><img style="width: 55%;" src="images/${val.imagen}"
                                            alt="Profile Image" /></span>
                                        <span>
                                            <h4>${val.nombre}</h4>
                                        </span>
                                    </a>
                                </li>`);
                        })
            })
            .fail(function (e) {
                
            });
    }

    get tbl_CDC_ReadAll() {
        var miAccion = 'ReadAll';
        $.ajax({
            type: "POST",
            url: "class/DataCenter.php",
            data: {
                action: miAccion
            }
        })
            .done(function (e) {
                dataCenter.drawCDC_DataTable(e);
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }

    drawCDC_DataTable(e) {
        if (e){
            var dataCDC = JSON.parse(e);
            $('#tb_CDC').DataTable({
                data: dataCDC,
                destroy: true,
                autoWidth: false,
                language: {
                    "infoEmpty": "Sin Centros de Datos Creados",
                    "emptyTable": "Sin Centros de Datos Creados",
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
                        width: "90%",
                        targets: 1
                    },
                    {
                        title: "Eliminar",
                        targets: 8,
                        visible: true,
                        mRender: function (e) {
                            return '<button class=btnEliminarCDC onclick="deleteCDC(this)" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> </button>';
                        }
                    }
                ]
            });
    
            $('#tb_CDC tbody').on('click', 'tr', function () {
                dataCenter.clear;
                dataCenter.id = $('#tb_CDC').DataTable().row(this).data().id;
                // dataCenter.cargarResponsablebyID;
            });
        }
        
    }
}

let dataCenter = new DataCenter();  