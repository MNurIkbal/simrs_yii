
var _dataArray = [];
var _listData = [];
var _konfigValidasi = [];
$(document).ready(function () {
    var savedSelected;
    localStorage.clear();
    const progress = $(".progress");
    $(document).ready(function() {
        $("#btn-cek-unduh-dokumen").attr("disabled", true);
        $("#btn-proses").attr("disabled", true);
        $('#cetak-berkas').attr('disabled', true);
        $("#btn-unduh-dokumen").attr("disabled", true);
        const formattedToday = moment().locale('en').format("DD-MMM-YYYY");
        const dateRange = `${formattedToday} - ${formattedToday}`;
        table = $("#example").docoTabel({
            filter: true,
            columnDefs: [{
                orderable: false,
                className: "select-checkbox",
                targets: 0
            }],
            select: {
                style: 'multi',
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollY: true,
            ajax: {
                url: `/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-data?advancedFilter[tgl_pendaftaran]=${encodeURIComponent(dateRange)}`,
            },
            stateSave: true,
            columns: [
                {
                     data: null,
                     searchable: false,
                     orderable: false,
                     defaultContent: "", // 0
                },
                {
                    data: "rowNum",
                    name : "rowNum",
                    searchable: false,
                    orderable: false // 1
                },
                {
                    title: "Tgl Masuk / Tgl Keluar",
                    data: "tgl_daftar_keluar",
                    name: "tgl_pendaftaran",
                    searchable: true,
                    orderable: true, // 2
                },
                {
                    title: "Pasien",
                    data: "pasien",
                    name : "nama_pasien",
                    searchable: true,
                    orderable: true, // 3
                },
                {
                    title: "Instalasi / Ruangan",
                    data: "instalasi_ruangan",
                    name: "instalasi_nama",
                    searchable: false,
                    orderable: true,  // 4
                },
                {
                    title: "Cara Bayar / Penjamin",
                    data: "carabayar_penjamin",
                    name : "carabayar_nama",
                    searchable: false,
                    orderable: true, // 5
                },
                {
                    title: "Dokter Penanggung<br> Jawab",
                    data: "dokter_dpjp",
                    name: "dokter_dpjp",
                    searchable: false,
                    orderable: false, // 6
                },
                {
                    title: "Total Tagihan RS",
                    data: "tarif_rs",
                    name: "tarif_rs",
                    searchable: false,
                    orderable: false, // 7
                },
                {
                    title: "Total Klaim BPJS",
                    data: "plafon",
                    name: "plafon",
                    searchable: false,
                    orderable: false, // 8
                },
                {
                    title: "Status",
                    data: "status",
                    name : "status_verifikasi",
                    searchable: true,
                    orderable: true, // 9
                },
                {
                    title: "Ruangan",
                    data: "ruangan_nama",
                    name: "ruangan_id",
                    visible: false // 10
                },
                {
                    title: "No. Rekam Medik",
                    data: "no_rekammedik",
                    name: "no_rekammedik",
                    visible: false // 11
                },
                {
                    title: "Tanggal Pulang",
                    data: "tglpasienpulang",
                    name: "tgl_pulang",
                    visible: false // 12
                },
                {
                    title: "No Registrasi",
                    data: "no_pendaftaran",
                    visible: false // 13
                },
            ],
            drawCallback: function(e) {
                var api = this.api();
                let selected = localStorage.getItem('kunjungan_id');
                var data = JSON.parse(selected);

                for (var i = 0; api.rows().count() > i; i++) {
                    var rowData = api.row(i).data();
                    var rowNode = api.row(i).node();
                    
                    $.each(data, function (k, v) {
                        if(rowData.kunjungan_id == v){
                            api.row(i).node().setAttribute("data-id",rowData.kunjungan_id)
                            api.row(i).select();
                        }
                    })
        
                    if(rowData.nosep == null ) {
                        $(rowNode).removeClass("final-klaim");
                        $(rowNode).removeClass("proses-klaim");
                        $(rowNode).removeClass("sudah-koreksi");
                        $(rowNode).removeClass("belum-koreksi");
                        $(rowNode).addClass("belum-ada-sep");
                    }else{
                        if(rowData.status_kunjungan == 549) {
                            $(rowNode).removeClass("final-klaim");
                            $(rowNode).removeClass("proses-klaim");
                            $(rowNode).removeClass("sudah-koreksi");
                            $(rowNode).addClass("belum-koreksi");
                        }
                        else if(rowData.status_kunjungan == 550){
                            $(rowNode).removeClass("final-klaim");
                            $(rowNode).removeClass("proses-klaim");
                            $(rowNode).removeClass("belum-koreksi");
                            $(rowNode).addClass("sudah-koreksi");
                        } else if(rowData.status_kunjungan == 556){
                            $(rowNode).removeClass("final-klaim");
                            $(rowNode).removeClass("sudah-koreksi");
                            $(rowNode).removeClass("belum-koreksi");
                            $(rowNode).addClass("proses-klaim");
                        } else if(rowData.status_kunjungan == 551){
                            $(rowNode).removeClass("proses-klaim");
                            $(rowNode).removeClass("sudah-koreksi");
                            $(rowNode).removeClass("belum-koreksi");
                            $(rowNode).addClass("final-klaim");
                        }
                    }
                }
            },
            formFilters: [
                {
                    fieldName: "tgl_pendaftaran",
                    label: "Tanggal Masuk",
                    type: {
                       name: "rangeDate",
                    }
                },
                {
                    fieldName: "tgl_pulang",
                    label: "Tanggal Keluar",
                    type: {
                        name: "rangeDate",
                        payload: {
                            allDate: true,
                        }
                    }
                },
                "no_pendaftaran",
                {
                    fieldName: "nama_pasien",
                    label: "Nama Pasien"
                },
                {
                    fieldName: "no_rekamedik",
                    label: "No.Rekam Medik"
                },
                {
                    fieldName: "instalasi_pasien",
                    label: "Instalasi",
                    type: {
                        name: "select",
                        payload: instalasiStatus
                    }
                },
                {
                    fieldName: "ruangan_nama",
                    label: "Ruangan",
                    type: {
                        name: "select",
                        payload: []
                    }
                },
                {
                    fieldName: "status_kunjungan",
                    label: "Status",
                    type: {
                        name: "selectMultiple",
                        payload: statusOptions,
                        // url: "/penjamin-asuransi/informasi-pasien-ranap-bpjs/filters",
                        // additionalPayload: {
                        //     type: "status",
                        // }
                    }
                },
                {
                    fieldName: "cara_bayar",
                    label: "Cara bayar",
                    type: {
                        name: "dropdownScroll",
                        url: "/penjamin-asuransi/informasi-pasien-ranap-bpjs/filters",
                        additionalPayload: {
                            type: "carabayar",
                        }
                    }
                },
                {
                    fieldName: "penjamin_kode",
                    label: "Penjamin",
                    type: {
                        name: "select",
                    }
                },
                {
                    fieldName: "dokter_nama",
                    label: "Dokter DPJP",
                },
                {
                    fieldName: "no_pembayaran",
                    label: "No. Pembayaran",
                },
                {
                    fieldName: "status_unduh_dokumen",
                    label: "Unduh Dokumen",
                    type: {
                        name: "select",
                        payload: [
                            {
                                id: belum_unduhDokumen,
                                text: "Belum Unduh Dokumen"
                            },
                            {
                                id: onProgres_unduhDokumen,
                                text: "Proses Unduh Dokumen"
                            },
                            {
                                id: selesai_unduhDokumen,
                                text: "Selesai Unduh Dokumen"
                            }
                        ]
                    }
                },
            ],
            filterRendered: (wrapper) => {
                $(wrapper).find('[name="tempat_instalasi"]').val($('[name="tempat_instalasi"] option:eq(1)').val()).trigger('change')
            
                $(wrapper).find('[name="instalasi_pasien"]').bind('change', ({ currentTarget }) => {
                    if ($(currentTarget).val() == '' || $(currentTarget).val() == null) {
                        var data = [
                            {
                                id: '',
                                text: '-Semua-'
                            }
                        ]
                        $(wrapper).find('[name="ruangan_nama"]').html('')
                        $(wrapper).find('[name="ruangan_nama"]').select2({
                            data,
                            placholder: "-- Pilih Instalasi --"
                        })
                    } else {
                        $.ajax({
                            url: `/penjamin-asuransi/informasi-pasien-ranap-bpjs/filters`,
                            data: {
                                type: "ruangan",
                                case: $(currentTarget).val()
                            },
                            success: (res) => {
                                var data = [
                                    {
                                        id: '',
                                        text: '-Semua-'
                                    }
                                ]
                                data = data.concat(res?.data)
                                $(wrapper).find('[name="ruangan_nama"]').html('')
                                $(wrapper).find('[name="ruangan_nama"]').select2({
                                    data,
                                })
                            },
                            error: (xhr, status, error) =>{
                                var err = eval("(" + xhr.responseText + ")");
                                console.log((err.Message))
                            }
                        })
                    }
                })

                $(wrapper).find('[name="cara_bayar"]').bind('change', ({ currentTarget }) => {
                    if ($(currentTarget).val() == '' || $(currentTarget).val() == null) {
                        var data = [
                            {
                                id: '',
                                text: '-Semua-'
                            }
                        ]
                        $(wrapper).find('[name="penjamin_kode"]').html('')
                        $(wrapper).find('[name="penjamin_kode"]').select2({
                            data,
                        })
                    } else {
                        $.ajax({
                            url: `/penjamin-asuransi/informasi-pasien-ranap-bpjs/filters`,
                            data: {
                                type: "penjamin",
                                case: $(currentTarget).val()
                            },
                            success: (res) => {
                                var data = [
                                    {
                                        id: '',
                                        text: '-Semua-'
                                    }
                                ]
                                data = data.concat(res?.data)
                                $(wrapper).find('[name="penjamin_kode"]').html('')
                                $(wrapper).find('[name="penjamin_kode"]').select2({
                                    data,
                                })
                            },
                            error: (xhr, status, error) =>{
                                var err = eval("(" + xhr.responseText + ")");
                                console.log((err.Message))
                            }
                        })
                    }
                })
            },
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if(aData.status_unduh_dokumen == onProgres_unduhDokumen) {
                    $('td:eq(3)', nRow).css('background-color', '#bcbcbc');
                }
                if(aData.status_unduh_dokumen == selesai_unduhDokumen) {
                    $('td:eq(3)', nRow).css('background-color', '#f29ef5');
                }
            },
            // stateSaveParams: function(settings, data) {
            //     data.selected = this.api().rows({selected: true})[0];
            // },
            // stateLoadParams: function(settings, data){
            //     savedSelected = data.selected;  
            // },
            initComplete: function(){
                let selected = localStorage.getItem('kunjungan_id');
                var data = JSON.parse(selected);
                // this.api().rows(savedSelected).select();
                // this.api().state.save();
                // this.api().rows(data).select()
            }
        });
        $(".dataTables_filter").hide();
        dateRangeHelper(".dateStart1",".dateEnd1",".dateTarget1");
        dateRangeHelper(".dateStart2",".dateEnd2",".dateTarget2");

        $(document).on("keyup", '#form-filter__example', function(e) {
            e.preventDefault();
            if (e.key == "Enter") {
                $('#search-button').trigger("click")
            }
        })
        
    });

    // validationDataUnduh.map((v) => {
    //     _konfigValidasi.push(v)
    // })

    $(document).on('click', '#example tr', function(){
        var _dataSelect = [];
        var local = localStorage.getItem('kunjungan_id');
        if(_dataSelect.length == 0){
            local = JSON.parse(local)
            $.each(local, function (indexInArray, valueOfElement) { 
                 if(! _dataSelect.includes(valueOfElement)){   
                    _dataSelect.push(valueOfElement)
                }  
            });

        }

        var _data = table.rows('.selected').data();
        var total_checklist = _data.length;
        var hasil = 0;

        if($(this).attr('data-id') != undefined) {
            var attributeId = $(this).attr('data-id') 
            var newData =  [];
            _dataSelect = _dataSelect.map((value) => {
                if(value != attributeId) {
                    newData.push(value)
                } else {
                    $(this).removeAttr("data-id")
                }
            })

            _dataSelect = newData
        }

        for (var i = 0; i < _data.length; i++) {
            if(typeof _data[i].kunjungan_id != "undefined") {
                if(! _dataSelect.includes(_data[i].kunjungan_id)) {
                    _dataSelect.push(_data[i].kunjungan_id)
                    $(this).attr('data-id', _data[i].kunjungan_id)
                }
            }
        }

        _listData = _dataSelect;
        localStorage.setItem('kunjungan_id', JSON.stringify(_dataSelect));

        $.each(_data, function (k, v) {
            if (typeof _data[k] !== 'undefined') {
                if(v['status_kunjungan']) {
                    true;
                } else {
                    hasil++;
                }
            }
        })

        if(typeof _data !== 'undefined'){
            if(total_checklist == 1 && _data['0']['status_unduh_dokumen'] == selesai_unduhDokumen) {
                $('#btn-cek-unduh-dokumen').prop("disabled", false);
            }else{
                $('#btn-cek-unduh-dokumen').prop("disabled", true);
            }
        }

        console.log(hasil)
        if(typeof _data !== 'undefined' && hasil > 0){
            $('#btn-unduh-dokumen').prop("disabled", true);
        }else{
            if(total_checklist == 0) {
                $('#btn-unduh-dokumen').prop("disabled", true);
            }else{
                $('#btn-unduh-dokumen').prop("disabled", false);
            }
        }

        if(typeof _data !== 'undefined') {
            kunjungan = _data[0]
            if(total_checklist == 1 && kunjungan.status_kunjungan == 551) {
                $('#cetak-berkas').prop("disabled", false);
            }else{
                $('#cetak-berkas').prop("disabled", true);
            }
        } 

        if(typeof _data !== 'undefined' && total_checklist > 0){
            _data = _data[0];

            if(total_checklist == 1) {
                $('.btn-proses').prop("disabled", false);
            } else {
                $('.btn-proses').prop("disabled", true);
            }
        }
    });

    $(document).on('click','#btn-unduh-dokumen',function(){
        confirmationDialog(`Terdapat ${_listData.length} data yang di checklist. Apakah anda yakin untuk mengunduh dokumen?`, function (condition) {
            if (condition) {
                $.ajax({
                    method: 'POST',
                    data: {
                        data: _listData
                    },
                    url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/unduh-dokumen',
                    success: function(data) {
                        docoNotification("success", "Proses Berhasil", data.text);
                        localStorage.clear();
                        $('#btn-unduh-dokumen').prop("disabled", true);
                        table.ajax.reload();
                        $('#check-all').prop('checked', false)
                    }
                });
            } else {
                return false;
            }
        });
    });

    $(document).on("click", "#btn-sync", function (event) {
        event.preventDefault();

        var header = "Perhatian!";
        var message = "Apakah anda yakin untuk melakukan sinkronisasi?";
        var label = {
            buttons: {
                "Yes": "button-yes",
                "No": "button-no"
            },
        };

        $.showQuestionDialog(header, message, label, function (reaction) {
            if (reaction == "Yes") {
                $.ajax({
                    url : "/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-random-string",
                    success : function (data) {
                        var randString = data
                        if(randString !== null || randString != "") {
                            $("#modal_progress").modal("toggle");
                            updateProgressBarSinkron(randString)
                        }
                    },
                    error: function (data) {
                        let err = (data.responseJSON.response.error != undefined) ? data.responseJSON.response.error : ""
                        let msg = (data.responseJSON.response.message != undefined) ? data.responseJSON.response.message : ""
                        docoNotification("error", "Terjadi Kesalahan", msg +"<br>"+ err);
                    }
                });
            } else {
                hideQuestionDialog();
            }
        });
    });

    $('#btn-search').on('click', function(e) {
        e.preventDefault()
        sinkronOnSearch()
    })
    progress.css("display", "none")

    const setPresentase = function(progress) {
        $(".progress .label-persentase").html(progress)
        $(".progress .progress-bar").css("width", progress +"%")
        .attr("aria-valuenow", progress)
        .attr("aria-volume", progress);
    }


    const showInfo = () => {
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve($(".populate-data").html(`mempersiapkan data ...`))
            }, 1000);
            setTimeout(() => {
                resolve($(".populate-data").css("display", "none"))
                resolve(progress.css("display", "block"))
                resolve($(".label-progress").html(`<p style="font-size:16px;font-weight:bold;"> menyiapkan data ... </p>`))
            }, 2000);
        })
    }

    async function updateProgressBarSinkron(randString) {
        let config = await $.getJSON("./../../json/setup.json")
        if (config.origin == "true") {
            var socket = io.connect(window.location.origin);
        } else {
            var socket = io.connect(config.ip+':'+config.port);
        }
    
        const channel = `export-excel:`
        await showInfo()
        $.ajax({
            url : '/penjamin-asuransi/informasi-pasien-ranap-bpjs/sinkron?randString=' + randString,
            success : function (data) {
                let startNum = 5
                setPresentase(startNum)
                let totalProgres = parseInt(startNum) + parseInt(data.totalPerPage) + 20;
                $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Sinkronisasi sedang berjalan </p>`);
                socket.on(channel + data.randString, (message) => {
                    const _data = $.parseJSON(message);
                    const { status , messageProcess , progress} = _data
                    if(status == 'finish') {
                        $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                        setPresentase(progress)
                        if (progress == 100) {
                            table.draw();
                            docoNotification("success", "Proses Berhasil", "Data Berhasil Tersinkronisasi");
                            $('#modal_progress').modal('hide');
                        }
                    } else if (status == 'finish') {
                        docoNotification('error','Proses Gagal!', messageProcess)
                    } else {
                        startNum++
                        setPresentase(Math.ceil((startNum/totalProgres) * 100))
                        $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Sinkronisasi sedang berjalan  </p>`)
                    }
                });
    
            }
        });
    }

    function sinkronOnSearch() {
        $.ajax({
            url : '/penjamin-asuransi/informasi-pasien-ranap-bpjs/sinkron?randString=1111',
            success : function (data) {
                console.log(data)
            }
        });
    }
    $(document).on("click", ".btn-reset", function (e) {
        localStorage.clear();
        const tableId = "example";
        const element = $(`#filter-section__${tableId}`)
        const formWrapper = $(`#form-filter__${tableId}`)
        element.find('input').val('')
        element.find('select').val(null).trigger('change')
        element.find('#tgl_pendaftaran-startDate').val(moment().locale('en').format("DD-MMM-YYYY")).trigger("change");
        element.find('#tgl_pendaftaran-endDate').val(moment().locale('en').format("DD-MMM-YYYY")).trigger("change");
        const tableElement = $(`#${tableId}`).DataTable()
        tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
        tableElement.ajax.reload()
     });

    $('#check-all').on('click', function () {
        if ($('#check-all:checked').val() === 'on') {            
            $.ajax({
                url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-data?checkAll=true',
                method: 'GET',
                data: $("#form-filter__example").serializeArray(),
                success: (res) => {
                    localStorage.setItem('kunjungan_id', JSON.stringify(res.data));
                    table.ajax.reload()
                    $('#btn-unduh-dokumen').prop("disabled", false);
                    _listData = res.data
                },
            })
        } else {
            localStorage.setItem('kunjungan_id', JSON.stringify([]));
            table.ajax.reload()
            $('#btn-unduh-dokumen').prop("disabled", true);
            _listData = []
        }
    });
});