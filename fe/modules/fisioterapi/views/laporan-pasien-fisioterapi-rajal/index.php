<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\widgets\filters\DropdownRuanganRajal\DHSelectRuanganRajal;
use app\widgets\filters\DropdownJenisPemeriksaan\DHSelectJenisPemeriksaan;
use app\widgets\filters\DropdownStatusProgramFisioterapi\DHSelectStatusProgramFisioterapi;

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
                <?= Yii::$app->controller->renderPartial('partials/tabs') ?>
                <?= Yii::$app->controller->renderPartial('partials/table') ?>
            </div>
        </div>
    </div>
</div>

<?php
$formFilter = [
    'ruangan' => DHSelectRuanganRajal::widget([
        'prompt' => 'Semua'
    ]),
    'statusProgram' => DHSelectStatusProgramFisioterapi::widget([
        'prompt' => 'Semua'
    ]),
    'jenisPemeriksaan' => DHSelectJenisPemeriksaan::widget([
        'id' => 'filterJenisPemeriksaan',
        'prompt' => 'Semua',
        'isDepToChild' => true,
        'depUrl' => '/api/master/get-pemeriksaan-fisioterapi-dep',
        'idDepChild' => 'filterNamaPemeriksaan',
        'dataDependPrompt' => 'Semua'
    ])
];
$dataFilter = [
    'listDokterDpjp' => ArrayHelper::getValue($dataView, 'listDokterDpjp'),
    'listDokter' => ArrayHelper::getValue($dataView, 'listDokter'),
    'listStatusPeriksa' => ArrayHelper::getValue($dataView, 'listStatusPeriksa'),
];
$this->registerJsVar('dataFilter', $dataFilter);
$this->registerJsVar('formFilter', $formFilter);
$this->registerJs($this->render("js/index.js"), View::POS_END, 'js');
?>