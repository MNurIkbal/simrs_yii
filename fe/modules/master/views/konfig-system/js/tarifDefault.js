// Global Var
var table;
var state_key = window.location.pathname;
var isEdit = false

$(document).ready(function () {
    table = $('#table-konfig-tarif-default').docoTabel({
        filter: true,
        columnDefs: [{
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }],
        select: {
            style: 'os',
            selector: 'tr'
        },
        sorting: [
            [4, 'desc']
        ],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: true,
        stateDuration: -1,
        ajax: baseUrl + 'master/konfig-system/get-konfig-tarif-default',
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
            {
                title: no, data: "rowNum", searchable: false, orderable: false 
            },
            {
                title: cara_bayar,
                data: 'carabayar_nama',
                name: 'carabayar_nama',
                searchable: false,
                orderable: false,
            },
            {
                title: penjamin,
                data: 'penjamin_nama',
                name: 'penjamin_nama',
                searchable: false,
                orderable: false,
                class:"penjamindefault_id",
                render: (columnData, row, data) => {
                    return `<select class="select-penjamin-input-${data.carabayar_id}" name="konfigtarif" data-carabayar_id="${data.carabayar_id}" data-penjamindefault_id="${data.penjamindefault_id}"><option value="${data.carabayar_id}">${data.penjamin_nama}</option></select> 
                            <p class="select-penjamin-text-${data.carabayar_id}">${data.penjamin_nama}</p>`
                }
            },
            {
                title: 'Action',
                data: 'btn_action',
                searchable: false,
                orderable:false
            },
        ],
        createdRow: (row, data, dataIndex, cells) => {
            $(row).find('.penjamindefault_id').addClass(`default_id-${data.carabayar_id}`)
        },
        drawCallback: (settings) => {
            $('.edit-konfig-tarif').bind('click', ({delegateTarget}) => {
                const itemIndex = table.data().toArray().findIndex(item => item.carabayar_id == $(delegateTarget).data('carabayar_id') )
                if ( itemIndex < 0) {
                    return null;
                }
                $(`tr`).children(`td.default_id-${$(delegateTarget).data('carabayar_id')}`).children($('span.select2')).show()
                $(`.select-penjamin-text-${$(delegateTarget).data('carabayar_id')}`).hide()
                $(`.edit-konfig-tarif[data-carabayar_id=${$(delegateTarget).data('carabayar_id')}]`).hide()
                $(`.cancel-konfig-tarif[data-carabayar_id=${$(delegateTarget).data('carabayar_id')}]`).removeClass("hidden")
            })

            $('.cancel-konfig-tarif').bind('click', ({delegateTarget}) => {
                $(`tr`).children(`td.default_id-${$(delegateTarget).data('carabayar_id')}`).children($('span.select2')).hide()
                $(`.select-penjamin-text-${$(delegateTarget).data('carabayar_id')}`).show()
                $(`.edit-konfig-tarif[data-carabayar_id=${$(delegateTarget).data('carabayar_id')}]`).show()
                $(`.cancel-konfig-tarif[data-carabayar_id=${$(delegateTarget).data('carabayar_id')}]`).addClass("hidden")
            })

            $("select[name='konfigtarif']").select2InfinityScroll({
                url: '/master/konfig-system/get-penjamin-default'
            })

            $("select[name='konfigtarif']").on('change', ({ currentTarget }) => {
                if ($(currentTarget).val() != '') {
                    const { carabayar_id } = $(currentTarget).data()
                    $(`tr`).children(`td.default_id-${carabayar_id}`).children($('span.select2')).hide()
                    $.ajax({
                        url: '/master/konfig-system/update-penjamin-default',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            carabayar_id,
                            penjamindefault_id: $(currentTarget).val(),
                        }),
                        beforeSend: function() {
                            $(`.select-penjamin-text-${carabayar_id}`).text('Memuat . . .').show()
                         },
                        success: function(data) {
                            table.draw()
                        }
                    })
                }
            })
            $('.select2').hide()
        },
    });
    $('.dataTables_filter').hide();
});