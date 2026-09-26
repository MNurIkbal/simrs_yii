var { pasien_id, pendaftaran_id, jenis_pelayanan } = jsVar;
var tableModalRanap = null;
var tempProgramDatas = [];
var programDatasPayload = [];

$(function () {
    tempProgramDatas = [];
    initDatatable();
    initButtonPeriksaEvent();
})

function initDatatable() {
    tableModalRanap = $("#table_pilih_program_ranap").docoTabel({
        select: {
            style: "multi",
            selector: "td:first-child.select-checkbox"
        },
        filter: false,
        sorting: [2, "desc"],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: false,
        ajax: `/fisioterapi/informasi-pasien-fisioterapi-ranap/program-ranap?pasien_id=${pasien_id}&pendaftaran_id=${pendaftaran_id}&jenis_pelayanan=${jenis_pelayanan}&`,
        columnDefs: [
            {
                orderable: false,
                className: "select-checkbox",
                targets: 0,
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
                orderable: false,
                visible: false,
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
            }
        ],
        drawCallback: function () {
            let api = this.api();
            let dataRows = api.rows({ page: "current" }).data();
            $.each(dataRows, function (key, val) {
                // Default Checked,
                const currentDatas = {
                    programterapi_id: val.programterapi_id,
                    pendaftaran_id: val.pendaftaran_id,
                    pasien_id: val.pasien_id
                }
                const statusProgramFisioId = val.status_program_fisio_id
                const programTerapiId = val.programterapi_id
                const objIndex = tempProgramDatas.findIndex((obj => obj.programterapi_id == programTerapiId))
                const isEmpty = (objIndex < 0) | (objIndex === false)
                if (!isEmpty) {
                    tableModalRanap.row(`:eq(${key})`).select();
                }
                // statusProgramFisioId tidak OPEN dan CLOSE
                if (statusProgramFisioId != 1171 && statusProgramFisioId != 1170) {
                    $(tableModalRanap.row(`:eq(${key})`).node()).find(`.select-checkbox`).removeClass(`select-checkbox`)
                }
            })
            initSelectedEvent();
        },
    });
}

function initSelectedEvent() {
    $("#table_pilih_program_ranap tbody tr td.select-checkbox").off(`click`);
    $("#table_pilih_program_ranap tbody tr td.select-checkbox").on(`click`, function (event) {
        event.preventDefault();
        const isSelected = !$(this).parent().hasClass('selected')
        const selectedRow = tableModalRanap.row(this).data()
        const currentDatas = {
            programterapi_id: selectedRow.programterapi_id,
            pendaftaran_id: selectedRow.pendaftaran_id,
            pasien_id: selectedRow.pasien_id,
        }
        if (isSelected) {
            tempProgramDatas.push(currentDatas);
        } else {
            const programTerapiId = currentDatas.programterapi_id
            const objIndex = tempProgramDatas.findIndex((obj => obj.programterapi_id == programTerapiId))
            const isEmpty = (objIndex < 0) | (objIndex === false)
            if (!isEmpty) tempProgramDatas.splice(objIndex, 1);
        }
        if (tempProgramDatas.length > 0) {
            $(`#btn-periksa-ranap-modal`).removeAttr(`disabled`);
        } else {
            $(`#btn-periksa-ranap-modal`).attr(`disabled`, true);
        }
    });
}

function initButtonPeriksaEvent() {
    $(`#btn-periksa-ranap-modal`).off(`click`);
    $(`#btn-periksa-ranap-modal`).on(`click`, function () {
        periksaMultipleOrder();
    });
}

function getPayload() {
    let selectedDatas = tempProgramDatas
    const programTerapiIds = [];
    let pendaftaranId = null;
    let pasienId = null;
    let programTerapiIdsString = ``
    selectedDatas.map(function (val) {
        if (!pendaftaranId) pendaftaranId = val.pendaftaran_id;
        if (!pasienId) pasienId = val.pasien_id;
        programTerapiIds.push(val.programterapi_id)
        programTerapiIdsString = `${programTerapiIdsString}${val.programterapi_id},`
    })
    programTerapiIdsString = removeCommas(programTerapiIdsString);
    const resultPayload = {
        pendaftaran_id: pendaftaranId,
        pasien_id: pasienId,
        programterapi_ids: programTerapiIds
    }
    const queryString = `pendaftaran_id=${pendaftaranId}&program_terapi_ids=${programTerapiIdsString}`;
    return queryString;
}

function removeCommas(str) {
    if (str.startsWith(`,`) && str.endsWith(`,`)) {
        return str.slice(1, -1);
    }
    if (str.startsWith(`,`)) {
        return str.slice(1);
    }
    if (str.endsWith(`,`)) {
        return str.slice(0, -1);
    }
    return str;
}

function periksaMultipleOrder() {
    const queryString = getPayload();
    const targetUrl = `/fisioterapi/pemeriksaan-ranap?${queryString}`
    $.ajax({
        url: targetUrl,
        type: 'GET',
        beforeSend: () => {
            showLoader();
        },
        success: (r) => {
            $('#modal_backdrop').modal('hide');
            docoNotification('success', i18next.t('Berhasil'), i18next.t('Berhasil memilih program'));
            window.location.href = targetUrl;
            return false;
        },
        error: (data) => {
            var error = data.responseJSON.response;
            docoNotification('error', 'Pilih Program Gagal', error.text);
        }
    });
    return true;
}

// $('#tbl_pilih_program tbody').on('click', 'button', function (e) {
//     var disabled = $(this).hasClass('disabled');
//     var _this = $(this);
//     if (disabled == false) {
//         const { programTerapiId, pendaftaranId } = _this.data();
//         $.ajax({
//             url: _this.data('target'),
//             type: 'GET',
//             beforeSend: function () {
//                 showLoader();
//                 _this.button('loading');
//             },
//             success: function (r) {
//                 $('#modal_backdrop').modal('hide');
//                 docoNotification('success', i18next.t('Berhasil'), i18next.t('Berhasil memilih program'));
//                 window.location.href = '/fisioterapi/pemeriksaan-ranap/index?pendaftaran_id=' + pendaftaranId + '&program_terapi_id=' + programTerapiId;
//                 return false;
//             },
//             error: function (data) {
//                 _this.button('reset');
//                 var error = data.responseJSON.response;
//                 docoNotification('error', 'Pilih Program Gagal', error.text);
//             }
//         });
//     }
// });