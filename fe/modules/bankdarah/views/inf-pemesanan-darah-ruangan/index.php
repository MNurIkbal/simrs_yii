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
                    'cross' => [
                        'title' => Yii::t('fe', "Cross Match"),
                        'icon' => "fa fa-check",
                        'attributes' => [
                            "data-target" => $module.'cross-match?id=',
                        ]
                    ],
                    'detail' => [
                        'title' => Yii::t('fe', "Detail"),
                        'icon' => "fa fa-eye",
                        'attributes' => [
                            "data-target" => $module.'detail?id=',
                        ]
                    ],
                    'pdf',
                    'excel',
                    'reset'
                ]);?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>

                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?= Yii::t("fe", "Tanggal Pemesanan") ?></th>
                            <th><?= Yii::t("fe", "Nomor Pemesanan") ?></th>
                            <th><?= Yii::t("fe", "Nama Pasien/No Rekam Medik") ?></th>
                            <th><?= Yii::t("fe", "Ruangan Pemesan") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Permintaan Dikirim") ?></th>
                            <th><?= Yii::t("fe", "Golongan Darah") ?></th>
                            <th><?= Yii::t("fe", "Jenis Darah") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
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
            ajax: baseUrl+"bankdarah/inf-pemesanan-darah-ruangan/get-data",
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
                    data: "tgl_pesandarah"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Pemesanan")).'",
                    data: "no_pesandarah"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Pasien/No Rekam Medik")).'",
                    data: "nama_pasien"
                },
                {
                    title: "'.(\Yii::t("fe", "Ruangan Pemesan")).'",
                    data: "ruangan_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Permintaan Dikirim")).'",
                    data: "tgl_mintakirim",
                },
                {
                    title: "'.(\Yii::t("fe", "Golongan Darah")).'",
                    data: "golongandarah_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Darah")).'",
                    data: "jenisdarah_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'",
                    data: "status_pesan",
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                1,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=1 readonly="true"></div>\'
            ],
            [
                5,
                \'<div class="input-group"><input type="text" id="rangeDemoStart2" value="'.date('d-M-Y').'" class="form-control startDate2" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish2" value="'.date('d-M-Y').'" class="form-control endDate2" /><input type="text" style="display:none" class="targetDate2" col-index=5 readonly="true"></div>\'
            ],
            [
                4, 
                \''.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('ruangan_nama', '', 
                        ArrayHelper::map($dataRequest['ruangan'], 'ruangan_id', 'ruangan_nama'), 
                        [
                            'id' => 'filter_ruangan', 
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', 'Ruangan Pemesan')
                        ]
                    )
                )).'\'
            ],
            [
                6, 
                \''.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('golongandarah_nama', '', 
                        ArrayHelper::map($dataRequest['golongan_darah'], 'lookup_id', 'lookup_name'), 
                        [
                            'id' => 'filter_goldar', 
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', 'Golongan Darah')
                        ]
                    )
                )).'\'
            ],
            [
                7, 
                \''.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('jenisdarah_nama', '', 
                        ArrayHelper::map($dataRequest['jenis_darah'], 'jenisdarah_id', 'jenisdarah_nama'), 
                        [
                            'id' => 'filter_jenis', 
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', 'Jenis Darah')
                        ]
                    )
                )).'\'
            ],
            [
                8, 
                \''.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('status_pesan', '', 
                        ArrayHelper::map($dataRequest['status_pesan'], 'lookup_id', 'lookup_name'), 
                        [
                            'id' => 'filter_status', 
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', 'Status')
                        ]
                    )
                )).'\'
            ],
        ], {
            1:0,
            5:1,
            2:2,
            3:3,
            4:4,
            6:5,
            7:6,
            8:7
        });

        dateRangeHelper(".startDate",".endDate",".targetDate");
        dateRangeHelper(".startDate2",".endDate2",".targetDate2");
        $(".pickadate").pickadate({
            format: "dd-mm-yyyy"
        });
    });

', View::POS_END, 'index')
?>