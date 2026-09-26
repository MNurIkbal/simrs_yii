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
    <div class="panel panel-white">
    <div class="panel-heading">
        <h5 class="panel-title"><?= $title ?></h5>
    </div>
    <div class="panel-heading">
        <table id="tbl-detail" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th width="10%">No</th>
                    <th><?=\Yii::t("fe", "Instalasi");?></th>
                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                    <th><?=\Yii::t("fe", "Dokter");?></th>
                    <th><?=\Yii::t("fe", "Tgl. Keluar");?></th>
                    <th><?=\Yii::t("fe", "Cara Keluar");?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center"><?=Yii::t('fe','Data tidak tersedia')?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>

<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>

<?php 
$this->registerJs('var no_rekam_medik = "'.$no_rekam_medik.'"', View::POS_END);
$this->registerJs($this->render('../js/detailkunjungan.js'), View::POS_END); ?>