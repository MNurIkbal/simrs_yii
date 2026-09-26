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
                    'back',
                    'approve' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Approve'),
                        'icon' => 'fa fa-check',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'data-approve',
                            'data-options' => 'click',
                        ],
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <table class="table" border="0" cellpadding="0" cellspacing="0" style="border: none">
                        <tr>
                            <th>Tanggal Permintaan</th>
                            <td>: <?= $data_detail['tgl_permintaan'] ?></td>
                            <th>No. Pendaftaran</th>
                            <td>: <?= $data_detail['no_pendaftaran'] ?></td>
                        </tr>
                        <tr>
                            <th>Instalasi - Ruangan Asal</th>
                            <td>: <?= $data_detail['ruangan_asal'] ?></td>
                            <th>Nama Pasien</th>
                            <td>: <?= $data_detail['nama_pasien'] ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-12">
                    <table id="example" class="table table-striped table-condensed table-hover" width="100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1">No.</th>
                                <th><?= \Yii::t('fe', 'Tanggal Permintaan') ?></th>
                                <th><?= \Yii::t('fe', 'Nama Obat') ?></th>
                                <th><?= \Yii::t('fe', 'Qty') ?></th>
                                <th><?= \Yii::t('fe', 'Satuan') ?></th>
                                <th><?= \Yii::t('fe', 'Status') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center"><?= \Yii::t('fe', 'Data tidak ditemukan') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs("
        var id = '$pendaftaran_id_display';
    ", VIEW::POS_END, 'js-kunings');
    $this->registerJs($this->render('../assets/js/informasi-permintaaan-bmhp/detail.js'));
 ?>
