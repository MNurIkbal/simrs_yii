<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use kartik\widgets\DatePicker;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;
use app\widgets\master\DHSelectObat;


$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style type="text/css" media="screen">
    .more-filter{
        display: none;
    }
</style>
<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
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
                <?php
                    $btn_toolbar = [
                        'search',
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Export Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-table-id' => 'riwayat-obat-pasien',
                                'data-url' => Url::home() . 'apotek/riwayat-obat-pasien/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ],
                    ];
                ?>
                <?= DocoHelpers::generateToolbar($btn_toolbar, '#example'); ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <div class="advanced-filter-new"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Transaksi");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "No. Resep");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                            <th><?=\Yii::t("fe", "Signa");?></th>
                            <th><?=\Yii::t("fe", "Qty");?></th>
                            <th><?=\Yii::t("fe", "Satuan");?></th>
                            <th><?=\Yii::t("fe", "Instalasi - Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar - Penjamin");?></th>
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
    let list_tabel = [
        { title: "'. (\Yii::t('fe', 'No.')) .'", data: "rowNum", searchable: false, orderable: false, width: "50px"},
        { title: "'. (\Yii::t('fe', 'Tanggal Transaksi')) .'", data: "tgl_transaksi" },
        { title: "'. (\Yii::t('fe', 'Nama Pasien')) .'", data: "pasien_id", visible: false },
        { title: "'. (\Yii::t('fe', 'No. Resep')) .'", data: "no_resep", searchable: false },
        { title: "'. (\Yii::t('fe', 'Nama Obat Alkes')) .'", data: "obatalkes_nama", name: "obatalkes_id" },
        { title: "'. (\Yii::t('fe', 'Signa')) .'", data: "signa_nama", searchable: false },
        { title: "'. (\Yii::t('fe', 'Qty')) .'", data: "qty", searchable: false },
        { title: "'. (\Yii::t('fe', 'Satuan')) .'", data: "satuanunit_nama", searchable: false },
        { title: "'. (\Yii::t('fe', 'Instalasi - Ruangan')) .'", data: "instalasi_ruangan", searchable: false },
        { title: "'. (\Yii::t('fe', 'Cara Bayar - Penjamin')) .'", data: "carabayar_penjamin", searchable: false },
    ];

    $(document).on("keydown", null, "enter", function (event) {
        $("#find-data").click();
    });

    $(document).on("keydown", null, function (e) {
        if (e.key == "Enter") {
            $("#find-data").click();
        }

        if (e.key == "F7") {
            $("#reset-data").click();
        }
    });

    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            columnDefs: [ {
                sortable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            filter: true,
            sorting: [[2, "asc"], [5, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: "/apotek/riwayat-obat-pasien/get-data",
            columns: list_tabel
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    1,
                        \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
                ],
                [
                    2,
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                            Html::dropDownList('pasien_id', '',
                                [],
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'select2_pasien',
                                    'prompt' => \Yii::t('fe', 'Nama Pasien'),
                                ]
                            )
                        )).'</div>\'
                ],
                [
                    4,
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                            DHSelectObat::widget([
                                'id' => 'obatalkes_id',
                                'className' => 'select2',
                            ])
                        )).'</div>\'
                ],
            ],{
                1:0,
                2:1,
                4:2,
            });

            $("#select2_pasien").select2({
                ajax: {
                    url: "/apotek/riwayat-obat-pasien/search-pasien",
                    delay: 300,
                    processResults: function(result) {
                        return {
                            results: result.list
                        }
                    },
                },
                placeholder: "Nama Pasien",
                minimumInputLength: 2
            });

            $("#select2_obat").select2({
                ajax: {
                    url: "/apotek/riwayat-obat-pasien/search-obat",
                    delay: 300,
                    processResults: function(result) {
                        return {
                            results: result.list
                        }
                    },
                },
                placeholder: "Nama Obat",
                minimumInputLength: 2
            });
           dateRangeHelper(".startDate",".endDate",".targetDate");

        $(".pickadate").pickadate({
            format: "dd-mm-yyyy"
        });
    });
', View::POS_END, 'b-index');
?>
