<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-13 17:22:47
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-13 19:13:24
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'intra-alat-ditubuh-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'jenis_alat')->dropdownList([], ['class'=>'select2 select-jenis-alat']); ?>
    <?=$form->field($model, 'jumlah')->textInput(); ?>
    <?=$form->field($model, 'lokasi')->textInput(); ?>
    <?=Html::activeHiddenInput($model, 'inpostoperasi_id', ['class'=>'pegawai-inpost-id'])?>
    <?=Html::activeHiddenInput($model, 'jenis_alat_nama', ['class'=>'jenis-alat-nama'])?>
    <?=Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', ['class'=>'pegawai-pasienmasukpenunjang'])?>
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
    $("#intra-alat-ditubuh-form").docoForm("submit",{
        success : function(data) {
            _tablealatditubuh.draw();
            $('#modal_backdrop').modal('toggle')
        }
    });
    $(document).ready(function(){
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())
        $('.select-jenis-alat').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/bedah/informasi-pasien-operasi/get-jenis-alat',
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
        $('.select-jenis-alat').on('change', function(){
            $('.jenis-alat-nama').val( $('.select-jenis-alat').select2('data')[0].text )
        })
    })
</script>