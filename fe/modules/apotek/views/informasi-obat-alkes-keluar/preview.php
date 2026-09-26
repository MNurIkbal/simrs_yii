<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = Yii::t('fe', 'Detail Pemesanan Obat Alkes');
$this->params['breadcrumbs'][] = ['label' => 'Informasi Pemesanan Obat Alkes', 'url' => ['index']];
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
                <?=DocoHelpers::generateToolbar($btn_toolbar);?>
            </div>
            <div class="panel-body">
                <div class="col-md-12 text-center">
                    <?php $ruangan = !empty($data->ruangan_pemesan) ? ucfirst($data->ruangan_pemesan) : '-' ?>
                    <h4><b><?= Yii::t('fe','PEMESANAN OBAT ALKES') ?></b></h4>

                    <hr>
                </div>

                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Detail Pemesanan Obat Alkes') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <table width="100%" class="tabel">
                                <tbody>
                                    <tr>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Tanggal Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->tglpemesanan) ?  date('d M Y',strtotime($data->tglpemesanan)) : '-' ?>
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
                                            <?= $ruangan ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Nomer Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->nopemesanan) ?  $data->nopemesanan : '-' ?>
                                        </td>
                                        <td class="bold w-15">
                                            <?= Yii::t('fe','Pemesan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->nama_pemesan) ?  $data->nama_pemesan : '-' ?>
                                        </td>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Status Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->statusdistribusiobat) ?  $data->statusdistribusiobat : '-' ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <br>
                            <br>
                            <table id="detail-transaksi" class="table datatable-basic table-striped table-hover dataTable no-footer" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= \Yii::t("fe", "Nama Obat Alkes") ?></th>
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
                            <div>
                                <p>Catatan : <?= !empty($data->keterangan_pesan) ?  $data->keterangan_pesan : '' ?></p>
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
    var status_terima = '.DocoConstants::STATUS_TERIMA.';
    var status_mutasi = "'.$data->statusmutasi_id.'";
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
            filter: false,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            columnDefs: [{
                targets: 3,
                render: function(data, type, row) {
                    if(status_mutasi == status_terima) {
                        return data;
                    } else {
                        return "-";
                    }
                }
            }],
            serverSide: true,
            scrollX: true,
            stateSave: true,
            ajax: baseUrl+"apotek/informasi-obat-alkes-keluar/get-data-pemesanan?id=\''.$idParent .'\'",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'",  data: "obatalkes_nama"},
                {title: "'.(\Yii::t("fe", "Qty Pemesanan")).'",  data: "qty_pemesanan"},
                {title: "'.(\Yii::t("fe", "Qty Konversi")).'",  data: "qty_konversi"},
                {title: "'.(\Yii::t("fe", "Qty Terima")).'",  data: "qty_terima"},
            ]
        });
        $(".dataTables_filter").hide();

        $("#verifikasi-pemesanan").on("click",function(){
            $(this).docoForm("click", {
                url: $(this).data("url"),
                method: "GET",
                success: function (data) {
                    docoNotification("success", data.response.message, data.response.text);

                    setTimeout(() => {
                        window.location.href = "/apotek/informasi-obat-alkes-keluar";
                    }, 150);
                }
            });
        });

        $("#batal-pemesanan").on("click",function(){
            $(this).docoForm("click", {
                url: $(this).data("url"),
                method: "GET",
                confirmMessage: "Apakah Anda yakin untuk membatalkan pemesanan obat alkes ini?",
                skipErrorNotif: true,
                success: function (data) {
                    setTimeout(() => {
                        window.location.href = "/apotek/informasi-obat-alkes-keluar";
                    }, 200);
                }
            });
        });
    });

',View::POS_END,'b-index');
