$(document).ready(function () {
    var global_menu = null;
    $('.list-modules-hide').hide()

    $(".list-modules").click(function () {
        var index_menu = $(this).data("index");
        var data_value = $(this).data("value");
        var data_image = $(this).data("image");

        var main_menu = sessionStorage.getItem('main_menu');
        var decrypt_main_menu = (main_menu) ? js_decrypt(main_menu, true) : null;

        var this_menu = decrypt_main_menu[index_menu];
        
        global_menu = this_menu; 

        var id_installation = this_menu['installation'][0]['id'];
        var _form_extends = generateRoom2(this_menu['moduleID'], false, id_installation, data_value, data_image);
        $("#modal-pemilihan-ruangan .modal-title").text("Pilih Ruangan " + data_value);
        $("#modal-pemilihan-ruangan .modal-body").html(_form_extends);

        $("#modal-pemilihan-ruangan").modal({
            keyboard: false,
            backdrop: 'static'
        });
    });

    window.generateRoom2 = function (idx, multiple, id_installation, data_value, data_image) {
        var module_id = (multiple) ? $(idx).data("module") : idx;
        var installation_id = (multiple) ? $(idx).data("installation") : id_installation;
        var installation_index = (multiple) ? $(idx).data("index") : 0;

        var rooms = global_menu['installation'][installation_index]['rooms'];
        
        var _cards_html = ''; 
        var _modal_chrome = ''; 

        if (rooms != undefined && rooms.length >= 1) {
            title = i18next.t("pilih_ruangan");
            layanan = i18next.t("Pilih Layanan ");
            var idx = 1;
            var list_active = true;
            
            $.each(rooms, function (id, val) {
                if (val['is_modul']) {
                    _cards_html += '<a href="#" class="menu-card" ' +
                        'data-module="' + module_id + '" ' +
                        'data-installation-index="' + installation_index + '" ' +
                        'data-installation="' + installation_id + '" ' +
                        'data-room="' + val["id"] + '" ' +
                        'data-room-index="' + id + '" ' +
                        'data-url="' + global_menu["url"] + '" ' +
                        'onclick="initModule(this)">' + 
                        
                        '<div class="icon-container">' + 
                            '<span class="icon">' +
                            '<img src="'+ data_image +'" style="height:35px;">' +
                            '</span>' +
                        '</div>' +
                        
                        '<div class="text-content">' + 
                            '<h7 class="no-margin text-semibold" style="font-size: 13px;color:#00008B;font-weight: bold;">' + val["name"] + '</h7>' +
                            '<p></p>' +
                        '</div>' +
                    '</a>';
                    idx++;
                };
            });

            if (list_active) {
                _modal_chrome += '<div class="item active">';
            } else {
                _modal_chrome += '<div class="item">';
            };

            _modal_chrome += '<div class="carousel-item cr-main module-selection">' + 
                    '<div class="popup-room-list">' +
                        _cards_html + 
                    '</div>' +
                '</div>' + 
                '</center>' +
                '</div>';

        } else {
            _modal_chrome += '<div class="col-md-12">';
            _modal_chrome += '<span>' + i18next.t("user_belum_di_assign_ruangan") + '</span>';
            _modal_chrome += '</div>';
        }

        var _form_extends = _modal_chrome;
        
        if (multiple) {
            _footer += '<div class="modal-footer back">';
            _footer += '<button type="button" class="btn bg-slate btn-sm" onclick="backInstallation()">' + i18next.t("kembali") + '</button>';
            _footer += '</div>';

            $(".modal-title").html(title)
            $(".installation").css("display", "none");
            $(".room").html(_form_extends);
            $('.modal-content').append(_footer);
        } else {
            return _form_extends
        }
    }

    window.backInstallation = function () {
        $(".modal-title").html(i18next.t("pilih_instalasi"))
        $(".installation").css("display", "block");
        $(".room").empty();
        $(".back").remove();
    }


    window.initModule = function (idx) {
        $.ajax({
            url: baseUrl + "site/set-method",
            type: "POST",
            data: {
                _csrf: $('meta[name="csrf-token"]').attr('content'),
                moduleID: $(idx).data("module"),
                instalasiIndex: $(idx).data("installation-index"),
                instalasiID: $(idx).data("installation"),
                roomIndex: $(idx).data("room-index"),
                roomID: $(idx).data("room"),
            },
            beforeSend: function () {
                $(idx).html('<i class="icon-spinner4 spinner position-center spinner_init"></i><br>' + i18next.t("memuat"));
            },
            success: function (data, status, xhr) {
                $('.modal-content').empty();
                $(".back").remove();

                var _html = '<div class="text-center">';
                _html += '<h3><i class="icon-spinner4 spinner position-center spinner_init"></i>&nbsp;&nbsp;<b>' + i18next.t("memuat") + ' . . . </b></h3>';
                _html += '</div>';

                $('.modal-content').html(_html);

                window.location.href = baseUrl + $(idx).data("url");
            }
        });
    }

    window.closeCarousel = function () {
        $("#newsimrsCaraousel").css('display', 'none');
        $(".carousel-control").css('display', 'none');
    }
    
    if ((draftSoap.rj != '0' && draftSoap.rj != null) || (draftSoap.rd != '0' && draftSoap.rd != null) || (draftSoap.ri != '0' && draftSoap.ri != null) || (draftRm.ri != '0' && draftRm.ri != null)) {
        draftSoap.rj = (draftSoap.rj == null) ? 0 : draftSoap.rj;
        draftSoap.rd = (draftSoap.rd == null) ? 0 : draftSoap.rd;
        draftSoap.ri = (draftSoap.ri == null) ? 0 : draftSoap.ri;
        draftRm.ri = (draftRm.ri == null) ? 0 : draftRm.ri;
        if (kelompokPegawai != 1) {
            $("#modal-draft-soap").modal('show', {
                keyboard: false,
                backdrop: 'static'
            })
        }
        $("#list-soap").hide()
        $("#list-rm").hide()
        $("#rd-soap").text("SOAP : " + draftSoap.rd)
        $("#rj-soap").text("SOAP : " + draftSoap.rj)
        $("#ri-soap").text("SOAP : " + draftSoap.ri)
        $("#ri-rm").text("Resume Medis : " + draftRm.ri)
        $(".draft-soap").bind('click', ({ currentTarget }) => {
            $(".draft-soap").removeClass('draft-soap--active')
            $(currentTarget).addClass('draft-soap--active')
            showLoader()
            $.ajax({
                url: `/allow/unfinished-soap?type=${$(currentTarget).data('type')}`,
                success: (res) => {
                    $("#list-soap tbody").html('')
                    if (res.soap.data.length == 0) {
                        $("#list-soap tbody").append(`
                            <tr>
                                <td class="text-center" colspan="5">No data available in table</td>
                            </tr>
                        `)
                    }
                    else {
                        res.soap.data.map((item, index) => {
                            $("#list-soap tbody").append(`
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td>${item.no_pendaftaran}</td>
                                    <td>${item.nama_pasien}</td>  
                                    <td>${(item.tgl_cppt != null) ? moment(item.tgl_cppt).format('DD/MM/YYYY HH:mm:ss', 'id') : ' - '}</td>
                                    <td>
                                        <a id="submit-anamnesa" href="${item.url}" class="btn btn-info btn-labeled btn-xs"><b><i class="fa fa-sign-in"></i></b>Input SOAP</a>
                                    </td>
                                </tr>
                            `)
                        })
                    }
                    $("#list-soap").show()

                    if ($(currentTarget).data('type') == "RI") {
                        $("#tabtable").show()
                        $("#list-rm tbody").html('')
                        if (res.rm.data.length == 0) {
                            $("#list-rm tbody").append(`
                                <tr>
                                    <td class="text-center" colspan="5">No data available in table</td>
                                </tr>
                            `)
                        }
                        else {
                            res.rm.data.map((item, index) => {
                                $("#list-rm tbody").append(`
                                    <tr>
                                        <td class="text-center">${index + 1}</td>
                                        <td>${item.no_pendaftaran}</td>
                                        <td>${item.nama_pasien}</td>
                                        <td>${moment(item.tgl_pendaftaran).format('DD/MM/YYYY HH:mm:ss', 'id')}</td>
                                        <td>
                                            <a id="submit-anamnesa" href="${item.url}" class="btn btn-info btn-labeled btn-xs"><b><i class="fa fa-sign-in"></i></b>Input Resume Medis</a>
                                        </td>
                                    </tr>
                                `)
                            })
                        }
                        if ($("#tab_soap").hasClass('active')) {
                            $("#list-soap").show()
                            $("#list-rm").hide()
                        }
                        else if ($("#tab_rm").hasClass('active')) {
                            $("#list-rm").show()
                            $("#list-soap").hide()
                        }
                    }
                    else {
                        $("#tabtable").hide()
                        $("#list-rm").hide()
                        $("#list-rm tbody").html('')
                    }
                }
            })
        })
        $("#tab_soap").bind('click', () => {
            $("#list-soap").show()
            $("#list-rm").hide()
        })
        $("#tab_rm").bind('click', () => {
            $("#list-rm").show()
            $("#list-soap").hide()
        })
    }
});

moment.locale('id')