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
                    'btn-pembatalan' => [
                        'title' => 'Pembatalan',
                        'icon' => 'fa fa-refresh',
                        'attributes' => [
                            'data-options' => 'click',
                            'id' => 'btn-pembatalan',
                        ]
                    ],
                    'btn-resend' => [
                        'title' => 'Resend',
                        'icon' => 'fa fa-paper-plane',
                        'attributes' => [
                            'data-options' => 'click',
                            'id' => 'btn-resend',
                        ]
                    ],
                    'btn-pengesahan' => [
                        'title' => 'Pengesahan',
                        'icon' => 'fa fa-floppy-o',
                        'attributes' => [
                            'data-options' => 'click',
                            'id' => 'btn-pengesahan',
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <?= Yii::$app->controller->renderPartial('_informasi_pasien', [
                    'dataPeserta' => $dataPeserta,
                    'sisaLimit' => $sisaLimit,
                    'diagnosaKode' => $diagnosaKode,
                    'diagnosaText' => $diagnosaText
                ]) ?>
                <?= Yii::$app->controller->renderPartial('_list_data', [
                    'pendaftaranId' => $pendaftaranId,
                    'dataPeserta' => $dataPeserta,
                    'isCob' => $isCob
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs("
        var pendaftaranId = '".$pendaftaranId."';
        var penjaminId = '".$dataPeserta['penjamin_id']."';
        var noKlaim = '".$dataPeserta['no_klaim']."';
        var asuransiId = '".$asuransiId."';
        var diagnosaKode = `".$diagnosaKode."`;
        var diagnosaText = `".$diagnosaText."`;
    ", View::POS_END);
    $this->registerJs($this->render('proses.js'), View::POS_END);
?>