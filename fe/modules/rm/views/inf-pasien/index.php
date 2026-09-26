<?php
// Author : Budi
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

use yii\helpers\ArrayHelper;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// $this->registerJs($this->render('assets/js/pasien.js'));
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset',

                    // 'excel',
                    // 'add' => [
                    //     'attributes' => [
                    //         'data-toggle' => 'modal',
                    //         'data-target' => '#modal_backdrop',
                    //         'action' => '/rm/dok-rekam-medis/create',
                    //     ]
                    // ],

                    // 'edit' => [
                    //     'attributes' => [
                    //         'data-options' => 'modal',
                    //         'data-target' => '#modal_backdrop',
                    //         'data-url' => '/rm/dok-rekam-medis/update?id=',
                    //     ]
                    // ],
                    // 'delete' => [
                    //     'attributes' => [
                    //         'data-additional' => 'data-rm'
                    //     ]
                    // ]
                ]); ?>

            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                        
                            <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                            <th><?=\Yii::t("fe", "Alamat");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Lahir");?></th>
                            <th><?=\Yii::t("fe", "Umur");?></th>
                            <th><?=\Yii::t("fe", "Nama Ibu Kandung");?></th>
                            <th><?=\Yii::t("fe", "Status Dokumen Rekam Medis");?></th>
                            <th width="1">Aksi</th>
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
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<!-- Modal form pasien -->
<div id="modal_backdrop" class="modal fade"  style="z-index: 1064" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- Modal form pasien -->

<?php 
$this->registerCss('
.daterangepicker{
    // top:187px !important;
}
');
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
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rm/inf-pasien/get-data",
            columns: [
            {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Tanggal rekam medik")).'", data: "tgl_rekam_medik"},
            {title: "'.(\Yii::t("fe", "No rekam medik")).'", data: "no_rekam_medik"},
            {title: "'.(\Yii::t("fe", "Nama pasien")).'", data: "nama_pasien"},
            {title: "'.(\Yii::t("fe", "Jenis kelamin")).'", data: "jenis_kelamin", searchable: false},
            {title: "'.(\Yii::t("fe", "Alamat")).'", data: "alamat_pasien", searchable: false},
            {title: "'.(\Yii::t("fe", "Tanggal lahir")).'", data: "tanggal_lahir", searchable: false},
            {title: "'.(\Yii::t("fe", "Umur")).'", data: "umur", searchable: false},
            {title: "'.(\Yii::t("fe", "Nama ibu kandung")). '", data: "nama_ibu", searchable: false},
            {title: "' . (\Yii::t("fe", "Status Dokumen Rekam Medis")) . '", data: "status_dokumen_rekam_medis", searchable: false},

            {
                data: "aksi",
                searchable: false,
                orderable: false,
                class: "text-center"
            }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1,
                    \'<div class="input-group"><input value='.date("d-M-Y").' type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input value='.date("d-M-Y").' type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\' 
                    //\'<div class="input-group"><span class="input-group-addon"><i class="icon-calendar22"></i></span><input type="text" class="form-control daterange-basic" value="" placeholder="'.(\Yii::t('fe', 'Tanggal rekam medik')).'" col-index="1"></div>\'
                ],
                [
                    2, 
                    \'<div class="">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('no_rekam_medik', NULL, ArrayHelper::map($pasien['response']['data'], 'no_rekam_medik', 'no_rekam_medik'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'No rekam medik'), "col-index" => "2"]))).'</div>\'
                ],
                [
                    3, 
                    \'<div class="">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_pasien', NULL, ArrayHelper::map($pasien['response']['data'], 'nama_pasien', 'nama_pasien'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Nama pasien'), "col-index" => "3"]))).'</div>\'
                ],
                
            ], {
                1:0,
                2:1,
                3:2,
            }, true
        );

        $(".daterange-basic").daterangepicker({
            // autoUpdateInput: false,
            startDate: new Date("'.(date("d-M-Y")).'"),
            endDate: new Date("'.(date("d-M-Y")).'"),
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });

        dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
    });
', View::POS_END, 'b-index');
?>