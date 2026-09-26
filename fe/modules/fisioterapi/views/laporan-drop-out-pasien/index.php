<?php

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\widgets\filters\DropdownStatusProgramFisioterapi\DHSelectStatusProgramFisioterapi;
use app\widgets\DHMonthRangePickerWidget;

$title = $dataView['title'];
$this->params['breadcrumbs'][] = ['label' => 'Fisioterapi', 'url' => ['/fisioterapi']];
$this->params['breadcrumbs'][] = ['label' => 'Laporan', 'url' => ['/fisioterapi']];
$this->params['breadcrumbs'][] = $title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <?= Yii::$app->controller->renderPartial('partials/table') ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12" style="padding-left: 50px; padding-right: 50px">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <center>
                        <h4>Laporan Grafik Drop Out Pasien Fisioterapi</h4>
                    </center>
                </div>
                <div class="row" style="margin-bottom: 10px; margin-left: 5px">
                    <button type="button" class="btn btn-info btn-labeled btn-xs data-filter-chart">
                        <b><i class="fa fa-search"></i></b>Cari
                    </button>
                    <button type="button" class="btn btn-info btn-labeled btn-xs export-pdf-chart">
                        <b><i class="fa fa-print"></i></b>Export PDF
                    </button>
                    <button type="button" class="btn btn-primary btn-icon btn-rounded pull-right btn-information" data-popup="popover-custom" data-placement="left" title="" data-html="true" data-content="
<pre style='margin-bottom: 20px'>
Numerator
<hr style='width: 40%; display: inline-block; margin-bottom:0px; margin-top: 0px'>  X 100 %
Denomirator
</pre>
                        Keterangan <br>
                        <b>Numerator : </b> Jumlah Seluruh Pasien Yang Drop Out<br>
                        <b>Denuminator : </b>  Jumlah Seluruh Pasien<br>
                        " data-original-title="Keterangan Informasi" data-trigger="click" style="padding:0px 8px!important;background-color: rgba(0, 0, 0, 0.5)!important;">
                        <i class="fa fa-info"></i>
                    </button>
                </div>
                <div class="row" style="margin-bottom: 20px">
                    <div class="col-md-4">
                        <label>Bulan Permintaan :</label>
                        <br />
                        <?= DHMonthRangePickerWidget::widget([
                            'with_default' => true,
                            'start_time' => date('m-Y'),
                            'end_time' => date('m-Y', strtotime('+1 year'))
                        ]); ?>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-body-chart">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$formFilter = [
    'statusProgram' => DHSelectStatusProgramFisioterapi::widget([
        'prompt' => 'Semua'
    ])
];
$dataFilter = [];
$this->registerJsVar('dataFilter', $dataFilter);
$this->registerJsVar('formFilter', $formFilter);
$this->registerJs($this->render("js/index.js"), View::POS_END, 'js');
?>