<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-13 17:03:14
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-13 17:04:47
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'intra-penggunaan-cairan-form', 
    'type' => ActiveForm::TYPE_VERTICAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'kegiatan')->textInput(); ?>
    <?=$form->field($model, 'cairan_masuk')->textInput(); ?>
    <?=$form->field($model, 'cairan_keluar')->textInput(); ?>
    <?=$form->field($model, 'keterangan')->textArea(); ?>
    <?=Html::activeHiddenInput($model, 'inpostoperasi_id', ['class'=>'pegawai-inpost-id'])?>
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
    $("#intra-penggunaan-cairan-form").docoForm("submit",{
        success : function(data) {
            _tablepenggunaancairan.draw();
            $('#modal_backdrop').modal('toggle')
        }
    });
    $(document).ready(function(){
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())
    })
</script>