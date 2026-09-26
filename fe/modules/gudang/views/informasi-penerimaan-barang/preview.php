<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Detail Pemesanan Barang Masuk');
$this->params['breadcrumbs'][] = ['label' => 'Informasi Pemesanan Obat Alkes', 'url' => []];
$this->params['breadcrumbs'][] = 'Detail Pemesanan';
$idParent = DocoHelpers::encrypt($id);
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'pdf' => [
                            'type' => 'link',
                            'attributes' => [
                                'data-options' => 'link',
                                'class' => 'btn btn-info btn-labeled btn-xs data-print',
                                'id' => 'cetak-pdf',
                                'url' => '/gudang/informasi-penerimaan-barang/export-pdf?id=' . $idParent
                            ]
                        ],
                        'back'
                    ]);?>
            </div>
            <div class="panel-body">
                <div class="col-md-12 text-center">
                    <?php $ruangan = !empty($data->ruangan_penerima) ? strtoupper($data->ruangan_penerima) : '-' ?>
                    <h4><b><?= Yii::t('fe','PENERIMAAN BARANG') ?></b></h4>
                    <h4><b><?= Yii::t('fe',"{$ruangan}") ?></b></h4>
                    <hr>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Informasi Transaksi') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <label for="" class="col-lg-5 control-label"><?= Yii::t('fe','No Penerimaan') ?></label>
                                <div class="col-md-7 detail-pasien text-left">
                                    :&nbsp;<?= !empty($data->noterimamutasi) ?  $data->noterimamutasi : '' ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="" class="col-lg-5 control-label"><?= Yii::t('fe','Tanggal Penerimaan') ?></label>
                                <div class="col-md-7 detail-pasien text-left">
                                    :&nbsp;<?= !empty($data->tglterima) ?  date('d M Y',strtotime($data->tglterima)) : '' ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="" class="col-lg-5 control-label"><?= Yii::t('fe','Instalasi Pengirim') ?></label>
                                <div class="col-md-7 detail-pasien text-left">
                                    :&nbsp;<?= !empty($data->instalasi_pengirim) ?  $data->instalasi_pengirim : '' ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="" class="col-lg-5 control-label"><?= Yii::t('fe','Ruangan Pengirim') ?></label>
                                <div class="col-md-7 detail-pasien text-left">
                                    :&nbsp;<?= !empty($data->ruangan_pengirim) ? $data->ruangan_pengirim : '' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Detail Transaksi') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <table id="detail-transaksi" class="table table-bordered table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= \Yii::t("fe", "Nama Barang") ?></th>
                                        <th><?= \Yii::t("fe", "Qty") ?></th>
                                        <th><?= \Yii::t("fe", "Satuan Kecil") ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="6">Data tidak ditemukan.</td>
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
    // Global Var
    var tableJenisKertas;
    $("#cetak-pdf").on("click",function (event) {
        event.preventDefault();
        var url = window.location.origin;
        var target = $(this).attr(\'data-target\');
        window.open(url+target);
    });
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableJenisKertas = $("#detail-transaksi").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            stateSave: true,
            ajax: baseUrl+"gudang/informasi-penerimaan-barang/get-data-pemesanan?id=\''.$idParent .'\'",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Nama Barang")).'",  data: "barang_nama"},
                {title: "'.(\Yii::t("fe", "Qty")).'",  data: "jmlterima"},
                {title: "'.(\Yii::t("fe", "Satuan Kecil")).'",  data: "satuanunit_nama"},
            ]
        });
        $(".dataTables_filter").hide();
    });

',View::POS_END,'b-index');
