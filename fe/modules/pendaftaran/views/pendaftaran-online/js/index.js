/*
* @Author: Sigit
* @Date:   2018-09-26 10:37:32
*/

var table;
var key_start = "key_start";
var key_end = "key_end";

$(document).ready(function() {
    localStorage.removeItem(key_start);
    localStorage.removeItem(key_end);

    table = $("#tb-pendaftaran-online").docoTabel({
        filter: true,
        displayLength: 10,
        processing: true,
        serverSide: true,
        // scrollX: true,
        ajax: baseUrl + "pendaftaran/pendaftaran-online/get-data-pendaftaran-online",
        columns: [
            {title: aksi, data: "aksi", searchable: false, orderable: false},
            {title: no, data: "no", searchable: false, orderable: false},
            {title: noAntrian, data: "no_antrian", searchable: false, orderable: false},
            {title: noPendaftaran, data: "no_pendaftaranol", orderable: false},
            {title: noRekamMedik, data: "no_rekam_medik", orderable: false},
            {title: namaPasien, data: "nama_pasien", orderable: false, visible: false},
            {title: noAsuransi, data: "no_asuransi", searchable: false, orderable: false},
            {title: poliTujuan, data: "ruangan_nama", orderable: false},
            {title: dokter, data: "nama_pegawai", orderable: false},
            {title: caraBayar, data: "carabayar_nama", orderable: false},
            {title: jamMulaiPelayanan, data: "jam_kunjungan", searchable: false, orderable: false},
            {title: tanggalDaftar, data: "tgl_pendaftaran", searchable: false, orderable: false},
            {title: tanggalKunjungan, data: "tgl_kunjungan", orderable: false},
            {title: status, data: "status_daftar_ol", visible: false, orderable: false},
        ],
        scrollCollapse: true,
        language: {
            emptyTable: emptyTable,
            info: info,
            infoEmpty: infoEmpty,
            infoFiltered: infoFiltered,
            lengthMenu: lengthMenu,
            loadingRecords: loadingRecords,
            processing: processing,
            search: search,
            zeroRecords: zeroRecords,
            aria: {
                sortAscending: sortAscending,
                sortDescending: sortDescending
            }
        },
        rowCallback: function (row, data) {
            var btn_panggil = $(row).find(".btn-panggil");

            btn_panggil.on("click", function () {
                var text = data.no_antrian;
                var panggilan = ["Kosong", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan"];
                var player = $("#playerAudio");
                var arrayText = text.split("");
                var index = 0;
                arrayText.push("stop");
                arrayText = arrayText.filter(Boolean);

                arrayText.forEach(function(item, key) {
                    if (item == "0" || item == "1" || item == "2" || item == "3" || item == "4" || item == "5" || item == "6" || item == "7" || item == "8" || item == "9") {
                        arrayText[key] = panggilan[item];
                    }
                });

                player[0].defaultPlaybackRate = 1;
                player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
                player[0].play();

                player[0].addEventListener("ended", function () {
                    index = index + 1;

                    if (index < arrayText.length) {
                        player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
                        if (arrayText[index] != "stop") {
                            player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
                            player[0].play();
                        }
                    }
                });
            });

            var td = btn_panggil.parent();

            td.addClass("aksi"+data.primary);
        }
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table , [
        [12, "<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' value='"+date+"'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' value='"+date+"'/><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>"],
        [7, dropdownPoliTujuan],
        [8, dropdownDokter],
        [9, dropdownCaraBayar],
        [13, dropdownStatus],
    ], {
        0:12,
        1:4,
        2:5,
        3:3,
        4:7,
        5:8,
        6:9,
        7:13,
    });

    $("#rangeDemoStart").on("change", function() {
        localStorage.setItem(key_start, $(this).val());
        localStorage.setItem(key_end, $("#rangeDemoFinish").val());
    });

    $("#rangeDemoFinish").on("change", function() {
        localStorage.setItem(key_end, $(this).val());
    });

    $(".data-reset").on("click", function() {
        var rangeDemoFormat = '%e-%b-%Y';
        var rangeDemoConv = new AnyTime.Converter({format:rangeDemoFormat});
        var start_date = new Date(localStorage.getItem(key_start));
        var end_date = new Date(localStorage.getItem(key_end));
        $("#rangeDemoStart").AnyTime_noPicker().val(rangeDemoConv.format(start_date)).AnyTime_picker({format: rangeDemoFormat});
        $("#rangeDemoFinish").AnyTime_noPicker().val(rangeDemoConv.format(end_date)).AnyTime_picker({format: rangeDemoFormat});
    });

    $("#scanner").focus();

    dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
});

$("#scanner").on("keyup", function (event) {
    event.preventDefault();

    if (event.keyCode == 13) {
        $.ajax({
            url: cekUrl+$(this).val(),
            method: "GET",
            dataType: "json",
            success: function(response) {
                if(typeof response.pendaftaranol_id != "undefined") {
                    var action = url + response.pendaftaranol_id;

                    $("#btn-cari").attr("action", action);

                    $("#btn-cari").click();
                } else {
                    docoNotification("error", errorTitle, errorMessage);
                    $("#scanner").val("");
                    $("#scanner").focus();
                }
            }
        });
    }
});

$("#modal_backdrop").on("hidden.bs.modal", function(event) {
    $("#scanner").val("");
    $("#scanner").focus();
});