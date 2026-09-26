/*
* @Author: Rizqi Fitrianto
* @Date:   2018-01-17 16:23:58
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2018-01-17 16:57:23
*/
	
	$(function(){
        var pickdate = $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',            
        });        
        $('.pickadate').val($(this).data('default'));
    });

    // $(document).ready(function(){
    // 	table = $("#example").docoTabel({
    //         filter: true,
    //         sorting: [[1, "asc"]], 
    //         displayLength: 10,
    //         processing: true,
    //         serverSide: true,
    //         stateSave: true,
    //         scrollX: true,
    //         ajax: baseUrl+"rm/inf-pemesanan-obat/get-data",
    //         columns: [
    //             {
    //                 title: "No",
    //                 data: "rowNum",
    //                 searchable: false,
    //                 orderable: false
    //             },
    //             {title: "Tanggal Pemesanan",  data: "lokasirak_nama"},
    //             {title: "Nomor Pemesanan",  data: "lokasirak_namalainnya"},
    //             {title: "Instalasi Tujuan",  data: "lokasirak_nama"},
    //             {title: "Ruangan Tujuan",  data: "lokasirak_namalainnya"},
    //             {title: "Status",  data: "lokasirak_nama"},                
    //             {
    //                 title: "Aksi",
    //                 data: "aksi",
    //                 searchable: false,
    //                 orderable: false,
    //                 class: "text-center"
    //             }
    //         ]
    //     });
    //     $(".dataTables_filter").hide();
    //     $(".filter-form").datatableBootstrapFilter(table);


    // });