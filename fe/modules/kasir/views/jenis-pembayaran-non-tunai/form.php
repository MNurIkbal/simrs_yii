<?php

/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

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
            <?= $form->field($model, 'kode'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'nama'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'bank_id')->dropDownList([], [
                'class' => 'select2 selectBank',
                'prompt' => '— PILIH —',
            ])->label(Yii::t('fe', 'Bank')); ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'is_active')->dropDownList($status, [
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
    $(document).ready(function(){
        var bank_id = '<?= $model->bank_id ?>';
        var nama_bank = '<?= $nama_bank ?>';
        var newOption = new Option(nama_bank, bank_id, true, true);
        
        $('.selectBank').append(newOption).trigger('change');
        $(".selectBank").docoPaginationSelec2(
            config = {
                placeholder : "-- Cari Bank --",  
                _api : "/kasir/jenis-pembayaran-non-tunai/get-data-bank",
            }
        );
    });

    $('#form').docoForm('submit',{
        success : function(data) {
            this.formInput[0].reset();
            $('#modal_backdrop').modal('hide');
            table.draw();
        }
    });
</script>


