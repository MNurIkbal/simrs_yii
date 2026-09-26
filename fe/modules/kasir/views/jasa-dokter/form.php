<?php

/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
        'id' => 'form',
        'type' => ActiveForm::TYPE_VERTICAL,
    ]);
    ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'jasadokter_kode'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'jasadokter_nama'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'is_active')->dropDownList([1 => 'Aktif', 0 => 'Tidak Aktif'], [
                'class' => 'select2',
                'prompt' => '— PILIH —',
            ])->label(Yii::t('fe', 'Status')); ?>

        </div>
    </div>
    <hr>
    <div class="modal-footer">
            <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), [
                'class' => 'btn bg-teal'
            ]) ?>
            <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>'.Yii::t('fe', ' Kembali'),[
                'class' => 'btn bg-slate',
                'data-dismiss' => 'modal'
            ]) ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $('#form').docoForm('submit',{
        success : function(data) {
            this.formInput[0].reset();
            $('#modal_backdrop').modal('hide');
            table.draw();
        }
    });
</script>
