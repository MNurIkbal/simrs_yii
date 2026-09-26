<?php

/**
 * @Author: Asri Nurul M
 * @Date:   2024-06-20
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Informasi Produksi Obat', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .tooltip {
        margin-top: 100px;
        z-index: 99999 !important;

    }
    .tooltip-inner {
        max-width: 500px!important;
        text-align: left!important;
        display: grid;
        flex-wrap: wrap;
        word-break: break-all;
        z-index: 99999 !important;
        position: relative !important;
        text-align: justify;
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
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-12">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'batal' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Batal'),
                            'icon' => 'fa fa-close',
                            'attributes' => [
                                'class' => 'data-batal',
                                'id' => 'data-batal',
                                'data-options'=>'click',
                                'disabled' => true
                            ]
                        ],
                        'varifikasi' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Verifikasi'),
                            'icon' => 'fa fa-check',
                            'attributes' => [
                                'class' => 'data-varifikasi',
                                'id' => 'data-varifikasi',
                                'data-options'=>'click',
                                'disabled' => true
                            ]
                        ],
                        'excel' => [
                            'title' => Yii::t('fe', 'Unduh'),
                            'attributes'=>[
                                'data-target'=> Url::home().'apotek/inf-produksi-obat/export-excel?'
                            ]
                        ],
                        'pesan' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Pesan'),
                            'icon' => 'fa fa-cart-plus',
                            'attributes' => [
                                'class' => 'btn-pesan',
                                'data-target' => 'transaksi-pemesanan-produksi/request?',
                                'data-options' => 'link'
                            ]
                        ],
                        'edit' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-edit',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'btn-edit',
                                'data-target' => 'transaksi-pemesanan-produksi/request?id=',
                                'disabled' => true,
                            ]
                        ],
                        'cetak-detail' => [
                            'title' => 'Cetak',
                            'icon' => 'fa fa-file-pdf-o',
                            'attributes' => [
                                'data-target' => Url::home() . 'reports/viewer/produksi-obat?',
                                'id' => 'btn-cetak-produksi',
                                'data-options'=>'click',
                                'disabled' => true
                            ]
                        ]

                    ]);?>
                </div>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">

                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "No.Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Verifikasi");?></th>
                            <th><?=\Yii::t("fe", "No.Produksi");?></th>
                            <th><?=\Yii::t("fe", "Pemesan");?></th>
                            <th><?=\Yii::t("fe", "Obat Pesanan");?></th>
                            <th style="width:20%"><?=\Yii::t("fe", "Catatan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs('
        var user_login = "' . $user_login . '";
        var status_dropdown = \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
            Html::dropDownList('status_id', '', $_status,
                [
                    'class' => 'form-control select2',
                    'prompt' => \Yii::t('fe', 'ALL')
                ]
            ))).'</div>\';
    ', View::POS_END,'js-kuning');
    $this->registerJs($this->render('js/index.js'));
 ?>