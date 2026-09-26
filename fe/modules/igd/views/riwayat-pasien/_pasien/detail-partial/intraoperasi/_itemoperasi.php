<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-13 11:03:47
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-07 10:29:22
 */

use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php 
$dis = false;
$params = ['class'=>'cyto-check'];
if($key != ''){
    $dis = true;
    $params = ['class'=>'cyto-check','disabled'=>'disabled'];
}
$form = ActiveForm::begin([
    'id' => 'intra-item-operasi-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'daftartindakan_id')->dropDownList([], ['id'=>'daftartindakan-id','class'=>'select2 select-tindakan', 'prompt'=>'']); ?>
    <?=$form->field($model, 'golonganoperasi_id')->widget(DepDrop::classname(), [
                                 'data'=>$opsi['golonganoperasi'],
                                 'options'=>['id'=>'golonganoperasi_id', 'class'=>'select2 golonganoperasi-id','disabled'=>$dis],
                                 'pluginOptions'=>[
                                        'depends'=>['daftartindakan-id'],
                                        'placeholder'=>'',
                                        'url'=>Url::to(['get-jenis-operasi'])
                                    ],
                                'pluginEvents'=>[
                                    'depdrop:afterChange'=>"function(event){ 
                                        $.each($('#golonganoperasi_id').depdrop('getAjaxResults').output, function(k,v){
                                            _arrOptions[v.id] = v.operasi_id
                                        })
                                    }",
                                ]
                                ]
                                 ); ?>
    <?=$form->field($model, 'jenisanastesi_id')->dropDownList($options['jenisanastesi'], ['class'=>'select2 select-jenisanastesi', 'prompt'=>'']); ?>
    <?=$form->field($model, 'jenis_luka')->dropDownList($options['jenisluka'], ['class'=>'select2 select-jenisluka', 'prompt'=>'']); ?>
    <?=$form->field($model, 'is_cyto')->checkbox($params); ?>
    <?php 
    if($key != ''){
        echo Html::activeHiddenInput($model, 'daftartindakan_id');
        echo Html::activeHiddenInput($model, 'golonganoperasi_id');
        echo Html::activeHiddenInput($model, 'is_cyto');
    }
    ?>
    <?=Html::activeHiddenInput($model, 'daftartindakan_nama', ['class'=>'daftartindakan-nama'])?>
    <?=Html::activeHiddenInput($model, 'golonganoperasi_nama', ['class'=>'golonganoperasi-nama'])?>
    <?=Html::activeHiddenInput($model, 'jenisanastesi_nama', ['class'=>'jenisanastesi-nama'])?>
    <?=Html::activeHiddenInput($model, 'operasi_id', ['class'=>'operasi-id'])?>
    <?=Html::activeHiddenInput($model, 'jenis_luka_nama', ['class'=>'jenis-luka-nama'])?>
    <?=Html::activeHiddenInput($model, 'tarif_satuan', ['class'=>'tarif-satuan'])?>
    <?=Html::activeHiddenInput($model, 'tarif_tindakan', ['class'=>'tarif-tindakan'])?>
    <?=Html::activeHiddenInput($model, 'tarif_cyto', ['class'=>'tarif-cyto'])?>
    <?=Html::activeHiddenInput($model, 'inpostoperasi_id', ['class'=>'pegawai-inpost-id'])?>
    <?=Html::activeHiddenInput($model, 'cyto', ['class'=>'cyto-txt'])?>
    <?=Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', ['class'=>'pegawai-pasienmasukpenunjang'])?>
    <?=Html::activeHiddenInput($model, 'default')?>
    <?=Html::hiddenInput('opsi_operasi',$opsi['daftartindakan'], ['class'=>'opsi-operasi'])?>
</div>
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'> ". Yii::t('fe', 'Simpan')."</i>", ['class' => 'btn bg-teal']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'> ". Yii::t('fe', 'Kembali')."</i>",[
                        'class' => 'btn bg-slate',
                        'data-dismiss' => 'modal'
                        ]); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    var _arrOptions = [];
    $("#intra-item-operasi-form").docoForm("submit",{
        success : function(data) {
            // console.log(data)
            _tableitemoperasi.draw();
            _tablepenggunaanbmhp.draw();
            $('#modal_backdrop').modal('toggle')
        }
    });
    $(document).ready(function(){
        if( $('.opsi-operasi').val() !== ''){
            var _opsi = $.parseJSON($('.opsi-operasi').val())
            var _options = new Option(_opsi.name, _opsi.id, false, false)
            $('.select-tindakan').append(_options).val(_opsi.id).trigger('change').prop('disabled', true)
        }
        var _persencyto = false;
        var _penjamin = $('.penjamin-id').val()
        var _kelaspelayanan = $('.kelaspelayanan-id').val()
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())
        $('.select-tindakan').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/igd/riwayat-pasien/get-tindakan?penjamin='+_penjamin+'&kelaspelayanan='+_kelaspelayanan,
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            templateSelection: function (data, container) {
                $(data.element).attr('data-tarif-tindakan', data.harga_tariftindakan);
                $(data.element).attr('data-persen-cyto', data.persencyto_tindakan);
                return data.text;
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });
        $('.select-tindakan').on('change', function(){
            var _tarifcyto = 0
            if(_persencyto){
                _tarifcyto = (parseInt($('.select-tindakan').select2('data')[0].element['attributes'][2].value) * parseInt($('.select-tindakan').select2('data')[0].element['attributes'][1].value)) /100;
            }
            $('.tarif-satuan').val( $('.select-tindakan').select2('data')[0].element['attributes'][1].value )
            $('.tarif-tindakan').val( $('.select-tindakan').select2('data')[0].element['attributes'][1].value )
            $('.tarif-cyto').val( _tarifcyto )
            $('.daftartindakan-nama').val(  $('.select-tindakan').select2('data')[0].text )
        })
        $('.golonganoperasi-id').on('change', function(){
            $('.golonganoperasi-nama').val( $('.golonganoperasi-id').select2('data')[0].text )
            $('.operasi-id').val( _arrOptions[$(this).val()] )
        })
        $('.select-jenisanastesi').on('change', function(){
            $('.jenisanastesi-nama').val( $('.select-jenisanastesi').select2('data')[0].text )
        })
        $('.select-jenisluka').on('change', function(){
            $('.jenis-luka-nama').val( $('.select-jenisluka').select2('data')[0].text )
        })
        $('.cyto-check').on('click', function(){
            if($(this).is(':checked')){
                _persencyto = true;
            }else{
                _persencyto = false;
            }

            if(_persencyto){
                var _tarifcyto = (parseInt($('.select-tindakan').select2('data')[0].element['attributes'][2].value) * parseInt($('.select-tindakan').select2('data')[0].element['attributes'][1].value)) /100;
                $('.tarif-cyto').val( _tarifcyto )
                $('.cyto-txt').val('✓')
            }else{
                $('.tarif-cyto').val( 0 )
                $('.cyto-txt').val('-')
            }
        })
    })
</script>
