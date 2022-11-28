class Sala {
    // Constructor
    constructor(id, nombre) {
        this.id = id || null;
        this.nombre = nombre || "";
    }
    //Getter
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
                if (sala.id!=null){
                    sel_sala.val(sala.id);
                }
                $('#sel_sala').trigger('change');
            })
            .fail(function (e) {
                $sel_sala.html('<option id="-1">Cargando...</option>');
            });
        // var miAccion = 'ReadbyID';
        // $.ajax({
        //     type: "POST",
        //     url: "class/Sala.php",
        //     data: {
        //         action: miAccion,
        //         id: sala.id
        //     }
        // })
        //     .done(function (e) {
        //         return dataSala = JSON.parse(e);
        //     })
        //     .fail(function (e) {
        //         // dataCenter.showError(e);
        //     });
    }
}

let sala = new Sala();
