/*
* @Author: Rizqi Fitrianto
* @Date:   2018-01-11 10:34:02
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2018-01-24 11:42:09
*/

    $(function(){
        var pickdate = $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',
        });
    });

    let selectRm = $('.selectRm');
    let selectPengiriman = $('.selectPengiriman');
    let selectRak = $('.selectRak');
    let selectSubRak = $('.selectSubRak');
    let noRm = $('.noRm');
    let noPengiriman = $('.noPengiriman');
    let list = {list_rm: {} };  
    let listx = {list_pengiriman: {} };  
    let listz = {list_rak: {} };  
    let listy = {list_subrak: {}};
    let listw = {list_warna: {}};
    let norak = $('.noRak');
    let nosubrak = $('.noSubRak');
    let dokrm = $('.dokrm_id');
    let warnadok = $('.warnadok');
    let tglrekammedik = $('.tglrekammedik');

    $.ajax({
        url: '/rm/transaksi-penyimpanan-dokumen/get-list-data',
        type: 'json',
        success: function(response){
            let listrm = [];
            let data_rm =response.data_rm;  
            for (var i in data_rm) {
                listrm.push({id: data_rm[i].pasien_id, text: data_rm[i].no_rekam_medik});
                list.list_rm[data_rm[i].pasien_id] = data_rm[i];
            }
            let data_warna = response.data_warna;
            for(var w in data_warna){
                listw.list_warna[data_warna[w].warnadokrm_kodewarna] = data_warna[w].warnadokrm_id;
            }
            selectRm.select2({
                data: listrm,
                type: "GET",
                quietMillis: 50,
                minimumInputLength: 2,
            });
            selectRm.change(function(){
                var id = $(this).val();
                var selected = list.list_rm[id];
                
                $.ajax({
                    url: '/rm/transaksi-penyimpanan-dokumen/get-dokumen?id='+id,
                    type: 'json',
                    success: function(response){
                        let datadokumen = response.data_dokumen[0];
                        if(typeof datadokumen !== 'undefined'){
                            selectRak.val(response.data_dokumen[0].lokasirak_id).trigger('change')
                            selectSubRak.val(response.data_dokumen[0].subrak_id).trigger('change')
                            //nosubrak.val(response.data_dokumen[0].subrak_id);
                            dokrm.val(datadokumen.dokrekammedis_id);
                        }else{
                            norak.val("");
                            nosubrak.val("");
                            dokrm.val("");
                        }
                    }
                });
                 
                if (typeof selected !== 'undefined') {
                    noRm.val(selected.pasien_id);
                    let warnakode = $(".selectRm option:selected").text().substring(0,1);
                    warnadok.val(listw.list_warna[warnakode]);
                    tglrekammedik.val(selected.tgl_rekam_medik);
                }
                
            });

            let listpengiriman = [];
            let data_pengiriman =response.data_pengiriman;
             
            for (var x in data_pengiriman) {
                listpengiriman.push({id: data_pengiriman[x].pengirimanrm_id, text: data_pengiriman[x].nomor_pengiriman});            
                listx.list_pengiriman[data_pengiriman[x].pengirimanrm_id] = data_pengiriman[x];
            }
            selectPengiriman.select2({
                data: listpengiriman,
                type: "GET",
                quietMillis: 50,
                minimumInputLength: 1,
            });

            selectPengiriman.change(function(e){
                var id = $(this).val();
                var selected_pengiriman = listx.list_pengiriman[id];
                if (typeof selected_pengiriman !== 'undefined') {
                    noPengiriman.val(selected_pengiriman.nomor_pengiriman);
                } 
            });

            let listrak = [];
            let data_rak = response.data_rak;

            for (var z in data_rak){
                listrak.push({id: data_rak[z].lokasirak_id, text: data_rak[z].lokasirak_nama});
                listz.list_rak[data_rak[z].lokasirak_id] = data_rak[z];
            }            
            selectRak.select2({
                data: listrak,
                type: "GET",
                quietMillis: 50,
                minimumInputLength: 1,
            });
            
            selectRak.change(function(e){
                norak.val($(this).val());

                let listsubrak = [];
                let data_subrak = response.data_subrak[$(this).val()];
                if(typeof data_subrak !== 'undefined'){
                    for (var z in data_subrak){
                        listsubrak.push({id: data_subrak[z].subrak_id, text: data_subrak[z].subrak_nama});
                        listy.list_subrak[data_subrak[z].subrak_id] = data_subrak[z];
                    }

                //console.log(data_subrak[$(this).val()]);
                selectSubRak.select2({
                    data: listsubrak,
                    type: "GET",
                    quietMillis: 50,
                });

                }

            });
            selectSubRak.change(function(e){
                nosubrak.val($(this).val());
            });
        }
    });

    $(document).ready(function(){
        $("#penyimpanandokumen-form").docoForm("submit", {
            success: function (data) {
                // if (data.status == 201)
                    this.formInput[0].reset();
            }
        });
    });
    //event on change lokasi rak
    $('#no_rak').on('change', function(){
        var valuedata = $(this).val();
         console.log(valuedata);
        if (valuedata) {
            $.ajax({
                type: 'GET',
                url: '/rm/transaksi-penyimpanan-dokumen/get-data-subrak?lokasirak_id='+valuedata,
                success: function(response){
                    var select = $('#no_sub_rak');
                    select.children().remove();
                    $('#no_sub_rak').append($('<option>', { value : '' }).text('-- Pilih --'));
                    $.each(response.result, function(index, item) {
                        $('#no_sub_rak').append($('<option>', { value : item.id }).text(item.text));
                    });
                }
            });
        }
    });

