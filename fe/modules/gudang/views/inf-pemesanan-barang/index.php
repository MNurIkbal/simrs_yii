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
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"),
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style type="text/css">
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
                    'reset',
                    'lihat' => [
                        'title' => Yii::t('fe', "Lihat"),
                        'icon' => "fa fa-eye",
                        'attributes' => [
                            "data-target" => $module.'lihat?id=',
                        ]
                    ],
                    'mutasi' => [
                        'title' => Yii::t('fe', "Mutasi"),
                        'icon' => "fa fa-check",
                        'attributes' => [
                            "id" => "btn-mutasi",
                            "data-target" => $module.'mutasi?id=',
                        ]
                    ],
                ]);?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>

                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?= Yii::t("fe", "Tanggal Pemesanan") ?></th>
                            <th><?= Yii::t("fe", "No. Pemesanan") ?></th>
                            <th><?= Yii::t("fe", "Instalasi - Ruangan Pemesan") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Dikirim") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Terima") ?></th>
                            <th><?= Yii::t("fe", "No. Referesi") ?></th>
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
$this->registerJs('
    var table;

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
            scrollX: true,
            ajax: baseUrl+"gudang/inf-pemesanan-barang/get-data",
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
                    data: "tgl_pesanbarang"
                },
                {
                    title: "'.(\Yii::t("fe", "No. Pemesanan")).'",
                    data: "no_pemesanan"
                },
                {
                    title: "'.(\Yii::t("fe", "Instalasi - Ruangan Pemesan")).'",
                    data: "instalasi_ruangan_pemesan",
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'",
                    data: "status_pengiriman"
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Dikirim")).'",
                    data: "tgl_mutasibarang",
                    searchable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Terima")).'",
                    data: "tglterima",
                    searchable: false
                },
                {
                    title: "'.(\Yii::t("fe", "No. Referensi")).'",
                    data: "reference"
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                4,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('instalasi_ruangan', '',
                        $ruangan,
                        [
                            'id' => 'filter_instalasi',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', 'ALL')
                        ]
                    )
                )).'</div>\'
            ],
            [
                5,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status', '',
                        $status,
                        [
                            'id' => 'filter_status',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', 'ALL')
                        ]
                    )
                )).'</div>\'
            ]
        ], {
            2:0,
            3:1,
            4:2,
            5:3,
            8:4
        });

        dateRangeHelper(".startDate",".endDate",".targetDate");
        dateRangeHelper(".startDateTerima",".endDateTerima",".targetDateTerima");

        $(".pickadate").pickadate({
            format: "dd-mm-yyyy"
        });
    });

    $(document).on("click", "#example tr", function(){
        var tbl = table.row(".selected").data();
        if(tbl.status_pengiriman == "Belum Dikirim")
        {
            $("#btn-mutasi").attr("disabled", false);
        }else{
            $("#btn-mutasi").attr("disabled", true);
        }
    });

', View::POS_END, 'index')
?>
