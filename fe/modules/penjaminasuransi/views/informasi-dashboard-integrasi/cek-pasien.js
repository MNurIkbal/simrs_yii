$(document).ready(function(){
    $('#btn-cari-pasien').on('click', function(){
        let noKartu = $('#no_kartu').val();
        let penjaminId = $('#penjamin_id').val();

        if (noKartu == '') {
            docoNotification('error', 'Peringatan!', 'No Kartu tidak boleh kosong');
            return false;
        }

        if (penjaminId == '') {
            docoNotification('error', 'Peringatan!', 'Penjamin tidak boleh kosong');
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '/penjamin-asuransi/informasi-dashboard-integrasi/cek-eligible-peserta',
            data: {
                no_kartu: noKartu,
                penjamin_id: penjaminId
            },
            success: function (response) {
                let dataPeserta = response?.data?.dataPeserta
                let dataBenefit = response?.data?.dataBenefit

                if (dataPeserta !== undefined) {
                    let newTglLahir = new Date(dataPeserta.tanggallahir).toLocaleDateString('id-ID');

                    $('#no_kartu_peserta').html(dataPeserta.nokartu);
                    $('#member_id').html(dataPeserta.memberid);
                    $('#nama_peserta').html(dataPeserta.namapeserta);
                    $('#no_bpjs').html(dataPeserta.nomorbpjs);
                    $('#tanggal_lahir').html(newTglLahir);
                    $('#jenis_kelamin').html(dataPeserta.jeniskelamin == 'L' ? 'Laki-laki' : 'Perempuan');
                    $('#nama_perusahaan').html(dataPeserta.namaperusahaan);
                    $('#no_polis').html(dataPeserta.nomorpolis);
                    if (dataPeserta?.pesertavip !== "") {
                        $('#member_vip').html(dataPeserta.pesertavip);
                    }
                }

                if (dataBenefit !== undefined) {
                    $('.data-benefit').html('');
                    dataBenefit.map((v, i) => {
                        $('.data-benefit').append(`
                            <tr>
                                <td>${v.namabenefit}</td>
                            </tr>
                        `)
                    })
                }
            },
            error: function (response) {
                if (response.status !== 200) {
                    docoNotification('error', 'Peringatan!', response.responseJSON.message);
                }
            }
        });
    });
});