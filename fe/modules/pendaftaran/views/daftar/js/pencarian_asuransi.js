$(document).ready(function () {
    $('#btn-cari-pasien-asuransi').on('click', function () {
        var nik = $('#nik_pasien').val();
        var tanggalLahir = $('#tanggal_lahir_pasien').val();
        var penjamin_id = $(".selectPenjamin").val();

        if (nik == '') {
            docoNotification('warning', 'Perhatian!', 'NIK belum diisi !');
            $('.nik-validate').text('NIK belum diisi !');
            return;
        }

        $('.nik-validate').text('');

        if (tanggalLahir == '') {
            docoNotification('warning', 'Perhatian!', 'Tanggal lahir belum diisi !');
            $('.birthdate-validate').text('Tanggal lahir belum diisi !');
            return;
        }

        $('.birthdate-validate').text('');

        $().docoForm('click', {
            data: {
                'nik': nik,
                'tanggal_lahir': tanggalLahir,
                'penjamin_id': penjamin_id
            },
            url: '/pendaftaran/daftar/cek-pasien-asuransi-nik',
            type: 'POST',
            skipNotifyMessage: true,
            skipConfirm: true,
	        skipSuccessNotif: true,
            success: function (data) {
                $('.data-asuransi').html('');

                if (data?.data?.data == null) {
                    docoNotification('error', 'Perhatian!', 'Data peserta tidak ditemukan !');
                    return;
                }

                if (data?.data?.data?.status == 201) {
                    docoNotification('error', 'Perhatian!', data?.data?.data.message);
                    return;
                }

                if (data?.data?.data?.status == 200) {
                    docoNotification('success', 'Berhasil', 'Data peserta ditemukan !');
                    var dataPasien = data?.data?.data?.data;
                    if (dataPasien != undefined && dataPasien != null) {
                        dataPasien.map((v, i) => {
                            $('.data-asuransi').append(`
                                <tr>
                                    <td>${v.nomor_kartu}</td>
                                    <td>${v.nama_peserta}</td>
                                    <td>${v.nik_karyawan} / ${v.nama_perusahaan}</td>
                                    <td>${v.tgl_lahir} / ${v.jenis_kelamin}</td>
                                    <td>
                                        <button class="btn btn-info btn-xs btn-labeled pilih-pasien-asuransi" data-kartu="${v.nomor_kartu}">
                                            <b><i class="fa fa-check"></i></b>
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            `)
                        })
                    }
                    return false
                }
            },
            error: function (data) {
                console.log(data);
            }
        })
    })
});

$(document).on('click', '.pilih-pasien-asuransi', function () {
    var kartu = $(this).data('kartu');
    $('#tipepasienform-no_asuransi').val(kartu).trigger('change');

    $('#modal_pencarian_lanjutan').modal('hide');
})