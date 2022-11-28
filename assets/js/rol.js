class Rol {
  // Constructor
  constructor(id, nombre) {
    this.id = id || null;
    this.nombre = nombre || "";
  }
  // GetterloadIdUser
  get readAll() {
      var miAccion = "ReadAll";
      var sel_rol = $('#inp_rol');
      $.ajax({
          type: "POST",
          url: "class/Roles.php",
          data: {
              action: miAccion
          }
      })
          .done(function (e) {
              var data = JSON.parse(e);
              sel_rol.html('');
              $.each(data, function (key, val) {
                  var newOption = new Option(val.text, val.id, false, false);
                  $('#inp_rol').append(newOption).trigger('change');
              })

          })
          .fail(function (e) {
              $sel_rol.html('<option id="-1">Cargando...</option>');
          });
  }

  get tb_readAll() {
    var miAccion = "ReadAll";
    $.ajax({
      type: "POST",
      url: "class/Roles.php",
      data: {
        action: miAccion,
      },
    })
      .done(function (e) {
        rol.draw_tblRoles(e);
      })
      .fail(function (e) {
        $sel_rol.html('<option id="-1">Cargando...</option>');
      });
  }

  get clearRolxEventos() {
    $.each($("#chkbox_rol").find("input"), function (iChk, itemChk) {
      $(itemChk).prop("checked", false);
      $("#inp_nombreRol").val("");
    });
  }

  get update() {
    var miAccion = "Update";
    $.ajax({
      type: "POST",
      url: "class/Roles.php",
      data: {
        action: miAccion,
        obj: JSON.stringify(rol),
      },
    })
      .done(function (e) {
        var result = JSON.parse(e);
        usuario.clearRolxEventos;
        result
          ? rol.SwalAlert("success", "Usuario Actualizado")
          : rol.SwalAlert("error", "Error al Actualizar");
        $("#modal_NuevoRol").modal("toggle");
      })
      .fail(function (e) {});
  }

  get loadbyUser() {
      var miAccion = "LoadbyUser";
      var sel_rol = $('#inp_rol');
      $.ajax({
          type: "POST",
          url: "class/Roles.php",
          data: {
              action: miAccion,
              idUsuario: this.idUsuario
          }
      })
          .done(function (e) {
              var data = JSON.parse(e);
              var arrayRol = [];
              $.each( data, function( key, value ) {
                  arrayRol.push(value.idRol);
              })
              $("#inp_rol").val(arrayRol).trigger("change");
          })
          .fail(function (e) {
              $sel_rol.html('<option id="-1">Cargando...</option>');
          });
  }

  SwalAlert(tipo, titulo) {
    usuario.responsables_ReadAll_list;
    usuario.usuarios_ReadAll_list;
    usuario.visitantes_ReadAll_list;
    usuario.autorizadores_ReadAll_list;
    usuario.tramitantes_ReadAll_list;
    Swal.fire({
      position: "top-end",
      type: tipo,
      title: titulo,
      showConfirmButton: false,
      timer: 1500,
    });
  }

  draw_tblRoles(e) {
    let dataRol = JSON.parse(e);
    $("#tb_roles").DataTable({
      data: dataRol,
      destroy: true,
      autoWidth: false,
      language: {
        infoEmpty: "Sin Roles Creados",
        emptyTable: "Sin Roles Creados",
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
            return '<button class=btnEliminarRol onclick="deleteRol(this)" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> </button>';
          },
        },
      ],
    });

    $("#tb_roles tbody").on("click", "tr", function () {
      rol.clearRolxEventos;
      update = true;
      rol.id = $("#tb_roles").DataTable().row(this).data().id;
      rol.nombre = $("#tb_roles").DataTable().row(this).data().text;
      evento.readEventosxRol(rol.id);
      $("#inp_nombreRol").val(rol.nombre);
      $("#btn_guardarRol").text("Actualizar");
      $("#modal_NuevoRol").modal("toggle");
    });
  }

}

let rol = new Rol();
