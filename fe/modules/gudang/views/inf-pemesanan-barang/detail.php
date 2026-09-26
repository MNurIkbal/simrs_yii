<?php
use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"),
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = "Detail";

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
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
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
                            <table id="detail-transaksi" class="table table-striped table-condensed" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?= Yii::t("fe", "No.") ?></th>
                                        <th><?= Yii::t("fe", "Nama Barang") ?></th>
                                        <th><?= Yii::t("fe", "Qty Pemesanan") ?></th>
                                        <th><?= Yii::t("fe", "Satuan Pesan") ?></th>
                                        <th><?= Yii::t("fe", "Qty Kirim") ?></th>
                                        <th><?= Yii::t("fe", "Satuan Kirim") ?></th>
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
    var tableDetailTransaksi;

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableDetailTransaksi = $("#detail-transaksi").docoTabel({
            filter: false,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"gudang/inf-pemesanan-barang/get-detail?id=\''. $primary .'\'",
            columns: [
                {
                    title: "No.",
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
                    data: "qty_besar"
                },
                {
                    title: "'.(\Yii::t("fe", "Satuan Pesan")).'",
                    data: "satuan_besar"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty Kirim")).'",
                    data: "jumlah_mutasi"
                },
                {
                    title: "'.(\Yii::t("fe", "Satuan Kirim")).'",
                    data: "satuan_kirim"
                },
            ]
        });
        $(".dataTables_filter").hide();

        $("#batal-pemesanan").on("click",function(){
            $(this).docoForm("click", {
                url: $(this).data("url"),
                method: "GET",
                skipErrorNotif: true,
                confirmMessage: "Apakah Anda yakin untuk membatalkan pemesanan barang ini?",
                success: function (data) {
                    setTimeout(() => {
                        window.location.href = "/gudang/inf-pemesanan-barang/";
                    }, 200);
                }
            });
        });
    });

',View::POS_END,'b-index');
