/*
* @Author: Sigit
* @Date:   2018-08-20 13:04:31
*/

$(document).on("click", ".btn-deletes", function(e) {
    $('#section-racikan').find('.child-'+$(this).attr('data-iteration')).remove();
});

$(document).on('click', '#btn-reset-reseptur', function() {
    if (isEditReseptur != "1") {
        resetAll(true);
    }

    $("#berat_badan").val(berat_badan);
    $("#tinggi_badan").val(tinggi_badan);
    $("#luas_tubuh").val(luas_tubuh);

    $.ajax({
        url: $(this).attr('data-url'),
        success: function(){
            tabel_reseptur.draw();
        }
    });
});

$(document).on('change', '.bb_tb', function() {
    var bb = parseFloat(docoHelper.convertToAngka($("#berat_badan").val()))
    var tb = parseFloat(docoHelper.convertToAngka($("#tinggi_badan").val()))

    if (bb != '' && tb != '') {
        var mosteller = Math.sqrt(Number(bb) * Number(tb) / 3600);

        $("#luas_tubuh").val(docoHelper.convertToRupiah(mosteller.toFixed(2)));
    }
});

$("#btn-cetak-reseptur").on("click", function(event) {
    event.preventDefault();

    window.open("/igd/pemeriksaan-igd/cetak-reseptur?id="+pendaftaran_id+"&instruksi_id="+$("#instruksiform-instruksi_id").val());
});

// $('#depdrop_reseptur_nr').on('depdrop:afterChange', function(event, id, value, jqXHR, textStatus) {
//     let ajaxResults = $('#depdrop_reseptur_nr').depdrop('getAjaxResults');
//     list_obat = ajaxResults['output'];
// });

function saveSessionReseptur() {
    let data = tabel_reseptur.$("input, select");
    let data_depo = $("#select_depo");
    let data_iter = $("#reseptur_iter");
    let data_general = $.merge(data_depo, data_iter);
    let all = $.merge(data, data_general);
    let all_serialize = all.serializeArray();

    let dataReseptur = $("#form-reseptur").serializeArray();
    let dataInstruksi = $("#form-instruksi").serializeArray();

    let allData = $.merge(all_serialize, dataReseptur);
    allData = $.merge(allData, dataInstruksi);
    allData.push({name:'pasien_id_now', value:pasien_id_now});
    allData.push({name:'ruangan_now', value:ruangan_now});
    $("#btn-save-reseptur").docoForm("click", {
        data: allData,
        skipSuccessNotif: true,
        success : function(data) {
            resetAll(true);

            PNotify.prototype.options.styling = "bootstrap3";
            (new PNotify({
                title: "Berhasil",
                text: "Data berhasil disimpan, apakah Anda ingin melakukan cetak?",
                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                type: "success",
                buttons: {
                    closer: false,
                    sticker: false
                },
                hide: false,
                confirm: {
                    confirm: true
                },
                history: {
                    history: false
                }
            })).get().on('pnotify.confirm', function() {
                window.open("/igd/pemeriksaan-igd/cetak-reseptur?id="+pendaftaran_id+"&instruksi_id="+data.response.data.instruksi_id);
            }).on('pnotify.cancel', function() {

            });

            $("#btn-back-terapi").trigger('click');
        }
    });
}

function batalSessionReseptur(index) {
    $(index).docoForm("delete", {
        success : function(response) {
            resetAll(false);
            tabel_reseptur.clear();
            tabel_reseptur.draw();
        }
    });
}

// mencegah karakter lain selain angka desimal
$(document).on('input', '.doco-decimal', function() {
    match = (/(\d{0,9})[^.]*((?:\.\d{0,9})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
    this.value = match[1] + match[2];
});
