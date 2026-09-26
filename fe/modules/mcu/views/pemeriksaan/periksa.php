<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 14:21:43
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-14 13:59:43
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Medical Check Up'), 'url' => ['/mcu/dashboard']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$instalasi_id = DocoHelpers::encrypt(Yii::$app->docoVars->workspace('instalasi_id'));
?>
<style type="text/css">
    .row {
        padding-right: 5px;
    }
</style>
<div class="row" >
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading" >
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <!--button back-->
            <div class="panel-toolbar clearfix">
                <?=
                DocoHelpers::generateToolbar([
                    'back' => [
                        'attributes' => [
                            'href' => '/mcu/informasi-pasien-mcu',
                        ]
                    ],
                    'riwayat' => [
                        'title' => Yii::t('fe', 'Riwayat Pasien'),
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'data-options' => 'link',
                            'data-hash'=>'0',
                            'target' => '_blank',
                            // 'data-target' => '/mcu/pemeriksaan/detail-riwayat?no_pendaftaran='.$response['no_pendaftaran'],
                            'data-target' => '/igd/riwayat-pasien/index?norm='.$response['no_rekam_medik'].'&instalasi='.$instalasi_id,true,
                        ]
                    ],
                    'custom-print' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Cetak'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'class' => 'print',
                            'method' => 'json',
                            'data-options' => 'link',
                            'disabled' => $is_disabled
                        ],
                    ],
                    ]) ?>

            </div>
            <div class="panel-body">
                <div class="row">
                    <?=Yii::$app->controller->renderPartial('_pasien_identitas', [
                        'data_pasien' => $response
                    ]);?>
                </div>
                <div class="row">
                    <?=Yii::$app->controller->renderPartial('_pasien_pemeriksaan', [
                         'encrytedPendaftaranId' => $encrytedPendaftaranId,
                         'format_mcu' => $format_mcu,
                    ]);?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs('
        var config = "'.$config.'";
        var pendaftaran_id = "'. $id .'";
        var pasien_id = "'. $pasien_id .'";
        var kelaspelayanan_id = "'. $kelaspelayanan_id .'";
        var pegawai_id = "'.$pegawai_id.'";
        var ruangan_id = "'.$ruangan_id.'";
    ', View::POS_END);
    $this->registerJs($this->render('js/_pemeriksaan.js'), View::POS_END);
?>