<?php
    use yii\web\View;
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use kartik\widgets\DateTimePicker;
?>
<style>
    .datepicker>div{
        display:block;
    }
    .btn-custom {
        padding : 5.5px 12px!important;
    }
    .modal {
      overflow: auto;
    }

    .modal-body {
      overflow: visible;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <?php
    $form = ActiveForm::begin([
            'id' => 'update-tanggal-form',
            'enableAjaxValidation'=>false,
            'enableClientValidation'=>false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
        ]);
    ?>
        <?= $form->field($model, 'tanggal_tindakan', [
        'horizontalCssClasses' => [
            'label' => 'text-left control-label col-sm-3',
            'wrapper' => 'col-md-6'
    ],
        'labelOptions' => [
            'class' => 'text-right col-sm-9']
        ])->widget(DateTimePicker::classname(), [
            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
            'readonly' => true,
            'language' => 'en',
            'pluginOptions' => [
                'startDate' => date('d-M-Y H:i:s', strtotime($startDate)),
                'endDate' => date('d-M-Y H:i:s', strtotime($endDate)),
                'autoclose' => true,
                'todayBtn' => true,
                'format' => 'dd-M-yyyy hh:ii:ss',
            ]
        ]); ?>
        <?=$form->field($model, 'alasan_edit', [
             'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-3',
                'wrapper' => 'col-md-6'
            ],
            'labelOptions' => [
                'class' => 'text-right col-sm-9']
            ])->textArea([
            'placeholder' => Yii::t('fe', 'Alasan'),
            'class' => 'form-control input-sm', 
            'id' => 'alasan-edit',
            'rows' => 5
            ])->label(Yii::t('fe', 'Alasan')); ?>
        <hr></hr>

    <div class="modal-footer" style="padding:0px !important;">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
                'class' => 'btn btn-info btn-labeled btn-xs',
                'id' => 'update-tanggal'
            ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
                    'class' => 'btn btn-info btn-labeled btn-xs',
                    'data-dismiss' => 'modal',
                    'id' => 'close-tanggal'
            ]); ?>
    </div>
    <?= Html::hiddenInput('EditTindakanForm[tanggal_asal]', $tanggal_asal,['id' => 'tanggal_asal']); ?>
<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $('#update-tanggal').on('click', function (event) {
        event.preventDefault();
        var _form = $('#update-tanggal-form');
        $(this).docoForm('click',{
            url : _form.attr('action'),
            data : _form.serializeArray(),
            success : function (res) {
                $("#modal_backdrop").modal('toggle');
                table.draw();
            }
        });
    })
</script>