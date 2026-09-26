// Global Var
var { id_enc, is_mcu } = phpVars
var tablePaketFisio;
var tabelSubFisio;

$(document).ready(function () {
    var tampung_tindakan = {
        "data": [{
            id: 6,
            text: "Adm. Surat Keterangan Kematian",
            selected: true
        }],
        "draw": "2",
        "recordsTotal": 1,
        "recordsFiltered": 0
    };
    const urlAjax = `/master/tindakan/paket-fisio-get-paket-detail?id=${id_enc}&is_mcu=${is_mcu}`
    tabelSubFisio = $("#table-sub-mcu-" + id_enc).docoTabel({
        filter: false,
        sorting: [
            [1, "asc"]
        ],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        scrollY: "200px",
        scrollCollapse: true,
        ajax: urlAjax,
        columns: [{
            title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false
        },
        {
            title: "Tindakan",
            data: "tindakan_paket_nama",
            searchable: false
        },
        {
            title: "Kelompok",
            data: "kelompoktindakan_nama",
            searchable: false
        },
        {
            title: "Instalasi/Ruangan",
            data: "instalasi_ruangan",
            searchable: false
        }
        ],
    });
    $(".dataTables_filter").hide();
});