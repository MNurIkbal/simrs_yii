<?php
// Author : Naufal Ziyad L
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'asalrujukan-form', 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-3 control-label"><?=Yii::t('fe', 'Asal Rujukan')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'asalrujukan_nama')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-3 control-label"><?=Yii::t('fe', 'Asal Rujukan Kode')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'asalrujukan_kode')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label"><?=Yii::t('fe', 'Institusi Asal Rujukan')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'asalrujukan_institusi')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label"><?=Yii::t('fe', 'Nama Lainnya')?></label>
        <div class="col-lg-6">
            <?= $form->field($model, 'asalrujukan_namalainnya')
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
    $('#asalrujukan-form').docoForm('submit',{
        success : function(data) {
            tableAsalRujukan.draw();
        }
    });
</script>