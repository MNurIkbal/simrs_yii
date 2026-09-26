var { pasien_id, pendaftaran_id, jenis_pelayanan } = jsVar;

$(document).ready(() => {
    tableModalPeriksa = $("#table_pilih_program_rajal").docoTabel({
        select: {
            style: "multi",
            selector: "tr"
        },
        filter: false,
        sorting: [2, "desc"],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: false,
        // scrollX: true,
        ajax: "/fisioterapi/informasi-pasien-fisioterapi/program?pasien_id="+pasien_id+"&pendaftaran_id="+pendaftaran_id+"&jenis_pelayanan="+jenis_pelayanan +"&",
        columnDefs: [
            {
                orderable: false,
                className: "select-checkbox",
                targets: 0,
                checkBox: {
                    selectedRow: true
                },
                createdCell: (td, cellData, rowData, row, col) => {
                    var isRemove = cellData.aksi;
                    if (isRemove) {
                        $(td).removeClass('select-checkbox');
                    }
                }
            }
        ],
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: "",
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal Rujukan",
                data: "tgl_rujukan",
                searchable: false,
                orderable: false
            },
            {
                title: "Jenis Terapi",
                data: "jenispemeriksaanfisio_nama",
                searchable: false,
                orderable: false
            },
            {
                title: "Nama Terapi", 
                data: "terapi_nama",
                searchable: false,
                orderable: false
            },
            {
                title: "Dokter Perujuk", 
                data: "dokter_perujuk",
                searchable: false,
                orderable: false
            },
            {
                title: "Frekuensi",
                data: "frekuensi",
                searchable: false,
                orderable: false
            },
            {
                title: "Realisasi",
                data: "realisasi",
                searchable: false,
                orderable: false
            },
            {
                title: "Sisa Terapi",
                data: "sisa",
                searchable: false,
                orderable: false
            },
            {
                title: "Tidak Hadir",
                data: "jumlah_ketidakhadiran",
                searchable: false,
                orderable: false
            },
            {
                title: "Bayar",
                data: "bayar",
                searchable: false,
                orderable: false
            },
            {
                title: "Status",
                data: "status_program_fisio_nama",
                searchable: false,
                orderable: false
            }
        ],
        createdRow: (row, data, dataIndex) => {
            var isRemove = data.aksi;
            if (isRemove) {
                $(row).addClass('ignoreme');
            }
        }
    });

    tableModalPeriksa.on('select', function (e, dt, type, indexes) {
        if (type === 'row') {
            var rows = tableModalPeriksa.rows(indexes).nodes().to$();
            $.each(rows, function() {
                if ($(this).hasClass('ignoreme')) tableModalPeriksa.row($(this)).deselect();
            })
        }
    });

    $("#table_pilih_program_rajal tbody").on("click", "tr", () => {
        var data = tableModalPeriksa.row(".selected").data();
        if (!data) {
            $("#btn-periksa-modal").attr("disabled", true);
            return false;
        }

        // Status program != OPEN button PERIKSA akan tetap disabled
        const statusProgramId = data.status_program_fisio_id;
        if (statusProgramId != 1171 && statusProgramId != 1170) {
            $("#btn-periksa-modal").attr("disabled", true);
            return false;
        }

        // Enabled button periksa
        $("#btn-periksa-modal").attr("disabled", false);

    });

    $("#btn-periksa-modal").on("click", () => {
        var rows = tableModalPeriksa.rows('.selected').indexes();
        var data = tableModalPeriksa.rows(rows).data();
        var programTerapiIds = [];
        var programTerapiDetailIds = [];
        var dataTarget = $("#btn-periksa-modal").attr('data-target') + pendaftaran_id;

        for (let i = 0; i < data.length; i++) {
            programTerapiIds.push(data[i].programterapi_id);
            programTerapiDetailIds.push(data[i].programterapidetail_id);
        }

        $.ajax({
            url: dataTarget,
            type: 'GET',
            beforeSend: () => {
                showLoader();
            },
            success: (r) => {
                $('#modal_backdrop').modal('hide');
                docoNotification('success', i18next.t('Berhasil'), i18next.t('Berhasil memilih program'));
                window.location.href='/fisioterapi/pemeriksaan?pendaftaran_id='+pendaftaran_id+'&program_terapi_id='+programTerapiIds+'&program_terapi_detail_id='+programTerapiDetailIds;
                return false;
            },
            error: (data) => {
                var error = data.responseJSON.response;
                docoNotification('error', 'Pilih Program Gagal', error.text);
            }
        });
    });
});
