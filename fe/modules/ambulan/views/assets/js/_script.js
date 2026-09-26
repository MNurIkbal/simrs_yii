/*
    Author : Budi
*/
var tabAktif = '';
$(document).ready(function(){
    var attributes = {};
    var luar = true;
    var rs = false;
    var baseController = "/ambulan/permintaan-ambulan/";

    $('#content-luar').docoLoad({
        url: baseController+'pasien-luar',
        dataType: 'html',
        success : function(data) {
            tabAktif = 'luar';

        }
    });

    $(document).on("click", ".btn-tambah", function (event) {
        event.preventDefault();
        var _tipePasien = $('ul.nav-tabs > li.active').attr("id");
        var _tipeId = 1;

        if (_tipePasien !== "tab-luar") _tipeId = 2;

        var _form = $(this).closest("form");
        var dataPost = _form.serializeArray();
        $(this).docoForm("click", {
            url: "/ambulan/permintaan-ambulan/tambah-tindakan?tipe=" + _tipeId,
            method: "POST",
            type: "json",
            data: dataPost,
            skipConfirm: true,
            success: function (data) {
                $("#daftartindakan_id").val('').trigger('change');
                $("#formaddtindakan-qty").val('')
                tableTindakan.draw();
            }
        });
    });

    $(document).on("click", ".btn-tambah-obat", function (event) {
        event.preventDefault();
        var dataPost = {
            obatalkes_id: $("#obatalkes_id").val(),
            qty: $(".qty_obat").val()
        };
        $(this).docoForm("click", {
            url: "/ambulan/permintaan-ambulan/tambah-obat",
            method: "POST",
            type: "json",
            data: dataPost,
            skipConfirm: true,
            success: function (data) {
                $("#obatalkes_id").val('').trigger('change');
                $(".qty_obat").val('')
                tableObat.draw();
            }
        });
    });

    $(document).on("click", ".btn-tambah-obat-rs", function (event) {
        event.preventDefault();
        var dataPost = $('#add-obat-alkes').serializeArray();
        $(this).docoForm("click", {
            url: "/ambulan/permintaan-ambulan/tambah-obat?tipe=2",
            method: "POST",
            type: "json",
            data: dataPost,
            skipConfirm: true,
            success: function (data) {
                $("#obatalkes_id_rs").val('').trigger('change');
                $(".qty_obat_rs").val('')
                tableObatRs.draw();
            }
        });
    });

    $(document).on("click", ".btn-tambah-tindakan", function (event) {
        event.preventDefault();
        var dataPost = {
            daftartindakan_id: $("#daftartindakan_id_rs").val(),
            qty: $(".qty_tindakan").val()
        };
        $(this).docoForm("click", {
            url: "/ambulan/permintaan-ambulan/tambah-tindakan?tipe=1",
            method: "POST",
            type: "json",
            data: dataPost,
            skipConfirm: true,
            success: function (data) {
                $("#daftartindakan_id_rs").val('').trigger('change');
                $(".qty_tindakan").val('')
                tableTindakan.draw();
            }
        });
    });

    $(document).on("click", ".btn-tambah-tindakan-rs", function (event) {
        event.preventDefault();
        var pasien_id = $("#pasien_id").val();
        var dataPost = $('#add-tindakan-rs').serializeArray();
        dataPost.push({
            name : "pasien_id",
            value : pasien_id
        })
        if (pasien_id) {
            $(this).docoForm("click", {
                url: "/ambulan/permintaan-ambulan/tambah-tindakan?tipe=2",
                method: "POST",
                type: "json",
                data: dataPost,
                skipConfirm: true,
                success: function (data) {
                    $("#daftartindakan_id_rs").val('').trigger('change');
                    $(".qty_tindakan").val('')
                    tableTindakanRs.draw();
                }
            });
            return true;
        }
        docoNotification("error","Proses Gagal !","Pasien harus dipilih");
    });

    $(document).on('click','.delete-cache-tindakan-rs', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            skipConfirm : true,
            success : function (data) {
                tableTindakanRs.draw();
            }
        });
    });

    $(document).on('click','.delete-cache-obat-rs', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            skipConfirm : true,
            success : function (data) {
                tableObatRs.draw();
            }
        });
    });

    $(document).on('click','.delete-cache-tindakan-luar', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            skipConfirm : true,
            success : function (data) {
                tableTindakan.draw();
            }
        });
    });

    $(document).on('click','.delete-tindakan-luar', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            skipConfirm : true,
            success : function (data) {
                tableTindakan.draw();
            }
        });
    });

    $(document).on('click','.delete-obat-rs', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            skipConfirm : true,
            success : function (data) {
                tableObatRs.draw();
            }
        });
    });

    $("#simpan").on("click", function(event){
        event.preventDefault();
        var $form = '';
        var $jenis = tabAktif;
        var urlCetak;

        var jamAwal = "";
        var jamAkhir = new Date();

        var _tipePasien = $('ul.nav-tabs > li.active').attr("id");
        $form = $("#pasien-luar-form").serializeArray();

        if (_tipePasien !== "tab-luar") $form = $("#pasien-rs-form").serializeArray();

        if (_tipePasien !== "tab-luar"){
            jamAwal = new Date($("#pesanambulanform-tgl_pesanambulan_rs").val())
        }
        else{
            jamAwal = new Date($("#pesanambulanform-tgl_pesanambulan").val())
        }

        if(jamAwal < jamAkhir){
            docoNotification('error', 'Terjadi kesalahan', 'Waktu Pemesanan Kurang Dari Waktu Saat Ini')
        }
        else{
            $(this).docoForm("click", {
                url : "/ambulan/permintaan-ambulan/save-pasien",
                method : "POST",
                type : "json",
                data: $form,
                success : function (data) {
                    var no_pesanambulan = data.response.no_pesanambulan;
                    table.draw();
                    $(".no_pesanambulan").val(no_pesanambulan);

                    if(_tipePasien === "tab-luar") {
                        $("#tab-luar").css("pointer-events", "auto");
                        urlCetak = "/ambulan/permintaan-ambulan/cetak?no_pesanambulan="+data.response.no_pesanambulan;
                    } else {
                        $("#tab-rs").css("pointer-events", "auto");
                        urlCetak = "/ambulan/permintaan-ambulan/cetak-rs?no_pesanambulan="+data.response.no_pesanambulan;
                    }

                    setTimeout(function(){
                        $("#simpan").prop('disabled', true);
                    }, 100);

                    (new PNotify({
                        title: "Berhasil",
                        text: "Data Berhasil di Simpan dengan Nomor Permintaan " + "<strong>" + no_pesanambulan + "</strong>" + " , Apakah Anda Ingin Mencetak Bukti Permintaan?",
                        addclass: "alert alert-success alert-arrow-right alert-styled-right",
                        type: "success",
                        buttons: {
                            closer: false,
                            sticker: false
                        },
                        hide: false,
                        confirm: {
                            confirm: true,
                            buttons: [
                                {
                                    text: 'Ya',
                                    addClass: 'btn btn-xs btn-success',
                                },
                                {
                                    text: 'Tidak',
                                    addClass: 'btn btn-xs btn-danger',
                                }
                            ]
                        },
                        history: {
                            history: false
                        }
                    })).get().on('pnotify.confirm', function() {
                        window.open(urlCetak);
                        location.reload();
                    }).on('pnotify.cancel', function() {
                        location.reload();
                    });
                }
            });
        }
    });

    $('#tab-rs').on("click", function(e) {
        if (!rs) {
            tabAktif = 'rs';
            rs = true;
            $('#content-rs').docoLoad({
                url: baseController+'pasien-rs',
                dataType: 'html',
                success : function(data) {

                }
            });
        }
    });
});

$(document).on('change', ".order-date-form", ({currentTarget}) => {
    $(".ambulan_id").val('')
    $(".is_emergency").text('-')
    $(".plat-nomor").text('-')
    if ($(currentTarget).val() != '') {
        $("#search-ambulance-btn").prop('disabled', false)
        $("#search-ambulance-btn").attr('action', `/ambulan/permintaan-ambulan/list-pemesanan?tgl_pesanambulan=${moment($(currentTarget).val()).format('Y-MM-DD')}`)
    } else {
        $("#search-ambulance-btn").prop('disabled', true)
    }
})

$(document).on('change', ".order-date-form-rs", ({currentTarget}) => {
    $(".ambulan_id_rs").val('')
    $(".is_emergency_rs").text('-')
    $(".plat-nomor-rs").text('-')
    if ($(currentTarget).val() != '') {
        $("#search-ambulance-btn-rs").prop('disabled', false)
        $("#search-ambulance-btn-rs").attr('action', `/ambulan/permintaan-ambulan/list-pemesanan?tipe=rs&tgl_pesanambulan=${moment($(currentTarget).val()).format('Y-MM-DD')}`)
    } else {
        $("#search-ambulance-btn-rs").prop('disabled', true)
    }
})