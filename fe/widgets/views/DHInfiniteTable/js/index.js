/**
 *
 * Widget ini digunakan untuk menampilkan tabel data (datatable) dengan konsep **infinite scroll**. 
 * Fitur ini bekerja dengan menambahkan tombol **"Load More"** untuk memuat data tambahan sesuai jumlah limit yang ditentukan,
 * dan tombol **"Hide"** untuk mengurangi data yang ditampilkan sesuai jumlah limit.
 * 
 * Widget ini dirancang untuk menghindari penggunaan *count query* pada backend, karena query jenis tersebut dapat memperlambat proses pengambilan data.
 * 
 * Jika terdapat opsi `datatable` yang belum diimplementasikan, Anda bisa menambahkannya. Contohnya adalah `createdRow` dan `initComplete`.
 * 
 * Apabila ingin menambahkan fungsi bawaan pada opsi, yang kemudian diikuti dengan fungsi custom, 
 * gunakan format seperti pada `stateSaveCallback` dan `stateLoadCallback`.
 *
 */

$(document).ready(function () {
    let ajaxPayload = ajaxInfiniteDataTableWidget.data;
    let table;
    let limitDefault = limitInfiniteDataTableWidget;
    let limit = limitInfiniteDataTableWidget;

    if (functionsInfiniteDataTableWidget != undefined || functionsInfiniteDataTableWidget != null) {
        var createdRow = functionsInfiniteDataTableWidget.createdRow;
        var initComplete = functionsInfiniteDataTableWidget.initComplete;
        var drawCallback = functionsInfiniteDataTableWidget.drawCallback;
        var rowCallback = functionsInfiniteDataTableWidget.rowCallback;
        var footerCallback = functionsInfiniteDataTableWidget.footerCallback;
        var filterRendered = functionsInfiniteDataTableWidget.filterRendered;
    }

    table = $('#'+idInfiniteDataTableWidget).docoTabel({
        filter: false,
        cacheFilter: false,
        processing: true,
        serverSide: true,
        paging: false,
        sorting: sortingInfiniteDataTableWidget != undefined || sortingInfiniteDataTableWidget != null ? sortingInfiniteDataTableWidget : false,
        info: false,
        ajax: {
            url: baseUrl + ajaxInfiniteDataTableWidget.url,
            data: function (data) {
                if (ajaxPayload != undefined || ajaxPayload != null) {
                    Object.entries(ajaxPayload).forEach(([key, value]) => {
                        data[key] = value
                    });
                }
                let functionData = functionsInfiniteDataTableWidget.data;
                if (functionData != undefined || functionData != null) {
                    functionData = eval(functionData);
                    functionData(data);
                }
                let currentPageInfiniteDataTable = 0;
                data.start = currentPageInfiniteDataTable * limit;
                data.length = limit;
            },
            dataSrc: function(json) {
                return json.data;
            }
        },
        columns: columnsInfiniteDataTableWidget,
        formFilters: filtersInfiniteDataTableWidget,
        createdRow: createdRow != undefined || createdRow != null ? eval(createdRow) : null,
        initComplete: initComplete != undefined || initComplete != null ? eval(initComplete) : null,
        drawCallback: (settings) => {
            if (settings.json.load_more == true) {
                $('.btn-load-infinite-datatable-widget').prop('disabled', false);
            }  else {
                $('.btn-load-infinite-datatable-widget').prop('disabled', true);
            }

            if (limit <= limitDefault) {
                $('.btn-hide-infinite-datatable-widget').prop('disabled', true);
            } else {
                $('.btn-hide-infinite-datatable-widget').prop('disabled', false);
            }

            if (drawCallback != undefined || drawCallback != null) {
                var functionDrawCallback = eval(drawCallback);
                functionDrawCallback(settings);
            }
        },
        rowCallback: rowCallback != undefined || rowCallback != null ? eval(rowCallback) : null,
        stateSaveCallback: function (settings, data) {
            localStorage.setItem( "DataTables_" + settings.sInstance, JSON.stringify(data) )
            let stateSaveCallback = functionsInfiniteDataTableWidget.stateSaveCallback;
            if (stateSaveCallback != undefined || stateSaveCallback != null) {
                stateSaveCallback = eval(stateSaveCallback);
                stateSaveCallback(data);
            }
        },
        stateLoadCallback: function(settings, callback) {
            let stateLoadCallback = functionsInfiniteDataTableWidget.stateLoadCallback;
            if (stateLoadCallback != undefined || stateLoadCallback != null) {
                stateLoadCallback = eval(stateLoadCallback);
                stateLoadCallback(data);
            }
            return JSON.parse( localStorage.getItem( "DataTables_" + settings.sInstance ) )
        },
        footerCallback: footerCallback != undefined || footerCallback != null ? eval(footerCallback) : null,
        filterRendered: filterRendered != undefined || filterRendered != null ? eval(filterRendered) : null,
    });

    $('.btn-load-infinite-datatable-widget').off('click').on('click', function(event) {
        if (!event.detail || event.detail === 1) {
            limit = limit + limitDefault;
            table.ajax.reload(null, false);
        }
    });

    $('.btn-hide-infinite-datatable-widget').off('click').on('click', function(event) {
        if (!event.detail || event.detail === 1) {
            limit = limit - limitDefault;
            table.ajax.reload(null, false);
        }
    });
})