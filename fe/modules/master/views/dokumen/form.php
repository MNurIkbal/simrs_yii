<?php
// Author : Naufal Ziyad L
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\ActiveForm;

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
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => false,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-4 control-label"><?=Yii::t('fe', 'Nama Dokumen')?></label>
        <div class="col-lg-7">
            <?= $form->field($model, 'nama_dokumen')
                ->textInput(['class' => 'form-control'])
                ->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Nama Lain Dokumen')?></label>
        <div class="col-lg-7">
            <?= $form->field($model, 'nama_dokumen_lainnya')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-4 control-label"><?=Yii::t('fe', 'Jenis Dokumen')?></label>
        <div class="col-lg-7">
            <?= $form->field($model, 'jenis_dokumen_id')
                ->dropDownList($jenisDokumen, ['class' => 'form-control select2', 'prompt' => 'Pilih'])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-4 control-label">Status</label>
        <div class="col-lg-7">
            <?= $form->field($model, 'is_active')->radioList([1 => 'Aktif', 0 => 'Tidak Aktif'], ['label' => false])->label(false); ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
        <?= Html::submitButton("<i class='fa fa-floppy-o'> Simpan</i>", ['class' => 'btn bg-teal']) ?>
        <?= Html::button("<i class='fa fa-arrow-left'> Kembali</i>",[
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
        ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $('#form').docoForm('submit',{
        success : function(data) {
            table.draw();
            $("#modal_backdrop").modal("toggle");
        }
    });
</script>