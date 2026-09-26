
<?php
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\ActiveForm;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'invoice-form',
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
    <div class='row'>
        <?= Html::radioList('jenis_invoice', 1, $jenis_invoice, ['inline' => true]) ?>
    </div>
    <?php
        echo "<div class='text-right'>";
        echo Html::button('<i class="fa fa-print"></i> ' . Yii::t('fe', 'Cetak'), [
            'class'=>'btn btn-info cetak-invoice-belum-bayar']);
        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
        echo Html::button('<i class="fa fa-arrow-left"></i> ' . Yii::t('fe', 'Kembali'), ['class'=>'btn bg-slate', 'data-dismiss' => 'modal']);
        echo "</div>";
    ?>
</div>
<?php ActiveForm::end(); ?>
<?php $this->registerJs($this->render('../js/modal.js')); ?>