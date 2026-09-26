<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Asuransi Penjamin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .table-custom {
        border: none !important;
    }

    .table-tindakan>tbody>tr>td {
        border: none !important;
        border-color: white !important;
        padding: 10px !important;
        height: 100px !important;
    }

    .table-custom>tbody>tr>td {
        border: none !important;
        border-color: white !important;
        padding: 5px !important;
    }
</style>
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'back' => [
                        'attributes' => [
                            'href' => '/penjamin-asuransi/informasi-dashboard-integrasi'
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="row" style="padding-left: 50px; padding-right: 50px; padding-top: 20px">
                    <div class="row">
                        <div class="col-md-3">
                            <div style="margin-bottom: 10px">
                                <b>No Pendaftaran / No SEP</b>
                                <?= Html::dropDownList('addmission_number', null, [], ['class' => 'form-control input-sm', 'id' => 'addmission_number', 'prompt' => 'No Pendaftaran / SEP']) ?>
                            </div>
                        </div>
                    </div>
                    <div id="loadContent"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs($this->render('cob-pasien.js'), View::POS_END);
?>
