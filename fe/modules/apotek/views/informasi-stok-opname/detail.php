<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-12-06 11:46:06
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-28 18:20:41
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $header, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
.redClass {
    background-color: #ffd2d2!important;
}

.group {
    font-weight: bold;
    font-size: 11pt;
}

.group td, tr.group:hover {
    background-color: #f1f1f1!important;
    padding-bottom: 8px!important;
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
                      <h3 class="panel-title"><b><?= $this->title ?></b></h3>
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
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar($btn_toolbar, '#exampleDetail');?>
                </div>
            </div>
            <div class="panel-body">
                <br>
                <div class="row" style="font-size: 16px">
                    <div class="col-md-4">
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Tanggal Stok Opname") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?=isset($data['tglstokopname']) ? date('d-M-Y H:i:s', strtotime($data['tglstokopname'])) : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Nomor Stok Opname") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?=isset($data['nostokopname']) ? $data['nostokopname'] : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Jenis Stok Opname") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?=isset($data['jenisstokopname']) ? ($data['jenisstokopname'] == 'P') ? \Yii::t('fe', 'Penyesuaian') : \Yii::t('fe', 'Stok Awal') : '-' ?> </p>
                            </div>
                        </div>
                        <?php
                            if($isWithVerified):
                        ?>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Status Verifikasi") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?=isset($data['is_verifikasi']) ? ($data['is_verifikasi'] == TRUE) ? \Yii::t('fe', 'Sudah Verifikasi') : \Yii::t('fe', 'Belum Verifikasi') : '-' ?> </p>
                            </div>
                        </div>
                        <?php
                            endif;
                        ?>
                    </div>
                    <div class="col-md-4 col-md-offset-4">
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Tanggal Formulir Stok Opname") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?=isset($data['tglformulir']) ? date('d-M-Y H:i:s', strtotime($data['tglformulir'])) : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Nomor Formulir Stok Opname") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?=isset($data['noformulir']) ? $data['noformulir'] : '-' ?> </p>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-12">
                        <table id="exampleDetail" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?=\Yii::t("fe", "Rak Obat");?></th>
                                    <th><?=\Yii::t("fe", "Laci Obat");?></th>
                                    <th><?=\Yii::t("fe", "Nama obat alkes");?></th>
                                    <th><?=\Yii::t("fe", "Stok Saat Stok Opname");?></th>
                                    <th><?=\Yii::t("fe", "Stok Fisik");?></th>
                                    <th><?=\Yii::t("fe", "Selisih Stok Opname");?></th>
                                    <th><?=\Yii::t("fe", "Stok Saat Ini");?></th>
                                    <th><?=\Yii::t("fe", "Selisih Saat Ini");?></th>
                                    <th><?=\Yii::t(
                                        "fe",
                                        @$configBasePriceVal == 0 ? "Weighted Avg" : "HNA"
                                    );?></th>
                                    <th><?=\Yii::t("fe", "Total Selisih");?></th>
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
                <div class="row" style="font-size: 16px">
                    <div class="col-md-5">
                        <?php
                        $total_weighted_avg_sistem = isset($data['total_weighted_avg_sistem']) ? $data['total_weighted_avg_sistem'] : 0;
                        $total_weighted_avg_fisik = isset($data['total_weighted_avg_fisik']) ? $data['total_weighted_avg_fisik'] : 0;
                        ?>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Total Weighted Average Fisik") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; Rp. <?=number_format($total_weighted_avg_fisik, 2, ',', '.')?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Total Weighted Average Sistem") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; Rp. <?=number_format($total_weighted_avg_sistem, 2, ',', '.')?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Selisih") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; Rp. <?= number_format($total_weighted_avg_fisik - $total_weighted_avg_sistem, 2, ',', '.') ?> </p>
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
    var table;
    var is_verified = "'.$data["is_verifikasi"].'";

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#exampleDetail").docoTabel({
            filter: true,
            searching: false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            lengthMenu: [
                [10, 25, 50, 100, 150, 200],
                [10, 25, 50, 100, 150, 200],
            ],
            pageLength: 200,
            ajax: baseUrl+"apotek/informasi-stok-opname/get-data-detail?id='.$parent_id.'",
            columnDefs: [ {
                visible: false,
                targets: 1
            }],
            order: [[1, "asc"],[2,"asc"],[3,"asc"]],
            rowGroup: {
                dataSrc: "rakobat_nama"
            },
            createdRow: function(row, data, dataIndex){
                var is_outofstock = (data.stok_sistem + data.stok_selisih) < 0 ? true : false;
                if(!is_verified && is_outofstock){
                    $(row).children().addClass("redClass");
                }
            },
            columns: [
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Rak Obat")).'", data: "rakobat_nama", searchable: false, visible: false},
                {title: "'.(\Yii::t("fe", "Laci Obat")).'", data: "laci", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Nama obat alkes")).'", data: "obatalkes_nama"},
                {title: "'.(\Yii::t("fe", "Stok Saat Stok Opname")).'", data: "volume_sistem", searchable: false, orderable: false, class: "text-right"},
                {title: "'.(\Yii::t("fe", "Stok Fisik")).'", data: "volume_fisik", searchable: false, orderable: false, class: "text-right"},
                {title: "'.(\Yii::t("fe", "Selisih Stok Opname")).'", data: "selisih", searchable: false, orderable: false, class: "text-right"},
                {title: "'.(\Yii::t("fe", @$data['is_verifikasi'] == TRUE ? "Stok Akhir" : "Stok Saat Ini")).'", data: "stok_sistem", searchable: false, orderable: false, class: "text-right"},
                {title: "'.(\Yii::t("fe", @$data['is_verifikasi'] == TRUE ? "Selisih Akhir" : "Selisih Saat Ini")).'", data: "stok_selisih", searchable: false, orderable: false, class: "text-right"},
                {title: "'.(\Yii::t("fe", @$configBasePriceVal == 0 ? "Weighted Avg (Rp)" : "HNA (RP)")).'", data: "weighted_avg", searchable: false, orderable: false, class: "text-right"},
                {title: "'.(\Yii::t("fe", "Total Selisih (Rp)")).'", data: "selisih_weighted_avg", searchable: false, orderable: false, class: "text-right"}
            ]
        });
        $("#verifikasi").on("click",function(){
            var id = "'.$parent_id.'";
            var target = "/apotek/informasi-stok-opname/verifikasi?id="+id;
            var messageText = "Apakah Anda yakin ingin melakukan verifikasi stok opname ini?";

            $(this).attr("action", target);
            $(this).docoForm("delete", {
                confirmMessage: messageText,
                success: function (data) {
                    setTimeout(() => {
                        window.location.href = "/apotek/informasi-stok-opname/inf-stok-formulir-opname";
                    }, 150);
                }
            });
        });
    });
', View::POS_END, 'b-index');
?>
