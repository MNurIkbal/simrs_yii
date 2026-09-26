<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

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
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'view' => [
                        'type' => 'link',
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-eye',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'data-lihat',
                            'data-target' => '/apotek/informasi-permintaan-bmhp/detail?id=',
                        ],
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="advanced-filter"></div>
                    <table id="example" class="table table-striped table-condensed table-hover" width="100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th></th>
                                <th width="1">No.</th>
                                <th><?= \Yii::t('fe', 'Tanggal Permintaan') ?></th>
                                <th><?= \Yii::t('fe', 'Ruangan Tujuan') ?></th>
                                <th><?= \Yii::t('fe', 'No. Pendaftaran') ?></th>
                                <th><?= \Yii::t('fe', 'Nama Pasien') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center"><?= \Yii::t('fe', 'Data tidak ditemukan') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs('
        var ruangan_dropdown = \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
            Html::dropDownList('ruangan_asal_id', '', $ruangan,
                [
                    'class' => 'form-control select2',
                    'prompt' => \Yii::t('fe', 'ALL')
                ]
            ))).'</div>\';
    ', View::POS_END,'js-kuning');
    $this->registerJs($this->render('../assets/js/informasi-permintaaan-bmhp/index.js'));
 ?>