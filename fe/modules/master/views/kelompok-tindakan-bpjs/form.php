<?php

    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use app\components\DocoHelpers;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
        'id' => 'kelompoktindakanbpjs-form', 
        'options' => [
                'class' => 'form-horizontal', 
                'enableAjaxValidation' => true,
                'role' => 'form'
            ],
        ]); 
    ?>
    <div class="form-group">
        <label for="kelompoktindakan_nama" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Kelompok Tindakan'); ?><b style="color:red;"> * </b>
        </label>
        <div class="col-lg-6">
            <?=$form->field($model, 'kelompoktindakan_nama')
                ->textInput([
                    'class' => 'form-control input-sm'
                ])->label(false); 
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="groupinacbg_id" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Kelompok Inacbgs'); ?><b style="color:red;"> * </b>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'groupinacbg_id')
                    ->dropDownList($inacbg,[
                        'class' => 'select2',
                        'id' => 'groupinacbg_id',
                        'multiple' => 'multiple'
                        ])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="is_active" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Status'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'is_active')->checkbox()->label(false); ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
            <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), [
                'class' => 'btn bg-teal',
                'id' => 'btn-submit'
            ]) ?>
            <!-- <?= Html::resetButton('<b><i class="fa fa-repeat"></i></b>'.Yii::t('fe', ' Ulang'), [
                'class' => 'btn btn-aqua',
            ]) ?> -->
            <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>'.Yii::t('fe', ' Kembali'),[
                'class' => 'btn bg-slate',
                'data-dismiss' => 'modal'
            ]) ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $('#kelompoktindakanbpjs-form').docoForm('submit',{
        success : function(data) {
            this.formInput[0].reset();
            $('#modal_backdrop').modal('hide');
            table.draw();
        }
    });
</script>
