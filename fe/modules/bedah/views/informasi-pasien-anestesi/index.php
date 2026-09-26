<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;


$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Anestesi'), 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    // 'reset',
                    'proses' => [
                        'title' => \Yii::t('fe', 'Proses'),
                        'icon' => 'fa fa-stethoscope',
                        'attributes' => [
                            'data-target' => Url::to(['proses', 'id' => '']),
                        ]
                    ],
                ], '#tb-pasien-anestesi') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tb-pasien-anestesi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>&nbsp;</th>
                            <th><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "Nama") ?></th>
                            <th><?= Yii::t("fe", "No. Rekam Medik") ?></th>
                            <th><?= Yii::t("fe", "No. Pendaftaran") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Rencana") ?></th>
                            <th><?= Yii::t("fe", "Operasi") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
                            <th><?= Yii::t("fe", "Proses") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$phpVars = [
    'list_sts_op' => $list_sts_op,
    'status_anestesi' => $status_anestesi
];
$this->registerJsVar('pageVars', $phpVars);
$this->registerJsVar('table', null);
$this->registerJs($this->render('index.js'));
?>
