<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
    <h5 class="panel-title text-center mb-10">Apakah Kunjungan ini untuk prosedur terapi berkelanjutan?</h5>
</div>

<div class="modal-footer text-center">
    <?=Html::button(\Yii::t('fe', 'Ya'), ['class' => 'btn btn btn-prosedur-berkelanjutan bg-teal btn-sm btn-save']); ?>
    <?=Html::button(\Yii::t('fe', 'Tidak'),['class' => 'btn btn-prosedur-tidak bg-slate btn-sm',]); ?>
</div>

<?php 
$this->registerJs('
    var url;

    $(".btn-prosedur-berkelanjutan").on("click", function(event) {
        event.preventDefault();
        $("#flag_procedure").val(1);
        url = "/pendaftaran/daftar-rajal/prosedur-bpjs?param=1";
        modalTujuanKunjunganBpjs(url);
    });

    $(".btn-prosedur-tidak").on("click", function(event) {
        event.preventDefault();
        $("#flag_procedure").val(0);
        url = "/pendaftaran/daftar-rajal/prosedur-bpjs?param=0";
        modalTujuanKunjunganBpjs(url);
    });
', View::POS_END);
?>
