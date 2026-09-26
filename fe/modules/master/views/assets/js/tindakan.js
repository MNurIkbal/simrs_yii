/*
    Author : Randy Vianda Putra (aweutist)
*/

// tab load
$(document).ready(function(){
    // status
    var kategori = false;
    var kelompok = false;
    var kegiatan = false;
    var tindakan = false;
    var tindakanBmhp = false;
    var tindakanLuarBedah = false;
    var groupInacbg = false;
    var paket = false;
    var paketFisio = false;
    var paketRuangan = false;
    var komponen = false;
    var perda = false;
    var jenisTarif = false;
    var tarif = false;
    var tindakanRuangan = false;
    var baseController = "/master/tindakan/";


    // SPA Function
    $(document).on('click', '.spa', function(e) {
        e.preventDefault();
        var type = $(this).attr('data-type');
        var render = $(this).attr('data-render');
        var target = $(this).attr('data-target');
        var tab = $(this).attr('data-tab');
        var action = $(this).attr('action');
        var form_id = $(this).attr('form-id');
        var contentTarget = $(target + " div").attr("id");
        var urlRender;
        if (type == 'wp') {
            var tableId = $(this).attr('data-table');
            var table = $(tableId).DataTable();
            var tableData = table.row(".selected").data();
            if (typeof tableData !== 'undefined') {
                var primaryId = tableData.primary;
                if (typeof primaryId !== 'undefined') {
                    urlRender = baseController + render + primaryId;
                }
            } else {
                docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
                return false;
            }
        } else {
            urlRender = baseController + render;
        }

        if (typeof action !== 'undefined') {
            var data_form = $('#' + form_id).serializeArray();
            $(this).docoForm("click", {
                data: data_form,
                url: action,
                method: 'POST',
                success: function (data) {
                    setTimeout(function () {
                        $('#' + contentTarget).docoLoad({
                            url: urlRender,
                            dataType: 'html',
                            success : function(data) {
                                $(" .select2 ").select2();
                            }
                        });
                    }, 1000);
                }
            });
        } else {
            $('#' + contentTarget).docoLoad({
                url: urlRender,
                dataType: 'html',
                success : function(data) {
                    $(" .select2 ").select2();
                }
            });
        }

    });

    $('#content-kategori').docoLoad({
        url: '/master/tindakan/kategori',
        dataType: 'html',
        success : function(data) {
            $(" .select2 ").select2();
        }
    });
    $('#tab-kategori').on("click", function(e){
        if (!kategori){
            // kelompok = true;
            $('#content-kategori').docoLoad({
                url: '/master/tindakan/kategori',
                dataType: 'html',
                success : function(data) {
                    $(" .select2 ").select2();
                }
            });
        }
    });
    $('#tab-kelompok').on("click", function(e){
        if (!kelompok){
            // kelompok = true;
            $('#content-kelompok').docoLoad({
                url: '/master/tindakan/kelompok',
                dataType: 'html',
                success : function(data) {
                    $(" .select2 ").select2();
                }
            });
        }
    });
    $('#tab-kegiatan').on("click", function(e){
        if (!kegiatan){
            // kegiatan = true;
            $('#content-kegiatan').docoLoad({
                url: '/master/tindakan/kegiatan',
                dataType: 'html',
                success : function(data) {
                    $(" .select2 ").select2();
                }
            });
        }
    });
    $('#tab-paket').on("click", function(e){
        if (!paket){
            // paket = true;
            $('#content-paket').docoLoad({
                url: '/master/tindakan/paket',
                dataType: 'html',
                success : function(data) {
                    $(" .select2 ").select2();
                }
            });
        }
    });
    $('#tab-paket-fisio').on("click", function(e){
        if (!paketFisio) {
            $('#content-paket-fisio').docoLoad({
                url: '/master/tindakan/paket-fisio-index',
                dataType: 'html',
                success : function(data) {
                    $(" .select2 ").select2();
                }
            });
        }
    });
     $(document).on("click", "#tab-tindakan-ruangan", function(e){
        var href = $('#tab-tindakan-ruangan a').attr('href')
        if (!tindakanRuangan) {
            // tindakanRuangan = true;
            $('#content-tindakan-ruangan').docoLoad({
                url: '/master/tindakan/tindakan-ruangan',
                dataType: 'html',
                success : function(data) {
                    $(".select2").select2();
                }
            });
        }
    });
    $('#tab-paket-ruangan').on("click", function (e) {
        if (!paketRuangan) {
            // paketRuangan = true;
            $('#content-paket-ruangan').docoLoad({
                url: '/master/tindakan/paket-ruangan-tindakan',
                dataType: 'html',
                success: function (data) {
                    console.log(data)
                    $(" .select2 ").select2();
                }
            });
        }
    });
    $('#tab-group-inacbg').on("click", function(e){
        if (!groupInacbg) {
            // groupInacbg = true;
            $('#content-group-inacbg').docoLoad({
                url: '/master/tindakan/group-inacbg',
                dataType: 'html',
                success : function(data) {
                    $(".select2").select2();
                }
            });
        }
    });

    $('#tab-tindakan-spesialis').on("click", function (e) {
        $('#content-tindakan-spesialis').docoLoad({
            url: '/master/tindakan/tindakan-spesialis',
            dataType: 'html',
            success: function (data) {
                $(" .select2 ").select2();
            }
        });
    });

    $('#tab-tindakan').on("click", function(e){
        if (!tindakan) {
            // tindakan = true;
            $('#content-tindakan').docoLoad({
                url: '/master/tindakan/tindakan',
                dataType: 'html',
                success : function(data) {
                    $('.select2').select2();
                    setTimeout(() => {
                        removeclone();
                    }, 1000);
                }
            });
        }
    });

    $('#tab-tindakan-bmhp').on("click", function(e){
        if (!tindakanBmhp) {
            // tindakanBmhp = true;
            $('#content-tindakan-bmhp').docoLoad({
                url: '/master/tindakan/tindakan-bmhp',
                dataType: 'html',
                success : function(data) {
                    $('.select2').select2();
                    setTimeout(() => {
                        removeclone();
                    }, 1000);
                }
            });
        }
    });

    $('#tab-tindakan-luar-bedah').on("click", function(e){
        if (!tindakanLuarBedah) {
            // tindakanLuarBedah = true;
            $('#content-tindakan-luar-bedah').docoLoad({
                url: '/master/tindakan/tindakan-luar-bedah',
                dataType: 'html',
                success : function(data) {
                    $('.select2').select2();
                    setTimeout(() => {
                        removeclone();
                    }, 1000);
                }
            });
        }
    });

    $('#tab-reseptur').on("click", function(e){
        if (!reseptur) {
            // reseptur = true;
            $('#content-reseptur').docoLoad({
                url: '/rajal/pemeriksaan/reseptur?id=' + pendaftaran_id + '&pasien_id=' + pasien_id,
                dataType: 'html',
                success : function(data) {
                    $('.select2').select2();
                }
            });
        }
    });

    $('#tab-laboratorium').on("click", function(e){
        if (!laboratorium) {
            // laboratorium = true;
            $('#content-laboratorium').docoLoad({
                url: '/rajal/pemeriksaan/penunjang?jenis=laboratorium&id=',
                dataType: 'html',
                success : function(data) {
                    penunjangJs('laboratorium');
                }
            });
        }
    });

    $('#tab-radiologi').on("click", function(e){
        if (!radiologi) {
            // radiologi = true;
            $('#content-radiologi').docoLoad({
                url: '/rajal/pemeriksaan/penunjang?jenis=radiologi&id=',
                dataType: 'html',
                success : function(data) {
                    penunjangJs('radiologi');
                }
            });
        }
    });

    $('#tab-rehabmedis').on("click", function(e){
        if (!rehabmedis) {
            // rehabmedis = true;
            $('#content-rehabmedis').docoLoad({
                url: '/rajal/pemeriksaan/penunjang?jenis=rehabmedis&id=',
                dataType: 'html',
                success : function(data) {
                    penunjangJs('rehabmedis');
                }
            });
        }
    });

    $('#tab-bedahsentral').on("click", function(e){
        if (!bedahsentral) {
            // bedahsentral = true;
            $('#content-bedahsentral').docoLoad({
                url: '/rajal/pemeriksaan/penunjang?jenis=bedahsentral&id=',
                dataType: 'html',
                success : function(data) {
                    penunjangJs('bedahsentral');
                }
            });
        }
    });

    $('#tab-rujukanpasien').on("click", function(e){
        if (!rujukanpasien) {
            // rujukanpasien = true;
            $('#content-rujukanpasien').docoLoad({
                url: '/rajal/pemeriksaan/rujukan-pasien?id=' + pendaftaran_id + '&pasien_id=' + pasien_id,
                dataType: 'html',
                success : function(data) {
                    rujukanpasienJs();
                }
            });
        }
    });

    $('#tab-pembebasantarif').on("click", function(e){
        if (!pembebasantarif) {
            // pembebasantarif = true;
            $('#content-pembebasantarif').docoLoad({
                url: '/rajal/pemeriksaan/pembebasan-tarif?id=' + pendaftaran_id,
                dataType: 'html',
                success : function(data) {
                }
            });
        }
    });

    $('#tab-konsulpoli').on("click", function(e){
        if (!konsulpoli){
            // konsulpoli = true;
            $('#content-konsulpoli').docoLoad({
                url: '/rajal/pemeriksaan/konsulpoli?id=' + pendaftaran_id + '&pasien_id=' + pasien_id,
                dataType: 'html',
                success : function(data) {
                    $(" .select2 ").select2();

                }
            });
        }
    });

    function removeclone(){
        $('.DTFC_Cloned').remove();
    }
});
