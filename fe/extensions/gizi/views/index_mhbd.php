<?php

/*
* @Author: ilhamsyah
* @Date:   2022-02-22 16:03:25
*/

use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'DAFTAR PESANAN MAKANAN PASIEN');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Gizi'), 'url' => ['/gizi']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
	.custom-col-remark {
		width: 400px;
	}
</style>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b><?= $this->title ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'excel' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs export-excel',
                        ],
                    ],
                    'reset',
                ], '#tb-lap-permintaan-makan');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table class="table table-striped table-condensed table-hover" id="tb-lap-permintaan-makan" style="width:100%">
                    <thead>
                    <tr class="bg-inverse">
                        <th rowspan="2" width="1"><?= Yii::t('fe', 'No') ?></th>
                        <th rowspan="2" ><?= Yii::t('fe', 'Bed No (Billable Class)') ?></th>
                        <th rowspan="2" ><?= Yii::t('fe', 'MRID Patient Name') ?></th>
                        <th rowspan="2" ><?= Yii::t('fe', 'DOB Age') ?></th>
                        <th rowspan="2" ><?= Yii::t('fe', 'Admit Date / LOS Primary Doctor') ?></th>
                        <!-- <th rowspan="2" ><?= Yii::t('fe', 'Diet') ?></th> -->
                        <th rowspan="2"><?= Yii::t('fe', 'Diet Type') ?></th>
                        <!-- <th rowspan="2"><?= Yii::t('fe', 'New Diet') ?></th> -->
                        <th rowspan="2" class="custom-col-remark"><?= Yii::t('fe', 'Remark') ?></th>
                        <th rowspan="2" ><?= Yii::t('fe', 'Latest Diagnosa') ?></th>
                        <th colspan="16" class="text-center"><?= Yii::t('fe', 'Checklist') ?></th>
                    </tr>
                    <tr class="bg-inverse">
                        <th rowspan="1" class="text-center"><?= Yii::t('fe', 'BF') ?></th>
                        <th rowspan="1" class="text-center"><?= Yii::t('fe', 'S1') ?></th>
                        <th rowspan="1" class="text-center"><?= Yii::t('fe', 'LN') ?></th>
                        <th rowspan="1" class="text-center"><?= Yii::t('fe', 'S2') ?></th>
                        <th rowspan="1" class="text-center"><?= Yii::t('fe', 'DN') ?></th>
                        <th rowspan="1" class="text-center"><?= Yii::t('fe', 'S3') ?></th>
                        <th rowspan="1" class="text-center"><?= Yii::t('fe', 'SP') ?></th>
                        <th rowspan="1" class="text-center"><?= Yii::t('fe', 'EX') ?></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var no = "'.(\Yii::t("fe", "No")).'";
    var nama_pasien = "'.(\Yii::t("fe", "MRID<br> Patient Name")).'";
    var no_bed = "'.(\Yii::t("fe", "Bed No<br>(Billable Class)")).'";
    var tanggal_lahir = "'.(\Yii::t("fe", "DOB Age")).'";
    var tgl_admisi = "'.(\Yii::t("fe", "Admit Date /<br> LOS Primary Doctor")).'";
    var diet = "'.(\Yii::t("fe", "Diet")).'";
    var type_diet = "'.(\Yii::t("fe", "Diet Type")).'";
    var new_diet = "'.(\Yii::t("fe", "New Diet")).'";
    var remark = "'.(\Yii::t("fe", "Remark")).'";
    var diagnosa = "'.(\Yii::t("fe", "Latest Diagnosa")).'";
    var checklist = "'.(\Yii::t("fe", "Checklist")).'";

    var BF = "'.(\Yii::t("fe", "BF")).'";
    var S1 = "'.(\Yii::t("fe", "S1")).'";
    var LN = "'.(\Yii::t("fe", "LN")).'";
    var S2 = "'.(\Yii::t("fe", "S2")).'";
    var DN = "'.(\Yii::t("fe", "DN")).'";
    var S3 = "'.(\Yii::t("fe", "S3")).'";
    var SP = "'.(\Yii::t("fe", "SP")).'";
    var EX = "'.(\Yii::t("fe", "EX")).'";

    var inputTanggal = \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value='.date('d-M-Y').' /></div>\';
', View::POS_END, 'index');

$this->registerJs($this->render('index_mhbd.js'), View::POS_END);
?>