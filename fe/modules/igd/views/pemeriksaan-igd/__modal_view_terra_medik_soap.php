<?php
use app\components\DocoConstants;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
?>
<style type="text/css">
.modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
}
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="tabbable" style="position: relative">
        <div class="nav-sticky-wrapper nav-sticky-cppt" id="nav-sticky">
            <div class="nav nav-tabs nav-tab-cppt" id="nav-tab" role="tablist">
                <a id="tab-soap" class="nav-item nav-tab-type nav-link active" data-toggle="tab" href="#view-soap" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">SOAP</a>
                <a id="tab-obat" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-obat" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Riwayat Obat</a>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane active" id="view-soap">
                <div id="content-soap">
                    <?=Yii::$app->controller->renderPartial('/pemeriksaan-igd/terra-medik/__soap', [
                        'pasien_terra' => $pasien_terra
                    ]);?>
                </div>
            </div>
            <div class="tab-pane" id="view-obat">
                <div id="content-obat">
                    <?=Yii::$app->controller->renderPartial('/pemeriksaan-igd/terra-medik/__obat', [
                        'pasien_terra' => $pasien_terra
                    ]);?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer text-left">

</div>
