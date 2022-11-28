class Tarjeta {
    // Constructor
    constructor(id, idSala, estado, consecutivo, tb_pool_tarjetas) {
        this.id = id || null;
        this.idSala = idSala || null;
        this.estado = estado || null;
        this.consecutivo = consecutivo || null;
        this.tb_pool_tarjetas = tb_pool_tarjetas || null;
    }

    get carga_tarjetas_by_sala() {
      var miAccion = 'ReadBySala';
      $.ajax({
          type: "POST",
          url: "class/Tarjeta.php",
          data: {
              action: miAccion,
              obj: JSON.stringify($("#sel_sala").val())
          }
      })
        .done(function (e) {
          var data = JSON.parse(e);
          $('#sel_tarjeta').html('');
          $.each(data, function (key, val) {
              if (val.estado == "0" || val.id == tarjeta.id){
                var newOption = new Option(val.consecutivo, val.id, false, false);
                $('#sel_tarjeta').append(newOption).trigger('change');
              }
          })
          if (tarjeta.id!=null){
            $("#sel_tarjeta").val(tarjeta.id);
          }
        })
        .fail(function (e) {
            // tarjeta.showError(e);
        });
    }
    //Gette
    get carga_pool_tarjetas() {
        var miAccion = 'ReadAll';

        $.ajax({
            type: "POST",
            url: "class/Tarjeta.php",
            data: {
                action: miAccion,
                obj: JSON.stringify(tarjeta)
            }
        })
            .done(function (e) {
                tarjeta.drawTarjetas(e);
            })
            .fail(function (e) {
                // tarjeta.showError(e);
            });
    }

    drawTarjetas(e) {
        var data_tajeta = JSON.parse(e);
        $('#tb_pool_tarjetas thead tr')
          .clone(true)
          .addClass('filters')
          .appendTo('#tb_pool_tarjetas thead');

       this.tb_pool_tarjetas = $('#tb_pool_tarjetas').DataTable({
				orderCellsTop: true,
				fixedHeader: true,
        destroy: true,
        // sScrollX: "100%",
        autoWidth: false,
				data: data_tajeta,
				columns: [
					{
						title: "idTarjeta",
						data: "id",
						visible: false
					},
					{
						title: "Consecutivo",
						data: "consecutivo"
					},
					{
						title: "Centro de Datos",
						data: "nombreDC"
					},
					{
						title: "idSala",
						data: "idSala",
						visible: false
					},
					{
						title: "Sala",
						data: "nombreSala"
					},
					{
						title: "Estado",
						data: "estado",
						mRender: function (e, rowIndex, obj) {
							return ( (e == 0) ? 'Disponible' : (e == 1) ? 'Asiganada' : (e == 2) ? 'Deshabilitada' : 'Otro') +`<button class=btnEliminarTarjeta style="margin-left: 15px;" onclick="tarjeta.editTarjeta('`+obj['id']+`','`+obj['id']+`')" > <i class="fa fa-pencil" style="color:#337ab7; padd" aria-hidden="true"></i> </button> <button class=btnEliminarTarjeta style="margin-left: 15px;" onclick="tarjeta.deleteTarjeta('`+obj['id']+`')" > <i class="fa fa-trash-o" style="color:firebrick; padd" aria-hidden="true"></i> </button>`
						}
					},
					// {
					// 	title: "Acción",
					// 	visible: true,
          //   searchable: false,
					// 	mRender: function (e) {
					// 		return '<button class=btnEliminarTarjeta onclick="deleteTarjeta(this)" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> </button>';
					// 	}
					// }
				],
				initComplete: function () {
					var api = this.api();

					// For each column
					api
						.columns()
						.eq(0)
						.each(function (colIdx) {
							// Set the header cell to contain the input element
							var cell = $('.filters th').eq(
								$(api.column(colIdx).header()).index()
							);
							var title = $(cell).text();
							$(cell).html('<input type="text" placeholder="' + title + '" />');

							// On every keypress in this input
							$(
								'input',
								$('.filters th').eq($(api.column(colIdx).header()).index())
							)
								.off('keyup change')
								.on('change', function (e) {
									// Get the search value
									$(this).attr('title', $(this).val());
									var regexr = '({search})'; //$(this).parents('th').find('select').val();

									var cursorPosition = this.selectionStart;
									// Search the column for that value
									api
										.column(colIdx)
										.search(
											this.value != ''
												? regexr.replace('{search}', '(((' + this.value + ')))')
												: '',
											this.value != '',
											this.value == ''
										)
										.draw();
								})
								.on('keyup', function (e) {
									e.stopPropagation();

									$(this).trigger('change');
									$(this)
										.focus()[0]
										.setSelectionRange(cursorPosition, cursorPosition);
								});
						});
				},
			});
    }

    get Entregar() {
        var miAccion = 'Entregar';

        $.ajax({
            type: "POST",
            url: "class/Tarjeta.php",
            data: {
                action: miAccion,
                value: $('#modalVisitanteConsecutivoTarjeta').data("id"),
                id: tarjeta.id,
                obj: JSON.stringify(formulario)
            }
        })
            .done(function (e) {
                var result = JSON.parse(e);
                if(result){
                    Swal.fire({
                        position: 'top-end',
                        type: 'success',
                        title: 'Tarjeta Registrada',
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
                else{
                    Swal.fire({
                        type: 'error',
                        title: 'ERROR',
                        text: 'Ocurrio un problema al registrar la tarjeta!',
                        timer: 3000
                    })
                }
                $("#inp_identificacion").val("");
            })
            .fail(function (e) {
                Swal.fire({
                    type: 'error',
                    title: 'ERROR',
                    text: 'Ocurrio un problema contacte al 2002-4040!',
                    timer: 3000
                })
            });
    }

    get Recibir() {
        var miAccion = 'Recibir';

        $.ajax({
            type: "POST",
            url: "class/Tarjeta.php",
            data: {
                action: miAccion,
                value: $('#inp_identificacion').val()
            }
        })
            .done(function (e) {
                var result = JSON.parse(e);
                if(result){
                    Swal.fire({
                        position: 'top-end',
                        type: 'success',
                        title: 'Tarjeta Registrada',
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
                else{
                    Swal.fire({
                        type: 'error',
                        title: 'ERROR',
                        text: 'Ocurrio un problema al registrar la tarjeta!',
                        timer: 3000
                    })
                }
                $("#inp_identificacion").val("");
            })
            .fail(function (e) {
                Swal.fire({
                    type: 'error',
                    title: 'ERROR',
                    text: 'Ocurrio un problema contacte al 2002-4040!',
                    timer: 3000
                })
            });
    }

    Check_usuario_has_tarjeta(idUsuario){
      var miAccion = 'Check_usuario_has_tarjeta';
      $.ajax({
          type: "POST",
          url: "class/Tarjeta.php",
          data: {
              action: miAccion,
              id: JSON.stringify(idUsuario)
          }
      })
        .done(function (e) {
          var data = JSON.parse(e);

          if (data.t_id){
            $('#inp_tarjeta_fija').trigger('click');
            $('#inp_tarjeta_fija').prop( "checked", true );
            sala.id = data.s_id;
            tarjeta.id = data.t_id;
            $("#sel_centro_datos").val(data.d_id).change();


            $("#sel_sala").val(data.s_id);
            $("#sel_tarjeta").val(data.t_id);
            $('.form-tajeta-fija').show();
          }
          else{
            $('#inp_tarjeta_fija').prop( "checked", false );
            $('.form-tajeta-fija'). hide();
          }
        })
        .fail(function (e) {
            // tarjeta.showError(e);
        });
    }

    get Crear(){
      $.ajax({
          type: "POST",
          url: "class/Tarjeta.php",
          data: {
              action: tarjeta.accion,
              obj: JSON.stringify(this)
          }
      })
        .done(function (e) {
          var data = JSON.parse(e);
          var miAccion = 'ReadAll';

          $.ajax({
              type: "POST",
              url: "class/Tarjeta.php",
              data: {
                  action: miAccion,
                  obj: JSON.stringify(tarjeta)
              }
          })
              .done(function (e) {
                  var data_tajeta = JSON.parse(e);
                  $('#tb_pool_tarjetas').DataTable().clear().rows.add(data_tajeta).draw();
                  Swal.fire({
                      position: "top-end",
                      type: 'success',
                      title: 'Tarjeta Registrada',
                      showConfirmButton: false,
                      timer: 1500,
                  });
              })
              .fail(function (e) {
                  // tarjeta.showError(e);
              });
        })
        .fail(function (e) {
            // tarjeta.showError(e);
        });

    }

    get ValidarTarjeta(){
      var miAccion = 'ValidarTarjeta';
      var values = { consecutivo: $.trim($('#inp_tarjeta').val()) };
      $.ajax({
          type: "POST",
          url: "class/Tarjeta.php",
          data: {
              action: miAccion,
              obj: JSON.stringify(values)
          }
      })
        .done(function (e) {
          var data = JSON.parse(e);
          if (data != "noexiste"){
            $("#valConsecutivo").css('color', 'red');
            $('#btn_guardarTarjeta').attr("disabled", "disabled");
          }
          else{
            $("#valConsecutivo").css('color', 'white');
            $("#btn_guardarTarjeta").removeAttr("disabled");
          }
        })
        .fail(function (e) {
            // tarjeta.showError(e);
        });

    }

    Search_by_id (id) {
      var miAccion = 'Search_by_id';

      $.ajax({
          type: "POST",
          url: "class/Tarjeta.php",
          data: {
              action: miAccion,
              id: id
          }
      })
          .done(function (e) {
              var result = JSON.parse(e);
              if(result){
                sala.id = result.idSala;
                $("#sel_centro_datos").val(result.idDC).change();
                $("#inp_tarjeta").val(result.consecutivo);
                $("#sel_estado").val(result.estado);
                $("#btn_guardarTarjeta").removeAttr("disabled");
			          $('.modal-title').text('Actualizar Tarjeta');
			          tarjeta.accion = "Update";
          			$("#modal_NuevaTarjeta").modal("toggle");
              }
              else{
                  Swal.fire({
                      type: 'error',
                      title: 'ERROR',
                      text: 'Ocurrio un problema al registrar la tarjeta!',
                      timer: 3000
                  })
              }
              $("#inp_identificacion").val("");
          })
          .fail(function (e) {
              Swal.fire({
                  type: 'error',
                  title: 'ERROR',
                  text: 'Ocurrio un problema contacte al 2002-4040!',
                  timer: 3000
              })
          });
    }

    editTarjeta(id){
      tarjeta.id = id;
      tarjeta.Search_by_id(id);
    }
    deleteTarjeta(id){
      tarjeta.id = id;
      alert("AUN NO");
    }
}

let tarjeta = new Tarjeta();
