/*
* @Author: Rizqi Fitrianto
* @Date:   2018-04-13 17:27:43
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-08-30 14:44:14
*/

$(document).on('change','.selectPenjamin', function(e){
    arrTarif = [];
    dataTarif = [];
    var kpId = $('.selectKp').val();
    var ruanganId = $('.selectRuangan').val();
    var penjamin_id = $('.selectPenjamin').val();
    var karcisTitle;
    var hargaTitle;
    var pasienStatus = 1;
    if(karcisTitle == ''){
        karcisTitle = $('.karcis-title').text();
    }
    if(hargaTitle == ''){
        hargaTitle =$('.harga-title').text();
    }
    
    if($('input[name=chk-statuspasien]').is(':checked')){
        pasienStatus = 0;
    }
    var tbl;
    tbl = $('#tbl-karcis').docoTabel({
        filter: true,
        destroy: true,
        paging: false,
        sorting: [[0, 'asc']], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+'pendaftaran/daftar/get-karcis?ruangan_id='+ruanganId+'&kp_id='+kpId+'&status='+pasienStatus+'&penjamin_id='+penjamin_id,
        fnFooterCallback: function(row, data, start, end, display) {
              var api = this.api();
               var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
              total_tarif = api
                .column(4)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
              $( api.column(2).footer() ).addClass('kolom-total-tarif').html('Rp. '+docoHelper.convertToRupiah(total_tarif));
          },
        drawCallback: function(settings){
            var api = this.api();
            $.each(api.rows().data(), function(key, val){
                arrTarif.push(val);
            });
            $.each(arrTarif, function(key, val){
                if(val.is_default == true){
                    // dataTarif.push(val);
                    dataTarif[val.number] = val;
                }
            })
        },
        columns: [
            {
                title: 'No',
                data: 'number',
                searchable: false,
                orderable: false
            },
            {title: karcisTitle, data: 'daftartindakan_nama',searchable: false,orderable: false},
            {title: hargaTitle, data: 'tmp_view',searchable: false,orderable: false},
            {title: '',  data: 'aksi',searchable: false,orderable: false},
            {data: 'tmp_total', visible:false, searchable: false, orderable: false}
        ],
        
    });
    $('.dataTables_filter').hide();
});

$(document).on('click','.check-aksi', function(){
    let subtotal_tarif;
    var key = $(this).attr('data-key'); 
    if($(this).is(':checked')){
        total_tarif = total_tarif+arrTarif[key].harga_tariftindakan;
        dataTarif[key] = arrTarif[key];
        // console.log(dataTarif);
    }
    else{
        total_tarif = total_tarif-arrTarif[key].harga_tariftindakan;
        if(typeof dataTarif[key] != 'undefined'){
            delete dataTarif[key];
        }
    }
    $('.dataTables_scrollFoot').find('.kolom-total-tarif').html('Rp. '+ docoHelper.convertToRupiah(total_tarif) );
});
