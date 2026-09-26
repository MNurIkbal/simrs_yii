var table_log_diet
var tableDietUrlParams = new URLSearchParams({
    id: (typeof id != 'undefined' ? id : ''),
    konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : '')
}).toString();

table_log_diet = $('#tabel-log-diet').docoTabel({
    filter: false,
    info: false,
    displayLength: 10,
    processing: true,
    serverSide: true,
    paginate: true,
    sorting: [[1, 'desc']],
    ajax: `${_url}/get-data-diet-pasien?${tableDietUrlParams}`,
    columns: [
        {
            data: null,
            orderable: false,
            width: '10%',
            render: (data, rowElement, rowData, rowAdditionalData) => {
                var tableInfo = table_log_diet.page.info()
                return tableInfo.start + rowAdditionalData.row + 1
            }
        },

        {
            title: "Tanggal Permintaan Makan",
            data: 'tgl_permintaanmakan',
            searchable: false,
            width: '25%',
        },
        {
            title: "Jenis Diet",
            data: 'catatan_diet',
            searchable: false,
            orderable: false,

        },
        {
            title: "Nama Pegawai",
            data: 'pegawai_nama',
            searchable: false,
            orderable: false,
            width: '30%',

        }

    ],
    fixedColumns: true,
});

$(() => {
    $("#btn-save-diet-pasien").bind("click", () => {
        let _form = $("#diet-pasien-form").serializeArray();
        $().docoForm("click", {
            data: _form,
            skipScrollUp: false,
            skipConfirm: true,
            url: $("#diet-pasien-form").attr("action"),
            success: function (data) {
                $("#modal-lab").find(".close").click();
            },
        });
    });
});

$(document).ready(function() {
    validasiClosePopup();
});

$("#dietpasienform-catatan_diet").on('keypress blur', function() {
    validasiClosePopup();
});


/* FUNGSI VALIDASI CLOSE POP UP */
function validasiClosePopup() {
    var dietForm = $("#dietpasienform-catatan_diet").val();

    if(dietForm != '') {
        var isUpdate = true;
    } else {
        var isUpdate = false;
    }

    if(isUpdate == true) {
        $('.close-modal-diet').attr('data-dismiss-confirmation', 'modal');
        $('.close-modal-diet').removeAttr('data-dismiss');
    } else {
        $('.close-modal-diet').removeAttr('data-dismiss-confirmation');
        $('.close-modal-diet').attr('data-dismiss', 'modal');
    }
}