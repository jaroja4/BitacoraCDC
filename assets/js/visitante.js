class Visitante {
    // Constructor
    constructor(id, identificacion, nombre, empresa, fechaRegistro,array_visitantes) {
        this.id = id || null;
        this.identificacion = identificacion || "";
        this.nombre = nombre || "";
        this.empresa = empresa || "";
        this.fechaRegistro = fechaRegistro || null;
        this.array_visitantes = array_visitantes || [];
    }

    //Getter
    get ReadbyID() {
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

let visitante = new Visitante();  