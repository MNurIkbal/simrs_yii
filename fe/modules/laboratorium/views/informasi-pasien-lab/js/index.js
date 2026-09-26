// Global Var
var table;

// Event click
$(document).on("click", ".data-reset", function () {
    // Reload table
    table.draw();

    // Disable edit and delete button
    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
    $("#cetak-tagihan").prop("disabled", true);

    $("#btn-ubah").prop("disabled", true);
    $("#btn-speciment").prop("disabled", true);
    $("#btn-hasil").prop("disabled", true);
    $("#btn-cetak").prop("disabled", true);
    $("#edit-pemeriksaan").prop("disabled", true);

});

// Event click
$(document).on("click", "#table-pasien-lab tbody tr", function () {
    // Try catch
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
        statusPeriksa = table.row(".selected").data().status_periksa ? table.row(".selected").data().status_periksa : null;
        caraBayar = table.row(".selected").data().carabayar_nama ? table.row(".selected").data().carabayar_nama : null;
        isBayar = table.row(".selected").data().is_bayar ? table.row(".selected").data().is_bayar : null;
        statusBayar = table.row(".selected").data().status_bayar ? table.row(".selected").data().status_bayar : null;
        pegawai_id = table.row(".selected").data().pegawai_id ? table.row(".selected").data().pegawai_id : null;
        pasienmasukpenunjang_id = table.row(".selected").data().pasienmasukpenunjang_id ? table.row(".selected").data().pasienmasukpenunjang_id : null;
        pendaftaran_id = table.row(".selected").data().pendaftaran_id ? table.row(".selected").data().pendaftaran_id : null;
        noRegis = table.row(".selected").data().noRegis ? table.row(".selected").data().noRegis : null;
    } catch (e) {
        // Make it false
        primaryKey = false;
    }

    // Assign to ubah
    // $("#btn-edit").attr("action", updateUrl + primaryKey);
    $("#btn-approve").attr("data-target", approveUrl + primaryKey);
    $("#btn-batal").attr("data-target", batalUrl + primaryKey);
    // $("#cetak-tagihan").attr("data-target", cetakUrl + primaryKey);

    // Check class selected
    if ($('#table-pasien-lab tr.selected').length == 0) {
        // Disable edit button
        $("#btn-approve").prop("disabled", true);
        $("#btn-batal").prop("disabled", true);
        $("#cetak-tagihan").prop("disabled", true);

        $("#btn-ubah").prop("disabled", true);
        $("#btn-speciment").prop("disabled", true);
        $("#btn-hasil").prop("disabled", true);
        $("#btn-cetak").prop("disabled", true);
        $("#edit-pemeriksaan").prop("disabled", true);

    } else {
        // Disable edit button
        $("#btn-approve").prop("disabled", false);
        $("#btn-batal").prop("disabled", false);
        $("#btn-batal").attr("data-options", 'link');
        $("#cetak-tagihan").prop("disabled", false);

        $("#btn-ubah").prop("disabled", false);
        $("#btn-speciment").prop("disabled", false);
        $("#btn-hasil").prop("disabled", false);
        $("#btn-cetak").prop("disabled", false);


        if (caraBayar != 'Umum') {
            $("#btn-approve").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-error-message", "Pasien jaminan tidak bisa melakukan pembatalan");
        }
        if (isBayar) {
            $("#btn-approve").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-error-message", "Tidak Bisa membatalkan karena sudah melakukan pembayaran");
        }

        // validasi berdasarkan status
        if (statusPeriksa == 'Belum Periksa') {
            $("#btn-hasil").prop("disabled", true);
            $("#btn-cetak").prop("disabled", true);
        }

        if (statusPeriksa == 'Ambil Sampel') {
            $("#btn-ubah").prop("disabled", true);

            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-error-message", "Tidak Bisa membatalkan karena sudah mengambil sampel");
        }

        if (statusPeriksa == 'Periksa') {
            $("#btn-ubah").prop("disabled", true);
            // $("#btn-batal").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-error-message", "Tidak Bisa membatalkan karena sudah input hsil pemeriksaan");
        }

        if (statusPeriksa == 'Batal') {
            $("#btn-batal").prop("disabled", true);
            $("#btn-ubah").prop("disabled", true);
            $("#btn-speciment").prop("disabled", true);
            $("#btn-hasil").prop("disabled", true);
            $("#btn-cetak").prop("disabled", true);

        }

        if (statusPeriksa == 'Selesai') {
            $("#btn-batal").prop("disabled", true);
            $("#btn-ubah").prop("disabled", true);
        }
        // validasi berdasarkan status

        if (pegawai_id != null) {
            var _url = "/laboratorium/speciment/index?id=" + primaryKey;
            $("#btn-speciment").attr("data-options", "link");
            $("#btn-speciment").attr("data-target", _url);
            $("#btn-speciment").removeAttr("data-url");
        }
        console.log(statusPeriksa)
        if (statusBayar == "Belum Bayar" && statusPeriksa == "Belum Periksa") {
            $("#edit-pemeriksaan").prop("disabled", false);
        } else {
            $("#edit-pemeriksaan").prop("disabled", true);
        }

    }
    // console.log(primaryKey);
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
    $("#edit-pemeriksaan").prop("disabled", true);
});

// Event Ready
$(document).ready(function () {

    $("#btn-approve").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
    $("#cetak-tagihan").prop("disabled", true);

    $("#btn-ubah").prop("disabled", true);
    $("#btn-speciment").prop("disabled", true);
    $("#btn-hasil").prop("disabled", true);
    $("#btn-cetak").prop("disabled", true);
    $("#edit-pemeriksaan").prop("disabled", true);


    // Generate Table
    table = $('#table-pasien-lab').docoTabel({
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
            [3, 'asc']
        ],

        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollY: true,
        scrollX: false,
        ajax: baseUrl + 'laboratorium/informasi-pasien-lab/get-data',
        columns: [{
            data: null,
            searchable: false,
            orderable: false,
            defaultContent: '',
        },
        { title: no, data: "rowNum", searchable: false, orderable: false },
        {
            title: status_periksa,
            data: 'status_periksa_btn',
            searchable: false,
        },
        {
            title: tanggal_pendaftaran,
            data: 'tglmasukpenunjang'
        },
        {
            title: nama_pasien,
            data: 'nama_pasien',
            searchable: false,
        },
        {
            title: dokter,
            data: 'dokter_penunjang'
        },
        {
            title: cara_bayar,
            data: 'carabayar_nama'
        },
        {
            title: status_bayar,
            data: 'status_bayar_btn',
            searchable: false,
        },
        {
            title: asal_rujukan,
            data: 'asalrujukan_nama',
            searchable: false,
        },
        {
            title: no_lab,
            data: 'no_masukpenunjang',
            searchable: false,
        },
        {
            title: 'No Pendaftaran',
            data: 'no_pendaftaran',
            visible: false,
        },
        {
            title: 'No Lab',
            data: 'no_masukpenunjang',
            visible: false,
        },
        {
            title: 'No Rekam Medis',
            data: 'no_rekam_medik',
            visible: false,
        },
        {
            title: 'Tanggal Lahir',
            data: 'tanggal_lahir',
            visible: false,
            searchable: false,
        },

        {
            title: 'Asal Rujukan',
            data: 'tipe_pasien',
            name: 'tipe_pasien',
            visible: false,
        },
        {
            title: 'Instalasi',
            data: 'asalrujukan_id',
            name: 'asalrujukan_id',
            visible: false,
            searchable: false,

        },
        {
            title: 'Ruangan',
            data: 'ruanganasal_id',
            name: 'ruanganasal_id',
            visible: false,
            searchable: false,
        },
        {
            title: 'Status Bayar',
            data: 'status_bayar',
            visible: false,
        },
        {
            title: 'Status',
            data: 'status_periksa',
            visible: false,
        },
        ],

        rowCallback: function (row, data) {
            if (data.is_cyto == true) {
                $('td:eq(4)', row).css({ "background-color": "rgb(255,137,0)", "color": "#ffffff" });
            }
        }



    });
    $('.dataTables_filter').hide();
    $('.filter-form').datatableBootstrapFilter(table, [
        [3, filterTanggalPendaftaran],
        [5, autocompleteDokter],
        [6, dropdownCaraBayar],
        [13, filterTanggalLahir],
        [14, dropdownAsalRujukan],
        [15, asal_2],
        [17, dropdownStatusBayar],
        [18, dropdownStatus],
        [20, asal_3],
        //  [12, asal_1],
    ], {
        3: 0,
        10: 1,
        11: 2,
        12: 3,
        5: 4,
        6: 5,
        17: 6,
        18: 7,
        14: 8,
    }, true);
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
    // $("#asal_rujukan_1").change(function(){
    //     $("#asal_rujukan_2").find('option').remove();
    //     // $("#asal_rujukan_3").find('option').remove();
    //     var asal_1 = $("#asal_rujukan_1").val();
    //     var html = '<option value=""> --- Silahkan Pilih --- </option>';
    //     $.getJSON(baseUrl +"laboratorium/informasi-pasien-lab/get-asal-rujukan2",{asal_1:asal_1}, function (result) {
    //         $.each(result.results, function (i, field) {
    //             html += '<option value="' + field.id + '"> ' + field.text + ' </option>';
    //         });
    //         // console.log(html);

    //         $("#asal_rujukan_2").html(html);
    //     });
    // });
    // $("#asal_rujukan_2").change(function () {
    //     $("#asal_rujukan_3").find('option').remove();
    //     var asal_1 = $("#asal_rujukan_1").val();
    //     var asal_2 = $("#asal_rujukan_2").val();
    //     var html = '<option value=""> --- Silahkan Pilih --- </option>';
    //     $.getJSON(baseUrl+"laboratorium/informasi-pasien-lab/get-asal-rujukan3", {
    //         asal_1: asal_1, asal_2:asal_2
    //     }, function (result) {
    //         $.each(result.results, function (i, field) {
    //             html += '<option value="' + field.id + '"> ' + field.text + ' </option>';
    //         });
    //         // console.log(html);
    //         $("#asal_rujukan_3").html(html);
    //     });
    // });
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
            url: "/laboratorium/informasi-pasien-lab/panggil-antrian",
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

});

$("#edit-pemeriksaan").click(function () {
    if (statusBayar == "Belum Bayar" && statusPeriksa == "Belum Periksa") {
        $("#edit-pemeriksaan").attr("data-url", "/laboratorium/informasi-pasien-lab/form-edit-pemeriksaan?noRegis=" + noRegis + "&id=");
    } else {
        docoNotification('error', 'Terjadi kesalahan pada input.', 'Pemeriksaan ini tidak bisa diubah!')
        return false;
    }
});