// Global Var
var table;
var state_key = window.location.pathname;

// Event click
$(document).on("click", ".data-reset", function () {
    disabledAllButton();
});

// Event click
$(document).on("click", "#table-pasien-rad tbody tr", function () {
    // Try catch
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
        statusPeriksaId = table.row(".selected").data().status_periksa_id ? table.row(".selected").data().status_periksa_id : null;
        caraBayar = table.row(".selected").data().carabayar_nama ? table.row(".selected").data().carabayar_nama : null;
        isBayar = table.row(".selected").data().is_bayar ? table.row(".selected").data().is_bayar : null;
        groupCarBay = table.row(".selected").data().groupcarabayar_id ? table.row(".selected").data().groupcarabayar_id : null;
        pegawai_id = table.row(".selected").data().pegawai_id ? table.row(".selected").data().pegawai_id : null;
        tarifId = table.row(".selected").data().tindakanpelayanan_id ? table.row(".selected").data().tindakanpelayanan_id : null;
        daftarId = table.row(".selected").data().daftartindakan_id ? table.row(".selected").data().daftartindakan_id : null;
        status_bayar = table.row(".selected").data().status_bayar ? table.row(".selected").data().status_bayar : null;
        hasilpemeriksaanrad_id = table.row(".selected").data().hasilpemeriksaanrad_id ? table.row(".selected").data().hasilpemeriksaanrad_id : null;
        status_batal = table.row(".selected").data().status_batal ? table.row(".selected").data().status_batal : null;
        sepesial_pemeriksaan = table.row(".selected").data().sepesial_pemeriksaan ? table.row(".selected").data().sepesial_pemeriksaan : null;
        hasilpemeriksaanrad_id = table.row(".selected").data().hasilpemeriksaanrad_id ? table.row(".selected").data().hasilpemeriksaanrad_id : null;
    } catch (e) {
        // Make it false
        primaryKey = false;
    }

    $("#btn-approve").attr("data-target", approveUrl + primaryKey);
    $("#btn-batal").attr("data-target", batalUrl + primaryKey);
    $("#cetak-tagihan").attr("data-target", cetakUrl + primaryKey);
    $("#btn-cetak").attr("data-target", cetakPemerikssan + primaryKey);

    // Check class selected
    if ($('#table-pasien-rad tr.selected').length == 0) {
        disabledAllButton();
        $("#btn-batal-periksa").prop("disabled", true);
    } else {
        $("#btn-approve").prop("disabled", false);
        $("#btn-batal").prop("disabled", false);
        $("#btn-batal").attr("data-options", 'link');
        $("#cetak-tagihan").prop("disabled", false);
        $("#btn-ubah").prop("disabled", false);
        $("#btn-speciment").prop("disabled", false);
        $("#btn-hasil").prop("disabled", false);
        $("#cetak-label-luar").prop("disabled", true);
        $("#btn-batal-periksa").prop("disabled", true);
        // $("#btn-cetak-pemeriksaan").prop("disabled", true);

        if ((groupCarBay == 417 && status_bayar == null && !sepesial_pemeriksaan) || status_batal) {
            $("#btn-hasil").prop("disabled", true);
        }

        if (hasilpemeriksaanrad_id && (statusPeriksaId == statusPeriksa || statusPeriksaId == statusSelesai)) {
            $("#btn-cetak").prop("disabled", false);
            // $("#btn-cetak-pemeriksaan").prop("disabled", false);
        }
        else {
            $("#btn-cetak").prop("disabled", true);
            // $("#btn-cetak-pemeriksaan").prop("disabled", true);
        }

        if (isBayar) {
            $("#btn-approve").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-error-message", "Tidak Bisa membatalkan karena sudah melakukan pembayaran");
        }

        if (statusPeriksaId == statusPeriksa) {
            $("#btn-ubah").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-error-message", "Tidak Bisa membatalkan karena sudah input hsil pemeriksaan");
        }

        if (statusPeriksaId == statusBatal) {
            $("#btn-batal").prop("disabled", true);
            $("#btn-ubah").prop("disabled", true);
            $("#btn-speciment").prop("disabled", true);
            $("#btn-hasil").prop("disabled", true);
            $("#btn-cetak").prop("disabled", true);
            $("#cetak-tagihan").prop("disabled", true);
            // $("#btn-cetak-pemeriksaan").prop("disabled", true);
        }

        if (statusPeriksaId == statusSelesai) {
            $("#btn-batal").prop("disabled", true);
            $("#btn-ubah").prop("disabled", true);
        }

        if (tarifId && daftarId) {
            $("#cetak-label-luar").prop("disabled", false);
        }

        if (pegawai_id != null) {
            var _url = `/radiologi/hasil-rad/index?id=${primaryKey}&pelayananId=${tarifId}&tindakanId=${daftarId}`;
            $("#btn-hasil").attr("data-options", "link");
            $("#btn-hasil").attr("data-target", _url);
            $("#btn-hasil").removeAttr("data-url");
        }

        if (status_batal) {
            $("#btn-batal-periksa").prop("disabled", true);
            $("#cetak-tagihan").prop("disabled", true);
        } else {
            if (hasilpemeriksaanrad_id == null && !status_bayar) {
                $("#btn-batal-periksa").prop("disabled", false);
            }
        }
    }
});

$("#btn-batal").on('click', function () {
    if ($("#btn-batal").attr("data-options") == "click") {
        docoNotification('error', "Gagal Melakukan Batal", $("#btn-batal").attr("data-error-message"));
    }
});

$("#btn-cari").on('click', function () {
    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
    $("#cetak-tagihan").prop("disabled", true);

    $("#btn-ubah").prop("disabled", true);
    $("#btn-speciment").prop("disabled", true);
    $("#btn-hasil").prop("disabled", true);
    $("#btn-cetak").prop("disabled", true);
});

// Event Ready
$(document).ready(function () {
    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
    $("#cetak-tagihan").prop("disabled", true);
    $("#btn-ubah").prop("disabled", true);
    $("#btn-speciment").prop("disabled", true);
    $("#btn-hasil").prop("disabled", true);
    $("#btn-batal-periksa").prop("disabled", true);
    // $("#btn-cetak-pemeriksaan").prop("disabled", true);

    // Generate Table
    table = $('#table-pasien-rad').docoTabel({
        info: false,
        filter: true,
        //add for handle checkbox
        columnDefs: [{
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }],
        select: {
            style: 'os',
            selector: 'tr'
        },
        sorting: [
            [4, 'desc']
        ],
        displayLength: 50,
        processing: true,
        serverSide: true,
        stateSave: true,
        stateDuration: -1,
        ajax: baseUrl + 'radiologi/informasi-pasien-rad/get-data',
        columns: [
            {
                //0
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
            {
                //1
                title: no, data: "rowNum", searchable: false, orderable: false 
            },
            {
                //2
                title: no_antrian,
                data: 'no_antrian',
                name: 'no_antrian',
                searchable: false,
                visible: false,

            },
            {
                //3
                title: 'Status Periksa',
                data: 'status_periksa_btn',
                name: 'status_periksa'
            },
            {
                //4
                title: tanggal_pendaftaran,
                data: 'tglmasukpenunjang'
            },
            {
                //4
                title: 'Detail Diagnosa',
                data: 'detail_diagnosa'
            },
            {
                //5
                title: 'Tanggal Lahir',
                data: 'tanggal_lahir',
                visible: false
            },
            {
                //6
                title: 'No Rekam Medis',
                data: 'no_rekam_medik',
                searchable: true,
                visible: false
            },
            {
                //7
                title: "Pasien",
                data: 'nama_pasien',
                render: (data, rowElement, rowData) => {
                    return `<p style="margin-bottom: 2px">
                        <b>${rowData.nama_pasien} (${rowData.jk})</b>
                            ${rowData.tanggal_lahir != null ? ` <p style="margin-bottom: 2px"> ${rowData.tanggal_lahir}</p>` : ' - '}
                        </p>

                        <p> ${rowData.no_pendaftaran} / ${rowData.no_rekam_medik}</p>
                        `
                }
            },
            {
                //8
                title: "No. Pendaftaran",
                data: 'no_pendaftaran',
                visible: false
            },
            {
                //9
                title: "Nama Pemeriksaan",
                data: 'daftartindakan_nama'
            },
            {
                //10
                title: dokter,
                data: 'pegawai_id',
                render: (columnData, row, data) => {
                    return data.pegawai_id != null && !data.is_mcu && data.is_update_doctor && !data.tgl_verifikasi ? `<select name="dokter" data-no_masukpenunjang="${data.no_masukpenunjang}" data-daftartindakan_id="${data.daftartindakan_id}"><option value="${data.pegawai_id}">${data.dokter_penunjang}</option></select>` : (data.pegawai_id != '') ? data.dokter_penunjang : '-'
                }
            },
            {
                //11
                title: cara_bayar,
                data: 'carabayar_nama',
                render: (data, rowElement, rowData) => {
                    return `
                        <p> ${rowData.carabayar_nama} / ${rowData.penjamin_nama}</p>
                        `
                }
            },
            // {
            //     //12
            //     title: no_rad,
            //     data: 'no_masukpenunjang'
            // },
            // {
            //     //13
            //     title: 'Asal Rujukan',  //update to asalrujukan_nama
            //     data: 'asalrujukan_nama',
            //     name: 'asalrujukan_nama',
            //     visible: true
            // },
            {
                //14
                title: 'Ruangan Asal',
                data: 'rujukandari_nama',
                name: 'rujukandari_nama',
                visible: true,
                searchable: false
            },
            {
                //15
                title: 'Ruangan Asal', //update to rujukandari_id
                data: 'rujukandari_nama',
                name: 'rujukandari_nama',
                visible: false
            },
            {
                //16
                title: 'Status Bayar',
                data: 'status_bayar_btn',
                searchable: false,
                orderable: false,
            },
            {
                //17
                title: 'Status Expertise',
                data: 'tgl_hasilrad',
                visible: false,
                searchable: true,
            }
        ],
        drawCallback: () => {
            $("select[name='dokter']").select2InfinityScroll({
                url: '/radiologi/informasi-pasien-rad/doctors'
            })
            $("select[name='dokter']").on('change', ({ currentTarget }) => {
                if ($(currentTarget).val() != '') {
                    const { no_masukpenunjang, daftartindakan_id } = $(currentTarget).data()
                    $.ajax({
                        url: '/radiologi/informasi-pasien-rad/update-doctor',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            no_masukpenunjang,
                            daftartindakan_id,
                            pegawai_id: $(currentTarget).val()
                        })
                    })
                }
            })
        },
        rowCallback: function (row, data) {
            if (data.cyto_tindakan == true) {
                $('td:eq(4)', row).css({ "background-color": "#ff8900", "color": "#ffffff" });
            }
            if (data.is_referred == true) {
                $('td:eq(5)', row).css({ "background-color": "#FFC300", "color": "#000000" });
            }
        },
        stateSaveCallback: function (settings, data) {
            localStorage.setItem( "DataTables_" + settings.sInstance, JSON.stringify(data) )
        },
        stateLoadCallback: function(settings) {
            return JSON.parse( localStorage.getItem( "DataTables_" + settings.sInstance ) )
        },
    });
    $('.dataTables_filter').hide();
    $('.filter-form').datatableBootstrapFilter(table, [
        [4, filterTanggalPendaftaran],
        [5, filterTanggalLahir],
        [11, `<select name='searchDoctor' id='searchDoctor' class = "select2"></select>`],
        [12, dropdownCaraBayar],
        [14, `<select name='searchRuangan' id='searchRuangan' class = "select2"></select>`],
        [3, dropdownStatus],
        // [13, asal_1],


        // // [13, `<select name='searchInstalasi'></select>`],
        [15, dropdownStatusExpert]
    ], {
        4:0,
        8:1,
        7:2,
        10:3,
        9:4,
        6:5,
        11:6,
        12:7,
        13:8,
        14:9,
        15:10,
    }, true);

    $("select[name='searchDoctor']").select2InfinityScroll({
        url: '/radiologi/informasi-pasien-rad/doctors',
    })
    $("select[name='searchInstalasi']").select2InfinityScroll({
        url: '/radiologi/informasi-pasien-rad/dropdown',
        callbackData: (params) => {
            return {
                payload: {
                    term: params.term,
                    page: params.page || 1,
                    limit: params.limit,
                    type: 'instalasi'
                }
            }
        }
    })
    $("select[name='searchRuangan']").select2InfinityScroll({
        url: '/radiologi/informasi-pasien-rad/dropdown',
        callbackData: (params) => {
            return {
                payload: {
                    term: params.term,
                    page: params.page || 1,
                    limit: params.limit,
                    type: 'ruanganrujukandari',
                    // dependentId: {
                    //     instalasi_id: $("select[name='searchInstalasi']").val()
                    // }
                }
            }
        }
    })
    // $("select[name='searchRuangan']").prop('disabled', true)
    // $("select[name='searchInstalasi']").bind('change', ({ currentTarget }) => {
    //         $("select[name='searchRuangan']").prop('disabled', $(currentTarget).val() == '' || $(currentTarget).val() == null)
    //         $("select[name='searchRuangan']").val(null).trigger('change')
    //     })
    // Generate Table

    dateRangeHelper(".rangeLahirStart", ".rangeLahirFinish", ".targetDateLahir");
    dateRangeHelper(".startDate", ".endDate", ".targetDate");

    $(".daterange-basic").daterangepicker({
        startDate: '<?=(date("01-M-Y"))?>',
        autoUpdateInput: true,
        endDate: '<?=(date("d-M-Y"))?>',
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

    // select change asalrujukan_1
    // $("#asal_rujukan_1").change(function() {
    //     $("#asal_rujukan_2").find('option').remove();
    //     // $("#asal_rujukan_3").find('option').remove();
    //     var asal_1 = $("#asal_rujukan_1").val();
    //     var html = '<option value=""> --- Silahkan Pilih --- </option>';
    //     $.getJSON(baseUrl + "radiologi/informasi-pasien-rad/get-asal-rujukan2", { asal_1: asal_1 }, function(result) {
    //         $.each(result.results, function(i, field) {
    //             html += '<option value="' + field.id + '"> ' + field.text + ' </option>';
    //         });
    //         // console.log(html);

    //         $("#asal_rujukan_2").html(html);
    //     });
    // });
    $("#asal_rujukan_2").change(function () {
        $("#asal_rujukan_3").find('option').remove();
        var asal_1 = $("#asal_rujukan_1").val();
        var asal_2 = $("#asal_rujukan_2").val();
        var html = '<option value=""> --- Silahkan Pilih --- </option>';
        $.getJSON(baseUrl + "radiologi/informasi-pasien-rad/get-asal-rujukan3", {
            asal_1: asal_1,
            asal_2: asal_2
        }, function (result) {
            $.each(result.results, function (i, field) {
                html += '<option value="' + field.id + '"> ' + field.text + ' </option>';
            });
            // console.log(html);
            $("#asal_rujukan_3").html(html);
        });
    });
    // select change asalrujukan_1

    // panggil antrian
    // Modal
    $(document).on("click", ".antrian", function (event) {
        // Pendaftaran id
        // var id = $("#temp-pendaftaran").val($(this).data("id"));
        const id_pendaftaran = $(this).attr("data-id");
        const no_antrian = $(this).attr("data-antrian");
        const dataPost = {
            no_antrian: no_antrian
        };
        $.ajax({
            url: "/radiologi/informasi-pasien-rad/panggil-antrian",
            data: dataPost,
            type: "post",
            success: function (res) {
                if (typeof res.teks_panggil !== "undefined") {
                    let text = res.teks_panggil;
                    let player = $("#playerAudio");
                    let arrayText = text.split(" ");
                    arrayText.push("stop");
                    arrayText = arrayText.filter(Boolean);

                    let index = 0;

                    player[0].defaultPlaybackRate = 1;
                    player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
                    player[0].play();

                    player[0].addEventListener("ended", function () {
                        index = index + 1;

                        if (index < arrayText.length) {
                            player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
                            if (arrayText[index] == "stop") {
                                // hapusAntrianAudio();
                            } else {
                                player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
                                player[0].play();
                            }
                        }
                    });
                }
            }
        });

    });
    // panggil antrian

    //Batal Pemeriksaan
    $("#btn-batal-periksa").click(function () {
        let tableData = table.row(".selected").data();
        if (typeof tableData !== 'undefined') {
            if (tableData.tindakanpelayanan_id === '') {
                //hanya apabila ada pemeriksaan atau ada tindakan saja di datatables
                docoNotification('error', i18next.t("Perhatian"), "Nama Pemeriksaan belum ada pada data ini");
            } else {
                const dataPost = {
                    no_pendaftaran: tableData.no_pendaftaran,
                    ruangan_id: tableData.ruangan_id,
                    tindakanpelayanan_id: tableData.tindakanpelayanan_id,
                    no_masukpenunjang: tableData.no_masukpenunjang
                };

                const link = '/radiologi/informasi-pasien-rad/batal-periksa';
                $(this).docoForm("click", {
                    url: link,
                    data: dataPost,
                    confirmMessage: i18next.t("Apakah anda yakin untuk membatalkan data ini ? "),
                    success: function (res) {
                        if (res.meta.result === 'success') {
                            $("#btn-batal-periksa").prop("disabled", true);
                            table.draw()
                        }

                    }
                });
            }
        }
    })

    $('.legend-information').css('cursor', 'pointer');
    $('.legend-information').each(function (params) {
        var _id = $(this).attr("id");
        $(document).on('click', "#" + _id, function () {
            var _type = $(this).attr("data-type");
            const tableElement = $(`#table-pasien-rad `).DataTable()
            showLoader();
            $('.legend-information').css('border', '1px solid #dddddd');
            if ($(this).attr("selected-filter") == "true") {
                $('#filter_kelompok_2').prop('disabled', false);
                _type = 0;
                $(this).attr("selected-filter", false);
            } else {
                $("#" + _id).css('border', '2px solid #2ca38b');
                $('.legend-information').attr("selected-filter", false);
                $(this).attr("selected-filter", true);
                $('#filter_kelompok_2').prop('disabled', true);
            }
            tableElement.ajax.url("/radiologi/informasi-pasien-rad/get-data?type=" + _type).load()
        })
    })

});

function disabledAllButton() {
    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
    $("#cetak-tagihan").prop("disabled", true);
    $("#btn-ubah").prop("disabled", true);
    $("#btn-speciment").prop("disabled", true);
    $("#btn-hasil").prop("disabled", true);
    $("#btn-cetak").prop("disabled", true);
    $("#cetak-label-luar").prop("disabled", true);
    // $("#btn-cetak-pemeriksaan").prop("disabled", true);
}