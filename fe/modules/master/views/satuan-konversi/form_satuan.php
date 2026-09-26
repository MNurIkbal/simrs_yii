<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<!-- Start avtive form -->
<?php $form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'validateOnSubmit' => false, 
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>

<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">
        <?= Yii::t('fe', 'Tambah Data') ?>
    </h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?= Html::hiddenInput('SatuanKonversiForm[obatalkes_id]', $id); ?>
    <?= Html::hiddenInput('SatuanKonversiForm[satuankecil_id]', @$dataObat['satuankecil_id']); ?>
    <div class="form-group">
        <label class="control-label text-left control-label col-sm-3">Nama Obat Alkes</label>
        <div class="col-md-5">
            <p style="margin-top: 6px;"><?= @$dataObat['obatalkes_nama'] ?></p>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label text-left control-label col-sm-3">
            <?= Yii::t('fe', 'Satuan Terkecil / Penyimpanan') ?></label>
        <div class="col-md-5">
            <p style="margin-top: 6px;"><?= @$dataObat['satuan_kecil'] ?></p>
        </div>
    </div>
    <?=
        $form->field($model, 'satuanbesar_id', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-3',
                'wrapper' => 'col-md-5'
            ]
        ])->dropDownList($satuan, [
            'class' => 'select2 satuanbesar_id',
            'id'=>'satuanbesar_id',
            'prompt' => '— Pilih Satuan Besar —',
        ])->label(Yii::t('fe', 'Satuan Besar'));
    ?>
    <?= $form->field($model, 'nilai_konversi', ['horizontalCssClasses' => [
            'label' => 'text-left control-label col-sm-3',
            'wrapper' => 'col-md-5',
        ]])->textInput([
                'class' => 'form-control text-right',
                'id'=>'nilai_konversi', 
        ]) 
    ?>
    <?=
        $form->field($model, 'is_active', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-3',
                'wrapper' => 'col-md-5'
            ]
        ])->checkbox(['label' => 'Aktif'])->label(Yii::t('fe', 'Status'))
    ?>
    <hr>
    <div class="modal-footer" style="padding:0px !important;">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
                'class' => 'btn btn-info btn-labeled btn-xs',
                'id' => 'btn-submit'
            ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'data-dismiss' => 'modal'
            ]); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $('select').on(
        'select2:close',
        function () {
            $("#satuanbesar_id").focus();
        }
    );

    $('input').on('input', function() {
      match = (/(\d{0,9})[^.]*((?:\.\d{0,9})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
      this.value = match[1] + match[2];
    });

    $(document).on('keydown', null, function (event) {
        if (event.key == 'Enter') {
            return false;
        }
    });

    $('#btn-submit').on('click', function (event) {
        event.preventDefault();
        var _form = $('#ajax-form');
        $(this).docoForm("click", {
            url : _form.attr('action'),
            data : _form.serializeArray(),
            success : function(data) {
                $("#form")[0].reset();
                $('#modal_backdrop').modal('hide');
                $("#satuankonversiform-satuanbesar_id").trigger("change");
                tableKonversi.draw();
            }
        });
    });
</script>

