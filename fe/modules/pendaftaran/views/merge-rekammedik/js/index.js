const url = "/pendaftaran/merge-rekammedik/confirm-merge?rm_awal=";
var no_rekammedik_asal = ''
var no_rekammedik_tujuan = ''
$(function () {
    $('#no_rekammedik_asal').select2({
        allowClear: false,
        ajax: {
            url: '/pendaftaran/merge-rekammedik/get-pasien',
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term,
                };
            },
        },
        placeholder: 'No Rm / Nama pasien / Tanggal lahir',
        minimumInputLength: 3,
        templateResult: function (noRm) {
            return noRm.text;
        },
        templateSelection: function (noRm) {
            return noRm.text;
        }
    });

    $('#no_rekammedik_tujuan').select2({
        allowClear: false,
        ajax: {
            url: '/pendaftaran/merge-rekammedik/get-pasien',
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term,
                };
            },
        },
        placeholder: 'No Rm / Nama pasien / Tanggal lahir',
        minimumInputLength: 3,
        templateResult: function (noRm) {
            return noRm.text;
        },
        templateSelection: function (noRm) {
            return noRm.text;
        }
    });
    $('#no_rekammedik_asal').focus()

    document.addEventListener('keydown', function(event) {
        if(event.altKey && event.keyCode == 83) {
            $('#btn-simpan').trigger('click')
        }
    });
});

$(document).on('change', '#no_rekammedik_tujuan', function(e) {
    $('#btn-cari').click();  
})

$('#btn-cari').click(function(event) {
    event.preventDefault();
    let rm_asal = $('#no_rekammedik_asal').val();
    let rm_tujuan = $('#no_rekammedik_tujuan').val();
    
    if (rm_asal != '' && rm_tujuan != '') {
        if (rm_asal == rm_tujuan) {
            docoNotification("error", 'Peringatan!', "Data pasien asal dan tujuan sama.");
            $('#no_rekammedik_tujuan').val(null).trigger('change');
        } else {
            no_rekammedik_asal = rm_asal
            no_rekammedik_tujuan = rm_tujuan
            $("#btn-simpan").attr("action", url+rm_asal+"&rm_tujuan="+rm_tujuan);
            $.ajax({
                url: 'merge-rekammedik/get-pasien?kunjungan=true',
                data: {
                    no_rekam_medik_asal: rm_asal,
                    no_rekam_medik_tujuan: rm_tujuan
                },
                success: (result) => {
                    let infoAsal = result.info_pasien_asal
                    let infoTujuan = result.info_pasien_tujuan
                    let kunjunganAsal = result.kunjungan_pasien_asal
                    let kunjunganTujuan = result.kunjungan_pasien_tujuan
                    let umurAsal = convertDateByFormat(infoAsal.tanggal_lahir, 'd-M-y')
                    let umurTujuan = convertDateByFormat(infoTujuan.tanggal_lahir, 'd-M-y')

                    var message_error_1 = '';
    
                    $('.norm_asal').text(infoAsal.no_rekam_medik)
                    $('.nama_asal').text(infoAsal.nama_pasien)
                    $('.tanggal_lahir_asal').text(infoAsal.tanggal_lahir)
                    $('.umur_asal').text(generateUmur(umurAsal))
                    $('.alamat_asal').text(infoAsal.alamat_pasien)
    
                    $('.no_pendaftaran_asal').text((kunjunganAsal !== null ) ? kunjunganAsal.no_pendaftaran : '-')
                    $('.tgl_registrasi_asal').text((kunjunganAsal !== null ) ? kunjunganAsal.tgl_pendaftaran : '-')
                    $('.ruangan_asal').text((kunjunganAsal !== null ) ? kunjunganAsal.ruangan_nama : '-')
                    $('.dokter_dpjp_asal').text((kunjunganAsal !== null ) ? kunjunganAsal.nama_pegawai : '-')
                    $('.status_asal').text((kunjunganAsal !== null ) ? kunjunganAsal.status_periksa : '-')
    
                    $('.norm_tujuan').text(infoTujuan.no_rekam_medik)
                    $('.nama_tujuan').text(infoTujuan.nama_pasien)
                    $('.tanggal_lahir_tujuan').text(infoTujuan.tanggal_lahir)
                    $('.umur_tujuan').text(generateUmur(umurTujuan))
                    $('.alamat_tujuan').text(infoTujuan.alamat_pasien)
    
                    $('.no_pendaftaran_tujuan').text((kunjunganTujuan !== null ) ? kunjunganTujuan.no_pendaftaran : '-')
                    $('.tgl_registrasi_tujuan').text((kunjunganTujuan !== null ) ? kunjunganTujuan.tgl_pendaftaran : '-')
                    $('.ruangan_tujuan').text((kunjunganTujuan !== null ) ? kunjunganTujuan.ruangan_nama : '-')
                    $('.dokter_dpjp_tujuan').text((kunjunganTujuan !== null ) ? kunjunganTujuan.nama_pegawai : '-')
                    $('.status_tujuan').text((kunjunganTujuan !== null ) ? kunjunganTujuan.status_periksa : '-')
                    
                    $(".data_pasien_asal").prop("hidden", false);
                    $(".data_pasien_tujuan").prop("hidden", false);

                    if (infoAsal.nama_pasien != infoTujuan.nama_pasien && infoAsal.tanggal_lahir == infoTujuan.tanggal_lahir) {
                        message_error_1 = 'Data nama berbeda.'
                        docoNotification("warning", 'Informasi!', message_error_1);
                    } else if (infoAsal.nama_pasien == infoTujuan.nama_pasien && infoAsal.tanggal_lahir != infoTujuan.tanggal_lahir) {
                        message_error_1 = 'Data tanggal lahir berbeda.'
                        docoNotification("warning", 'Informasi!', message_error_1);
                    } else if (infoAsal.nama_pasien != infoTujuan.nama_pasien && infoAsal.tanggal_lahir != infoTujuan.tanggal_lahir) {
                        message_error_1 = 'Data nama dan tanggal lahir berbeda.'
                        docoNotification("warning", 'Informasi!', message_error_1);
                    } else {
                        message_error_1 = 'Data dapat di gabungkan.'
                        docoNotification("success", 'Informasi!', message_error_1);
                    }
                },
                error: (result) => {
                    docoNotification("warning", 'Peringatan!', "Data pasien tidak ditemukan.");
                }
            });
        }
    } else {
        if(!rm_asal) {
            docoNotification("warning", 'Peringatan!', "Inputan no rekam medik asal belum di pilih.");
        } else {
            docoNotification("warning", 'Peringatan!', "Inputan no rekam medik tujuan belum di pilih.");
        }
    }
});

$('#btn-reset').click(function(event) {
    location.reload();
});