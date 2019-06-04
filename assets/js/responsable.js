class Responsable {
    // Constructor
    constructor(id, nombre) {
        this.id = id || null;
        this.nombre = nombre || "";
    }

    //Getter
    get ReadbyID() {
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
}

let responsable = new Responsable();  