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
$(document).on("click", "#table-pasien-rad tbody tr", function () {
    // Try catch
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
        statusPeriksa = table.row(".selected").data().status_periksa ? table.row(".selected").data().status_periksa : null;
        caraBayar = table.row(".selected").data().carabayar_nama ? table.row(".selected").data().carabayar_nama : null;
        isBayar = table.row(".selected").data().is_bayar ? table.row(".selected").data().is_bayar : null;
    } catch (e) {
        // Make it false
        primaryKey = false;
    }

    // Assign to ubah
    // $("#btn-edit").attr("action", updateUrl + primaryKey);
    $("#btn-approve").attr("data-target", approveUrl + primaryKey);
    $("#btn-batal").attr("data-target", batalUrl + primaryKey);
    $("#cetak-tagihan").attr("data-target", cetakUrl + primaryKey);

    // Check class selected
    if ($('#table-pasien-rad tr.selected').length == 0) {
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
            $("#btn-cetak").prop("disabled", true);
        }

        if (statusPeriksa == 'Periksa') {
            $("#btn-ubah").prop("disabled", true);
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
     moment.locale('en'); //set moment default to english

    // Generate Table
     table = $('#table-pasien-rad').docoTabel({
         filter: true,
         //add for handle checkbox
         sorting: [
             [4, 'asc']
         ],
         fnRowCallback: function (nRow, data, iDisplayIndex, iDisplayIndexFull) {

         },
         displayLength: 10,
         processing: true,
         serverSide: true,
         scrollX: true,
         ajax: baseUrl + 'radiologi/lap-pasien-radiologi/get-data-datatable',
         columns: [
             {
                 title: "No",
                 searchable: false,
                 sorting: false,
                 render: function (data, type, row, meta) {
                     return meta.row + meta.settings._iDisplayStart + 1;
                 }
             },
             {
                 title: tanggal_pendaftaran,
                 data: 'tglmasukpenunjang',
                 render: function (data, type, row, meta) {
                     return data ? moment(data).format('DD-MMM-YYYY HH:mm') : ''; 
                 }
             },
             {
                 title: tanggal_periksa,
                 data: 'tglperiksa',
                 searchable: false,
                 name: 'tglmasukpenunjang',
                 render: function (data, type, row, meta) {
                     return data ? moment(data).format('DD-MMM-YYYY HH:mm') : '';
                 }
             },
             {
                 title: status_cito,
                 data: 'is_cyto',
                 searchable: false,
                 name: 'cyto_tindakan',
                 render: function (data, type, row, meta) {
                     return data ? 'CITO' : 'NON CITO';
                 }
             },
             {
                 title: no_pendaftaran,
                 data: 'no_pendaftaran'
             },
             {
                 title: no_rekam_medik,
                 data: 'no_rekam_medik',
                 name: 'no_rekam_medik'
             },
             {
                 title: nama_pasien,
                 data: 'nama_pasien'
             },
             {
                 title: tanggal_lahir,
                 data: 'tanggal_lahir',
                 render: function (data, type, row, meta) {
                     return data ? moment(data).format('DD-MMM-YYYY') : '';
                 }
             },
             {
                 title: dokter_perujuk,
                 data: 'nama_pegawai',
                 searchable: false,                 
             },
             {
                 title: dokter,
                 data: 'dokter_penunjang',
                 searchable: false, //10
             },
             {
                 title: cara_bayar,
                 data: 'carabayar_nama',
                 searchable: false,
             },
             {
                 title: penjamin,
                 data: 'penjamin_nama',
                 searchable: false,
             },
             {
                 title: no_rad,
                 data: 'no_masukpenunjang',
             },
             {
                 title: asal_rujukan,
                 data: 'asalrujukan_nama',
             },
             {
                title: ruangan_asal,
                data: 'ruanganasal_nama',
                 searchable: false,
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
             }
         ],
     });
     $('.dataTables_filter').hide();
     $('.filter-form').datatableBootstrapFilter(table, [
         [1, filterTanggalPendaftaran],
         [7, filterTanggalLahir],
         [9, autocompleteDokter],
         [10, dropdownCaraBayar],         
         [13, asal_1],
        //  [15, dropdownStatus],
        //  [20, asal_2],
        //  [21, asal_3],
     ], {
         1  : 0,
         7  : 1,
         4  : 2,
         5  : 3,
         6  : 4,
         12 : 5,
         13 : 6,
        //  15 : 7,

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
        $.getJSON(baseUrl +"radiologi/informasi-pasien-rad/get-asal-rujukan2",{asal_1:asal_1}, function (result) {
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
        $.getJSON(baseUrl+"radiologi/informasi-pasien-rad/get-asal-rujukan3", {
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

});
