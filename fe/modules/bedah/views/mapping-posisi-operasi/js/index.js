
let table;
$(document).on('click', '#reload-btn', function () {
    table.draw();
});
$(document).on('click', '#datatable tbody tr', function () {
    $("#edit-btn").prop('disabled', typeof table.row('.selected').data() == 'undefined')
})
$(() => {
    $("input[name='prosentase']").bind('keyup', ({ currentTarget }) => {
        const value = docoHelper.convertToAngka($(currentTarget).val())
        if (value > 100) {
            $(currentTarget).val(100)
        }
    })
    $("#edit-btn").bind('click', () => {
        const dataRow = table.row('.selected').data() ? table.row('.selected').data() : null;
        if (typeof dataRow != 'undefined') {
            $('#surgery-action-form').select2InfinityScroll({
                url: '/bedah/mapping-posisi-operasi/tindakan-operasi',
                additionalOption: {
                    data: [
                        {
                            id: dataRow.daftartindakan_id,
                            text: dataRow.daftartindakan_nama,
                        }
                    ]
                }
            })

            $('#surgery-action-form').val(dataRow.daftartindakan_id).trigger('change')
            $("#surgery-team-form").val(dataRow.timoperasi_id).trigger('change')
            $("input[name='prosentase']").val(dataRow.persentase)
            $("input[name='is_active']").prop('checked', dataRow.is_active)
            $.uniform.update()
            $("#form-modal .modal-title").text('Ubah Mapping')
            $("#form-modal").modal({
                backdrop: 'static',
                keyboard: false
            })
            $("#mapping-form").prop('action', `/bedah/mapping-posisi-operasi/update?daftartindakan_id=${dataRow.daftartindakan_id}&timoperasi_id=${dataRow.timoperasi_id}`)
        }
    })
    $("#add-btn").bind('click', () => {
        $("#form-modal .modal-title").text('Tambah Mapping')
        $("#form-modal").modal({
            backdrop: 'static',
            keyboard: false
        })
        $("#mapping-form").trigger('reset').find('select').val(null).trigger('change')
        $("#mapping-form").prop('action', '/bedah/mapping-posisi-operasi/create')
        $("#mapping-form").data('title', 'add')
        $("input[name='is_active']").prop('checked', true)
        $.uniform.update()
    })
    $('#surgery-action-form').select2InfinityScroll({
        url: '/bedah/mapping-posisi-operasi/tindakan-operasi'
    })
    $("input[type='checkbox']").uniform({
        radioClass: 'choice'
    });
    $.uniform.update()
    table = $('#datatable').docoTabel({
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
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: '/bedah/mapping-posisi-operasi/datatable',
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
            {
                title: "Posisi Operasi",
                data: 'timoperasi_nama'
            },
            {
                title: "Nama Tindakan",
                data: 'daftartindakan_nama'
            },
            {
                title: "Prosentase",
                data: 'persentase',
                searchable: false,
                className: 'text-center',
                render: (data) => {
                    return `${data}%`
                }
            },
            {
                title: "Status",
                data: 'action',
                searchable: false,
                orderable: false
            },
        ],
        drawCallback: () => {
            $(".status-box").toggleSwitchStatus({
                callbackSuccess: (res) => {
                    docoNotification('success', 'Sukses', res.meta.message)
                }
            })
        }
    })
    $('.dataTables_filter').hide();
    $('.filter-form').datatableBootstrapFilter(table, [])
    $("#save-btn").bind('click', () => {
        validateForm($("#mapping-form"), null, {
            rules: {
                daftartindakan_id: 'required',
                timoperasi_id: 'required',
                prosentase: 'required|between:0,100'
            },
            successCallback: () => {
                $.ajax({
                    url: $("#mapping-form").prop('action'),
                    method: 'POST',
                    data: $("#mapping-form").serializeArray(),
                    success: (res) => {
                        const titleType = $("#mapping-form").data('title')
                        docoNotification('success', 'Sukses', `Data berhasil ${titleType == 'add' ? 'disimpan' : 'diperbarui'}`)
                        $("#form-modal").modal('hide')
                        table.draw()
                    }
                })
            }
        })
    })
})