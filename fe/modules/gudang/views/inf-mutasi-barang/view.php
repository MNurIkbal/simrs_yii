<?php

/**
 * @author Johndoe
 * @copyright 24 Maret 2018
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Mutasi Barang Masuk'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .input-group{
        margin-bottom: 0 !important;
    }

    .form-info-p{
        padding: 7px 5px;
    }

    .control-label{
        font-weight: bold;
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar">
                <?=DocoHelpers::generateToolbar([
                    'back',
                    'pdf' => [
                            'type' => 'link',
                            'attributes' => [
                                'data-options' => 'link',
                                'class' => 'btn btn-info btn-labeled btn-xs data-print',
                                'id' => 'cetak-pdf',
                                'url' => '/gudang/inf-mutasi-barang/print?id=' . $id
                            ]
                        ],                   
                ]);?>
            </div>

            <div class="panel-body">
                <div class="col-md-12 text-center">
                    <h4><b><?= Yii::t('fe','Mutasi Barang') ?></b></h4>

                    <hr>
                </div>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Detail Mutasi Barang') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <table width="100%" class="tabel">
                                <tbody>
                                    <tr>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Tanggal Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= $data->tgl_pesanbarang ?>
                                        </td>
                                        <td class="bold w-15">
                                            <?= Yii::t('fe','Instalasi - Ruangan Pemesan') ?>
                                        </td>
                                        <td>
                                            <?= $data->instalasi_ruangan ?>
                                        </td>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Nama Pemesan') ?>
                                        </td>
                                        <td>
                                            <?= $data->pemesan ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Tanggal Kirim') ?>
                                        </td>
                                        <td>
                                            <?= $data->tgl_mutasibarang ?>
                                        </td>
                                        <td class="bold w-15">
                                            <?= Yii::t('fe','No Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= $data->no_pemesanan ?>
                                        </td>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Nama Pengirim') ?>
                                        </td>
                                        <td>
                                            <?= $data->pengirim ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Status') ?>
                                        </td>
                                        <td>
                                            <?= $data->status_distribusi ?>
                                        </td>
                                        <td class="bold w-15">
                                            <?= Yii::t('fe','No Pengiriman') ?>
                                        </td>
                                        <td>
                                            <?= $data->nomutasi_barang ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <br>
                            <br>
                            <table id="example" class="table datatable-basic table-striped dataTable no-footer" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                                        <th><?=\Yii::t("fe", "Qty Mutasi");?></th>
                                        <th><?=\Yii::t("fe", "Qty Konversi");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs('
var table;
$(document).ready(function() {
    table = $("#example").docoTabel({
        filter: false,
        sorting: [[1, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl+"gudang/inf-mutasi-barang/get-data-detail?id='. $id .'",
        columns: [                
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Nama Barang")).'", data: "barang_nama"},
            {title: "'.(\Yii::t("fe", "Qty Mutasi")).'", data: "qty_mutasi"},
            {title: "'.(\Yii::t("fe", "Qty Konversi")).'", data: "qty_konversi"}
        ],            
    });
});

$("#cetak-pdf").on("click",function (event) {
    event.preventDefault();
    var url = window.location.origin;
    var target = $(this).attr(\'data-target\');
    window.open(url+target);
});
', View::POS_END, 'detail-mutasi-barang'); ?>
