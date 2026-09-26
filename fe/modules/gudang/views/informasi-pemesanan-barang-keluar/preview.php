<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Informasi Pemesanan Barang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = "Detail";
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar($btn_toolbar);?>
            </div>
            <div class="panel-body">
                <div class="col-md-12 text-center">
                    <h4><b><?= Yii::t('fe','PEMESANAN BARANG') ?></b></h4>

                    <hr>
                </div>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Detail Pemesanan Barang') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <table width="100%" class="tabel">
                                <tbody>
                                    <tr>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Tanggal Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->tgl_pesanbarang) ?  date('d M Y',strtotime($data->tgl_pesanbarang)) : '-' ?>
                                        </td>
                                        <td class="bold w-15">
                                            <?= Yii::t('fe','Ruangan Tujuan Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->ruangan_tujuan) ? $data->ruangan_tujuan : '-' ?>
                                        </td>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Ruangan Pemesan') ?>
                                        </td>
                                        <td>
                                            <?= $data->ruangan_pemesan ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Nomer Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->no_pemesanan) ?  $data->no_pemesanan : '-' ?>
                                        </td>
                                        <td class="bold w-15">
                                            <?= Yii::t('fe','Pemesan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->pemesan) ?  $data->pemesan : '-' ?>
                                        </td>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Status Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->status_distribusi) ?  $data->status_distribusi : '-' ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <br>
                            <br>
                            <table id="detail-transaksi" class="table table-striped table-condensed" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= \Yii::t("fe", "Nama Barang") ?></th>
                                        <th><?= \Yii::t("fe", "Qty Pemesanan") ?></th>
                                        <th><?= \Yii::t("fe", "Qty Konversi") ?></th>
                                        <th><?= \Yii::t("fe", "Qty Terima") ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="6">Data tidak ditemukan.</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="text-left control-label col-sm-1"><b>
                                        <?= Yii::t('fe','Catatan') ?></b></label>
                                    <div class="col-sm-11">
                                        <p><b>:</b>&nbsp;<?= !empty($data->keterangan_pesan) ? $data->keterangan_pesan : '' ?></p>
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
    // Global Var
    var tableJenisKertas;
    $("#cetak-pdf").on("click",function (event) {
        event.preventDefault();
        var url = window.location.origin;
        var target = $(this).attr(\'data-target\');
        window.open(url+target);
    });

    $("#batal-pemesanan").on("click",function(){
        $(this).docoForm("click", {
            url: $(this).data("url"),
            method: "GET",
            skipErrorNotif: true,
            confirmMessage: "Apakah Anda yakin untuk membatalkan pemesanan barang ini?",
            success: function (data) {
                setTimeout(() => {
                    window.location.href = "/gudang/informasi-pemesanan-barang-keluar/";
                }, 200);
            }
        });
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableJenisKertas = $("#detail-transaksi").docoTabel({
            filter: false,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"gudang/informasi-pemesanan-barang-keluar/get-detail?id=\''.$idParent .'\'",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Barang")).'",
                    data: "barang_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty Pemesanan")).'",
                    data: "qty_pesan"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty Konversi")).'",
                    data: "qty_konversi"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty Terima")).'",
                    data: "qty_terima"
                },
            ]
        });
        $(".dataTables_filter").hide();
    });

',View::POS_END,'b-index');
