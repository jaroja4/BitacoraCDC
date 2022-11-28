class Evento {
  // Constructor
  constructor(id, nombre) {
    this.id = id || null;
    this.nombre = nombre || "";
  }

  get tb_readAll() {
    var miAccion = "ReadAll";
    $.ajax({
      type: "POST",
      url: "class/Evento.php",
      data: {
        action: miAccion,
      },
    })
      .done(function (e) {
        rol.draw_tblEventos(e);
      })
      .fail(function (e) {
        $sel_rol.html('<option id="-1">Cargando...</option>');
      });
  }

  get readAll() {
    var miAccion = "ReadAll";
    $.ajax({
      type: "POST",
      url: "class/Evento.php",
      data: {
        action: miAccion,
      },
    })
      .done(function (e) {
        var eventos = JSON.parse(e);
        $.each(eventos, function (key, value) {
          $("#chkbox_rol").append(
            `<div class="checkbox"> \
        		<label> \
        		<input type="checkbox" \
        			value="${value.id}"> \
        			${value.menu} - ${value.opcion} \
        		</label> \
        </div > `
          );
        });
      })
      .fail(function (e) {
        alert("error al cargar eventos");
      });
  }

  readEventosxRol(e) {
    var miAccion = "readEventosxRol";
    $.ajax({
      type: "POST",
      url: "class/Evento.php",
      data: {
        action: miAccion,
        id: e,
      },
    })
      .done(function (e) {
        var index = 0;
        let dataEventos = JSON.parse(e);
        $.each($("#chkbox_rol").find("input"), function (iChk, itemChk) {
          index = iChk;
          $.each(dataEventos, function (iev, itemEv) {
            if ($(itemChk).val() == itemEv.idEvento) {
              $(itemChk).prop("checked", true);
            }
          });
          // $("#chkbox_rol").find("input").eq(2).prop("checked", true);
        });
        // rol.draw_tblEventos(e);
      })
      .fail(function (e) {
        $sel_rol.html('<option id="-1">Cargando...</option>');
      });
  }
  draw_tblEventos(e) {
    let dataEvento = JSON.parse(e);
    $("#tb_eventos").DataTable({
      data: dataRol,
      destroy: true,
      autoWidth: false,
      language: {
        infoEmpty: "Sin Eventos Creados",
        emptyTable: "Sin Eventos Creados",
        search: "Buscar",
        zeroRecords: "No hay resultados",
        lengthMenu: "Mostar _MENU_ registros",
        paginate: {
          first: "Primera",
          last: "Ultima",
          next: "Siguiente",
          previous: "Anterior",
        },
      },
      order: [[1, "asc"]],
      columns: [
        {
          title: "id",
          data: "id",
          targets: 0,
          visible: false,
        },
        {
          title: "Nombre",
          data: "text",
          width: "90%",
          targets: 1,
        },
        {
          title: "Eliminar",
          targets: 8,
          visible: true,
          mRender: function (e) {
            return '<button class=btnEliminarRol onclick="deleteEvento(this)" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> </button>';
          },
        },
      ],
    });

    $("#tb_eventos tbody").on("click", "tr", function () {
      alert("I am an alert box!");
      dataCenter.clear;
      dataCenter.id = $("#tb_roles").DataTable().row(this).data().id;
      // dataCenter.cargarResponsablebyID;
    });
  }
  // get loadbyUser() {
  //     var miAccion = "LoadbyUser";
  //     var sel_rol = $('#inp_rol');
  //     $.ajax({
  //         type: "POST",
  //         url: "class/Roles.php",
  //         data: {
  //             action: miAccion,
  //             idUsuario: this.idUsuario
  //         }
  //     })
  //         .done(function (e) {
  //             var data = JSON.parse(e);
  //             var arrayRol = [];
  //             $.each( data, function( key, value ) {
  //                 arrayRol.push(value.idRol);
  //             })
  //             $("#inp_rol").val(arrayRol).trigger("change");
  //         })
  //         .fail(function (e) {
  //             $sel_rol.html('<option id="-1">Cargando...</option>');
  //         });
  // }

  //GetterloadIdUser
  // get readAll() {
  //     var miAccion = "ReadAll";
  //     var sel_rol = $('#inp_rol');
  //     $.ajax({
  //         type: "POST",
  //         url: "class/Roles.php",
  //         data: {
  //             action: miAccion
  //         }
  //     })
  //         .done(function (e) {
  //             var data = JSON.parse(e);
  //             sel_rol.html('');
  //             $.each(data, function (key, val) {
  //                 var newOption = new Option(val.text, val.id, false, false);
  //                 $('#inp_rol').append(newOption).trigger('change');
  //             })

  //         })
  //         .fail(function (e) {
  //             $sel_rol.html('<option id="-1">Cargando...</option>');
  //         });
  // }
}

let evento = new Evento();
