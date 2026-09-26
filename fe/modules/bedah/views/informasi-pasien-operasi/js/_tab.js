/*
* @Author: rizqi_fitrianto
* @Date:   2018-08-09 10:02:11
* @Last Modified by:   rizqi_fitrianto
* @Last Modified time: 2018-09-05 13:52:50
*/
var _tableoperasi;
var _tableitemoperasi;
var _tablepenggunaancairan;
var _tablealatditubuh;
var _tablepemeriksaanpelengkap;
var _tablekonsultindakan;
var _tablepenggunaanbmhp;
var _tablepemasanganinfus;
var _tableinstrumen;
var idDokterBedah;
let dokterBedah = []

$('#tab-operasi').stepy({
    backLabel: 'Kembali <b><i class=\'fa fa-chevron-left\'></i></b>',
    nextLabel: 'Selanjutnya <b><i class=\'fa fa-chevron-right\'></i></b>',
    titleClick: true,
    legend: false,
    duration: 200,
    transition: 'fade',
    select: function (index) {
        loadContent(index)
    },
})
window.addEventListener("beforeunload", function logData() {
    let formdata = new FormData()
    formdata.append('url', window.location.path + window.location.search)
    navigator.sendBeacon('/bedah/informasi-pasien-operasi/unset-page', formdata);
});
$(document).ready(function () {
    var _posisi = $('.posisi-tab').val()
    var _state = $('.state-tab').val()
    if (_state == 0) {
        $('.stepy-navigator').addClass('hidden')
        $('#tab-operasi').stepy('step', _posisi) //ini biar default nampilin form intra operasi
    } else {
        $('#tab-operasi').stepy('step', 1) //ini biar default nampilin form intra operasi
    }
    $('#tab-operasi').find('.button-next').addClass('btn btn-xs btn-labeled btn-info');
    $('#tab-operasi').find('.button-back').addClass('btn btn-xs btn-labeled btn-info');
})

var loadContent = function (index) {
    $('.btn-batal-verifikasi').remove();
    var _steps = index - 1;
    var _obj = $('#tab-operasi-step-' + _steps).find('.content');
    var _source = _obj.attr('data-source')
    var _status = _obj.attr('data-status')
    $(_obj).docoLoad({
        url: _source,
        dataType: 'html',
        success: function (data) {
            if (_status) {
                $('.stepy-navigator .button-next').removeClass('hidden')
                if (_steps != 3) {
                    $('.submit-laporan').remove();
                    $('.cetak-laporan').remove();
                }
                else if (_steps == 0) {
                    $('.stepy-navigator .submit-intra-operasi').addClass('hidden')
                }
                else if (_steps == 1) {
                    $('#tab-operasi').find('.btn-save-post').addClass('hidden');
                }
                else if (_steps == 2) {
                    $('#tab-operasi').find('.button-batal').addClass('btn btn-xs btn-labeled btn-info');
                }
            }
            if (_status != null && _steps == 2) {
                $('.stepy-navigator').prepend('<span class="button-batal-verifikasi"><a class="btn-batal-verifikasi btn btn-xs btn-labeled btn-danger" style="' + roleBatalBtn + '">Batal Verifikasi <b><i class="fa fa-ban"></i></b></a></span>');
            }
            $('.submit-laporan-endoskopi').remove();
            $('.cetak-laporan-endoskopi').remove();


            // if (_steps != 3) {
            //     $('#tab-operasi').find('.submit-laporan').addClass('hidden');
            //     $('#tab-operasi').find('.cetak-laporan').addClass('hidden');
            // }
            // else if (_steps == 1) {
            //     $('#tab-operasi').find('.btn-save-post').addClass('hidden');
            // }
        }
    });
}

$(document).on('click', '.btn-batal-verifikasi', function (e) {
    var _id = $('.id-penunjang').val();
    add = $('#confirm-form').clone().removeClass('hidden');
    add.find('.input-pemakai').removeAttr('readonly');
    add.find('.input-pemakai').attr('value', '');
    add.find('.input-pemakai').attr('id', 'pemakai-validasi');
    add.find('.input-pemakai').attr('placeholder', 'Username');
    add.find('.input-sandi').attr('id', 'sandi-validasi');
    add = add.html();
    var message = 'Apakah anda yakin akan membatalkan verifikasi?' + add;
    var label = {
        buttons: {
            'Yes': 'button-yes',
            'No': 'button-no'
        },
        hidden: true
    };

    e.preventDefault();
    $.showQuestionDialog('Perhatian !', message, label, function (reaction) {
        if (reaction == 'Yes') {
            var user = $('#pemakai-validasi').val();
            var pass = $('#sandi-validasi').val();
            $().docoForm('click', {
                url: baseUrl + 'bedah/end-point/check-authorization',
                skipConfirm: true,
                skipSuccessNotif: true,
                data: {
                    nama_pemakai: user,
                    katakunci_pemakai: pass,
                    akses: 'batal-verif',
                    isAuthOnly: 1,
                },
                success: function (response) {
                    $('.btn-batal-verifikasi').button('loading');
                    $.ajax({
                        url: `/bedah/informasi-pasien-operasi/batal-verifikasi?id_penunjang=${_id}`,
                        beforeSend: function () {
                          showLoader();
                        },
                        success: function (data) {
                            title = 'Proses Berhasil';
                            text = 'Verifikasi Berhasil dibatalkan';
                            if(typeof data.response.title != undefined) {
                                text = data.response.title
                            }
                            if(typeof data.response.text != undefined) {
                                text = data.response.text
                            }
                            new PNotify({
                                title: title,
                                text: text,
                                addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                                type: 'success',
                            });
                            location.reload(true);
                        },
                        error: function (data) {
                            dataMessage = data.responseJSON.response.data
                            hideQuestionDialog();
                            title = 'Proses Gagal';
                            text = 'Batal verifikasi gagal.';
                            if(typeof dataMessage.title != undefined) {
                                text = dataMessage.title
                            }
                            if(typeof dataMessage.text != undefined) {
                                text = dataMessage.text
                            }
                            new PNotify({
                                title: title,
                                text: text,
                                addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
                                type: 'error',
                            });
                            hideLoader()
                            location.reload(true);
                        }
                    }).done(function () {
                        hideQuestionDialog();
                        $('body').find('.confirm-dialog-overlay').remove();
                        hideLoader()
                        btn.button('reset');
                      });
                }
            })
        }else{
            $('.btn-batal-verifikasi').button('reset');
            hideQuestionDialog();
            $('[data-popup="tooltip"]').tooltip();
        }
    })
})

$(document).on('click', '.delete-item', function (e) {
    e.preventDefault()
    var _key = $(this).attr('data-key');
    var _cachename = $(this).attr('data-cache');
    var _arrstring = _cachename.split('-')
    $(this).docoForm('click', {
        skipConfirm: true,
        skipConfirmMessage: true,
        url: '/bedah/informasi-pasien-operasi/unset-cache?key=' + _key + '&cacheName=' + _cachename,
        success: function (response) {
            if (_arrstring[0] == 'pegawaioperasi') {
                _tableitemoperasi.draw();
                _tableoperasi.draw()
                if(isEdit){
                    isChangeData()
                }
            } else if (_arrstring[0] == 'itemoperasi') {
                _tableitemoperasi.draw();
                _tablepenggunaanbmhp.draw();
            } else if (_arrstring[0] == 'penggunaancairan') {
                _tablepenggunaancairan.draw()
            } else if (_arrstring[0] == 'alatditubuh') {
                _tablealatditubuh.draw()
            } else if (_arrstring[0] == 'pemeriksaanpelengkap') {
                _tablepemeriksaanpelengkap.draw()
            } else if (_arrstring[0] == 'konsultindakan') {
                _tablekonsultindakan.draw()
            } else if (_arrstring[0] == 'penggunaanbmhp') {
                _tablepenggunaanbmhp.draw()
            } else if (_arrstring[0] == 'pemasanganinfus') {
                _tablepemasanganinfus.draw()
            } else if (_arrstring[0] == 'tindakanluarbedah') {
                _tabletindakanluarbedah.draw()
            } else if (_arrstring[0] == 'pemasanganinfus') {
                _tablepemasanganinfus.draw()
            } else if (_arrstring[0] == 'instrumen') {
                _tableinstrumen.draw()
            }
        }
    });
})
var populateOptions = function (_obj, _target) {
    var _options = new Option(_obj.name, _obj.id, false, false)
    $(_target).append(_options).val(_obj.id).trigger('change')
}

$(document).on('click', '.submit-intra-operasi', function () {
    $().docoForm('click', {
        data: $('#form-intra-operasi').serializeArray(),
        url: $('#form-intra-operasi').attr('action'),
        success: function (data) {
            $('#tab-operasi').stepy('step', '2')
        }
    });
})
var unhide = function (_obj) {
    var _target = '.' + _obj.attr('data-target')
    if (_obj.select2('data')[0].text.toLowerCase() == 'lain-lain') {
        if ($(_target).hasClass('hidden')) {
            $(_target).removeClass('hidden');
        }
    } else {
        if (!$(_target).hasClass('hidden')) {
            $(_target).addClass('hidden');
        }
        $(_target).find('input:text').val('')
    }
}