
var table_rujuk

$(document).ready(function () {

    table_rujuk = $('#tabel-rujuk-balik').docoTabel({
        filter: false,
        info: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        sorting: [[1, 'desc']],
        paginate: true,
        ajax: url+'/get-rujuk-balik?id=' + pendaftaran_id,

        columns: [
            {
                title: "No",
                data: 'rowNum',
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = table_rujuk.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                },
            },
            {
                title: "No. SRB",
                data: 'no_srb',
                searchable: false,
                orderable: false,
            },
            {
                title: "No. SEP",
                data: 'nosep',
                searchable: false,
                orderable: false,
            },
            {
                title: "No. RM",
                data: 'no_rekam_medik',
                searchable: false,
                orderable: false,
            },
            {
                title: "Tanggal Pembuatan",
                data: 'tgl_rujukbalik',
                searchable: false,
                orderable: false,
            },
            {
                title: "Aksi",
                data: 'aksi',
                searchable: false,
                orderable: false,
            },
        ],
    });

    

    var delRow = function (event) {
        event.preventDefault();
        var a = $(this)
        var action = a.attr("action")

        confirmationDialog('Apakah anda yakin menghapus data ini?', (isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: action,
                    method: "POST",
                    success: (response) => {
                        table_rujuk.ajax.reload();
                        return false
                    }
                })
            }
        })
    }

    $('#tabel-rujuk-balik').on('click', '.delete-rujuk-balik', delRow);
})




