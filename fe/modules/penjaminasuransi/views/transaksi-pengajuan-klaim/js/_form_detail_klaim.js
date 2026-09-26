var _cacheTagihan = {};
var _cacheTanggalMasukStart = {};
var _cacheTanggalMasukEnd = {};
var _cacheInstalasiKlaim = {};
var _cacheRuanganKlaim = {};
var _datatabels = [];

if (localStorage.getItem("tagihan")) {
    var _cacheTagihan = JSON.parse(localStorage.getItem("tagihan"));
}

if (localStorage.getItem("pk_tanggal_masuk_start")) {
    var _cacheTanggalMasukStart = JSON.parse(localStorage.getItem("pk_tanggal_masuk_start"));
}

if (localStorage.getItem("pk_tanggal_masuk_end")) {
    var _cacheTanggalMasukEnd = JSON.parse(localStorage.getItem("pk_tanggal_masuk_end"));
}

if (localStorage.getItem("instalasi_klaim")) {
    var _cacheInstalasiKlaim = JSON.parse(localStorage.getItem("instalasi_klaim"));
}

if (localStorage.getItem("ruangan_klaim")) {
    var _cacheRuanganKlaim = JSON.parse(localStorage.getItem("ruangan_klaim"));
}

var startDate;
var endDate;
var instalasi_nama;
var ruangan_nama;

var tanggalstart = function () {
    startDate = _cacheTanggalMasukStart;
    endDate = _cacheTanggalMasukEnd;
    $(".tanggal_masuk").val(startDate + " s/d " + endDate);
    $("#pengajuanklaimform-tgl_pelayanandari").val(startDate);
    $("#pengajuanklaimform-tgl_pelayanansampai").val(endDate);
    $("#pengajuanklaimform-tgl_keluardari").val(startDate);
    $("#pengajuanklaimform-tgl_keluarsampai").val(endDate);
}

var instalasi = function () {
    instalasi_nama = _cacheInstalasiKlaim.value;
    if (instalasi_nama == "") {
        $(".instalasi_nama").val("Semua Instalasi");
    }
    else {
        $(".instalasi_nama").val(instalasi_nama);
    }
}

var ruangan = function () {
    ruangan_nama = _cacheRuanganKlaim.value;
    if (ruangan_nama == "") {
        $(".ruangan_nama").val("Semua Ruangan");
    }
    else {
        $(".ruangan_nama").val(ruangan_nama);
    }
}

var listIdPendaftaran = {};
var total_tagihan = 0;
var maxTglPasienPulang = 0;
var tglPengajuanKlaim;
var tglJatuhTempo;
var tglToday = new Date();
var tglPengajuanKlaimElement;
var tglJatuhTempoElement;

var totaltagihan = function () {
    var _inc = 0;

    $.each(_cacheTagihan, function (key, value) {
        listIdPendaftaran[key] = {
            pasien_id: value.pasien_id,
            piutang: value.total_pengajuan,
            pasienadmisi_id: value.pasienadmisi_id,
            pendaftaran_id: value.pendaftaran_id,
            pembayaranpelayanan_id: value.pembayaranpelayanan_id,
        };

        value.rowNum = _inc + 1;
        _datatabels[_inc] = value;
        _inc++;

        var _pengajuan = value.total_pengajuan ? value.total_pengajuan : 0;
        total_tagihan += parseInt(_pengajuan);

        var tgl_pulang = new Date(value.tglpasienpulang);
        if(tgl_pulang.getTime() > maxTglPasienPulang) {
            maxTglPasienPulang = tgl_pulang.getTime();
        }
    });

    $(".total_piutang").val(total_tagihan).trigger("change");
    $(".pendaftaran_id").val(JSON.stringify(listIdPendaftaran));
}

$(document).ready(function () {
    var _penjamin = $("#pengajuanklaimform-penjamin_nama").val();

    if (_penjamin == "") {
        window.location.href = "/penjamin-asuransi/transaksi-pengajuan-klaim";
    }

    $(".pickadate-input").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        format: "dd-mmm-yyyy",
    });
    
    totaltagihan();
    tanggalstart();
    instalasi();
    ruangan();

    tglPengajuanKlaim = new Date(maxTglPasienPulang);

    tglPengajuanKlaimElement =  $("#tgl_pengajuanklaim");
    tglPengajuanKlaimElement.pickadate("set").set("select", tglToday);
    tglPengajuanKlaimElement.pickadate({
        min: tglPengajuanKlaim,
    });

    tglJatuhTempo = new Date();
    var formatedTglKlaim = new Date();
    formatedTglKlaim.setDate(formatedTglKlaim.getDate() + 30);

    tglJatuhTempoElement = $("#tgl_jatuhtempo");
    tglJatuhTempoElement.pickadate("set").set("select", formatedTglKlaim);
    tglJatuhTempoElement.pickadate("set").set("min", [
        formatedTglKlaim.getFullYear(),
        formatedTglKlaim.getMonth(),
        formatedTglKlaim.getDate(),
    ]);
    
    $("#tabel_detail").DataTable({
        data: _datatabels,
        filter: false,
        scrollX: true,
        sorting: [[5, "DESC"]], 
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "No. Pendaftaran",
                data: "no_pendaftaran"
            },
            {
                title: "No Rekam Medik",
                data: "no_rekam_medik"
            },
            {
                title: "No Invoice",
                data: "no_pembayaran"
            },
            {
                title: "Tanggal Masuk",
                data: "tgl_pendaftaran"
            },
            {
                title: "Tanggal Keluar",
                data: "tglpasienpulang"
            },
            {
                title: "No SEP",
                data: "nosep"
            },
            {
                title: "Nama Pasien",
                data: "nama_pasien"
            },
            {
                title: "Instalasi",
                data: "instalasi_nama"
            },
            {
                title: "Ruangan",
                data: "ruangan_nama"
            },
            {
                title: "Tagihan",
                data: "total_tagihan_label",
                searchable: false
            },
            {
                title: "Jumlah Pasien Bayar",
                data: "total_sdh_bayar_label",
                searchable: false
            },
            {
                title: "Jumlah Discount",
                data: "total_discount_label",
                searchable: false
            },
            {
                title: "Jumlah Pengajuan",
                data: "total_pengajuan_label",
                searchable: false
            },
        ],
        drawCallback: function (settings) {
            var api = this.api();
            var dataRows = api.rows({ page: "current" }).data();
            var minDate = dataRows[0].tglpasienpulang;
            var formatedDate = new Date(maxTglPasienPulang);
            tglPengajuanKlaimElement.pickadate("set").set("min", [
                formatedDate.getFullYear(),
                formatedDate.getMonth(),
                formatedDate.getDate(),
            ]);
            if (tglPengajuanKlaimElement.val() < minDate) {
                tglPengajuanKlaimElement.pickadate("set").set("select", tglToday);
                tglPengajuanKlaimElement.pickadate({
                    min: formatedDate,
                });
                formatedDate.setDate(formatedDate.getDate() + 30);
                tglJatuhTempoElement.pickadate("set").set("select", formatedDate);
                tglJatuhTempoElement.pickadate("set").set("min", [
                    formatedDate.getFullYear(),
                    formatedDate.getMonth(),
                    formatedDate.getDate(),
                ]);
            }
        },
    })


    $(document).on('change blur', '#tgl_pengajuanklaim', function (e) {
        e.preventDefault();
        var tglPengajuan = $(this).val();
        if (tglPengajuan) {
            var tglPengajuanFormated = new Date(tglPengajuan);
            var minDateJatuhTempo = new Date(tglPengajuan);
            tglPengajuanFormated.setDate(tglPengajuanFormated.getDate() + 30);
            tglJatuhTempoElement.pickadate("set").set("select", tglPengajuanFormated);
            tglJatuhTempoElement.pickadate("set").set("min", [
                minDateJatuhTempo.getFullYear(),
                minDateJatuhTempo.getMonth(),
                minDateJatuhTempo.getDate(),
            ]);
        }
        e.stopImmediatePropagation();
    });
});

$("#simpan-pengajuan").on("click", function (e) {
    e.preventDefault();
    var _data = $("#proses-form").serializeArray();
    _data.push({
        name: "instalasi_id",
        value: _cacheInstalasiKlaim.key
    });
    _data.push({
        name: "ruangan_id",
        value: _cacheRuanganKlaim.key
    });
    $().docoForm("click", {
        url: $("#proses-form").attr("action"),
        data: _data,
        success: function (data) {
            localStorage.clear();
            const result = data.response;
            var pengajuanklaim_id;
            var no_pengajuanklaim;

            if(typeof(result.pengajuanklaim_id) != "undefined" && result.pengajuanklaim_id !== null) {
                pengajuanklaim_id = result.pengajuanklaim_id;
            } else {
                pengajuanklaim_id = result.data.pengajuanklaim_id;
            }
            if(typeof(result.no_pengajuanklaim) != "undefined" && result.no_pengajuanklaim !== null) {
                no_pengajuanklaim = result.no_pengajuanklaim;
            } else {
                no_pengajuanklaim = result.data.no_pengajuanklaim;
            }

            showNotifPrint({
                noPengajuan: no_pengajuanklaim,
                pengajuanKlaimId: pengajuanklaim_id
            });
        },
        error: function (response) {
            if (response.status == 422) {
                docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan pada input.');
            }
            if (response.status == 500) {
                const jsonResponse = response.responseJSON.response
                docoNotification('error', jsonResponse.title, jsonResponse.message);
            }
        }
    });
});

function showNotifPrint({ noPengajuan, pengajuanKlaimId }) {
    (new PNotify({
        title: `Proses Berhasil !`,
        text: `Pembuatan data dengan Nomor Pengajuan <strong>` + noPengajuan + `</strong>` +
            ` berhasil disimpan, apakah Anda ingin melakukan cetak?`,
        addclass: `alert alert-success alert-arrow-right alert-styled-right`,
        type: `success`,
        buttons: {
            closer: false,
            sticker: false
        },
        hide: false,
        confirm: {
            confirm: true,
            buttons: [
                {
                    text: `Ya`,
                    addClass: `btn btn-xs btn-success`,
                },
                {
                    text: `Tidak`,
                    addClass: `btn btn-xs btn-danger`,
                }
            ]
        },
        history: {
            history: false
        }
    })).get().on(`pnotify.confirm`, function () {
        // Prevent Popup Blocker
        setTimeout(() => {
            window.location.href = "/penjamin-asuransi/transaksi-pengajuan-klaim";
        }, 5000)
        setTimeout(() => {
            window.open(`/penjamin-asuransi/transaksi-pengajuan-klaim/cetak-pengajuan?pengajuanklaim_id=` + pengajuanKlaimId)
        }, 3000);
    }).on(`pnotify.cancel`, function () {
        window.location.href = "/penjamin-asuransi/transaksi-pengajuan-klaim";
    });
}

function validationClient() {
    $(`[name="PengajuanKlaimForm[catatan]"]`).on("change", function (e) {
        const parentElement = $(this).parent();
        const currentVal = $(this).val();
        if (currentVal && currentVal.length > 200) {
            parentElement.parent().addClass(`has-error`);
            parentElement.parent().find(`.help-block`).html(`
                <div class="help-block">
                    <i class="fa fa-exclamation-circle" aria-hidden="true"></i> 
                    &nbsp; Catatan harus memiliki paling banyak 200 karakter.
                </div>
            `);
        }
    });
}