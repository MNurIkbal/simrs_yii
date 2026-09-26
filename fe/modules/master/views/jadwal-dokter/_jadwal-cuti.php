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
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Master'), 'url' => ['/']];
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
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/jadwal-dokter/create-cuti',
                            'disabled' => false
                        ]
                    ],
                    // 'edit' => [
                    //     'title' => \Yii::t('fe', 'Edit'),
                    //     'attributes' => [
                    //         'id' => 'data-edit',
                    //         'data-options' => false,
                    //         'data-target' => '/master/jadwal-dokter/update?id=',
                    //         'class' => 'btn btn-info btn-labeled btn-xs data-edit btn-toolbar',
                    //         'disabled' => true
                    //     ]
                    // ],
                    'batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Hapus'),
                        'icon' => 'fa fa-ban',
                        'attributes' => [
                            'id' => 'data-hapus',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'master/jadwal-dokter/delete-cuti?jadwalcuti_id=',
                            'data-params' => 'jadwalcuti_id',
                            'disabled' => false
                        ]
                    ],
                ], '#tb-jadwal-cuti') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tb-jadwal-cuti" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>&nbsp;</th>
                            <th><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "Nama Dokter") ?></th>
                            <th><?= Yii::t("fe", "Spesialis") ?></th>
                            <th><?= Yii::t("fe", "Ruangan") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Cuti") ?></th>
                            <th><?= Yii::t("fe", "Lama Cuti") ?></th>
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
    'list_ruangan' => $list_ruangan
];
$this->registerJsVar('pageVars', $phpVars);
$this->registerJsVar('table', null);
$this->registerJs($this->render('jadwal-cuti.js'));
?>
