<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
.redClass {
    background-color: #ffd2d2!important;
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
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?php
                    $btn_toolbar = [
                        'search',
                        'reset' => [
                            'attributes'=> [
                                'data-parent' => '.filter-form'
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => $module.'export-excel?'
                            ]
                        ]
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#laporan-pr');?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="laporan-pr" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "Nomor PO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Verifikasi PO");?></th>
                            <th><?=\Yii::t("fe", "Vendor");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Qty PO");?></th>
                            <th><?=\Yii::t("fe", "Qty Terima");?></th>
                            <th><?=\Yii::t("fe", "Nilai Konversi");?></th>
                            <th><?=\Yii::t("fe", "Harga (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "Diskon (%)");?></th>
                            <th><?=\Yii::t("fe", "PPn (%)");?></th>
                            <th><?=\Yii::t("fe", "Harga Akhir (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "Status PO");?></th>
                            <th><?=\Yii::t("fe", "Catatan 1");?></th>
                            <th><?=\Yii::t("fe", "Catatan 2");?></th>
                            <th><?=\Yii::t("fe", "Alasan Batal");?></th>
                            <th><?=\Yii::t("fe", "Nomor Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Nomor PR");?></th>
                            <th><?=\Yii::t("fe", "Tanggal PR");?></th>
                            <th><?=\Yii::t("fe", "Jenis PR");?></th>
                            <th><?=\Yii::t("fe", "Status PR");?></th>
                            <th><?=\Yii::t("fe", "Alasan Batal PR");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // Global Var
    var table;
    var belum_po = '.DocoConstants::VAR_BELUM_PO.';

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function(){
        $(".DTFC_Cloned").remove();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#laporan-pr").docoTabel({
            filter: true,
            sorting: [[1, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"pengadaan/laporan-purchase-requisition/get-data",
            columns: [
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                { title: "'.(\Yii::t("fe", "Nomor PR")).'", data: "no_pr" },
                { title: "'.(\Yii::t("fe", "Tanggal PR")).'", data: "tanggal_pr" },
                { title: "'.(\Yii::t("fe", "Jenis PR")).'", data: "jenis_pr", searchable: false },
                { title: "'.(\Yii::t("fe", "Status PR")).'", data: "status_pr", searchable: false },
                { title: "'.(\Yii::t("fe", "Alasan Batal PR")).'", data: "alasan_batal_pr",  searchable: false },
                { title: "'.(\Yii::t("fe", "Nomor PO")).'", data: "no_po" },
                { title: "'.(\Yii::t("fe", "Tanggal Verifikasi PO")).'", data: "tgl_verifikasi" },
                { title: "'.(\Yii::t("fe", "Vendor")).'", data: "vendor_obat" },
                { title: "'.(\Yii::t("fe", "Nama Obat")).'", data: "nama_obat" },
                { title: "'.(\Yii::t("fe", "Qty PO")).'", data: "qty_po", searchable: false },
                { title: "'.(\Yii::t("fe", "Qty Terima")).'", data: "qty_terima", searchable: false },
                { title: "'.(\Yii::t("fe", "Nilai Konversi")).'", data: "nilai_konversi", searchable: false },
                { title: "'.(\Yii::t("fe", "Harga (Rp.)")).'", data: "hna", searchable: false },
                { title: "'.(\Yii::t("fe", "Diskon (%)")).'", data: "disc", searchable: false },
                { title: "'.(\Yii::t("fe", "PPn (%)")).'", data: "ppn", searchable: false },
                { title: "'.(\Yii::t("fe", "Harga Akhir (Rp.)")).'", data: "harga_akhir", searchable: false },
                { title: "'.(\Yii::t("fe", "Status PO")).'", data: "status_po" },
                { title: "'.(\Yii::t("fe", "Catatan 1")).'", data: "catatan_1", searchable: false },
                { title: "'.(\Yii::t("fe", "Catatan 2")).'", data: "catatan_2", searchable: false },
                { title: "'.(\Yii::t("fe", "Alasan Batal PO")).'", data: "alasan_batal_po", searchable: false },
                { title: "'.(\Yii::t("fe", "Nomor Penerimaan")).'", data: "no_penerimaan" },
                { title: "'.(\Yii::t("fe", "Tanggal Penerimaan")).'", data: "tanggal_penerimaan", searchable: false }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                7,
                \'<div class="input-group"><input type="text" id="rangeDemoStart3" value="'.date('d-M-Y').'" class="form-control startDateVerif" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish3" value="'.date('d-M-Y').'" class="form-control endDateVerif" readonly /><input type="text" style="display:none" class="targetDate3" col-index=2 readonly="true"></div>\'
            ],
            [
                17,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status_po', '', $status_po,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            "col-index" => "2"
                        ]
                    )
                )).'</div>\'
            ],
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart2" class="form-control startDatePR" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish2" class="form-control endDatePR" /><input type="text" style="display:none" class="targetDate2" col-index=2 readonly="true"></div>\'
            ]
        ], {0:2, 1:7, 2:17}, true);

        dateRangeHelper(".startDatePR",".endDatePR",".targetDate2");
        dateRangeHelper(".startDateVerif",".endDateVerif",".targetDate3");

        $(document).on("change", "select[name=\'status_po\']", function(){
            $(".data-filter").click();
        });
    });

', View::POS_END, 'b-index');
?>
