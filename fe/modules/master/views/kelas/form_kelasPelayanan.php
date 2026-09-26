<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
?>
<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">

    <div class="form-group required">
        <label for="jeniskelas_id" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Jenis kelas'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'jeniskelas_id')
                ->dropDownList(
                    $listJenisKelas,
                    [
                        'class' => 'select2',
                        'prompt' => Yii::t('fe', '-- Pilih --')
                        ]
                        )->label(false); 
                        ?>
        </div>
    </div>
    <div class="form-group">
        <label for="kelaspelayanan_nama" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Nama'); ?>
        </label>
        <div class="col-lg-9">
            <?= $form->field($model, 'kelaspelayanan_nama')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="kelaspelayanan_namalainnya" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Nama lainnya'); ?>
        </label>
        <div class="col-lg-9">
            <?= $form->field($model, 'kelaspelayanan_namalainnya')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="jeniskelas_id" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Status'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'is_active')
                ->dropDownList(
                    $status,
                    [
                        'class' => 'select2',
                        'prompt' => Yii::t('fe', '-- Pilih --')
                        ]
                        )->label(false); 
                        ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>

</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            table.draw();
        }
    });
</script>


