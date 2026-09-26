
var table_nurse
var tableNurseUrlParams = new URLSearchParams({
        id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
        konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
        pasienadmisi_id: (typeof pasienadmisi_id != 'undefined' ? pasienadmisi_id : '')
    }).toString();

$(document).ready(function () {

    table_nurse = $('#tabel-nursing-note').docoTabel({
        filter: false,
        info: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        sorting: [[1, 'desc']],
        paginate: true,
        ajax: url+'/get-nursing-note?'+tableNurseUrlParams,

        columns: [
            {
                title: "No",
                data: 'rowNum',
                orderable: false,
                // render: (data, rowElement, rowData, rowAdditionalData) => {
                //     var tableInfo = table_nurse.page.info()
                //     return tableInfo.start + rowAdditionalData.row + 1
                // },
            },

            {
                title: "Tanggal",
                data: 'tanggal',
                searchable: false,
            },
            {
                title: "Jam",
                data: 'jam',
                searchable: false,
            },
            {
                title: "Kegiatan Perawat",
                data: 'kegiatan_perawat',
                searchable: false,
                orderable: false,
                width: "350px",
            },
            {
                title: "Catatan",
                data: 'catatan',
                searchable: false,
                orderable: false,
                width: "157px",
            },
            {
                title: "Nama Pegawai",
                data: 'nama_pegawai',
                searchable: false,
                orderable: false,
            },
            {
                title: "Aksi",
                data: 'aksi',
                searchable: false,
                orderable: false,
            }
        ],
        // fnRowCallback: function(nRow,data,iDisplayIndex, iDisplayIndexFull) {
        //     if(data.is_deleted){
        //         $(nRow).addClass("strikeout");
        //     }
        // }
    });
})

var delRow = function (event) {
    event.preventDefault();
    let _formData = $(this).serializeArray();
    var a = $(this)
    console.log(a.attr("href"))
    $.ajax({
        url: a.attr("href"),
        method: "POST",
        data: _formData,
        success: (response) => {
            $('#tabel-nursing-note').DataTable().ajax.reload();
            return false
        }
    })
}

$(document).on('click', '#delete-note', delRow);



