<?php
use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use kartik\widgets\DatePicker;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"),
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

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

    .datepicker>div{
        display:block;
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
                    'terima' => [
                        'title' => Yii::t('fe', "Penerimaan"),
                        'icon' => "fa fa-check",
                        'attributes' => [
                            "data-target" => '/bankdarah/inf-pemesanan-darah/penerimaan?id=',
                            "class" => "penerimaan",
                        ]
                    ],
                    'cetak-penerimaan' => [
                        'title' => \Yii::t('fe', 'Cetak PDF'),
                        'icon' => 'fa fa-print',
                        'method' => '',
                        'attributes' => [
                            'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/export-pdf-transaksi?id=',
                        ] 
                    ],
                    'excel',
                    'reset'
                ]);?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class='my-legend'>
                    <div class='legend-title'>Keterangan</div>
                    <div class='legend-scale'>
                    <ul class='legend-labels'>
                        <li><span style='background:#FFFfff;'></span>New</li>
                        <li><span style='background-color: rgba(204, 255, 204);'></span>Done</li>
                        <li><span style='background-color: rgba(255, 255, 204);'></span>Half</li>
                        <li><span style='background-color: rgba(255, 188, 188, 0.58);'></span>Overdue</li>
                    </ul>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?= Yii::t("fe", "Tanggal Pemesanan") ?></th>
                            <th><?= Yii::t("fe", "Nomor Pemesanan") ?></th>
                            <th><?= Yii::t("fe", "Nama PMI") ?></th>
                            <th><?= Yii::t("fe", "Jumlah Pemesanan") ?></th>
                            <th><?= Yii::t("fe", "Jumlah Diterima") ?></th>
                            <th><?= Yii::t("fe", "Sisa") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$today = date('Y-m-d');
$this->registerJs('
    var table;
    var today = "'.$today.'";
    $(".btn-cetak-penerimaan").attr("disabled", true);
    $(document).ready(function(){
        table = $("#example").docoTabel({
            filter: true,
            columnDefs: [
                {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
                }
            ],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"bankdarah/inf-pemesanan-darah/get-data",
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
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Pemesanan")).'",
                    data: "tgl_pesandarahpmi"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Pemesanan")).'",
                    data: "no_pesandarahpmi"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama PMI")).'",
                    data: "supplier_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Jumlah Pemesanan")).'",
                    data: "qty_pesan",
                    searchable: false,
                    class: "text-right",
                },
                {
                    title: "'.(\Yii::t("fe", "Jumlah Diterima")).'",
                    data: "qty_diterima",
                    searchable: false,
                    class: "text-right",
                },
                {
                    title: "'.(\Yii::t("fe", "Sisa")).'",
                    data: "qty_sisa",
                    searchable: false,
                    class: "text-right",
                    class: "text-right",
                }
            ],
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var _qty_diterima = parseInt(aData.qty_diterima);
                var _qty_sisa = parseInt(aData.qty_sisa);
                var _tgl_penerimaan = new Date(aData.tgl_pesandarahpmi);
                var d = new Date();

                if (d >= _tgl_penerimaan && _qty_diterima == 0) {
                    $(nRow).css("background", "rgba(255, 188, 188, 0.58)");
                } 
                else if(_qty_diterima == 0) {
                    $(nRow).css("background", "rgba(255, 255, 255, 1)");
                }
                else if(_qty_diterima != 0 && _qty_sisa == 0) {
                    $(nRow).css("background", "rgba(204, 255, 204)");
                } else {
                    $(nRow).css("background", "rgba(255, 255, 204)");
                }
            }
        });
        $(".dataTables_filter").hide();
        $("tbody", "#example").on("click", function() {
            $(".btn-cetak-penerimaan").attr("disabled", false);
        });

        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
        ]);

        dateRangeHelper(".startDate",".endDate",".targetDate");
        $(".pickadate").pickadate({
            format: "dd-mm-yyyy"
        });
        $(document).on("click", "#example tbody tr", function () {
        $(".penerimaan").attr("disabled", true);
            var qty_sisa = 0;
            try {
                qty_sisa = table.row(".selected").data().qty_sisa ? table.row(".selected").data().qty_sisa : 0;
            } catch (e) {
                qty_sisa = 0;
            }
            if (qty_sisa != 0 ) {
                $(".penerimaan").attr("disabled", false);
            }
        });
    });

', View::POS_END, 'index')
?>