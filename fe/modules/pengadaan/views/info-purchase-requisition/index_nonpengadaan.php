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
                        'reset'=> [
                            'attributes'=> [
                                'data-parent' => '.filter-form'
                            ]
                        ],
                        'detail' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Lihat'),
                            'icon' => 'fa fa-eye',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'btn-detail',
                                'data-options'=>'click'
                            ]
                        ],
                        'edit' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-edit',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'btn-edit',
                                'disabled' => true,
                                'data-target' => '/pengadaan/purchase-requisition/edit?id='
                            ]
                        ],
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#info-pr-nonpengadaan');?>
            </div>
            <div class="panel-body" style="overflow-x: scroll;">
            <div class="filter-form"></div>
            <div class="row">
                <?php echo $this->render('_filter_selected', [
                    'tableName' => 'info-pr-nonpengadaan',
                    'url' => '/pengadaan/info-purchase-requisition/get-list-data-obat?',
                    'type' => 'obat'
                ]); ?>
            </div>
                <table id="info-pr-nonpengadaan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "Tanggal PR");?></th>
                            <th><?=\Yii::t("fe", "Nomor PR");?></th>
                            <th><?=\Yii::t("fe", "Status PR");?></th>
                            <th><?=\Yii::t("fe", "Jenis PR");?></th>
                            <th><?=\Yii::t("fe", "Admin");?></th>
                            <th><?=\Yii::t("fe", "Consignment");?></th>
                            <th><?=\Yii::t("fe", "Approve");?></th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
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
    var belum_approved = '.DocoConstants::VAR_BELUM_APPROVED.';

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).on("click", "#btn-detail", function() {
        var selectedData = table.rows(".selected").data()[0];
        
        window.location = "/pengadaan/info-purchase-requisition/detail?type=OBAT&id=" + selectedData.primary;
    });

    $(document).ready(function() {
        $("#btn-edit").attr("disabled", true);

        // Generate Table
        table = $("#info-pr-nonpengadaan").docoTabel({
            filter: true,
            createdRow: function( row, data, dataIndex){
                if(data.is_prcyto){
                    $(row).children().addClass("redClass");
                }
            },
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[5, "asc"], [2, "desc"], [3, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"pengadaan/info-purchase-requisition/get-list-data-obat",
            columns: [
                {
                    title: "",
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    orderable: false,
                    width: "10%"
                },
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal PR")).'",
                    data: "tgl_pr"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor PR")).'",
                    data: "no_pr"
                },
                {
                    title: "'.(\Yii::t("fe", "Status PR")).'",
                    data: "status_pr"
                },
                {
                    title: "Cito",
                    data: "pr_cyto",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Admin")).'",
                    data: "pr_admin",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Consignment")).'",
                    data: "pr_consignment",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Approve")).'",
                    data: "pegawai_approve",
                },
                {
                    title: "'.(\Yii::t("fe", "Detail")).'",
                    data: "detail",
                    searchable: false,
                    orderable: false,
                    width:"1%"
                },
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                4,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status_pr', '',
                    [
                            "Belum Approved	" => "Belum Approved",
                            "Belum PO" => "Belum PO",
                            "Sudah PO" => "Sudah PO",
                            "Batal PR" => "Batal PR",
                            "PO Sebagian" => "PO Sebagian"

                        ],
                        [
                            'id' => 'status_pr',
                            'class' => 'form-control select2 statusPR',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],
        ],{
            0:2,
            1:3,
            2:4, 
            3:8,
        },true);

        dateRangeHelper(".startDate",".endDate",".targetDate");

        $(document).on("change", ".startDate, .endDate, .statusPR, .jenisPR", function(){
            $(".data-filter").click();
        });

        $(document).on("click", "#info-pr-nonpengadaan tr", function(){
            var _data = table.row(".selected").data();
            if(typeof _data !== "undefined" && _data.status == belum_approved) {
                $("#btn-edit").attr("disabled", false);
            } else {
                $("#btn-edit").attr("disabled", true);
            }
        });
    });

', View::POS_END, 'b-index');
?>
