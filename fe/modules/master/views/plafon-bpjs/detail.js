var tableDetail;
var _baseUrl = "/master/plafon-bpjs";

$(document).ready(function(){
    tableDetail = $("#detail_ruangan").docoTabel({
        filter: true,
        select: {
            style: "os",
            selector: "tr",
        },
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        cache: false,
        cacheFilter: false,
        ajax: {
            url: _baseUrl + "/get-data-detail?plafonbpjs_id=" + id,
        },
        columns: [
        {
            data: null,
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData, rowAdditionalData) => {
                var tableInfo = tableDetail.page.info();
                return tableInfo.start + rowAdditionalData.row + 1;
            },
        },
        {
            title: "Ruangan",
            data: "ruangan_nama",
            render: (data) => {
                return data == "" || data == null ? "-" : data;
            },
        },
        ],
        formFilters: [
            {
                fieldName: "ruangan_nama",
                label: "Ruangan",
                type: {
                    name: "dropdownScroll",
                    url: `${_baseUrl}/filters`,
                    additionalPayload: {
                        type: 'ruangan_nama',
                        instalasi_id: instalasiId
                    }
                },
            },
        ],
    });

    $(".dataTables_filter").hide();
    $("#btn-search__detail_ruangan").css("display", "none");
    $("#btn-reset__detail_ruangan").css("display", "none");
    $(document).on("click", ".btn-reset-detail-ruangan", function (e) {
        const tableId = "detail_ruangan";
        const element = $(`#filter-section__${tableId}`);
        const formWrapper = $(`#form-filter__${tableId}`);
        element.find("input").val("");
        element.find("select").val(null).trigger("change");
        const tableElement = $(`#${tableId}`).DataTable();
        showLoader();
        tableElement.context[0].ajax.data.advancedFilter =
        serializeArrayToJson(formWrapper);
        tableElement.ajax.reload();
    });
})

