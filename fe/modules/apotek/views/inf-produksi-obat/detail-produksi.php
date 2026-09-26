<?php

/**
 * @author : Asri Nurul M
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img alt="modul-icon" src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?php
                    $buttons = [
                        'kembali' => [
                            'type' => 'kembali',
                            'title' => \Yii::t('fe', 'kembali'),
                            'icon'=>'fa fa-arrow-left',
                            'attributes' => [
                                'class' => 'btn-pesan',
                                'data-target' => '/apotek/inf-produksi-obat/index-produksi',
                                'data-options' => 'link'
                            ]
                        ],
                        'produksi' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Produksi'),
                            'icon' => 'fa fa-medkit',
                            'attributes' => [
                                'id' => 'btn-produksi',
                                'data-width'  => '40%',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action'      => '/apotek/inf-produksi-obat/show-popup-produksi?id='.$id_produksi,
                            ]
                        ],
                        'alertharga' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Alert Harga'),
                            'icon' => 'fa fa-medkit',
                            'attributes' => [
                                'id' => 'btn-alert',
                                'data-width'  => '80%',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action'      => '/apotek/inf-produksi-obat/alert-harga?id='.$id_produksi,
                                'class' => 'hidden',
                            ]
                        ],
                        'batal' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Batal'),
                            'icon' => 'fa fa-close',
                            'attributes' => [
                                'class' => 'data-batal',
                                'id' => 'data-batal',
                                'data-options'=>'click',
                                'disabled' => true,
                            ]
                        ],
                        'edit' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-edit',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'btn-edit',
                                'data-target' => 'transaksi-pemesanan-produksi/request?id=',
                                'data-options' => 'click',
                                'disabled' => true,
                            ]
                        ],
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($buttons)?>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class='legend-index'>
                        <div class="legend-wrapper">
                            <div class="legend-information">
                                <div class="legend-information__color" style="background-color:#f4baba "></div>
                                <div class="legend-information__text" >Stok Tidak Tersedia Untuk Produksi</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Detail produksi obat') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body" style="max-height: 392px; overflow-y: scroll;">
                            <div class="row">
                                <table id="pemesanan-produksi-obat-alkes" class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th><?= \Yii::t("fe", "Nama obat produksi"); ?></th>
                                            <th><?= \Yii::t("fe", "Qty"); ?></th>
                                            <th><?= \Yii::t("fe", "Satuan"); ?></th>
                                            <th><?= \Yii::t("fe", "Pemesanan"); ?></th>
                                            <th><?= \Yii::t("fe", "Tanggal Pesan"); ?></th>
                                            <th><?= \Yii::t("fe", "Satatus"); ?></th>
                                            <th><?= \Yii::t("fe", "Harga Netto (Rp.)"); ?></th>
                                            <th><?= \Yii::t("fe", "Aksi"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="9">
                                                <?= \Yii::t("fe", "Data tidak ditemukan."); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Catatan produksi obat') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div>
                            <textarea name="catatan" id="catatan" cols="5" rows="5" class="form-control" disabled='disabled'><?= $catatan ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div id="modal-alert" class="modal fade" data-backdrop="static" >
                        <div class="modal-dialog" style="width: 80%">
                            <div class="modal-content ">
                                <div class="modal-header bg-inverse">
                                    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
                                    <h5 class="modal-title"><?=$title;?></h5>
                                </div>
                                <div class="modal-body">
                                </div>
                                <div class="modal-footer text-left">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs('
        var id = "' . $id_produksi . '";
        var pemesananProdukid = "' . $pemesananProdukid . '";
        var noPemesanan = "' . $noPemesanan . '";
        var statusProduksiId = "' . $statusProduksiId . '";
        var status_produksi = "' . $status . '";
        var statusVerifikasi = "' . DocoConstants::SUDAH_VERIFIKASI_PESANAN . '";
        var statusBatalProduksi = "' . DocoConstants::BATAL_PRODUKSI . '";
        var statusDefineMaterial = "' . DocoConstants::DEFINE_MATERIAL . '";
        var obatTidakTersedia = "' . $ObatTidakTersedia . '";
        const  detailKetersediaan = '.$detailKetersediaan.';
    ', View::POS_END,'js-kuning');
    $this->registerJs($this->render('js/detail-produksi.js'));
?>
