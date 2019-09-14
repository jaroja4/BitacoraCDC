var Session = {
    state: undefined,
    Check() {
        Session.state = undefined
        $.ajax({
            type: "POST",
            url: "class/Usuario.php",
            data: {
                action: 'CheckSession',
                url: window.location.href
                // success: function(data) {
                //     return data;
                // }
            }
        })
            .done(function (e) {
                var data = JSON.parse(e);
                switch (data.status) {
                    case 'login':
                        $('.right_col').show();
                        Session.setUsername(data.username, data.nombre);
                        Session.setMenu(data.eventos);
                        Session.state = true;
                        // Session.sideBarDraw(data);
                        $(".main_container").removeAttr("style");
                        break;
                    case 'nocredencial':
                        $('.right_col').hide();
                        // Session.setUsername(data.username, data.nombre);
                        // Session.setMenu(data.eventos);
                        Session.state = false;
                        alert('El usuario no tiene credenciales para ver esta página.');
                        /*swal({
                            //
                            type: 'error',
                            title: 'El usuario no tiene credenciales para ver esta página.',
                            showConfirmButton: false,
                            timer: 3000
                        });*/                        
                        location.href = 'index.html';
                        break;
                    case 'invalido':
                        Session.state = false;
                        location.href = 'login.html';
                        break;
                }
            })
            .fail(function (e) {
                showError(e);
                location.href = 'login.html';
            });
    },
    setUsername(un, n) {
        $('#call_name').html("");
        $('#call_name').html(
            '<img src="images/CDC_Logo.png" alt="" > ' + n + ' ' +
            '<span class=" fa fa-angle-down" ></span> '
        );
        // $('#call_name').text(n);
        // bodega
        $('#call_userName').text("Menu Para " + un);

    },
    setMenu(eventos) {
        $('#menubox').html('');
        // menu segun permisos de usuario.
        $.each(eventos, function (i, item) {
            item.menu = item.menu.replace(/ /g, "_");
            item.modulo = item.modulo.replace(/ /g, "_");
            item.opcion  = item.opcion.replace(/ /g, "_");
            //Si no existe el modulo lo crea junto con sus Menu y Opción
            if (!$(`#${item.modulo}`).length) {
                //Agrega el Modulo
                Session.AgregaModulo(item.iconoModulo, item.modulo);
                //Agrega el Menu
                Session.AgregaMenu(item.modulo, item.iconoMenu, item.menu);
                //Agrega la opcion
                Session.AgregaOpcion(item.menu, item.url, item.opcion);
            }
            //Si no existe el Menu lo crea junto con sus Opción
            else if (!$(`#${item.menu}`).length) {
                //Agrega el Menu
                Session.AgregaMenu(item.modulo, item.iconoMenu, item.menu);
                //Agrega la opcion
                Session.AgregaOpcion(item.menu, item.url, item.opcion);
            }
            //Si ya existe el Modulo y el Menu solo agrega la opción
            else if (!$(`#${item.opcion}`).length) {
                //Agrega la opcion
                Session.AgregaOpcion(item.menu, item.url, item.opcion);
            }
        });

        if (typeof init_sidebar === "function")
            init_sidebar();
        else {
            setTimeout(function () {
                Session.setMenu(eventos);
            }, 500);
        }

    },
    AgregaModulo(i, m) {
        $('#menubox').append(`
            <li>
                <a>
                    <i class="${i}"></i> ${m.replace(/_/g, " ")}
                    <span class="fa fa-chevron-down"></span>
                </a>
                <ul id="${m}" class="nav child_menu">
                </ul>
            </li>
        `);
    },
    AgregaMenu(mo, ic, me) {
        $(`#${mo}`).append(`
            <li>
                <a>
                    <i class="${ic} fa-xs"></i> ${me.replace(/_/g, " ")} 
                    <span class="fa fa-chevron-down"></span>
                </a>
                <ul id="${me}" class="nav child_menu">
                </ul>
            </li>
            `);
    },
    AgregaOpcion(m, u, o) {
        $(`#${m}`).append(`
            <li class="sub_menu"><a href="${u}">${o.replace(/_/g, " ")}</a></li>
        `);
    },
    End() {
        $.ajax({
            type: "POST",
            url: "class/Usuario.php",
            data: {
                action: 'EndSession'
            }
        })
            .done(function (e) {
                location.href = 'login.html';
            })
            .fail(function (e) {
                showError(e);
                //location.href= 'login.html';
            });
    }







}