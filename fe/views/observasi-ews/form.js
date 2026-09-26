var ewsScores = {};
var currentEwsRequest = {};

$(document).ready(function(){
    // Handle kv-datetime-remove button click to reset z-index
    $(document).on('click', '.kv-datetime-remove', function(e) {
        // Reset modal z-index when clear button is clicked
        setTimeout(function() {
            $('#modal_backdrop_ews').css('z-index', '1041');
        }, 100);
    });

    // Reset z-index when datepicker is hidden
    $(document).on('hide', '.bootstrap-datetimepicker-widget', function() {
        setTimeout(function() {
            $('#modal_backdrop_ews').css('z-index', '1041');
        }, 50);
    });
    
    // clearSkor()

    // Hide mandatory indicator if EWS type is already selected
    if (_jenisEwsId && _jenisEwsId !== '') {
        $('#error-jenis-ews').addClass('hidden');
    }

    if (ewsId && ewsId !== '') {
        loadExistingEwsData(ewsScores);
    } else if(isLastTtv) {
        updateTotalScore(ewsScores);
    } else {
        setTimeout(() => {
            $('.ews-input').trigger('change')
        }, 200);
    }

    // Fix for DateTimePicker not closing when clicking outside in modal
    $(document).on('click', function(e) {
        // Check if click is outside the datepicker
        if (!$(e.target).closest('.datepicker, .input-group-addon, .tanggal_ews, .bootstrap-datetimepicker-widget, .datetimepicker').length) {
            $('.datepicker').hide();
            $('.bootstrap-datetimepicker-widget').hide();
            $('.datetimepicker').hide();
            
            // Also trigger hide event on any active datepicker instances
            $('.tanggal_ews').each(function() {
                if ($(this).data('DateTimePicker')) {
                    $(this).data('DateTimePicker').hide();
                }
            });
            
            // Reset modal z-index when datepicker is closed
            setTimeout(function() {
                $('#modal_backdrop_ews').css('z-index', '1041');
            }, 50);
        }
    });

    // Handle modal show/hide events
    $('#modal_backdrop_ews').on('shown.bs.modal', function() {
        // Reinitialize datepicker when modal is shown
        $('.tanggal_ews').each(function() {
            if ($(this).data('DateTimePicker')) {
                $(this).data('DateTimePicker').destroy();
            }
        });
    });

    // Additional fix for datepicker events
    $(document).on('hide.bs.modal', '#modal_backdrop_ews', function() {
        $('.datepicker').hide();
        $('.bootstrap-datetimepicker-widget').hide();
    });

    // Handle escape key to close datepicker
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) { // Escape key
            $('.datepicker').hide();
            $('.bootstrap-datetimepicker-widget').hide();
        }
    });

    $(document).off('change', '.ews-input').on('change', '.ews-input', function() {
        let param = $(this).data('param');
        let value = $(this).val();
        let jenis = _jenisEwsId;

        let inputId = $(this).attr('id');
        // let labelText = $(`label[for='${inputId}']`).text().trim();

        if (param !== '' && jenis !== '' && value !== '') {
            if (currentEwsRequest[param]) {
                currentEwsRequest[param].abort();
            }
            currentEwsRequest[param] = $.ajax({
                url: `/${modul}${url}/get-skor`,
                type: 'GET',
                data: { jenis: jenis, parameter: param, value: value },
                beforeSend: function () {
                    $('#score-' + param).addClass('loading-score');
                    $("#btn-save-ews").prop("disabled",true);
                },
                success: function(res) {
                    let score = 0;
                    if(res.data.score) {
                        score = res.data.score;
                    }
                    ewsScores[param] = {
                        value: value,
                        score: score,
                        // label: labelText,
                    };

                    $('#score-' + param).text('[Skor: ' + score + ']');
                    let total = Object.values(ewsScores).reduce((sum, item) => sum + Number(item.score || 0), 0);
                    $('#total-skor').text(total);
                    $('#score-' + param).removeClass('loading-score');
                },
                error: function(res, status, error){
                    if (status !== 'abort') {
                        $('#score-' + param).html(`[Skor: <span class="text-danger">Gagal</span>]`);
                    }
                },
                complete: () => {
                    $('#score-' + param).removeClass('loading-score');
                    if(ewsId === ''){
                        isEditable = true;
                    }
                    if ($(`#form-ews`).find('.loading-score').length < 1 && isEditable) {
                        $("#btn-save-ews").prop("disabled",false);
                    }
                    delete currentEwsRequest[param];
                }
            });
        } else {
            // reset jika value kosong (misalnya pilih "-- Pilih --")
            if (currentEwsRequest[param]) {
                currentEwsRequest[param].abort();
            }
            $('#score-' + param).text('');
            if (ewsScores[param]) {
                ewsScores[param].value = '';
                ewsScores[param].score = '';
            }
    
            let validScores = Object.values(ewsScores)
            .filter(item => item.score !== null && item.score !== '' && !isNaN(item.score));

            if (validScores.length === 0) {
                $('#total-skor').text(''); // kosongkan kalau semua score kosong
            } else {
                let total = validScores.reduce((sum, item) => sum + Number(item.score), 0);
                $('#total-skor').text(total);
            }
        }
    });

    $('#reset-ews').on('click', function(e){
        e.preventDefault();
        clearSkor()
        clearEwsForm()
        
        $('#total-skor').text('');

        ewsScores = {};

        setTimeout(() => {
            $('#total-skor').text('');
        }, 100);
    })

    $("#btn-save-ews").on('click', function(e){
        e.preventDefault();
        var _form = $("#form-ews").serializeArray();
        
        if (ewsId && ewsId !== '') {
            _form = _form.filter(function(field) {
                return !['ObservasiEwsForm[tanggal_ews]', 'ObservasiEwsForm[jenis_ews_nama]', 'ObservasiEwsForm[pegawai_nama]'].includes(field.name);
            });
        } 

        _form.push({ name: "ObservasiEwsForm[pendaftaran_id]", value: pendaftaranId});
        if (!ewsId || ewsId === '') {
            _form.push({ name: "ObservasiEwsForm[jenis_ews]", value: _jenisEwsId});
            _form.push({ name: "ObservasiEwsForm[pegawai_id]", value: _pegawaiId});
        }
        _form.push({ name: "ObservasiEwsForm[list_skor]", value: JSON.stringify(ewsScores)});
        _form.push({ name: "ObservasiEwsForm[total_skor]", value: $('#total-skor').text()});

        if (typeof ewsId !== 'undefined' && ewsId !== null && ewsId !== '' && ewsId !== 'undefined') {
            _form.push({ name: "ews_id", value: ewsId});
            
            if (typeof ewsData !== 'undefined' && ewsData) {
                if (ewsData.tanggal_ews) {
                    _form.push({ name: "ObservasiEwsForm[tanggal_ews]", value: ewsData.tanggal_ews});
                }
                if (ewsData.jenis_ews) {
                    _form.push({ name: "ObservasiEwsForm[jenis_ews]", value: ewsData.jenis_ews});
                }
                if (ewsData.pegawai_id) {
                    _form.push({ name: "ObservasiEwsForm[pegawai_id]", value: ewsData.pegawai_id});
                }
                if (ewsData.jenis_ews_nama) {
                    _form.push({ name: "ObservasiEwsForm[jenis_ews_nama]", value: ewsData.jenis_ews_nama});
                }
                if (ewsData.pegawai_nama) {
                    _form.push({ name: "ObservasiEwsForm[pegawai_nama]", value: ewsData.pegawai_nama});
                }
            }
        }

        var actionUrl = `/${modul}${url}/input-ews?pendaftaran_id=${pendaftaranId}`;
        
        if (ewsId && ewsId !== '') {
            actionUrl += `&ews_id=${ewsId}`;
        }
        
        $(this).docoForm("click", {
            url: actionUrl,
            method: "POST",
            data: _form,
            success: function (res) {
                $('#modal_backdrop_ews').modal('hide');
                localStorage.setItem("last-jenis-ews", _jenisEwsId);
                // Use a more specific approach to refresh data
                if (typeof window.refreshEwsData === 'function') {
                    window.refreshEwsData();
                } else {
                    $('#btn-search-ews').trigger('click');
                }
                docoNotification('success', 'Berhasil', 'Data EWS berhasil disimpan.');
            },
        });
    })

    $("#btn-delete-ews").on('click', function (e) {
        e.preventDefault();

        if (!ewsId || ewsId === '') {
            docoNotification('error', 'Error', 'EWS ID tidak ditemukan');
            return false;
        }

        $(this).docoForm("click", {
            url: `/${modul}${url}/delete-ews`,
            method: "POST",
            data: {
                ews_id: ewsId,
                pendaftaran_id: pendaftaranId
            },
            confirmTitle: 'Konfirmasi Hapus',
            confirmMessage: 'Apakah Anda yakin ingin menghapus data EWS ini?',
            success: function (res) {
                $('#modal_backdrop_ews').modal('hide');
                if (typeof window.refreshAfterDelete === 'function') {
                    window.refreshAfterDelete();
                } else {
                    $('#btn-search-ews').trigger('click');
                }
                docoNotification('success', 'Berhasil', 'Data EWS berhasil dihapus.');
            },
            error: function (xhr, status, error) {
                docoNotification('error', 'Gagal', 'Terjadi kesalahan saat menghapus data EWS.');
            }
        });
    });

    $('.doco-number').on('input', function(){
        this.value = this.value.replace(/\D/g, '').slice(0, 3);
        if (this.value === '') {
            this.value = '';
        }
    })

    $(document).on('input', '.suhu', function(){
        let val = this.value;
        val = val.replace(/[^0-9.]/g, '');

        let parts = val.split('.');

        if (parts.length > 2) {
            val = parts[0] + '.' + parts[1];
            parts = val.split('.');
        }

        if (parts[0].length > 3) {
            parts[0] = parts[0].slice(0, 3);
        }

        if (parts[1] && parts[1].length > 2) {
            parts[1] = parts[1].slice(0, 2);
        }

        this.value = parts.join('.');
    });

    $(document).on('change', '.tanggal_ews', function(){
        $('.datepicker').hide();
        $('.bootstrap-datetimepicker-widget').hide();
        $('.datetimepicker').hide();
        
        // Reset modal z-index when datepicker is closed
        setTimeout(function() {
            $('#modal_backdrop_ews').css('z-index', '1041');
        }, 50);
    });

    $(document).on('dp.change', '.tanggal_ews', function(e) {
        $('.datepicker').hide();
        $('.bootstrap-datetimepicker-widget').hide();
        $('.datetimepicker').hide();
        
        // Reset modal z-index when datepicker is closed
        setTimeout(function() {
            $('#modal_backdrop_ews').css('z-index', '1041');
        }, 50);
    });
});

function clearSkor()
{
    for (let key in currentEwsRequest) {
        if (currentEwsRequest[key]) {
            try {
                currentEwsRequest[key].abort();
            } catch (e) {}
            delete currentEwsRequest[key];
        }
    }
    $('#score-frekuensi_nafas').text('');
    $('#score-sistolik').text('');
    $('#score-spo2').text('');
    $('#score-denyut_nadi').text('');
    $('#score-suhu').text('');
    $('#score-kesadaran').text('');
    $('#score-perilaku').text('');
    $('#score-sistem_kardiovaskuler').text('');
    $('#score-sistem_respirasi').text('');
    $('#score-nyeri').text('');
    $('#score-diastolik').text('');
    $('#score-pengeluaran').text('');
    $('#score-penggunaan_oksigen').text('');
    $('#score-protein_urine').text('');
    $('#total-skor').text('')
    if(ewsScores) {
        for (let key in ewsScores) {
            if (ewsScores.hasOwnProperty(key)) {
                ewsScores[key].value = '';
                ewsScores[key].score = '';
            }
        }
    }
}

function clearEwsForm() {
    $('#form-ews').find('.ews-input').val('');
    $('#form-ews').find('select').val('').trigger('change.select2');
}

function loadExistingEwsData(ewsScores) {
    if (typeof ewsData !== 'undefined' && ewsData.list_skor) {
        let listSkor = ewsData.list_skor;
        let totalScore = 0;
        let hasScore = false;
        
        for (let param in listSkor) {
            let scoreData = listSkor[param];
            let value = scoreData.value;
            let score = scoreData.score;

            if(score === null || score === undefined || score === '') {
                continue;
            }

            hasScore = true;
            $(`#score-${param}`).text(`[Skor: ${score}]`);
            ewsScores[param] = { value: value, score: score };
            totalScore += score;
        }

        if (hasScore) {
            $('#total-skor').text(totalScore);
        } else {
            $('#total-skor').text('');
        }
    }
}

function calculateLocalScore(param, value) {
    if (!scoringRules || !scoringRules[param]) return 0;
    for (let r of scoringRules[param]) {
        if (value >= r.min && value <= r.max) return r.score;
    }
    return 0;
}

function updateTotalScore(ewsScores) {
    let totalScore = 0;
    let hasScore = false;
    
    $('.ews-input').each(function () {
        let param = $(this).data('param');
        let value = parseFloat($(this).val());
        if (!isNaN(value)) {
            hasScore = true;
            score = calculateLocalScore(param, value);
            $(`#score-${param}`).text(`[Skor: ${score}]`);
            ewsScores[param] = { value: value, score: score };
            totalScore += calculateLocalScore(param, value);
        }
    });

    if (hasScore) {
        $('#total-skor').text(totalScore);
    } else {
        $('#total-skor').text('');
    }
}