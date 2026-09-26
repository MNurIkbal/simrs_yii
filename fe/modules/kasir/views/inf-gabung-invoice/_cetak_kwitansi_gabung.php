
<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'kwitansi-gabung-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_VERTICAL,
]);
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
    <?= $form->field($model, 'invoicegabung_id')->hiddenInput()->label(false); ?>
    <div class='row'>
        <?= $form->field($model, 'diterima_dari')->textInput(['class' => 'form-control']); ?>
        <?= $form->field($model, 'keterangan')->textInput(['class' => 'form-control'])->label(Yii::t('fe', 'Keterangan Pembayaran')); ?>
        <?= $form->field($model, 'jenis_kwitansi')->checkboxList($jenis_kwitansi, ['inline' => true]) ?></div>
    <?php
        echo "<div class='text-right'>";
        echo Html::button('<i class="fa fa-print"></i> ' . Yii::t('fe', 'Cetak'), [
            'class'=>'btn btn-info btn-cetak-kwt']);

        echo '&nbsp;&nbsp;&nbsp;&nbsp;';

        echo Html::button('<i class="fa fa-arrow-left"></i> ' . Yii::t('fe', 'Kembali'), ['class'=>'btn bg-slate', 'data-dismiss' => 'modal']);
        echo "</div>";
    ?>
</div>

<?php ActiveForm::end(); ?>

<?php $this->registerJs($this->render('js/cetak-kwitansi.js')); ?>