<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<!-- Start avtive form -->
<?php $form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
echo $form->field($model, 'barang_id')->hiddenInput(['value'=> $id])->label(false);
echo $form->field($model, 'satuankecil_id')->hiddenInput(['value'=> $dataBarang['satuankecil_id']])
->label(false);

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
    <div class="form-group">
        <label class="control-label text-left control-label col-sm-3">Nama Barang</label>
        <div class="col-md-5">
            <p style="margin-top: 6px;"><?= $dataBarang['barang_nama'] ?></p>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label text-left control-label col-sm-3">
            <?= Yii::t('fe', 'Satuan Kecil') ?></label>
        <div class="col-md-5">
            <p style="margin-top: 6px;"><?= $dataBarang['satuan_kecil'] ?></p>
        </div>
    </div>
    <?= $form->field($model, 'satuanbesar_id', ['horizontalCssClasses' => [
            'label' => 'text-left control-label col-sm-3',
            'wrapper' => 'col-md-5',
    ]])
        ->dropDownList($satuan, [
            'class' => 'form-control input-sm select2',
            'prompt' => Yii::t('fe', 'Satuan Besar'),
        ])->label(Yii::t('fe', 'Satuan Besar'));
    ?>
    <?= $form->field($model, 'nilai_konversi', ['horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-3',
                'wrapper' => 'col-md-5',
        ]])
            ->textInput([
                'class' => 'form-control input-sm doco-number'
            ]); 
    ?>
    <?=
        $form->field($model, 'is_active', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-3',
                'wrapper' => 'col-md-5'
            ]
        ])->checkbox(['label' => 'Aktif'])->label(Yii::t('fe', 'Status'))
    ?>
</div>

<!-- Modal footer -->
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'></i> ". Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
	$("#ajax-form").docoForm("submit", {
        success : function(data) {
            $("#form")[0].reset();
            $('#modal_backdrop').modal('hide');
            $("#satuankonversibarangform-satuanbesar_id").trigger("change");
            tableKonversi.draw();
        }
    });
</script>

