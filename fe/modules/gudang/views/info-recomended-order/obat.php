<?php
// Author : Ardi Pratama

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
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=> [
                        'attributes'=>[
                            'data-parent'=>'.filter-form'
                        ]
                    ],
                    'edit' => [
                        'title' => 'Detail',
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'data-target' => $module.'detail-obat?id='
                        ]
                    ],
                    'pdf' => [
                        'attributes' => [
                            'data-target' => $module.'export-pdf?'
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => $module.'export-excel?'
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Tanggal Rekomendasi");?></th>
                            <th><?=\Yii::t("fe", "Nomor Rekomendasi");?></th>
                            <th><?=\Yii::t("fe", "Status PO");?></th>
                            <th><?=\Yii::t("fe", "Catatan");?></th>
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
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"gudang/info-recomended-order/get-data",
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
                    title: "'.(\Yii::t("fe", "Tanggal Rekomendasi")).'", 
                    data: "tgl_rekomendasiobat"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Rekomendasi")).'", 
                    data: "no_rekomendasiobat"
                },
                {
                    title: "'.(\Yii::t("fe", "Status PO")).'", 
                    data: "status",
                    name : "status_po"
                },
                {
                    title: "'.(\Yii::t("fe", "Catatan")).'", 
                    data: "catatan",
                    searchable: false,
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                4, 
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('nama_pegawai', '', 
                        ArrayHelper::map($result['data_status'],'lookup_id','lookup_name'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '-- Pilih Semua --')
                        ]
                    )
                )).'</div>\'
            ],
        ]);
        dateRangeHelper(".startDate",".endDate",".targetDate");
        $("#filter_pegawai").select2({
            minimumInputLength: 3, 
            ajax : {
                url: "/pengadaan/info-recomended-order/get-pegawai",
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
        });
    });

    
', View::POS_END, 'b-index');
?>
