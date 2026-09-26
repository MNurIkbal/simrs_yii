<?php

use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use app\widgets\pendaftaran\DHSelectDokterPerujuk;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medik', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    th {
        font-weight: 0px !important; 
        font-size: 11px;
    }

    .border-tab {
        border-right: 1px solid white;
    }

    .dataTables_scroll {
    max-height: 99999em !important
    }

</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'id' => 'search'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'id' => 'reset'
                        ]
                    ],
                    'export-excel-serconn' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'rm/lap-persalinan/export-excel-serconn?',
                            'data-width' => '75%'
                        ]
                    ],
                    // 'excel'
                ], '#example'); ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="example" class="table table-condensed" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center border-tab" width="1"><?=\Yii::t("fe", "No");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Nama Bayi");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Tanggal Lahir");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Berat Lahir (gram)");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "No Rekam Medik Bayi");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Nama Ibu");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "No Rekam Medik Ibu");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Dokter Penanggung Jawab");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Jenis Persalinan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                            <tr>
                                <th colspan="9"></th>
                            </tr>
                        </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs('
var table;
var data;

    $(document).ready(function() {
        table = $("#example").DataTable({
            filter: true,
            sorting: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            sorting: [[2, "desc"]],
            paging: true,
            ajax: baseUrl+"rm/lap-persalinan/get-data",
            columns: [
                {
                    title: "'.(\Yii::t("fe", "No")).'",
                    data: "rowNum", 
                    searchable: false,
                    orderable: false
                }, //0 
                {
                    title: "'.(\Yii::t("fe", "Nama Bayi")).'",
                    data: "nama_bayi", 
                    searchable: true,
                    orderable: true
                }, //1 
                {
                    title: "'.(\Yii::t("fe", "Tanggal Lahir")).'",
                    data: "tgl_lahir_bayi", 
                    searchable: true,
                    orderable: true
                }, //2 
                {
                    title: "'.(\Yii::t("fe", "Berat Lahir (gram)")).'",
                    data: "berat_badan", 
                    searchable: false,
                    orderable: true
                }, //3 
                {
                    title: "'.(\Yii::t("fe", "No Rekam Medik Bayi")).'",
                    data: "no_rekam_medik_bayi", 
                    searchable: true,
                    orderable: true
                }, //4
                {
                    title: "'.(\Yii::t("fe", "Nama Ibu")).'",
                    data: "nama_ibu", 
                    searchable: true,
                    orderable: true
                }, //5 
                {
                    title: "'.(\Yii::t("fe", "No Rekam Medik Ibu")).'",
                    data: "no_rekam_medik_ibu", 
                    searchable: true,
                    orderable: true
                }, //6 
                {
                    title: "'.(\Yii::t("fe", "Dokter Penanggung Jawab")).'",
                    data: "dokter_dpjp_nama", 
                    name: "dokter_dpjp_id", 
                    searchable: true,
                    orderable: true
                }, //7 
                {
                    title: "'.(\Yii::t("fe", "Jenis Persalinan")).'",
                    data: "jenis_persalinan_nama", 
                    name: "jenis_persalinan_id", 
                    searchable: true,
                    orderable: true
                }, //8 
            ],
            footerCallback: function(row, data, start, end, display) {
                let api = this.api();
                let res = this.api().ajax.json();
                let jumlahTotal = 0;
                let footer = "";
                if(res) {
                    jumlahTotal = res.recordsTotal
                    let jumlah = res.rowJumlah
                    if(jumlah.length) {
                        jumlah.forEach(function (item, index) {
                            footer += " Total " + item.jenis_persalinan_nama + " : " + item.jumlah
                        });
                    }
                }
                $(api.column(0).footer()).html(
                    "Total Keselurhan : " + jumlahTotal + footer 
                );
            },
        });

        $(".dataTables_filter").hide();

        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    2,
                    \'<div class="input-group"><input value='.date("d-M-Y").' type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input value='.date("d-M-Y").' type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
                ],
                [
                    7, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('dokter_dpjp_id', '',
                            [],
                            [
                                'class' => 'form-control select2',
                                'id' => 'dokter_dpjp_id',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    7,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            DHSelectDokterPerujuk::widget([
                                'id' => 'filterDokterPerujuk',
                                'prompt' => '-- Pilih --',
                                'independent' => true,
                                'dataDependPrompt' => '-- Pilih --'
                            ])
                        )
                    ).'\'
                ],
                [
                    8, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('jenisPersalinan', '',
                            $jenisPersalinan,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
            ], {
                2:0,
                1:1,
                4:2,
                5:3,
                6:4,
                7:5,
                8:6
            }, true
        );

        dateRangeHelper(".startDate", ".endDate", ".targetDate", true);

        $("#dokter_dpjp_id").select2({
            placeholder: "-- Pilih --",
            minimumInputLength: 3, 
            ajax : {
                url: "/rm/lap-persalinan/get-dokter",
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