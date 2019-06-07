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
                if (dataCenter.id!=null){
                    sel_centro_datos.val(dataCenter.id);
                }
                sala.carga_SalabyDC;
            })
            .fail(function (e) {
                sel_centro_datos.html('<option id="-1">Cargando...</option>');
            });
    }
}

let dataCenter = new DataCenter();  