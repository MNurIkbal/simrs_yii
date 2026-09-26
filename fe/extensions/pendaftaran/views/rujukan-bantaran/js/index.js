// Event Ready
$(document).on("click", "#tb-rujukan-bantaran tbody tr", function() {
    var statusBelumVerif = 4035;
    var statusSudahVerif = 4036;
    var statusDitolak = 4037;
    var statusMenungguDidaftarkan = 4040;

    var id_bantaran;
    var status_verif_bantaran;
    var status_pelayanan_bantaran;
    var status_pelayanan_bantaran_id;
    var ruangan_id;
    try {
        id_bantaran = table.row(".selected").data().primary;
        status_verif_bantaran = table.row(".selected").data().status_verifikasi_bantaran_id;
        status_pelayanan_bantaran = table.row(".selected").data().status_pelayanan_bantaran;
        status_pelayanan_bantaran_id = table.row(".selected").data().status_pelayanan_bantaran_id;
        ruangan_id = table.row(".selected").data().ruangan_id;
    }
    catch(e) {
        id_bantaran = null;
        status_verif_bantaran = null;
        status_pelayanan_bantaran = null;
        status_pelayanan_bantaran_id = null;
        ruangan_id = null;
    }

    if(id_bantaran == null) {
        $("#btn-detail-bantaran").attr('disabled', true).removeAttr('href');
        $("#btn-dokumen-bantaran").attr('disabled', true).removeAttr('action');
        $("#btn-periksa-bantaran").attr('disabled', true);
        $("#btn-verifikasi-bantaran").attr('disabled', true).removeAttr('href');
        $("#btn-batal-reservasi-bantaran").attr('disabled', true);
        $("#btn-print-qr").attr('disabled', true).removeAttr('href');
    } else {
        $("#btn-detail-bantaran").attr('disabled', false).attr('href', '/pendaftaran/rujukan-bantaran/detail-rujukan?id='+id_bantaran);
        $("#btn-dokumen-bantaran").attr('disabled', false).attr('action', '/pendaftaran/rujukan-bantaran/kirim-dokumen?id='+id_bantaran);
        
        // Disable print QR and periksa if status verifikasi is Belum Verifikasi or if status pelayanan is menunggu didaftarkan
        if(status_verif_bantaran == statusBelumVerif || status_pelayanan_bantaran_id == statusMenungguDidaftarkan) {
            $("#btn-print-qr").attr('disabled', true).removeAttr('href');
        } else {
            $("#btn-print-qr").attr('disabled', false).removeAttr('href');
        }
        
        // Disable periksa button if status is ditolak or belum verifikasi
        if(status_verif_bantaran == statusDitolak || status_verif_bantaran == statusBelumVerif) {
            $("#btn-periksa-bantaran").attr('disabled', true);
        } else {
            $("#btn-periksa-bantaran").attr('disabled', false);
        }
        
        if(status_verif_bantaran == statusBelumVerif) {
            $("#btn-verifikasi-bantaran").attr('disabled', false).attr('href', '/pendaftaran/rujukan-bantaran/verifikasi-rujukan?id='+id_bantaran);
            $("#btn-batal-reservasi-bantaran").attr('disabled', true);
        } else if(status_verif_bantaran == statusSudahVerif) {
            $("#btn-verifikasi-bantaran").attr('disabled', true).removeAttr('href');
            // Enable batal verifikasi only if status pelayanan is menunggu didaftarkan
            if(status_pelayanan_bantaran_id == statusMenungguDidaftarkan) {
                $("#btn-batal-reservasi-bantaran").attr('disabled', false);
            } else {
                $("#btn-batal-reservasi-bantaran").attr('disabled', true);
            }
        } else {
            $("#btn-verifikasi-bantaran").attr('disabled', true).removeAttr('href');
            $("#btn-batal-reservasi-bantaran").attr('disabled', true);
        }
    }
});

// Print QR button click handler
$(document).on("click", "#btn-print-qr", function(e) {
    e.preventDefault();
    e.stopPropagation();

    var id_bantaran;

    try {
        id_bantaran = table.row(".selected").data().primary;
    } catch(e) {
        id_bantaran = null;
    }

    if(id_bantaran == null) {
        docoNotification('warning', 'Perhatian', 'Silakan pilih data rujukan bantaran terlebih dahulu.');
        return false;
    }

    var url = '/pendaftaran/rujukan-bantaran/print-qr?id=' + id_bantaran;
    window.open(url, '_blank');
    return false;
});

// Periksa button click handler
$(document).on("click", "#btn-periksa-bantaran", function(e) {
    e.preventDefault();
    
    var statusMenungguDidaftarkan = 4040;
    var id_bantaran;
    var ruangan_id;
    var status_pelayanan_bantaran_id;
    try {
        id_bantaran = table.row(".selected").data().primary;
        ruangan_id = table.row(".selected").data().ruangan_id;
        status_pelayanan_bantaran_id = table.row(".selected").data().status_pelayanan_bantaran_id;
    } catch(e) {
        id_bantaran = null;
        ruangan_id = null;
        status_pelayanan_bantaran_id = null;
    }
    
    if(id_bantaran != null) {
        var url = '/pendaftaran/rujukan-bantaran/periksa?id=' + id_bantaran;
        if(ruangan_id != null) {
            url += '&ruanganId=' + ruangan_id;
        }
        url += '#view-sbar';
        
        // Only show confirmation if status is menunggu didaftarkan
        if(status_pelayanan_bantaran_id == statusMenungguDidaftarkan) {
            confirmationDialog('Apakah Anda yakin akan memeriksa tahanan ini? Status Pelayanan akan berubah menjadi Dalam Pelayanan', function(confirmed) {
                if (!confirmed) {
                    return;
                }
                window.location.href = url;
            });
        } else {
            window.location.href = url;
        }
    }
});

// Batal Verifikasi button click handler
$(document).on("click", "#btn-batal-reservasi-bantaran", function(e) {
    e.preventDefault();
    
    var id_bantaran;
    var no_rujukanbantaran;
    try {
        id_bantaran = table.row(".selected").data().primary;
        no_rujukanbantaran = table.row(".selected").data().no_rujukanbantaran;
    } catch(e) {
        id_bantaran = null;
        no_rujukanbantaran = null;
    }
    
    if(id_bantaran == null) {
        docoNotification('warning', 'Perhatian', 'Silakan pilih data rujukan bantaran terlebih dahulu.');
        return;
    }
    
    confirmationDialog('Apakah Anda yakin ingin membatalkan verifikasi untuk rujukan <b>' + (no_rujukanbantaran || '') + '</b>?', function() {
        $.ajax({
            url: baseUrl + 'pendaftaran/rujukan-bantaran/batal-verifikasi',
            type: 'POST',
            data: {
                bantaran_id: id_bantaran
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    docoNotification('success', 'Berhasil', response.message || 'Verifikasi bantaran berhasil dibatalkan.');
                    table.ajax.reload(null, false);
                } else {
                    docoNotification('error', 'Gagal', response.message || 'Gagal membatalkan verifikasi bantaran.');
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = 'Terjadi kesalahan saat membatalkan verifikasi.';
                
                try {
                    var response = JSON.parse(xhr.responseText);
                    if(response.message) {
                        errorMessage = response.message;
                    }
                } catch(e) {
                    errorMessage = xhr.responseText || error;
                }
                
                docoNotification('error', 'Error', errorMessage);
            }
        });
    });
});
