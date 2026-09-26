/* 
    Author : Randy Vianda Putra (aweutist)
*/


$(document).ready(function() {

       // Event Disetujui
    $(document).on("click", "#data-setujui", function (e) {
        e.preventDefault();
        const action = $(this).attr("data-target");
        let $this = this;
        let table = $('#table-pemesanan-kamar').DataTable();
        let tableData = table.row(".selected").data();
        if (typeof tableData !== 'undefined') {
            const id = tableData.primary;
            const jenis_kelamin = (tableData.jeniskelamin !== null)
                ? tableData.jeniskelamin
                : tableData.jk
            const isPasienBaru = tableData.pasien_id;
            if (isPasienBaru == null) {
                docoNotification('warning', i18next.t('Terjadi Kesalahan'), i18next.t('Belum terdapat kunjungan rawat jalan & IGD, silahkan daftarkan terlebih dahulu!'));
            } else {
                $.ajax({
                    url: '/pendaftaran/pemesanan-kamar/check-kunjungan?no_rekam_medik=' + tableData.no_rekam_medik,
                    type: 'GET',
                    success: function(res) {
                        const dataPost = {
                            kamartempattidur_id: `${tableData.kamartempattidur_id}`,
                            jeniskelamin: `${jenis_kelamin}`,
                            kamarruangan_jenis: `${tableData.kamarruangan_jenis}`,
                            kamarruangan_id: `${tableData.kamarruangan_id}`,
                            jeniskasuspenyakit_id: `${tableData.jeniskasuspenyakit_id}`,
                            no_rekam_medik: `${tableData.no_rekam_medik}`,
                            nama_pasien: `${tableData.nama_pasien}`,
                            pasien_id: `${tableData.pasien_id}`,
                            kelaspelayanan_id: `${tableData.kelaspelayanan_id}`,
                            ruangan_id: `${tableData.ruangan_id}`,
                            bookingkamar_id: `${tableData.bookingkamar_id}`,
                            no_pemesanan: `${tableData.no_pemesanan}`,
                            kamarruangan_nokamar: `${tableData.kamarruangan_nokamar}`,
                            pendaftaran_id: `${res.response.pendaftaran_id}`,
                            is_booking: 1
                        };
                        console.log(dataPost)
                        if (!res.response) {
                            docoNotification('warning', i18next.t('Terjadi Kesalahan'), i18next.t('Belum terdapat kunjungan rawat jalan & IGD, silahkan daftarkan terlebih dahulu!'));
                        } else {
                            $($this).docoForm("click", {
                                url: action + id,
                                data: dataPost,
                                confirmTitle : i18next.t("Konfirmasi"),
                                confirmMessage : i18next.t("Apa anda yakin ingin melanjutkan pendaftaran?"),
                                success : function (data) {
                                    setTimeout(function () {
                                        window.location.href = "/pendaftaran/daftar?id_booking=" + id;
                                    }, 1000);
                                }
                            });
                        }
                    }
                });
            }

        } else {
            docoNotification('warning', i18next.t('Terjadi Kesalahan'), i18next.t('Belum ada data yang dipilih!'));
        }

        return false;
    });

    // Event Ditolak
    $(document).on("click", "#data-ditolak", function (e) {
        e.preventDefault();
        const action = $(this).attr("data-target");
        let table = $('#table-pemesanan-kamar').DataTable();
        let tableData = table.row(".selected").data();
        if (typeof tableData !== 'undefined') {
            const id = tableData.primary;
            const jenis_kelamin = (tableData.jeniskelamin !== null)
                ? tableData.jeniskelamin
                : tableData.jk
            const dataPost = {
                kamartempattidur_id: `${tableData.kamartempattidur_id}`,
                jeniskelamin : `${jenis_kelamin}`,
                kamarruangan_jenis: `${tableData.kamarruangan_jenis}`,
                kamarruangan_id: `${tableData.kamarruangan_id}`,
                is_batal: 2
            };

            $(this).docoForm("click", {
                url: action + id,
                data: dataPost,
                confirmTitle: i18next.t("Konfirmasi"),
                confirmMessage: i18next.t("Apa anda yakin ingin mengubah status data?"),
                success: function (data) {
                    table.draw();
                }
            });
        } else {
            docoNotification('warning', i18next.t('Terjadi Kesalahan'), i18next.t('Belum ada data yang dipilih!'));
        }

        return false;
    });

    // Event Batal
    $(document).on("click", "#data-batal", function (e) {
        e.preventDefault();
        const action = $(this).attr("data-target");
        let table = $('#table-pemesanan-kamar').DataTable();
        let tableData = table.row(".selected").data();
        if (typeof tableData !== 'undefined') {
            const id = tableData.primary;
            const jenis_kelamin = (tableData.jeniskelamin !== null)
                ? tableData.jeniskelamin
                : tableData.jk
            const dataPost = {
                kamartempattidur_id: `${tableData.kamartempattidur_id}`,
                jeniskelamin: `${jenis_kelamin}`,
                kamarruangan_jenis: `${tableData.kamarruangan_jenis}`,
                kamarruangan_id: `${tableData.kamarruangan_id}`,
                is_batal: 1
            };

            $(this).docoForm("click", {
                url: action + id,
                data: dataPost,
                confirmTitle: i18next.t("Konfirmasi"),
                confirmMessage: i18next.t("Apa anda yakin ingin mengubah status data?"),
                success: function (data) {
                    table.draw();
                }
            });
        } else {
            docoNotification('warning', i18next.t('Terjadi Kesalahan'), i18next.t('Belum ada data yang dipilih!'));
        }

        return false;
    });

    $(document).on('click', 'tbody tr', function() {
        let table = $('#table-pemesanan-kamar').DataTable();
        let tableData = table.row(".selected").data();
        if (typeof tableData !== 'undefined') {
            const primaryId = tableData.primary;
            const statusBooking = tableData.statusbooking;
            if (parseInt(statusBooking) == 371) {
                $('.data-edit').show();
                $('#data-setujui').show();
                $('#data-ditolak').show();
                $('#data-batal').show();
            } else {
                $('.data-edit').hide();
                $('#data-setujui').hide();
                $('#data-ditolak').hide();
                $('#data-batal').hide();
            }
        }

    });

});