
<?php

use app\components\DocoConstants;
use app\components\DocoHelpers;
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

$decryptedpembayaranId = (!empty($pembayaran_id)) ? $pembayaran_id : 0;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
    <div class='row'>
        <div class="col-sm-4">
            <?= $form->field($model, 'jenis_invoice')->radioList($jenis_invoice, [
                'inline' => true,
                'item' => function($index, $label, $name, $checked, $value) {
                    $return = '<label class="modal-radio">';
                    if($checked) {
                        $return .= '<input checked type="radio" name="' . $name . '" value="' . $value . '" class="jenis_invoice">&nbsp;';
                    }
                    else {
                        $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" class="jenis_invoice">&nbsp;';
                    }
                    $return .= '<i></i>';
                    $return .= '<span>' . ucwords($label) . '</span>';
                    $return .= '</label>';
                    return $return;
                }
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'penjamin_id')->dropDownList($listPenjamin, [
                'id' => 'penjamin_id',
                'class' => 'select2',
            ])->label(Yii::t('fe', 'Penjamin')); ?>
        </div>
    </div><br>
    <div class="row">
        <center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
        <div class="progress" style="margin-left: 12px;display:none;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                <span class="label-persentase">0</span>%
            </div>
        </div>
        <span class="help-block label-progress" style="margin-left: 12px;"></span>
    </div><br>
    <div class="row">
        <?php
            echo "<div class='text-right'>";
            echo Html::button('<i class="fa fa-eye"></i> ' . Yii::t('fe', 'Preview'), [
                'class'=>'btn btn-info btn-cetak-invoice']);
                echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            /* comment button pdf
            echo Html::button('<i class="fa fa-print"></i> ' . Yii::t('fe', 'Cetak'), [
                'class'=>'btn btn-info btn-direct-cetak-invoice']);
            echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            */
            echo Html::button('<i class="fa fa-arrow-left"></i> ' . Yii::t('fe', 'Kembali'), ['class'=>'btn bg-slate', 'data-dismiss' => 'modal']);
            echo "</div>";
        ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php
$is_bgprocess = isset($is_bgprocess) ? $is_bgprocess : true;
if(!empty($pembayaran_id) && is_numeric($pembayaran_id)) {
    $pembayaran_id = DocoHelpers::encrypt($pembayaran_id);
}
$this->registerJs('
var groupcarabayar_id = "'.$groupcarabayar_id.'";
var _pembayaranId = "'.$pembayaran_id.'";
var groupUmum = '.DocoConstants::GROUP_UMUM.';
var listPenjamin = '.json_encode($listPenjamin).';
var pembayaran_id = "'.$decryptedpembayaranId.'";
var isBgprocess = '.$is_bgprocess.';
var _pegawaiId = '.$pegawaiId.';

',View::POS_END,'b-index');
?>
<?php $this->registerJs($this->render($pathJs), View::POS_END); ?>
