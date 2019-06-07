class Usuario {
    // Constructor
    constructor(id, usuario, passwd, cedula, nombre, empresa, correo, fechaCreacion) {
        this.id = id || null;
        this.usuario = usuario || "";
        this.passwd = passwd || "";
        this.cedula = cedula || "";
        this.nombre = nombre || "";
        this.empresa = empresa || "";
        this.correo = correo || null;
        this.fechaCreacion = fechaCreacion || [];
    }

    get responsable_ReadAll() {
        var miAccion = 'ReadAll';
        $.ajax({
            type: "POST",
            url: "class/Responsable.php",
            data: {
                action: miAccion
            }
        })
            .done(function (e) {
                var data = JSON.parse(e);
                $("#sel_responsable").select2({"data": data });
                $("#sel_responsable").val(usuario.id);
                $('#sel_responsable').select2().trigger('change');
            })
            .fail(function (e) {
                // dataCenter.showError(e);
            });
    }

    get responsable_ReadbyID() {
        var miAccion = 'ReadbyID';
        $.ajax({
            type: "POST",
            url: "class/Responsable.php",
            data: {
                action: miAccion,
                id: usuario.id
            }
        })
            .done(function (e) {
                var data = JSON.parse(e);
                $("#sel_responsable").select2({"data": data });
                $("#sel_responsable").val(usuario.id);
                $('#sel_responsable').select2().trigger('change');
                $("#sel_responsable").select2({
                    ajax: {
                      url: "class/Responsable.php",
                      type: "post",
                      dataType: 'json',
                      delay: 250,
                      data: function (params) {
                        return {
                          action: "ReadAll",
                          search_value: params.term // search term
                        };
                      },
                      processResults: function (response) {
                        // result_Visitantes = response;
                        return {
                          results: response
                        };
                      },
                      cache: true
                    }
                  });
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
                id: usuario.id
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