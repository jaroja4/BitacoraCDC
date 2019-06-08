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
}

let dataCenter = new DataCenter();  