<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-10 11:12:41
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-13 10:18:31
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'intra-pegawai-operasi-form', 
    'type' => ActiveForm::TYPE_VERTICAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-1" style ="margin-top:10px">#</div>
        <div class="col-md-4">
            <?= $form->field($model, 'pegawai_id')->dropDownList([], ['class' => 'select2 select-pegawai', 'prompt' => '']); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'posisi_tim')->dropDownList([], ['class' => 'select-posisi', 'prompt' => '']); ?>
            <?= Html::activeHiddenInput($model, 'prosentase', ['id' => 'prosentase-pegawai']) ?>
            <?= Html::activeHiddenInput($model, 'pegawai_nama', ['class' => 'pegawai-nama']) ?>
            <?= Html::activeHiddenInput($model, 'posisi_tim_nama', ['class' => 'posisi-nama']) ?>
            <?= Html::activeHiddenInput($model, 'slug_posisi', ['id' => 'slug-posisi']) ?>
            <?= Html::activeHiddenInput($model, 'inpostoperasi_id', ['class' => 'pegawai-inpost-id']) ?>
            <?= Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', ['class' => 'pegawai-pasienmasukpenunjang']) ?>
            <?= Html::activeHiddenInput($model, 'daftartindakan_id', ['class' => 'pegawai-daftartindakan']) ?>
            <?= Html::activeHiddenInput($model, 'kegiatanoperasi_id', ['class' => 'kegiatanoperasi_id']) ?>
            <?= Html::activeHiddenInput($model, 'golonganoperasi_id', ['class' => 'golonganoperasi_id']) ?>
        </div>
        <div class="col-md-2">
            <?= ''; Html::submitButton("<i class='fa fa-plus'> </i>", ['class' => 'btn bg-teal', 'style' => 'margin-top:10px']) ?>
            <div id="btn-tambah-tim" style = "margin-top:10px" class="btn bg-teal"><i class='fa fa-plus'> </i></div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.kegiatanoperasi_id').val($('#kegiatanoperasi_id').val());
        $('.golonganoperasi_id').val($('#golonganoperasi-id').val());
        $("#intra-pegawai-operasi-form").docoForm("submit", {
            success: function(data) {
                _tableoperasi.draw();
            }
        });

        $("#btn-tambah-tim").on("click", function(){
            $().docoForm("click", {
                skipConfirm : true,
                url: $('#intra-pegawai-operasi-form').attr('action'),
                data: $("#intra-pegawai-operasi-form").serializeArray(),
                skipConfirm: true,
                success: function (data) {
                    let posisi_tim = $('.select-posisi').val();
                    if(posisi_tim == _dokterBedahId){
                        $('.pegawai-id').val($('.select-posisi').val())
                    }
                    _tableoperasi.draw();
                    _tableitemoperasi.draw();
                    setTimeout(() => {
                    }, 1000);
                    if(isEdit){
                        isChangeData()
                            let data = {
                                pegawai_id : $('.select-pegawai').find('option:selected').val(),
                                pegawai_nama : $('.pegawai-nama').val(),
                                posisi_tim : $('.select-posisi').find('option:selected').val(),
                                prosentase : $("#prosentase-pegawai").val(),
                                posisi_tim_nama : $('.posisi-nama').val(),
                                slug_posisi : $("#slug-posisi").val(),
                                inpostoperasi_id :  $('.pegawai-inpost-id').val(),
                                pasienmasukpenunjang_id : $('.pegawai-pasienmasukpenunjang').val(),
                                daftartindakan_id : $('.pegawai-daftartindakan').val(),
                                pegawai_input : pegawai_input,
                                kegiatanoperasi_id : (typeof $('.kegiatanoperasi_id').val() != 'undefined') ? $('.kegiatanoperasi_id').val() : '' ,
                                kegiatanoperasi_nama : (typeof $('.kegiatanoperasi_nama').val() != 'undefined') ? $('.kegiatanoperasi_nama').val() : '' ,
                                golonganoperasi_id : (typeof  $('#golonganoperasi-id').val() != 'undefined') ?  $('#golonganoperasi-id').val() : '' ,
                                golonganoperasi_nama : (typeof $('#golonganoperasi-nama').val() != 'undefined') ? $('#golonganoperasi-nama').val() : '' 
                            }
                            let key = $('.pegawai-daftartindakan').val();
                            dataPegawaiTindakan.push(data);

                    }
                }
            });
        })
        
        // $("#btn-tambah-tim").on("click", function(){
        //     _daftartindakanId = $('.pegawai-daftartindakan').val();
        //     _pegawaiId = $('.select-pegawai').val();
        //     _posisiTim = $('.select-posisi').val();
        //     _pegawaiNama = $('.pegawai-nama').val();
        //     _posisiTimNama = $('.posisi-nama').val();
        //     if(_daftartindakanId != "" && _pegawaiId != "" && _posisiTim != ""){
        //         _listTimOperasi[prefixTimOperasi(_daftartindakanId,_pegawaiId,_posisiTim)] = $("#intra-pegawai-operasi-form").serializeArray();
                
        //         $("#table-tim-operasi-v2").find("tbody").append(`
        //             <tr class = "${prefixTimOperasi(_daftartindakanId,_pegawaiId,_posisiTim)}">
        //                <td># </td>
        //                <td> ${_pegawaiNama} </td>
        //                <td> ${_posisiTimNama}</td>
        //                <td> <div class="btn btn-danger btn-xs" id="delete-item-local" data-prefix="${prefixTimOperasi(_daftartindakanId,_pegawaiId,_posisiTim)}" ><i class="fa fa-trash"></i></div></td>
        //             </tr>
        //         `);
        //     }
        // })

        var timOperasi = JSON.parse('<?= $timOperasi ?>')
        timOperasi.map((eachTim) => {
            $(".select-posisi").append(`
                <option value="${eachTim.id}" data-slug="${eachTim.slug}" data-prosentase="${eachTim.prosentase}">${eachTim.text}</option>
            `)
        })
        $(".select-posisi").select2()
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())
        $('.select-pegawai').select2({
            placeholder: '',
            // minimumInputLength: 3,
            ajax: {
                url: '/bedah/informasi-pasien-operasi/get-pegawai',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page) {
                    return {
                        q: term,
                        ruangan_id: $('.ruangan-id').val(),
                        page: page
                    }
                },
                processResults: function(data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function(m) {
                return m;
            },
        });
        $('.select-pegawai').on('change', function() {
            $('.pegawai-nama').val($('.select-pegawai').select2('data')[0].text)
        })
        $('.select-posisi').on('change', function() {
            const {
                data,
                prosentase,
                slug
            } = $('.select-posisi').find(':selected').data()
            $('.posisi-nama').val(data.text)
            $("#prosentase-pegawai").val(prosentase)
            $("#slug-posisi").val(slug)
        })

        $("#delete-item-local").on("click", function(){
           prefix = $(this).data("prefix")
            _listTimOperasi[prefix] = [];
            $(`.${prefix}`).remove();
        })

        $('.pegawai-daftartindakan').val($('.select-tindakan').val()); /** Init */
        $('.kegiatanoperasi_id').val($('.select-kegiatan').val());
        $('.golonganoperasi_id').val($('.select-kegiatan').val());
        $('.select-tindakan').on('change', function(){
            $('.pegawai-daftartindakan').val($('.select-tindakan').val());
        })
        $('.select-kegiatan').on('change', function(){
            $('.kegiatanoperasi_id').val($('.select-kegiatan').val());
        })
        $('.select-golongan').on('change', function(){
            $('.golonganoperasi_id').val($('.select-golongan').val());
        })
        // function prefixTimOperasi(daftartindakanId, pegawaiId, posisiTim){
        //     return `to-${daftartindakanId}-${pegawaiId}-${posisiTim}`;
        // }

    })
</script>
