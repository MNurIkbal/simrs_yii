var table
$(document).ready(function() {
    sumberTtv = JSON.parse(sumberTtv)
    const optionSumberTtv = Object.entries(sumberTtv).map(([_, value]) => ({
        id: value,
        text: value
    }));

    moment.locale("en");
    table = $('#example').docoTabel({
        filter: true,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        sorting: [[1, "desc"]],
        ajax: {
            url: `/${modul}${url}/get-data-monitoring-ttv?pendaftaran_id=${pendaftaran_id}`,
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                var tableInfo = table.page.info();
                return tableInfo.start + rowAdditionalData.row + 1;
                },
            },
            {
                name: 'tanggal_ttv',
                data: null,
                render: function(data, type, row) {
                    var date = new Date(row.tanggal_ttv);
                    var formattedDate = date.toLocaleString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }).replace(/\./g, ':');
                    return row.sumberttv + ' <br/>-----------------<br/> ' + formattedDate;
                }
            },
            {
                data: 'jenisttv'
            },
            {
                data: 'tingkatkesadaran'
            },
            {
                data: 'sistol'
            },
            {
                data: 'diastol'
            },
            {
                data: 'nadi'
            },
            {
                data: 'respirasi'
            },
            {
                data: 'spo2'
            },
            {
                data: 'suhu'
            },
            {
                data: 'tinggi_badan'
            },
            {
                data: 'berat_badan'
            },
            {
                data: 'gcs_e'
            },
            {
                data: 'gcs_v'
            },
            {
                data: 'gcs_m'
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    var hasAccess = 'disabled';
                    var lastModifiedDate = '';

                    if(row.hasAccess) {
                        hasAccess = '';
                    }

                    if(row.last_modified_date !== null) {
                        lastModifiedDate = "<br/><br/>Telah diupdate pada : <br/>" + new Date(row.last_modified_date).toLocaleString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }).replace(/\./g, ':');
                    }
                    
                    let sumberTtvId = row.sumberttv_id
                    if(sumberTtvId == sumberTtvEws || sumberTtvId == sumberTtvSbar) {
                        hasAccess = 'disabled';
                    }

                    // Edit button
                    buttons += `<button 
                        class="btn btn-info btn-xs btn-edit btn-toolbar btn-labeled ${hasAccess}" 
                        data-width="75%"
                        data-toggle="modal"
                        data-target="#modal_backdrop"
                        data-id="${row.vitalsign_id}"
                        ${hasAccess}
                        action="/${modul}${url}/edit-ttv?vitalsign_id=${row.vitalsign_id}&pendaftaran_id=${pendaftaran_id}&sumberttv_id=${row.sumberttv_id}"
                    >
                        <b><i class="fa fa-eye"></i></b>Edit
                    </button> <br/>`;
                    
                    // Delete button
                    buttons += `<button 
                        class="btn btn-danger btn-xs btn-labeled btn-delete ${hasAccess}" 
                        ${hasAccess}
                        data-id="${row.vitalsign_id}"
                    >
                        <b><i class="fa fa-trash"></i></b>Hapus
                    </button>`;

                    buttons += lastModifiedDate;

                    return buttons;
                }
            }
        ],

        formFilters: [
        {
            fieldName: "tanggal_ttv",
            label: "Tanggal TTV",
            type: {
            name: "rangeDate",
            },
        },
        {
            fieldName: "sumberttv",
            label: "Asal Inputan TTV",
            type: {
                name: "select",
                payload: optionSumberTtv,
            },
        },
        ],
    });
    $(".dataTables_filter").hide();
    $("#btn-search__example").css("display", "none");
    $("#btn-reset__example").css("display", "none");
    $(".more-filter").css("display", "none");
    
    // Handle edit button click
    $('#example').on('click', '.btn-edit', function(event) {
        event.preventDefault();
    });

    // Handle delete button click
    $('#example').on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var vitalsignId = $(this).data('id');
        $(this).docoForm('click', {
            url: `/${modul}${url}/remove-ttv`,
            type: 'POST',
            data: {
                'vitalsign_id': vitalsignId
            },
            success: function () {
                table.draw();
            },
            error: function () {
                table.draw();
            }

        })
    });

    $(document).on("click", ".btn-reset", function (e) {
        const tableId = "example";
        const element = $(`#filter-section__${tableId}`);
        const formWrapper = $(`#form-filter__${tableId}`);
        element.find("input").val("");
        element.find("select").val(null).trigger("change");
        element
        .find("#tanggal_ttv-startDate")
        .val(moment().format("DD-MMM-YYYY"))
        .trigger("change");
        element
        .find("#tanggal_ttv-endDate")
        .val(moment().format("DD-MMM-YYYY"))
        .trigger("change");
        const tableElement = $(`#${tableId}`).DataTable();
        showLoader();
        tableElement.context[0].ajax.data.advancedFilter =
        serializeArrayToJson(formWrapper);
        tableElement.ajax.url(`/${modul}${url}/get-data-monitoring-ttv?pendaftaran_id=${pendaftaran_id}`).load();
    });

    $(document).off('click', '#btn-cetak-ttv').on('click', '#btn-cetak-ttv', function(e) {
        e.preventDefault();
        window.open(`/reports/viewer/monitoring-ttv?id=${pendaftaranIdDecrypt}`);
    });

});

