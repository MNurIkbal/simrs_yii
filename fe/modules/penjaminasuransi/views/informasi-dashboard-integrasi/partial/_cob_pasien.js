$(document).ready(function () {
    $("#benefit_id").select2();
    $("#penjamin_id").select2();
    
    $('#btn-cek-eligible').on('click', function (e) {
        e.preventDefault()
        let noKartu = $('#no_kartu').val();
        let penjaminId = $('#penjamin_id').val();
        if (statusKunjunganId != 551) {
            docoNotification('error', 'Peringatan!', 'Data INACBGS Belum Final Klaim !');
            return false;
        }

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
                    let htmlBirthdate = `${newTglLahir} - ${dataPeserta.umur}`;
                    
                    $('#namapeserta_asuransi').html(dataPeserta.namapeserta);
                    $('#tgllahir_asuransi').html(htmlBirthdate);
                    $('#jeniskelamin_asuransi').html(dataPeserta.jeniskelamin == 'M' ? 'Laki-laki' : 'Perempuan');

                    $('#btn-daftar').prop('disabled', false);
                    docoNotification('success', 'Berhasil', `Data Peserta Berhasil Ditemukan atas namas <b>${dataPeserta.namapeserta}</b>`);
                }

                if (dataBenefit !== undefined) {
                    $('.data-benefit').html('');
                    $("#benefit_id").empty().trigger('change');
                    dataBenefit.map((v, i) => {
                        let option = new Option(v.namabenefit, v.kodebenefit, false, false);
                        $("#benefit_id").append(option).trigger('change');
                    })
                }
            },
            error: function (response) {
                if (response.status !== 200) {
                    docoNotification('error', 'Peringatan!', response.responseJSON.message);
                }
            }
        });
    })

    $('#btn-muatulang').on('click', function (e) {
        e.preventDefault()
        $('#no_kartu').val(null);
        $('#penjamin_id').val(null).trigger('change');
        $("#benefit_id").empty().trigger('change');
        $('#btn-daftar').prop('disabled', true);
    })

    $('#btn-back').on('click', function (e) {
        e.preventDefault()
        window.location.replace(baseUrl + 'penjamin-asuransi/informasi-dashboard-integrasi');
    })

    $("#btn-daftar").on('click', function (e) {
        e.preventDefault()
        let kunjungan = JSON.parse(dataKunjungan);
        if (kunjungan === null) {
            docoNotification('error', 'Peringatan!', 'Data Kunjungan Tidak Ditemukan');
            return false;
        }

        let pendaftaranData = {
            tglpendaftaran: kunjungan.tgl_pendaftaran,
            no_kartu: $('#no_kartu').val(),
            penjamin_id: $('#penjamin_id').val(),
            benefit_id: $('#benefit_id').val(),
            nomorsep: kunjungan.nosep,
            no_pendaftaran: kunjungan.no_pendaftaran,
            inacbgsamount: tarifInacbgs,
            inacbgscode: kodeInacbgs
        }

        $().docoForm('click', {
            data: pendaftaranData,
            url: '/penjamin-asuransi/informasi-dashboard-integrasi/pendaftaran-cob',
            skipSuccessNotif: true,
            skipErrorNotif: true,
            success: function (response) {
                console.log(response)
                if (response?.meta?.code === 200) {
                    docoNotification('success', 'Berhasil', response?.meta?.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500)
                    return false;
                }
                
                docoNotification('error', 'Peringatan!', response?.meta?.message);
            }
        })
    })
});