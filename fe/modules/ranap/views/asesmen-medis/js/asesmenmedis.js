// /*
// * @Author: rizqi_fitrianto
// * @Date:   2018-06-11 15:05:48
// * @Last Modified by:   Sigit
// * @Last Modified time: 2018-07-10 17:01:31
// */

// $(document).ready(function(){
//     /*----------  Populate riwayat penyakit terdahulu start  ----------*/
//     if(!jQuery.isEmptyObject(dataRiwayatPenyakit)){
//         $.each(dataRiwayatPenyakit, function(k, v){
//             if(k == 0){
//                 $('.row-default').find('input, select').each(function(kx, vx){
//                     if($(this).hasClass('select-tahun')){
//                         $(this).val(v.tahun).trigger('change')
//                     }else if($(this).hasClass('penyakit-default')){
//                         $(this).val(v.penyakit)
//                     }else if($(this).hasClass('terapi-default')){
//                         $(this).val(v.terapi)
//                     }
//                 })
//             }else{
//                 count++
//                 var _clone = $('.clone-div').clone()
//                                     .addClass('row-'+count)
//                                     .removeClass('clone-div hidden')
//                 _clone.find('input, select').each(function(kx,vx){
//                     if($(this).hasClass('select-tahun') ){
//                         $(this).addClass('select-tahun'+count).removeClass('select-tahun')
//                         setTimeout(function(){ $('.select-tahun'+count).val(v.tahun).select2(); }, 1)
//                     }else if($(this).attr('data-name') == 'penyakit'){
//                         $(this).val(v.penyakit)
//                     }else if($(this).attr('data-name') == 'terapi'){
//                         $(this).val(v.terapi)
//                     }
//                     $(this).attr('name','riwayat_penyakit[' + count + '][' + $(this).attr('data-name') + ']')
//                 });

//                 $('.isi-loop').append(_clone)
//             }
//         })
//     }
//     /*----------  Populate riwayat penyakit terdahulu start  ----------*/

//     addRow()
//     var opsi = detailBagianTubuh[$('.bagian-tubuh').val()]
//     $('.bagian-tubuh-detail').append(populateOpsi(opsi))
// })

// var populateOpsi = function(opsi){
//     var txtopsi = '';
//     $.each(opsi, function(k,v){
//         txtopsi += '<option value="'+k+'">'+v+'</option>'
//     })
//     return txtopsi
// }

// /*----------  Submit form start  ----------*/
// $('#asesmenmedis-form').on('submit', function(e){
//     e.preventDefault();
//     var asesmen = $('#asesmenmedis-form').serializeArray();
//     var arr = []
//     arr[0] = {name: 'anatomi', value: JSON.stringify(tmpData)}
//     var alldata = $.merge(asesmen, arr)

//     $(this).docoForm('submit',{
//         data: alldata,
//         before: function(){
//             return false;
//         },  
//         success : function(response) {
//             // console.log(response)
//             location.reload();
//             removeDisable();
//         }
//     }); 
// })

// $('.btn-hapus').on('click', function() {
//     // Asesmen id
//     var asesmenmedis_id = $('#asesmenmedisform-asesmenmedis_id').val();
    
//     // Ajax
//     $.ajax({
//         url: '/ranap/asesmen-medis/hapus-asesmen-medis?id='+asesmenmedis_id,
//         type: 'delete',
//         dataType: 'json',
//         success: function(data) {
//             // Cek status
//             if(xhr.status == 200) {
                
//             }
//             else {

//             }
//         }
//     });
// });

// /*----------  Submit form end  ----------*/


// var loading_spinner = '<i class="icon-spinner4 spinner position-center form-control-feedback spinner-text" style="display: block;"></i>';$(document).ready(function(){
//     $('.jml-rokok').hide()
// })

// $('input[name="AsesmenMedisForm[is_merokok]"]').on('change', function(){
//     if($(this).val() == 1){
//         $('.jml-rokok').show()
//     }else{
//         $('.jml_rokok').val('')
//         $('.jml-rokok').hide()
//     }
// })

// $('input[name="AsesmenMedisForm[discharge_plan]"]').on('change', function(){
//     if($(this).val() == 1){
        
//     }else{
//         $('.care-plan').val('')
//     }
// })


// $('input[name="AsesmenMedisForm[sumber_info]"]').on('change', function(){
//     if($(this).val() == 1){
//         $('.sumber-hubungan').val('')
//         $('.sumber-hubungan').attr('readonly', true)
//     }else{
//         $('.sumber-hubungan').attr('readonly', false)
//     }
// })
// /*----------  Append new riwayat penyakit form on click + start  ----------*/
// var count = 0;
// $(document).on('click','.btn-append', function(){
//     count++

//     var _clone = $('.clone-div').clone()
//                                 .addClass('row-'+count)
//                                 .removeClass('clone-div hidden')
//     _clone.find('input, select').each(function(k,v){
//         if($(this).hasClass('select-tahun') ){
//             $(this).addClass('select-tahun'+count)
//             setTimeout(function(){ $('.select-tahun'+count).select2(); }, 1)
            
//         }
//         $(this).attr('name','riwayat_penyakit[' + count + '][' + $(this).attr('data-name') + ']')
//     });

//     $('.isi-loop').append(_clone)
// })
// $(document).on('click', '.btn-remove', function(){
//     $(this).closest('.row').remove()
// })
// /*----------  Append new riwayat penyakit form on click + end  ----------*/

// /*----------  BB Ideal start  ----------*/

// $('.tinggi-badan').on('keyup', function(){
//     let bbideal = 0;
//     let tb = $(this).val()
//     if( tb != '' || tb != 0){
//         bbideal = parseFloat( (tb - 100) - (0.1 * (tb-100) ) ).toFixed(2);
//     }
//     if(typeof $('.berat-badan').val() !== 'undefined'){
//         $('.berat-badan').trigger('change')
//     }
//     $('.bb-ideal').val(bbideal);
// })

// /*----------  BB Ideal end  ----------*/

// /*----------  IMT start  ----------*/

// $('.berat-badan').on('change', function(){
//     let bb = $(this).val()
//     let tb = $('.tinggi-badan').val()
//     let imt = (bb / ( (tb/100) * (tb/100) )).toFixed(2)
//     let hasil;
//     $.each(dataBmi, function(k, v){
//         if(imt >= v.bmi_minimum && imt <= v.bmi_maksimum){
//             hasil = v.bmi_defenisi; return false;
//         }
//     })
//     $('.imt').val(imt)
//     $('.ket-imt').val(hasil)
// })

// /*----------  IMT end  ----------*/

// /*----------  Tekanan Darah start  ----------*/
// var nilaiTd = '';
// $('.sysdia').on('change', function(){
//     let diastolic = ($('.diastolic').val() != '') ? $('.diastolic').val() : 0
//     let systolic = ($('.systolic').val() != '') ? $('.systolic').val() : 0
//     $('.tekanan-darah').val(systolic + '/' +diastolic).trigger('change')
//     nilaiTd = systolic + '/' +diastolic
// })
// $('.tekanan-darah').on('change', function(){
//     var hasil;
//     $.ajax({
//         url: '/ranap/asesmen-medis/get-hasil-td?pendaftaran_id='+$('.pendaftaran_id').val()+'&nilai='+ $('.tekanan-darah').val()+'&golongan_umur='+$('.golongan_umur').val(),
//         type: 'get',
//         dataType: 'JSON',
//         beforeSend : function() {
//             $('.hasil-td').after(loading_spinner);
//         },
//         success: function(data, text, xhr){
//             if(xhr.status == 200){
//                 hasil = data.hasil
//             }
//         }
//     }).done(function() {
//         $('.hasil-td').val( hasil )
//         $(".spinner-text").remove();
//     });
// });
// /*----------  Tekanan Darah end  ----------*/

// /*----------  Gcs start  ----------*/
// var nilaiGcsEye = 0
// var nilaiGcsVerbal = 0
// var nilaiGcsMotorik = 0
// var hitungGcs = function(){
//     let nilaiGcs = nilaiGcsEye + nilaiGcsVerbal + nilaiGcsMotorik
//     let hasil;
//     $.each(dataGcs, function(key, value){
//         if(nilaiGcs >= value['gcs_nilaimin'] && nilaiGcs <= value['gcs_nilaimax']){
//             $('.hasil_gcs').val(value.gcs_nama)
//             return false
//         }
//     })

// }

// $(".gcs_eye").on("change", function(e) { 
//     nilaiGcsEye = parseInt($(this).find(':selected').attr('data-nilai'));
//     hitungGcs()
// });
// $(".gcs_verbal").on("change", function(e) { 
//     nilaiGcsVerbal = parseInt($(this).find(':selected').attr('data-nilai'))
//     hitungGcs()
// });
// $(".gcs_motorik").on("change", function(e) { 
//     nilaiGcsMotorik = parseInt($(this).find(':selected').attr('data-nilai'))
//     hitungGcs()
// });

// /*----------  Gcs end  ----------*/

// /*----------  Anatomi tubuh start  ----------*/
// var sumbuX = 0;
// var sumbuY = 0;



// $(window).keydown(function(e){
//     if (e.keyCode == 27) {
//         $('.add-caption').val('');
//         $('.bagian-tubuh').val('');
//         $('.bagian-tubuh-detail').find('option').remove();
//         $('.tag').attr({
//             style : 'display:none;',
//         });
//         $('.tag').data('show',1);
//         return false;
//     }
//     if (e.keyCode == 13) {
//         e.preventDefault();
//     }
// });

// $(document).on('click','.image-frame', function(event) {
//     event.preventDefault();
//     var posX = (event.pageX - $(this).offset().left),
//         posY = (event.pageY - $(this).offset().top) - 10,
//         tag = $('.tag');
//     sumbuX = posX;
//     sumbuY = posY;
//     if (tag.data('show') != 1) {
//         tag.attr({
//             style : 'display:none;',
//         });
//         tag.data('show',1);
//     } else {
//         tag.attr({
//             style : 'top: '+ (posY + 20) +'px; left: '+ posX +'px;width:500px;z-index:3;position:absolute;'
//         });
//         tag.data('show',2);
//     }
// });

// var addCaption = function(e) {
//     e.preventDefault();
//     var date = new Date()
//     var m = (date.getMonth()+1)
//     var d = (date.getDate())
//     var now = date.getFullYear() + '-' + ((''+m).length<2 ? '0' : '') + m + '-' + ((''+d).length<2 ? '0' : '') + d + ' ' + date.getHours() + ':' + date.getMinutes() + ':' +date.getSeconds()
//     var tabel = $('.tabel-anggotatubuh');
//     var _contentParent = $(this).closest('.well-sm');
//     var valBagian = $('.bagian-tubuh').val();
//     var valBagianDetail = $('.bagian-tubuh-detail').val();
//     if (e.keyCode == 13 || e.keyCode == 27) {
//         if (e.keyCode == 13 && (/[\w\d]+/.test($(this).val())) && valBagian != '') {
//             var bagian = typeof bagianTubuh[valBagian] != 'undefined' ? bagianTubuh[valBagian] : '-';
//             var bagianDetail = typeof detailBagianTubuh[valBagian][valBagianDetail] != 'undefined' ? detailBagianTubuh[valBagian][valBagianDetail] : '-';
//             tmpData[counter] = {
//                 counters : counter,
//                 bagian : bagian,
//                 bagianDetail : bagianDetail,
//                 bagiantubuh_id : valBagian,
//                 bagiantubuhdetail_id : valBagianDetail,
//                 koordinat_y : sumbuY,
//                 koordinat_x : sumbuX,
//                 created_date: now,
//                 catatan_tubuh : $(this).val()
//             }
//             addRow();
//             counter++;
//         }
//         $(this).val('');
//         $('.bagian-tubuh').val('');
//         $('.bagian-tubuh-detail').val('');
//         $('.tag').attr({
//             style : 'display:none;',
//         });
//         $('.tag').data('show',1);
//         return false;
//     }
// }

// var addRow = function () {
//     var _tabel = $('.tabel-anggotatubuh');
//     if (_tabel.find('tbody > tr').length == 1) {
//         $('.default-row').hide();
//     }
//     var clone  = $('.default-row').clone();
//     _tabel.find('tbody').html('<tr class=\"default-row\" style=\"display:none\">'+ clone.html() + '</tr>');
//     $.each(tmpData, function (key,items) {
//         $('.image-frame').append('<div style=\"top: '+ items.koordinat_y+'px; left: '+ items.koordinat_x +'px;z-index:3;position:absolute;\" class=\"tag-image counter-'+ items.counters +'\"><span class=\"badge bg-warning-400\">'+ items.counters +'</span></div>');
//         var html = '';
//         html += '<tr>';
//         html += '<td>'+ items.counters +'</td>';
//         html += '<td>'+ items.created_date +'</td>';
//         html += '<td>'+ items.bagian +'</td>';
//         html += '<td>'+ ( (items.bagianDetail != null) ? items.bagianDetail : '-' ) +'</td>';
//         html += '<td>'+ items.catatan_tubuh +'</td>';
//         html += '<td><button style="padding-left: 9px !important;" class=\"btn btn-danger btn-xs hapus-item\" data-counter=\"'+ items.counters +'\">'+
//                     '<i class=\"fa fa-trash\"></i></button></td>';
//         html += '</tr>';
//         _tabel.find('tbody').append(html);
//     })
// }

// var delRow = function (event) {
//     event.preventDefault();
//     var _this = $(this);
//     var _counter = _this.data('counter');
//     var _trParent = _this.closest('tr');
//     var _tabel = $('.tabel-anggotatubuh');
//     _trParent.remove();
//     delete tmpData[_counter - 1];

//     $.each(tmpData, function (key, items) {
//         if (_counter < key) {
//             delete tmpData[key];
//             tmpData[(key - 1)] = items;
//             tmpData[(key - 1)]['counters'] = items.counters - 1;
//         }
//     });

//     $('.counter-' + _counter).remove();
//     $('.tag-image').remove();
//     addRow();
//     if (_tabel.find('tbody > tr').length == 1) {
//         $('.default-row').show();
//     }
//     counter--;
// }

// $(document).on('click','.hapus-item', delRow);
// $('.add-caption').on('keyup',addCaption);
// var saveAnatomi = function (data) {
//     let res = data;
//     let pemeriksaanfisik_id = res.response['pemeriksaanfisik_id'] ? res.response['pemeriksaanfisik_id'] : null;
//     let url = "/ranap/asesmen-medis/save-anatomi";

//     if (Object.keys(tmpData).length) {
//         $.ajax({
//             type : 'POST',
//             dataType : 'json',
//             url : url,
//             data : {
//                 data:tmpData,
//                 pendaftaran_id : $('.pendaftaran_id').val(),
//                 pasien_id : $('.pasien_id').val(),
//                 pemeriksaanfisik_id : pemeriksaanfisik_id,
//             },
//             error : function (data) {
//                 console.log(data);
//             }
//         });
//     } else {
//         alert('Anatomi Harus Di isi');
//     }
// };

// $('.bagian-tubuh').on('change', function(){
//     var opsi = detailBagianTubuh[$('.bagian-tubuh').val()]
//     $('.bagian-tubuh-detail').find('option').remove().end().append(populateOpsi(opsi))
// })
// /*----------  Anatomi tubuh end  ----------*/