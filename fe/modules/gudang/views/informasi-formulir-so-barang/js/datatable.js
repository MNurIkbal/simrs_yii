var table

$(() => {
    table = $('#inf-formulir-so').docoTabel({
        filter: false,
        sorting: [],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: {
            url: '/gudang/informasi-formulir-so-barang/get-data',
        },
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }],
        select: {
            style: 'os',
            selector: 'td'
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                defaultContent: ''
            },
            {
                data: null,
                orderable: false,
                class: 'text-center',
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = table.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                data: "tglformulir",                
            },
            {
                data: "noformulir"
            },
            {
                data: "tglstokopname",
                searchable: false,
            },
            {
                data: "nostokopname",
            },
            {
                data: "instalasi_nama",
                searchable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    return `${rowData.instalasi_nama} / ${rowData.ruangan_nama}`
                }
            },
            {
                data: "pegawaiverifikasi",
                searchable: false,
            },
            {
                data: "status_so",
                orderable: false,
            },
            {
                data: "instalasi_id",
                visible : false
            },
            {
                data: "ruangan_id",
                visible : false
            },
        ],
        formFilters: [
            {
                fieldName: 'tglformulir',
                label: 'Tanggal Formulir',
                type : {
                    name : 'rangeDate'
                }
            },
            {
                fieldName: 'noformulir',
                label: 'Nomor Formulir'
            },
            {
                fieldName: 'nostokopname',
                label: 'Nomor Stok Opname'
            },
            {
                fieldName: 'status_so',
                label: 'Status Stok Opname',
                type: {
                    name: 'select',
                    payload: [
                        {id: 2,text: "Belum Input Hasil"},
                        {id: 0,text: "Belum Verifikasi"},
                        {id: 1,text: "Sudah Verifikasi"}
                    ]
                }
            },
            {
                fieldName: 'instalasi_id',
                label: 'Instalasi',
                type: {
                    name: 'select',
                    payload: dropdowninstalasi
                }
            },
            {
                fieldName: 'ruangan_id',
                label: 'Ruangan',
                type: {
                    name: 'select',
                }
            },
        ],
        filterRendered: (wrapper) => {
            $(wrapper).find('[name="tempat_instalasi"]').val($('[name="tempat_instalasi"] option:eq(1)').val()).trigger('change')
        
            $(wrapper).find('[name="instalasi_id"]').bind('change', ({ currentTarget }) => {
                console.log($(currentTarget).val());
                if ($(currentTarget).val() == '' || $(currentTarget).val() == null) {
                    var data = [
                        {
                            id: '',
                            text: '- Semua -'
                        }
                    ]
                    $(wrapper).find('[name="ruangan_id"]').html('')
                    $(wrapper).find('[name="ruangan_id"]').select2({
                        data,
                    })
                } else {
                    $.ajax({
                        url: `/gudang/informasi-formulir-so-barang/get-ruangan`,
                        data: {
                            additionalPayload: {
                                ruangan: $(currentTarget).val()
                            }
                        },
                        success: (res) => {
                            var data = [
                                {
                                    id: '',
                                    text: '- Semua -'
                                }
                            ]
                            data = data.concat(res)
                            $(wrapper).find('[name="ruangan_id"]').html('')
                            $(wrapper).find('[name="ruangan_id"]').select2({
                                data,
                            })
                        },
                        error: (xhr, status, error) =>{
                            var err = eval("(" + xhr.responseText + ")");
                            console.log((err.Message))
                        }
                    })
                }
            })
        },
    })
})

$(document).on('keypress',function(e) {
    if(e.which == 13) {
        $("#btn-search__inf-formulir-so").click();
    }
});

$(document).on("click", "#inf-formulir-so tr", function(){
    var tbl = table.row(".selected").data();
    $(".data-delete").show();
    $(".btn-custom").attr("disabled",false);
    $(".btn-stok-opname").attr("disabled",false);
    $(".data-delete").attr("disabled",false);
    $('#cetak-formulir-so-barang').attr('disabled', typeof tbl == 'undefined');
    if (typeof tbl === "undefined") return true;

    if(tbl.status_so == "Belum Input Hasil"){
        $(".btn-custom").attr("disabled",true);
    }else if(tbl.status_so == "Belum Verifikasi"){
        $(".btn-custom").attr("disabled",false);
    }else if(tbl.status_so == "Sudah Verifikasi"){
        $(".btn-custom").attr("disabled",false);
        $(".btn-stok-opname").attr("disabled",true);
        $(".data-delete").attr("disabled",true);
    }


});