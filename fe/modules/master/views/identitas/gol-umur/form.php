<?php
// Author : Naufal Ziyad L
// Modified By: Ardi Pratama
    use yii\widgets\ActiveForm;
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
            'id' => 'golonganumur-form', 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-3 control-label"><?=Yii::t('fe', 'Golongan Umur')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'golonganumur_nama')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label"><?=Yii::t('fe', 'Usia')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'golonganumur_namalainnya')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label"><?=Yii::t('fe', 'Umur Minimal')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'golonganumur_minimal')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label"><?=Yii::t('fe', 'Umur Maksimal')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'golonganumur_maksimal')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label">Status</label>
        <div class="col-lg-6">
            <?= $form->field($model, 'is_active')
                ->dropDownList($status,['class' => 'select2'])->label(false); ?>
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
    $('#golonganumur-form').docoForm('submit',{
        success : function(data) {
            table.draw();
        }
    });
</script>