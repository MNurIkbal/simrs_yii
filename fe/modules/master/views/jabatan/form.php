<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form
        ->field($model, 'kelompokjabatan_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($kelompokjabatan, 'kelompokjabatan_id', 'kelompokjabatan_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => 'Pilih Kelompok Jabatan'
        ]);
    ?>
    <?=$form
        ->field($model, 'indexing_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($indexing, 'indexing_id', 'indexing_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => 'Pilih Indexing'
        ]);
    ?>
    <?=$form->field($model, 'jabatan_urutan', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('jabatan_urutan'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'jabatan_nama', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('jabatan_nama'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'jabatan_lainnya', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('jabatan_lainnya'),'class' => 'form-control input-sm']); ?>
    <?=$form
        ->field($model, 'is_active', ['labelOptions' => ['class' => 'text-left']])
        ->checkbox([
            'class' => 'switch',
            'label' => false,
            'checked' => $model->is_active == 1,
            'data-on-color' => 'success',
            'data-off-color' => 'danger', 'data-size' => 'mini',
            'data-on-text' => $options['status']['1'],
            'data-off-text' => $options['status']['0'],
        ])
        ->label($model->getAttributeLabel('is_active'));
    ?>
    <script type="text/javascript">
        $(".form-group").find(".col-sm-offset-4").removeClass('col-sm-offset-4');
        $(".switch").bootstrapSwitch();
        $(document).on("switchChange.bootstrapSwitch", ".switch", function (e, state) {
            if (e.target.checked == true) {
                $value = '1';
                $('input.prop_state').val($value);
            } else {
                $value = '0';
                $('input.prop_state').val($value);
            }
        });
    </script>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
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