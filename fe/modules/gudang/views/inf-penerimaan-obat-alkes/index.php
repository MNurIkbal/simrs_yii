<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use yii\helpers\ArrayHelper;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['/']];
$this->params['breadcrumbs'][] = $title;

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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
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
                <?= DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'lihat' => [
                            'title' => \Yii::t('fe', 'Lihat'),
                            'icon' => 'fa fa-eye',
                            'attributes' => [
                                'id'=>'btn-hasil',
                                'data-target' => '/gudang/inf-penerimaan-obat-alkes/view?id=',
                            ]
                        ],
                        'retur' => [
                            'title' => \Yii::t('fe', 'Retur'),
                            'icon' => 'fa fa-undo',
                            'attributes' => [
                                'id'=>'btn-retur',
                                'data-target' => '/gudang/inf-penerimaan-obat-alkes/retur?id=',
                            ]
                        ],
                    ],'#penerimaan-barang');
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>

                </div>
                <table
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="penerimaan-barang" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1"><?=Yii::t('fe', 'No'); ?></th>
                            <th><?=Yii::t('fe', 'Tanggal Penerimaan'); ?></th>
                            <th><?=Yii::t('fe', 'No Penerimaan'); ?></th>
                            <th><?=Yii::t('fe', 'No Faktur'); ?></th>
                            <th><?=Yii::t('fe', 'Nama Supplier'); ?></th>
                            <th></th>
                            <th><?=Yii::t('fe', 'Status Verifikasi'); ?></th>
                            <th><?=Yii::t('fe', 'Tanggal Verifikasi'); ?></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php

$this->registerJs('
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#penerimaan-barang").docoTabel({
            filter: true,
            scrollX: true,
            sorting: [[2, "desc"], [3, "desc"]],
             columnDefs: [{
                 orderable: false,
                 className: \'select-checkbox\',
                 targets: 0
             }],
             select: {
                 style: \'os\',
                 selector: \'tr\'
             },
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax:baseUrl+"gudang/inf-penerimaan-obat-alkes/get-data",
            columns: [
                {
                    data: null,
                    searchable: false,
                    sortable: false,
                    defaultContent: ""
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Penerimaan")).'",
                    data: "tgl_penerimaan"
                },
                {
                    title: "'.(\Yii::t("fe", "No Penerimaan")).'",
                    data: "no_penerimaan"
                },
                {
                    title: "'.(\Yii::t("fe", "No Faktur")).'",
                    data: "no_faktur",
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Supplier")).'",
                    data: "supplier_nama",
                    searchable: false,
                    sortable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Supplier")).'",
                    data: "supplier_id",
                    visible : false
                },
                {
                    title: "'.(\Yii::t("fe", "Status Verifikasi")).'",
                    data: "status_verifikasi"
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Verifikasi")).'",
                    data: "tgl_verifikasi"
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="' . date('d-M-Y') . '"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate" value="' . date('d-M-Y') . '"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ],
            [
                5,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('supplier', '',
                        [],
                        [
                            'id' => 'filter_supplier',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],
            [
                7,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status', '',
                        $status_verifikasi,
                        [
                            'id' => 'filter_status',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],
        ], {
            2:0, 3:1, 7:2
        }, true);
        dateRangeHelper(".startDate",".endDate",".targetDate");

        $(document).on("click", "#penerimaan-barang tr", function(){
            var tbl = table.row(".selected").data();

            if(tbl.is_verifikasi) {
                $("#btn-retur").attr("disabled", false);
            } else {
                $("#btn-retur").attr("disabled", true);
            }
        });

        $("#filter_supplier").select2({
            minimumInputLength: 3,
            ajax : {
                url: "/gudang/inf-penerimaan-obat-alkes/get-supplier",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            },
        }).on("select2:select", function(e){
            var data = e.params.data;
        });
    });
    ', View::POS_END, 'js');

?>