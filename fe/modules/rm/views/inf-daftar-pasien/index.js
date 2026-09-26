/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

// Global Var
var table;
$("#btn-proses").attr("disabled", true);
// Event Reload
$(document).on("click", ".data-reload", function () {
    $('.advancedFilter [type=reset]').click();
    table.draw();
});

// Event Ready
$(document).ready(function () {
    // Generate Table
    table = $("#table-inf-daftar-pasien").docoTabel({
        filter: true,
        order: [],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        columnDefs: [{
            orderable: false,
            className: "select-checkbox",
            targets: 0
        }, {
            targets: 5,
            render: function (data, type, row) {
                return row['no_rekam_medik'] + " - " + row['nama_pasien'];
            }
        }, {
            targets: 6,
            render: function (data, type, row) {
                return row['instalasi_nama'] + " - " + row['ruangan_nama'];
            }
        }, {
            targets: 7,
            render: function (data, type, row) {
                return row['carabayar_nama'] + " - " + row['penjamin_nama'];
            }
        }],
        select: {
            style: "os",
            selector: "tr"
        },
        ajax: baseUrl + "rm/inf-daftar-pasien/get-data",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                width: "50px",
                defaultContent: ""
            },
            {
                data: "rowNum",
                name: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal Kunjungan",
                data: "tgl_pendaftaran"
            },
            {
                title: "No Pendaftaran",
                data: "no_pendaftaran", searchable: false
            },
            {
                title: "Status Pasien",
                data: "status_pasien_nama", searchable: false
            },
            {
                title: "No. RM - Nama Pasien",
                data: "no_rekam_medik",
                searchable: false
            },
            {
                title: "Instalasi - Ruangan",
                data: "instalasi_nama",
                name: "instalasi_id"
            },
            {
                title: "Cara Bayar - Penjamin",
                data: "carabayar_nama",
                name: "carabayar_id"
            },
            {
                title: "Dokter",
                data: "nama_pegawai"
            },
            {
                title: "Status",
                data: "status_konfirmasirm",
            },
            {
                title: "No. SEP",
                data: "nosep"
            },
        ],
        createdRow: function (row, data, dataIndex) {
            if (data.status_konfirmasirm_id == "664") {
                $(row).addClass("row-jingga");
            } else {
                $(row).removeClass("row-jingga");
            }
        }
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table,
        [
            [
                2,
                '<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=1></div>'
            ],
        ], {
        2: 0
    }, true
    );

    dateRangeHelper(".startDate", ".endDate", ".targetDate");

    $(document).on("click", "#table-inf-daftar-pasien tr", function () {
        var _data = table.row(".selected").data();
        if (typeof _data !== "undefined") {
            if (_data.status_konfirmasirm_id == "664") {
                $("#btn-proses").attr("disabled", false);
            } else {
                $("#btn-proses").attr("disabled", true);
            }
        }
    });
    $(document).on("click", "#btn-proses", function () {
        var tableData = table.row(".selected").data();
        if (typeof tableData !== "undefined" && "pendaftaran_id" in tableData) {
            $(this).docoForm("click", {
                url: "/rm/inf-daftar-pasien/update-proses?id=" + tableData.pendaftaran_id,
                title: "Sukses",
                method: "POST",
                type: "json",
                success: function () {
                    table.draw();
                }
            });
        } else {
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih");
        }
    });

    if (typeof (EventSource) !== "undefined") {
        var evtSource = new EventSource("/rm/inf-daftar-pasien/stream-pendaftaran");

        evtSource.onmessage = function (e) {
            var statusdaftar = JSON.parse(e.data);
            if (statusdaftar.is_new_registration) {
                notif();
                table.draw();
            }
        };

    } else {
        // document.getElementById("result").innerHTML = "Sorry, your browser does not support server-sent events...";
    }

    function notif() {
        var player = $("#playerNewData");

        player[0].defaultPlaybackRate = 1;
        docoHelper.getDataFromAction('/rm/inf-daftar-pasien/get-sound-file', function (response) {
            if (response.data != null) {
                player[0].src = response.data;
                docoHelper.repeatPlayAudio(player[0], 2);
            } else {
                console.warn(response.errorThrown);
            }
        });
    }
});