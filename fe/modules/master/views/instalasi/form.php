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
        ->field($model, 'profilers_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($profilRumahSakit, 'profilrs_id', 'nama_rumahsakit'), [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>
    <?=$form->field($model, 'instalasi_nama', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('instalasi_nama'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'instalasi_namalainnya', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('instalasi_namalainnya'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'instalasi_singkatan', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('instalasi_singkatan'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'satusehat_instalasi', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('satusehat_instalasi'),'class' => 'form-control input-sm', 'readonly' => true]); ?>
    <?=$form
        ->field($model, 'is_penunjang', ['labelOptions' => ['class' => 'text-left']])
        ->checkbox([
            'class' => 'switch',
            'label' => false,
            'checked' => $model->is_penunjang == 1,
            'data-on-color' => 'success',
            'data-off-color' => 'danger', 'data-size' => 'mini',
            'data-on-text' => $options['confirm']['1'],
            'data-off-text' => $options['confirm']['0'],
        ])
        ->label($model->getAttributeLabel('is_penunjang'));
    ?>
    <?=$form
        ->field($model, 'is_pelayanan', ['labelOptions' => ['class' => 'text-left']])
        ->checkbox([
            'class' => 'switch',
            'label' => false,
            'checked' => $model->is_pelayanan == 1,
            'data-on-color' => 'success',
            'data-off-color' => 'danger', 'data-size' => 'mini',
            'data-on-text' => $options['confirm']['1'],
            'data-off-text' => $options['confirm']['0'],
        ])
        ->label($model->getAttributeLabel('is_pelayanan'));
    ?>
    <?=$form
        ->field($model, 'instalasi_adakamar', ['labelOptions' => ['class' => 'text-left']])
        ->checkbox([
            'class' => 'switch',
            'label' => false,
            'checked' => $model->instalasi_adakamar == 1,
            'data-on-color' => 'success',
            'data-off-color' => 'danger', 'data-size' => 'mini',
            'data-on-text' => $options['confirm']['1'],
            'data-off-text' => $options['confirm']['0'],
        ])
        ->label($model->getAttributeLabel('instalasi_adakamar'));
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
            var form = $("#ajax-form");
            form[0].reset();
            $("#modal_backdrop").modal('toggle');
            setTimeout(function() {
                tableInstalasi.draw();
            }, 1000);
        }
    });
    /*$("#btn-simpan").on("click", function(event) {
        event.preventDefault();
        var data = $("#batal-periksa-form").serializeArray();
        $(this).docoForm('click',{
            url: '/rajal/inf-pasien-pulang/save-batal-periksa',
            data: data,
            success : function(res) {
                var form = $("#batal-periksa-form");
                form[0].reset();
                table.draw();
                $("#modal_backdrop").modal('toggle');
                //_afterSave()           
            }
        });
    });*/
</script>