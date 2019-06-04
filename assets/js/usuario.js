class Usuario {
    // Constructor
    constructor(id, identificacion, nombre, empresa, fechaRegistro, array_visitantes) {
        this.id = id || null;
        this.identificacion = identificacion || "";
        this.nombre = nombre || "";
        this.empresa = empresa || "";
        this.fechaRegistro = fechaRegistro || null;
        this.array_visitantes = array_visitantes || [];
    }

    get responsable_ReadbyID() {
        var miAccion = 'ReadbyID';
        $.ajax({
            type: "POST",
            url: "class/Responsable.php",
            data: {
                action: miAccion,
                id: visitante.id
            }
        })
            .done(function (e) {
                return data_responsable = JSON.parse(e);
                
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }

    get visitante_ReadbyID() {
        var miAccion = 'ReadbyID';

        $.ajax({
            type: "POST",
            url: "class/Visitante.php",
            data: {
                action: miAccion,
                id: visitante.id
            }
        })
            .done(function (e) {
                // componente.drawByDC(e);
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }

    

}

let usuario = new Usuario();