$(document).ready(function(){
            table = $('#example').docoTabel({
                filter: true,
                sorting: [[2, 'asc']],
                columnDefs: [ {
                    targets: 3,
                    render: function(data, type, row) {
                        return data + ' ' + row['satuan_besar'];
                    }
                }, {
                    targets: 4,
                    render: function(data, type, row) {
                        return data + ' ' + row['satuan_kecil'];
                    }
                } ],
                paging: false,
                bPaginate: false,
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: function(data, callback, settings){
                    $.ajax({
                        url: baseUrl+'gudang/informasi-adjustment-obat-alkes/get-data-adjustment?id='+obatalkes+'&no_adjusmen='+no_adjustment,
                        data: data,
                        success: function(data) {
                            callback(data);
                        }
                    });
                },
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                    {title: 'Kode Obat Alkes', data: 'obatalkes_kode', searchable: false,orderable: false},
                    {title: 'Nama Obat Alkes', data: 'obatalkes_nama', searchable: false,orderable: false},
                    {
                        title: 'Qty Penerimaan',
                        data: 'qty_input',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Qty Konversi',
                        data: 'qty_konversi',
                        searchable: false,
                        orderable: false
                    },
                    {title: 'Tanggal Kadaluarsa', data: 'tgl_kadaluarsa',searchable: false,orderable: false},
                    {
                        title: 'Harga Netto (Rp.)',
                        data: 'harga_netto',
                        searchable: false,
                        orderable: false,
                        class:'text-right'
                    },
                    {title: 'No. Batch',  data: 'no_batch',searchable: false,orderable: false},
                    {title: 'Keterangan',  data: 'keterangan',searchable: false,orderable: false}
                ],
            });

        $('.dataTables_filter').hide();
    });