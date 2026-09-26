<?php

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>" alt="logo">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                            'data-table-id' => 'tablePasienMeninggal',
                            'data-options' => 'click',
                            'id' => 'search-button'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                            'data-table-id' => 'tablePasienMeninggal',
                            'data-options' => 'click',
                        ]
                    ]
                ], '#tablePasienMeninggal'); ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="my-2 ml-2" style="display: flex; align-items: center;">
                            <div class="mr-5" style="width: 10%;">
                                <h5>Cron Hide Data Pasien Meninggal</h5>
                            </div>
                            <div>
                                <?= DocoHelpers::switchStatus($konfigValidate, 'hide-data-pasien-meninggal', 'hide-pasien-meninggal'); ?>
                            </div>
                        </div>
                        <table id="tablePasienMeninggal" class="table table-striped table-condensed table-hover" style="width: 100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No.RM / Nama pasien</th>
                                    <th>No. Pendaftaran</th>
                                    <th>Tanggal Pendaftaran</th>
                                    <th>Tanggal Pulang</th>
                                    <th>Status Aktif Pasien</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var statusActivasi = ' . $statusActivasi . ';
 var statusActivate = [];
 $.each(statusActivasi, function (index, value) {
     statusActivate.push({
         id: index,
         text: value,
     });
 });

', View::POS_END, "b-index");

$this->registerJs($this->render('index.js'), View::POS_END);
?>