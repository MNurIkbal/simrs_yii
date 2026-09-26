$(document).ready(function(){
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.datepicker, .input-group-addon, .tgl_sbar, .bootstrap-datetimepicker-widget, .datetimepicker').length) {
            $('.datepicker').hide();
            $('.bootstrap-datetimepicker-widget').hide();
            $('.datetimepicker').hide();
            $('.tgl_sbar').each(function() {
                if ($(this).data('DateTimePicker')) {
                    $(this).data('DateTimePicker').hide();
                }
            });
        }
    });
    $('#modal_backdrop_sbar').on('shown.bs.modal', function() {
        $('.tgl_sbar').each(function() {
            if ($(this).data('DateTimePicker')) {
                $(this).data('DateTimePicker').destroy();
            }
        });
    });
    $(document).on('hide.bs.modal', '#modal_backdrop_sbar', function() {
        $('.datepicker').hide();
        $('.bootstrap-datetimepicker-widget').hide();
    });
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) {
            $('.datepicker').hide();
            $('.bootstrap-datetimepicker-widget').hide();
        }
    });
    $("#btn-save-sbar").on('click', function(e){
        e.preventDefault();
        var _form = $("#form-sbar").serializeArray();
        if (sbarId && sbarId !== '') {
            _form = _form.filter(function(field) {
                return !['SbarForm[tanggal_sbar]'].includes(field.name);
            });
        } 

        _form.push({ name: "SbarForm[pendaftaran_id]", value: pendaftaranId});
        _form.push({ name: "SbarForm[tgl_pendaftaran]", value: _tglPendaftaran});
        _form.push({ name: "SbarForm[tglpasienpulang]", value: _tglPulang});
        var actionUrl = `/${modul}${url}/input-sbar?pendaftaran_id=${pendaftaranId}`;
        if (sbarId && sbarId !== '') {
            _form.push({ name: "SbarForm[sbar_id]", value: sbarId});
            actionUrl += `&sbar_id=${sbarId}`;
        }
        $(this).docoForm("click", {
            url: actionUrl,
            method: "POST",
            data: _form,
            success: function (res) {
                $('#modal_backdrop_sbar').modal('hide');
                $('#tb-sbar').DataTable().ajax.reload();
                docoNotification('success', 'Proses Berhasil!', 'Data SBAR berhasil disimpan.');
            },
            error: function(res) {
                if(res.responseJSON && res.responseJSON.response) {
                    var _response = res.responseJSON.response
                    if(!_response.data) {
                        var _title = _response.title
                        var _message = _response.message
                        docoNotification('error', _title, _message)
                    }
                } else if(res.responseJSON && res.responseJSON.title && res.responseJSON.message) {
                    var _title = res.responseJSON.title
                    var _message = res.responseJSON.message
                    docoNotification('error', _title, _message)
                } else {
                    docoNotification('error', 'Proses Gagal!', 'Terjadi kesalahan saat menyimpan data SBAR.')
                }
            }
        });
    })
    $('.btn-copy-ttv').unbind();
    $('.btn-copy-ttv').bind('click', () => {
        const { href, width } = $('.btn-copy-ttv').data()
        showLoader('Memuat Halaman...')
        $('#modal_riwayat').find('.modal-dialog').css('width', width)
        $('#modal_riwayat .modal-content').docoLoad({
            url: href,
            dataType: 'html',
            success: function (data) {
                hideLoader()
                $('#modal_riwayat .modal-content').parents('.modal').modal('show')
            },
            error: function () {
                hideLoader()
            }
        })
    });
    $(".selectDokter").select2InfinityScroll({
		url: `/${modul}${url}/filters-sbar`,
		callbackData: (param) => {
			return {
				payload: {
					...param,
                    type: 'dokter_tujuan',
                    instalasi_id: instalasiId
				}
			}
		},
    });
    $('.doco-number').on('input', function(){
        this.value = this.value.replace(/\D/g, '').slice(0, 3);
        if (this.value === '') {
            this.value = '0';
        }
    })
    $('.suhu-sbar, .berat_badan_sbar, .lingkar_kepala').on('input', function(){
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
    if(sbarId) {
        var newOption = new Option(_pegawaiNama, _pegawaiId, true, true);
        $('.selectDokter').append(newOption).trigger('change');
    }
    if(window._sbarCopyData) {
        const data = window._sbarCopyData;

        $('#sbarform-sistol').val(data.sistol);
        $('#sbarform-diastol').val(data.diastol);
        $('#sbarform-nadi').val(data.denyutNadi);
        $('#sbarform-respirasi').val(data.frekuensiNafas);
        $('#sbarform-tinggi_badan').val(data.tinggiBadan);
        $('#sbarform-berat_badan').val(data.beratBadan);
        $('#sbarform-spo2').val(data.spo2);
        $('#sbarform-suhu').val(data.suhu);
        $('#sbarform-lingkar_kepala').val(data.lingkarKepala);

        $('#sbarform-situasi').val(data.situation);
        $('#sbarform-asesmen').val(data.assessment);
        $('#sbarform-rekomendasi').val(data.recommendation);

        if (data.dokterId && data.dokterTujuan) {
            var newOption = new Option(data.dokterTujuan, data.dokterId, true, true);
            $('.selectDokter').append(newOption).trigger('change');
        }

        window._sbarCopyData = null;
    }
    $('form').on('keypress', function(e) {
        if (e.which === 13 && !$(e.target).is('textarea')) {
            e.preventDefault();
        }
    });
})