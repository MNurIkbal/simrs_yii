<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-14 11:09:02
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-29 14:42:43
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'intra-konsul-tindakan-form', 
    'type' => ActiveForm::TYPE_VERTICAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'daftartindakan_id')->dropDownList($options, ['class'=>'select2 select-tindakan', 'prompt'=>'']); ?>
    <?=$form->field($model, 'dokter_id')->dropDownList($options, ['class'=>'select2 select-dokter', 'prompt'=>'']); ?>
    <?=$form->field($model, 'bagian_tubuh')->textInput(); ?>
    <?=$form->field($model, 'alasan')->textArea(); ?>
    <?=Html::activeHiddenInput($model, 'inpostoperasi_id', ['class'=>'pegawai-inpost-id'])?>
    <?=Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', ['class'=>'pegawai-pasienmasukpenunjang'])?>
    <?=Html::activeHiddenInput($model, 'daftartindakan_nama', ['class'=>'daftartindakan-nama'])?>
    <?=Html::activeHiddenInput($model, 'dokter_nama', ['class'=>'dokter-nama'])?>
    <?=Html::activeHiddenInput($model, 'tarif_satuan', ['class'=>'tarif-satuan'])?>
    <?=Html::activeHiddenInput($model, 'tarif_tindakan', ['class'=>'tarif-tindakan'])?>
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
    $("#intra-konsul-tindakan-form").docoForm("submit",{
        success : function(data) {
            _tablekonsultindakan.draw();
            $('#modal_backdrop').modal('toggle')
        }
    });
    $(document).ready(function(){
        var _arr = {};
        var _penjamin = $('.penjamin-id').val()
        var _kelaspelayanan = $('.kelaspelayanan-id').val()
        var _ruanganid = "<?=!empty(Yii::$app->docoVars->workspace('ruangan_id')) ? Yii::$app->docoVars->workspace('ruangan_id') : null ?>"
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())
        $('.select-tindakan').on('change', function(){
            $('.daftartindakan-nama').val( $('.select-tindakan').select2('data')[0].text )
        })
        $('.select-dokter').on('change', function(){
            $('.dokter-nama').val( $('.select-dokter').select2('data')[0].text )
        })
        
        $('.select-tindakan').select2InfinityScroll({
            url: '/bedah/informasi-pasien-operasi/tindakan',
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                        kelaspelayanan_id: kelasPelayanan,
                        penjamin_id: penjaminId
                    }
                }
            }
        })
        
        $('.select-dokter').select2({
            placeholder: '',
            ajax: {
                url: '/bedah/informasi-pasien-operasi/get-dokter',
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
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });
    })
</script>