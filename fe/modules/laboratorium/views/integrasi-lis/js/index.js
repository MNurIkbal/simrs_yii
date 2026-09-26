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
});

// Event click
$(document).on("click", "#table-pasien-lab tbody tr", function () {
    // Try catch
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
        statusPeriksa = table.row(".selected").data().status_periksa ? table.row(".selected").data().status_periksa : null;
        namaStat = table.row(".selected").data().stat ? table.row(".selected").data().stat : null;
        caraBayar = table.row(".selected").data().carabayar_nama ? table.row(".selected").data().carabayar_nama : null;
        isBayar = table.row(".selected").data().is_bayar ? table.row(".selected").data().is_bayar : null;
        pegawai_id = table.row(".selected").data().pegawai_id ? table.row(".selected").data().pegawai_id : null;
        noRekamMedik = table.row(".selected").data().no_rekam_medik ? table.row(".selected").data().no_rekam_medik : null;
        instalasiIds = table.row(".selected").data().instalasi_ids ? table.row(".selected").data().instalasi_ids : null;
        pendaftaranIds = table.row(".selected").data().pendaftaran_ids ? table.row(".selected").data().pendaftaran_ids : null;
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
        var dataTargetRiwayat = $('#btn-riwayat-pasien').data('target');
        var dataRM = noRekamMedik.split(" - ");
        var noRM = dataRM[0];
        if (noRM) {
            $("#btn-riwayat-pasien").attr("data-target", dataTargetRiwayat + noRM);
        } else {
            $("#btn-riwayat-pasien").attr("data-target", null);
        }


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
        // if (statusPeriksa == 'Belum Periksa') {
        //     $("#btn-hasil").prop("disabled", true);
        //     $("#btn-cetak").prop("disabled", true);
        // }

        // if (statusPeriksa == 'Ambil Sampel') {
        //     $("#btn-ubah").prop("disabled", true);

        //     $("#btn-batal").attr("data-options", 'click');
        //     $("#btn-batal").attr("data-target", "#");
        //     $("#btn-batal").attr("data-error-message", "Tidak Bisa membatalkan karena sudah mengambil sampel");
        // }

        // if (statusPeriksa == 'Periksa') {
        //     $("#btn-ubah").prop("disabled", true);
        //     // $("#btn-batal").prop("disabled", true);
        //     $("#btn-batal").attr("data-options", 'click');
        //     $("#btn-batal").attr("data-target", "#");
        //     $("#btn-batal").attr("data-error-message", "Tidak Bisa membatalkan karena sudah input hsil pemeriksaan");
        // }

        // if (statusPeriksa == 'Batal') {
        //     $("#btn-batal").prop("disabled", true);
        //     $("#btn-ubah").prop("disabled", true);
        //     $("#btn-speciment").prop("disabled", true);
        //     $("#btn-hasil").prop("disabled", true);
        //     $("#btn-cetak").prop("disabled", true);

        // }
        // if (statusPeriksa == 'Selesai') {
        //     $("#btn-batal").prop("disabled", true);
        //     $("#btn-ubah").prop("disabled", true);
        // }
         // validasi berdasarkan status

         // validasi berdasarkan nama stat
        if (namaStat == 0) {
            $("#btn-hasil").prop("disabled", true);
            $("#btn-cetak").prop("disabled", true);
        }
        if (namaStat == 1) {
            $("#btn-hasil").prop("disabled", true);
            $("#btn-cetak").prop("disabled", true);
        }
        if (namaStat == 2) {
            $("#btn-hasil").prop("disabled", false);
            $("#btn-cetak").prop("disabled", false);
        }

         if(pegawai_id != null) {
            var _url = "/laboratorium/speciment/index?id=" + primaryKey; 
            $("#btn-speciment").attr("data-options", "link");
            $("#btn-speciment").attr("data-target", _url);
            $("#btn-speciment").removeAttr("data-url");
        }
        
    }
    // console.log(primaryKey);
});

$("#btn-batal").on('click', function () {
    if ($("#btn-batal").attr("data-options") == "click") {
        docoNotification('error', "Gagal Melakukan Batal", $("#btn-batal").attr("data-error-message"));
    }
});

$("#btn-cari").on('click',function(){
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
    $("#btn-cetak").prop("disabled", true);

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
             [2, 'asc']
         ],

         displayLength: 10,
         scrollX: true,
         processing: true,
         serverSide: true,
         ajax: baseUrl + 'laboratorium/integrasi-lis/get-data',
         columns: [{
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
            {
                title: no_antrian,
                data: 'no_antrian',
                name: 'no_antrian',
                searchable: false,
                visible: false

            },
            {
                title: tanggal_pendaftaran,
                data: 'tglmasukpenunjang'
            },
            {
                title: no_pendaftaran,
                data: 'no_pendaftaran'
            },
            {
                title: no_lab,
                data: 'no_masukpenunjang'
            },
            {
                title: no_rekam_medik,
                data: 'no_rekam_medik',
                name: 'no_rekam_medik'
            },
            {
                title: tanggal_lahir,
                data: 'tanggal_lahir'
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
                title: asal_rujukan,
                data: 'asalrujukan_nama',
            },
            {
                 title: status,
                 data: 'nama_stat'
            },
            {
                title: history,
                data: 'history',
                visible:false,
                searchable:false
            },
            {
                title: 'Asal Rujukan',
                data: 'tipe_pasien',
                name:'tipe_pasien',
                visible:false,
                searchable:false
            },
            {
                title: 'Instalasi',
                data: 'asalrujukan_id',
                name: 'asalrujukan_id',
                visible: false,
                searchable:false
            },
            {
                title: 'Ruangan',
                data: 'ruanganasal_id',
                name: 'ruanganasal_id',
                visible: false,
                searchable:false
            }
         ],
         createdRow: function(row, data, dataIndex){
            if (data.id_periksa == 476) {
                $(row).css("background-color", "#D24D57").css("color", "white");
            } else if(data.id_periksa == 475) {
                $(row).css("background-color", "#26A65B").css("color", "white");
            } else {
                $(row).css("background-color", "white").css("color", "black");
            } 

        },

     });
     $('.dataTables_filter').hide();
     $('.filter-form').datatableBootstrapFilter(table, [
         [2, filterTanggalPendaftaran],
         [6, filterTanggalLahir],
         [7, autocompleteDokter],
         [8, dropdownCaraBayar],
         [9, asal_1],
         [10, dropdownStatus],
     ], {
        2:0, // tgl pendaftaran
        3:1, // no pendaftaran
        4:3, // no rekam medis
        5:8, // no lab
        6:4, // tgl lahir
        7:5, // dokter
        8:9, // cara bayar
        9:2, // no lab
        10:6,
        // 11:6, 
        12:8, // asal 1
        13:9, // asal 2
     });
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
    $("#asal_rujukan_1").change(function(){
        $("#asal_rujukan_2").find('option').remove();
        // $("#asal_rujukan_3").find('option').remove();
        var asal_1 = $("#asal_rujukan_1").val();
        var html = '<option value=""> --- Silahkan Pilih --- </option>';
        $.getJSON(baseUrl +"laboratorium/informasi-pasien-lab-roche/get-asal-rujukan2",{asal_1:asal_1}, function (result) {
            $.each(result.results, function (i, field) {
                html += '<option value="' + field.id + '"> ' + field.text + ' </option>';
            });
            // console.log(html);

            $("#asal_rujukan_2").html(html);
        });
    });
    $("#asal_rujukan_2").change(function () {
        $("#asal_rujukan_3").find('option').remove();
        var asal_1 = $("#asal_rujukan_1").val();
        var asal_2 = $("#asal_rujukan_2").val();
        var html = '<option value=""> --- Silahkan Pilih --- </option>';
        $.getJSON(baseUrl+"laboratorium/informasi-pasien-lab-roche/get-asal-rujukan3", {
            asal_1: asal_1, asal_2:asal_2
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
            url: "/laboratorium/informasi-pasien-lab-wynacom/panggil-antrian",
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
var tablePenunajang;
$(document).on('click', '.btn-history', ({currentTarget}) => {
    console.log(currentTarget)
    showHistory($(currentTarget).data('id'), 'id')
})

$(document).on("click", "#btn-riwayat-pasien", function() {
    if (typeof table.row(".selected").data() === "undefined") {
        docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");

        return true;
    }

    window.open($(this).attr("data-target"), "_blank");
});