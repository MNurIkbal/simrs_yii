<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>
<style type="text/css">
.row-nosep {
    background-color: #ff7f00 !important;
    color: #FFFFFF;
    font-weight: bold;
}
</style>
<style>

    .my-legend .legend-title {
    text-align: left;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 90%;
    }
  .my-legend .legend-scale ul {
    margin: 0;
    padding: 0;
    float: left;
    list-style: none;
    }
  .my-legend .legend-scale ul li {
    display: block;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 80%;
    list-style: none;
    }
  .my-legend ul.legend-labels li span {
    display: block;
    float: left;
    height: 15px;
    width: 50px;
    border: solid 0.2px;
    }
  .my-legend .legend-source {
    font-size: 70%;
    color: #999;
    clear: both;
    }
  .my-legend a {
    color: #777;
    }

    .square-sukses {
    height: 30px;
    width: 120px;
    background-color: #26A65B;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
    .square-batal {
    height: 30px;
    width: 70px;
    background-color: #D24D57;
    color:#ffffff;
    padding: 5px 0 5px 10px;
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
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->

            </div>

            <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'setujui' => [
                            'title' => \Yii::t('fe', 'Setujui'),
                            'icon' => 'fa fa-check',
                            'attributes' => [
                                'action' => '/fisioterapi/informasi-perubahan-program-terapi/disetujui?id=',
                                'id' => 'btn-setujui',
                                'data-type' => 'setujui',
                                'data-options' => 'click',
                                'disabled' => true
                            ]
                        ],
                        'tolak' => [
                            'title' => \Yii::t('fe', 'Tolak'),
                            'icon' => 'fa fa-close',
                            'attributes' => [
                                'action' => '/fisioterapi/informasi-perubahan-program-terapi/ditolak?id=',
                                'class' => 'btn-aksi',
                                'data-options' => 'click',
                                'data-type' => 'tolak',
                                'id' => 'btn-tolak',
                                'disabled' => true
                            ]
                        ],
                    ], '#inf-perubahan-program-terapi');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter"></div>
                <div class="table-wrapper table-scroll-x">
                    <div class="col-md-6">
                        <div class='legend-index'>
                            <div class='legend-header'>Keterangan</div>
                            <div class="legend-wrapper">
                                <div class="legend-information btn-pengajuan" data-group="keterangan" data-type="status_approval" data-val="pengajuan">
                                    <div class="legend-information__color" style="background-color: #FFFFFF"></div>
                                    <div class="legend-information__text">Pengajuan</div>
                                    <!-- <input type="hidden" class="filter-is_stopakomodasi" id="is_stopakomodasi" value="0"> -->
                                </div>
                                <div class="legend-information btn-disetujui" data-group="keterangan" data-type="status_approval" data-val="disetujui">
                                    <div class="legend-information__color" style="background-color: #7efff5"></div>
                                    <div class="legend-information__text">Disetujui</div>
                                </div>
                                <div class="legend-information btn-ditolak" data-group="keterangan" data-type="status_approval" data-val="ditolak">
                                    <div class="legend-information__color" style="background-color: #FF0000"></div>
                                    <div class="legend-information__text">Ditolak</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="inf-perubahan-program-terapi" class="table table-striped table-condensed table-hover" style="width:100%;">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"></th>
                                <th width="1">No</th>
                                <th><?=\Yii::t("fe", "Tanggal Perubahan");?></th>
                                <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                                <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                                <th><?=\Yii::t("fe", "Terapi Sebelumnya");?></th>
                                <th><?=\Yii::t("fe", "Terapi Setelah Perubahan");?></th>
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
        </div>
    </div>
</div>
<?php
$this->registerJs('
// Global Var
var table;
var _base_table_url = "/fisioterapi/informasi-perubahan-program-terapi/get-data";

// Event Ready
$(document).ready(function() {

    // Initialize DataTable
    table = $("#inf-perubahan-program-terapi").docoTabel({
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true, // Enable DataTables built-in search filter
        sorting: [[2, "desc"]], // Default sorting by column index 2 (Tanggal Perubahan)
        displayLength: 10, // Number of rows per page
        processing: true, // Show processing indicator
        serverSide: true, // Enable server-side processing
        scrollX: true, // Enable horizontal scrolling
        ajax: baseUrl + "fisioterapi/informasi-perubahan-program-terapi/get-data", // AJAX data source
        columnDefs: [
            {
                orderable: false, // Disable ordering for checkbox column
                className: "select-checkbox",
                targets: 0 // Apply to the first column
            }
        ],
        columns: [
            {
                data: null, // Data not directly bound
                searchable: false, // Column is not searchable
                orderable: false, // Column is not orderable
                defaultContent: "", // Default empty content
            }, // 0
            {
                title: "No", // Column title
                data: "rowNum", // Data key for this column
                searchable: false, // Column is not searchable
                orderable: false // Column is not orderable
            }, // 1
            { title: "Tanggal Perubahan", data: "tanggal_perubahan" }, // 2
            { title: "No Rekam Medik", data: "no_rekam_medik"}, // 3
            { title: "Nama Pasien", data: "nama_pasien"}, // 4
            { title: "Terapi Sebelumnya", data: "tindakan_lama", searchable: false }, // 5
            { title: "Terapi Terbaru", data: "tindakan_baru", searchable: false }, // 6
        ],
        drawCallback: function(settings) {
            // Custom logic after table draw, if needed
        },
        scrollCollapse: true, // Allow table to collapse when scrolled
        rowCallback: function(rowElement, data) {
            // Define color based on status_approval
            var backgroundColor = "";
            switch (data.status_approval) {
                case "pengajuan":
                    backgroundColor = "#FFFFFF"; // White
                    break;
                case "disetujui":
                    backgroundColor = "#7efff5"; // Dodger Blue
                    break;
                case "ditolak":
                    backgroundColor = "#FF0000"; // Red
                    break;
                default:
                    backgroundColor = ""; // Default background color
            }
            // Apply the background color to the appropriate cell
            $(rowElement).css("background-color", backgroundColor);
        }
    });
    
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [2, \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate dateStart1" value="' . date('d-M-Y') . '" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate dateEnd1" value="' . date('d-M-Y') . '" /><input type="text" style="display:none" class="targetDate dateTarget1" col-index=2></div>\']
    ], {
        2:0,
        3:1,
        4:2
    });
    
    dateRangeHelper(".dateStart1",".dateEnd1",".dateTarget1");

    // Handle filter legend clicks
    var _legend_info = ".legend-information";

    $(_legend_info).css("cursor", "pointer");
    $(_legend_info).removeClass("active");

    $(_legend_info).on("click", function() {
        showLoader(); // Show a loading indicator

        var $this = $(this);
        var _dt_group = $this.data("group");
        var _dt_type = $this.data("type");
        var _adv_filter = [];

        // Filter keterangan
        if (_dt_group === "keterangan") {
            var isActive = $this.hasClass("active");
            
            // Toggle active state
            if (isActive) {
                // Remove active state and border
                $this.removeClass("active").css("border", "1px solid #dddddd");
            } else {
                // Remove active state from other elements
                $(`${_legend_info}[data-group="keterangan"].active`).removeClass("active").css("border", "1px solid #dddddd");

                // Add active state and border to clicked element
                $this.addClass("active").css("border", "2px solid #2ca38b");

                // Prepare filter query based on status
                var _dt_val = $this.data("val");
                var _filter_keterangan = `advancedFilter[${_dt_type}]=${_dt_val}`;
                _adv_filter.push(_filter_keterangan);
            }
        }

        // Reload DataTable with the new filter applied
        if (typeof table !== "undefined" && _base_table_url) {
            var _table_ajax_url = `${_base_table_url}?${_adv_filter.join("&")}`;
            table.ajax.url(_table_ajax_url).load();
        }
    });
});

$(document).on("click", "#inf-perubahan-program-terapi tbody tr", function () {
    var data = table.row(".selected").data();

    if(typeof data !== \'undefined\'){
        if(data.status_approval == "pengajuan"){
            $("#btn-tolak").prop("disabled", false);
            $("#btn-setujui").prop("disabled", false);
        }else{
            $("#btn-tolak").prop("disabled", true);
            $("#btn-setujui").prop("disabled", true);
        }
    }else{
        $("#btn-tolak").prop("disabled", true);
        $("#btn-setujui").prop("disabled", true);
    }
});

$(document).on("click", "#btn-tolak", function(){
    var data = table.row(".selected").data();
    if(typeof data !== \'undefined\'){
        var primary = data.primary;
            target = $(this).attr("action");
            type = $(this).attr("data-type");
        $(this).docoForm("click", {
            url: target+primary,
            confirmMessage: (type == "setujui") ? "Apakah anda yakin untuk Approve Perubahan Program Terapi ?" : "Apakah anda yakin untuk menolak perubahan program terapi ?",
            success: function(res){
                table.draw();
                $("#btn-tolak").prop("disabled", true);
                $("#btn-setujui").prop("disabled", true);
            }
        })
    }
});

$("#btn-setujui").click(function() {
    var data = table.row(".selected").data();
    if(typeof data !== \'undefined\'){
        var primary = data.primary;
            target = $(this).attr("action");
            type = $(this).attr("data-type");
        $(this).docoForm("click", {
            url: target+primary,
            confirmMessage: (type == "setujui") ? "Apakah anda yakin untuk Approve Perubahan Program Terapi ?" : "Apakah anda yakin untuk menolak perubahan program terapi ?",
            success: function(res){
                table.draw();
                $("#btn-tolak").prop("disabled", true);
                $("#btn-setujui").prop("disabled", true);
            }
        })
    }
});
', View::POS_END, 'b-index');
?>

