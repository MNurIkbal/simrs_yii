/*
* @Author: Sigit
* @Date:   2018-05-16 13:11:15
* @Last Modified by:   Ragnar-Lothbroc
* @Last Modified time: 2019-03-22 11:24:14
*/

// Global Var
var tindakanInapTable;
var tindakanDaruratTable;
var tindakanLabTable;
var tindakanRadTable;
var tindakanAmbulanTable;
var obatTable;

// Event click
$(document).on("click", ".data-reset", function() {
    // Reload table
    tindakanInapTable.draw();
    tindakanDaruratTable.draw();
    tindakanLabTable.draw();
    tindakanRadTable.draw();
    tindakanAmbulanTable.draw();
    obatTable.draw();
});

function initTable(tableId, _param){
       var id = $('#pendaftaran-id').val();
       var sumTindakan = 0;
       $(tableId).docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-list-tindakan?id="+id+"&key="+_param,
                dataSrc: function(data){
                    sumTindakan = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalTindakan, data: "tgl_pelayanan"},
                {title: namaTindakan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", orderable: false, class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", orderable: false, class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );
                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumTindakan);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
       $('.dataTables_filter').hide();
  }
var loadTindakan = function(){
    $.each(arrRuangan, function(k, v){
        initTable('#table-tindakan-'+v, v)
    })
}

// Event Ready
$(document).ready(function() {
    // Assign pendaftaran id
    var pendaftaran_id = $("#pendaftaran-id").val();
    console.log(pendaftaran_id);
    var rawatInap = "RI";
    var rawatDarurat = "RD";
    var rawatJalan = "RJ";
    var lab = "LAB";
    var radiologi = "RAD";
    var gudang = "GUD";
    var rehab = "REHAB";
    var rm = "RM";
    var kasir = "KASIR";
    var informasi = "INFO";
    var pendaftaran = "PDF";
    var ambulan = "ambulan";
    var bedah = "BDH";
    var tindakan = $('#id_tindakanAll').val()
    var tindakanRj_id = $('#id_tindakanRj').val();
    var tindakanRi_id = $('#id_tindakanRi').val();
    var tindakanRd_id = $('#id_tindakanRd').val();
    var obat_id = $('#id_obat').val();
    var lab_id = $('#id_lab').val();
    var radiologi_id = $('#id_radiologi').val();
    var id_gudang = $('#id_gudang').val();
    var id_rehab = $('#id_rehab').val();
    var id_rm = $('#id_rm').val();
    var id_kasir = $('#id_kasir').val();
    var id_informasi = $('#id_informasi').val();
    var id_pendaftaran = $('#id_pendaftaran').val();
    var id_bedah = $('#id_bedah').val();

    if(tindakan){
        loadTindakan()
    }

    // Generate Table
    var sumTindakanRJ = 0;
    if (tindakanRj_id) {
        tindakanInapTable = $("#tb-tindakan-jalan").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+rawatJalan,
                dataSrc: function(data){
                    sumTindakanRJ = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalTindakan, data: "tgl_pelayanan"},
                {title: namaTindakan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", orderable: false, class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", orderable: false, class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumTindakanRJ);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    // Generate Table
    var sumTindakanRI = 0;
    if (tindakanRi_id) {
        tindakanInapTable = $("#tb-tindakan-inap").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+rawatInap,
                dataSrc: function(data){
                    sumTindakanRI = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalTindakan, data: "tgl_pelayanan"},
                {title: namaTindakan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", orderable: false, class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", orderable: false, class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumTindakanRI);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    // Generate Table
    var sumTindakanRD = 0;
    if (tindakanRd_id) {
        tindakanDaruratTable = $("#tb-tindakan-darurat").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+rawatDarurat,
                dataSrc: function(data){
                    sumTindakanRD = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalTindakan, data: "tgl_pelayanan"},
                {title: namaTindakan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", orderable: false, class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", orderable: false, class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumTindakanRD);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    // Generate Table
    var sumLabTable = 0;
    if (lab_id) {
        tindakanLabTable = $("#tb-tindakan-lab").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+lab,
                dataSrc: function(data){
                    sumLabTable = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalPemeriksaan, data: "tgl_pelayanan"},
                {title: namaPemeriksaan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumLabTable);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    // Generate Table
    var sumRadiologi = 0;
    if (radiologi) {
        tindakanRadTable = $("#tb-tindakan-radiologi").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+radiologi,
                dataSrc: function(data){
                    sumRadiologi = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalPemeriksaan, data: "tgl_pelayanan"},
                {title: namaPemeriksaan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumRadiologi);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    // Generate Table
    var sumTableObat = 0;
    if (obat_id) {
        obatTable = $("#tb-obat").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-obat?id="+pendaftaran_id,
                dataSrc: function(data){
                    sumTableObat = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalTindakan, data: "tgl_pelayanan"},
                {title: namaObat, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", orderable: false, class: "text-right", render: $.fn.dataTable.render.number( '.', ',', 2 )},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\$,]/g, '.')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(5, {page: 'all'}).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                // Total over this page
                // pageTotal = api.column(5, {page: 'current'}).data().reduce(function (a, b) {
                //     // Return
                //     return intVal(a) + intVal(b);
                // }, 0 );


                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumTableObat);

                // Update footer
                $(api.column(5).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }
    // Generate Table
    var sumTableGudang = 0;
    if (id_gudang) {
        tindakanRadTable = $("#tb-gudang").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+gudang,
                dataSrc: function(data){
                    sumTableGudang = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalPemeriksaan, data: "tgl_pelayanan"},
                {title: namaPemeriksaan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumTableGudang);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    // Generate Table
    var sumRehab = 0;
    if (id_rehab) {
        tindakanRadTable = $("#tb-rehab").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+rehab,
                dataSrc: function(data){
                    sumRehab = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalPemeriksaan, data: "tgl_pelayanan"},
                {title: namaPemeriksaan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumRehab);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    // Generate Table
    var sumRM = 0;
    if (id_rm) {
        tindakanRadTable = $("#tb-rm").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+rm,
                dataSrc: function(data){
                    sumRM = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalPemeriksaan, data: "tgl_pelayanan"},
                {title: namaPemeriksaan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumRM);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    // Generate Table
    var  sumKasir = 0;
    if (id_kasir) {
        tindakanRadTable = $("#tb-kasir").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+kasir,
                dataSrc: function(data){
                    sumKasir = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalPemeriksaan, data: "tgl_pelayanan"},
                {title: namaPemeriksaan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumKasir);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    // Generate Table
    var sumInformasi = 0;
    if (id_informasi) {
        tindakanRadTable = $("#tb-informasi").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+informasi,
                dataSrc: function(data){
                    sumInformasi = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalPemeriksaan, data: "tgl_pelayanan"},
                {title: namaPemeriksaan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumInformasi);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    // Generate Table
    var sumPendaftaran = 0;
    if (id_pendaftaran) {
        tindakanRadTable = $("#tb-pendaftaran").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+pendaftaran,
                dataSrc: function(data){
                    sumPendaftaran = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalPemeriksaan, data: "tgl_pelayanan"},
                {title: namaPemeriksaan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumPendaftaran);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    // Generate Table
    var sumBedah = 0;
    if (id_bedah) {
        tindakanRadTable = $("#tb-bedah").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+bedah,
                dataSrc: function(data){
                    sumBedah = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalPemeriksaan, data: "tgl_pelayanan"},
                {title: namaPemeriksaan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumBedah);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }

    var sumAmbulan = 0;
    if(ambulan) {
        tindakanAmbulanTable = $("#table-tindakan-ambulan").docoTabel({
            filter: false,
            displayLength: 5,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            sorting: [[1, "asc"]],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Semua"]],
            ajax: {
                url: baseUrl + "kasir/inf-pasien-pulang/get-data-tindakan?id="+pendaftaran_id+"&instalasi="+ambulan,
                dataSrc: function(data){
                    sumAmbulan = data.total_biaya;
                    return data.data;
                }
            },
            columns: [
                {title: no, data: "row", orderable: false},
                {title: tanggalTindakan, data: "tgl_pelayanan"},
                {title: namaTindakan, data: "tindakan_obat_nama"},
                {title: qty, data: "qty", orderable: false},
                {title: tarifSatuan, data: "tarif_satuan", orderable: false, class: "text-right"},
                {title: tarifCyto, data: "tarif_cyto", orderable: false, class: "text-right"},
                {title: jumlahTarif, data: "sub_total", orderable: false, class: "text-right"},
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            footerCallback: function(row, data, start, end, display) {
                // Api
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    // Return
                    return typeof i === 'string' ? i.replace(/[\Rp.]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                // Add dots for number formatting
                function addDots(nStr) {
                    // Define string
                    nStr += '';
                    x = nStr.split('.');
                    x1 = x[0];
                    x2 = x.length > 1 ? '.' + x[1] : '';

                    // Regex
                    var rgx = /(\d+)(\d{3})/;

                    // Loop
                    while (rgx.test(x1)) {
                        // Replace
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                    }

                    // Return
                    return x1 + x2;
                };

                // Total over all pages
                total = api.column(6).data().reduce(function (a, b) {
                    // Return
                    return intVal(a) + intVal(b);
                }, 0 );

                var sumPage = docoHelper.convertToRupiah(total);
                var sumAll = docoHelper.convertToRupiah(sumAmbulan);

                // Update footer
                $(api.column(6).footer()).html(
                    'Rp.'+sumPage+'(Total : Rp.'+sumAll+')'
                );
            }
        });
    }
});
