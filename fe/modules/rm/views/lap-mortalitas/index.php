<?php

use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;

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
                    'excel'
                ], '#example'); ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="example" class="table table-condensed" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th rowspan="2" class="text-center border-tab" width="1"><?=\Yii::t("fe", "No");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Nama Lengkap");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Nomor Induk Kependudukan");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Tempat Lahir");?></th>
                            <th colspan="3" class="text-center border-tab"><?=\Yii::t("fe", "Tanggal Lahir");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Pendidikan");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Pekerjaan");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Status Kependudukan");?></th>
                            <th colspan="6" class="text-center border-tab"><?=\Yii::t("fe", "Alamat Sesuai KTP");?></th>
                            <th colspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Jam Meninggal");?></th>
                            <th colspan="3" class="text-center border-tab"><?=\Yii::t("fe", "Umur Saat Meninggal");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Apakah Lahir Mati");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Khusus Perempuan 10-54 tahun, almarhum dlm keadaan");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Tempat Meninggal");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Diagnosa");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Jika Meninggal Di Rs");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Rencana Pemulasaran");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Tempat Pemulasaran");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "Dokter Menerangkan");?></th>
                        </tr>
                        <tr class="bg-inverse">
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Tanggal");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Bulan");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Tahun");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Jalan/Gang");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "RT/RW");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Kel/Desa");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Kecamatan");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Kota/Kabupaten");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Telp Keluarga");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Tgl/Bulan/Tahun");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Jam");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Bulan");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Hari");?></th>
                            <th class="text-center border-tab"><?=\Yii::t("fe", "Tahun");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="20"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
var data;

    $(document).ready(function() {
        table = $("#example").DataTable({
            bPaginate: true,
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            sorting: [[30, "desc"]],
            ajax: baseUrl+"rm/lap-mortalitas/get-data",
            columns: [
                {
                    title: "'.(\Yii::t("fe", "No")).'",
                    data: "rowNum", 
                    searchable: false,
                    orderable: false
                }, //0 
                {
                    title: "'.(\Yii::t("fe", "Nama Lengkap")).'",
                    data: "nama_pasien", 
                    searchable: false,
                    orderable: true
                }, //1 
                {
                    title: "'.(\Yii::t("fe", "No Rekam Medik")).'",
                    data: "no_rekam_medik",
                    searchable: false,
                    orderable: true
                }, //2
                {
                    title: "'.(\Yii::t("fe", "Nomor Induk Kependudukan")).'",
                    data: "no_identitas_pasien", 
                    searchable: false,
                    orderable: false
                }, //3
                {
                    title: "'.(\Yii::t("fe", "Jenis Kelamin")).'",
                    data: "jeniskelamin", 
                    searchable: false,
                    orderable: true
                }, //4
                {
                    title: "'.(\Yii::t("fe", "Tempat Lahir")).'",
                    data: "tempat_lahir", 
                    searchable: false,
                    orderable: true
                }, //5
                {
                    title: "'.(\Yii::t("fe", "Tanggal")).'",
                    data: "tgllhr_tgl", 
                    searchable: false,
                    orderable: false
                }, //6
                {
                    title: "'.(\Yii::t("fe", "Bulan")).'",
                    data: "tgllhr_bln", 
                    searchable: false,
                    orderable: false
                }, //7
                {
                    title: "'.(\Yii::t("fe", "Tahun")).'",
                    data: "tgllhr_thn", 
                    searchable: false,
                    orderable: false
                }, //8
                {
                    title: "'.(\Yii::t("fe", "Pendidikan")).'",
                    data: "pendidikan_nama", 
                    searchable: false,
                    orderable: true
                }, //9
                {
                    title: "'.(\Yii::t("fe", "Pekerjaan")).'",
                    data: "pekerjaan_nama", 
                    searchable: false,
                    orderable: false
                }, //10
                {
                    title: "'.(\Yii::t("fe", "Status Kependudukan")).'",
                    data: "status_kependudukan", 
                    searchable: false,
                    orderable: false
                }, //11
                {
                    title: "'.(\Yii::t("fe", "Jalan/Gang")).'",
                    data: "alamat_pasien", 
                    searchable: false,
                    orderable: false
                }, //12
                {
                    title: "'.(\Yii::t("fe", "RT/RW")).'",
                    data: "rt_rw", 
                    searchable: false,
                    orderable: false
                }, //13
                {
                    title: "'.(\Yii::t("fe", "Kel/Desa")).'",
                    data: "kelurahan_nama", 
                    searchable: false,
                    orderable: true
                }, //14
                {
                    title: "'.(\Yii::t("fe", "Kecamatan")).'",
                    data: "kecamatan_nama", 
                    searchable: false,
                    orderable: true
                }, //15
                {
                    title: "'.(\Yii::t("fe", "Kota/Kabupaten")).'",
                    data: "kabupaten_nama", 
                    searchable: false,
                    orderable: true
                }, //16
                {
                    title: "'.(\Yii::t("fe", "Telp Keluarga")).'",
                    data: "penanggungjawab_notelp", 
                    searchable: false,
                    orderable: false
                }, //17
                {
                    title: "'.(\Yii::t("fe", "Tgl/Bulan/Tahun")).'",
                    data: "tgl_meninggal", 
                    searchable: false,
                    orderable: true
                }, //18
                {
                    title: "'.(\Yii::t("fe", "Jam")).'",
                    data: "jam_meninggal", 
                    searchable: false,
                    orderable: false
                }, //19
                {
                    title: "'.(\Yii::t("fe", "Tahun")).'",
                    data: "umur_tahun", 
                    searchable: false,
                    orderable: false
                }, //20
                {
                    title: "'.(\Yii::t("fe", "Bulan")).'",
                    data: "umur_bulan", 
                    searchable: false,
                    orderable: false
                }, //21
                {
                    title: "'.(\Yii::t("fe", "Hari")).'",
                    data: "umur_hari", 
                    searchable: false,
                    orderable: false
                }, //22
                {
                    title: "'.(\Yii::t("fe", "Apakah Lahir Mati")).'",
                    data: "lahir_mati", 
                    searchable: false,
                    orderable: false
                }, //23
                {
                    title: "'.(\Yii::t("fe", "Khusus Perempuan 10-54 tahun, almarhum dlm keadaan")).'",
                    data: "khusus_perempuan_10_sampai_54", 
                    searchable: false,
                    orderable: false
                }, //24
                {
                    title: "'.(\Yii::t("fe", "Tempat Meninggal")).'",
                    data: "tempat_meninggal", 
                    searchable: false,
                    orderable: false
                }, //25
                {
                    title: "'.(\Yii::t("fe", "Diagnosa")).'",
                    data: "diagnosa",
                    searchable: false,
                    orderable: false
                }, //26
                {
                    title: "'.(\Yii::t("fe", "Jika Meninggal Di Rs")).'",
                    data: "kondisikeluar_nama", 
                    searchable: false,
                    orderable: false
                }, //27
                {
                    title: "'.(\Yii::t("fe", "Rencana Pemulasaran")).'",
                    data: "rencana_pemulasaran", 
                    searchable: false,
                    orderable: false
                }, //28
                {
                    title: "'.(\Yii::t("fe", "Tempat Pemulasaran")).'",
                    data: "tempat_pemulasaran", 
                    searchable: false,
                    orderable: false
                }, //29
                {
                    title: "'.(\Yii::t("fe", "Dokter Menerangkan")).'",
                    data: "dokter_menerangkan", 
                    searchable: false,
                    orderable: true
                }, //30
                {
                    title: "'.(\Yii::t("fe", "Tanggal Pulang")).'",
                    data: "tglpasienpulang", 
                    searchable: true,
                    orderable: false,
                    visible: false,
                }, //31
                {
                    title: "'.(\Yii::t("fe", "Instalasi")).'",
                    data: "instalasi_id", 
                    searchable: true,
                    orderable: false,
                    visible: false,
                }, //32
                {
                    title: "'.(\Yii::t("fe", "Ruangan")).'",
                    data: "ruangan_id", 
                    searchable: true,
                    orderable: false,
                    visible: false,
                }, //33
                {
                    title: "'.(\Yii::t("fe", "Carabayar")).'",
                    data: "carabayar_id", 
                    searchable: true,
                    orderable: false,
                    visible: false,
                }, //34
                {
                    title: "'.(\Yii::t("fe", "Penjamin")).'",
                    data: "penjamin_id", 
                    searchable: true,
                    orderable: false,
                    visible: false,
                }, //35
            ]
        });

        table.on( \'xhr\', function () {
            data = table.ajax.params();
            // alert( \'Search term was: \'+data.search.value );
        });

        $(".dataTables_filter").hide();

        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    31,
                    \'<div class="input-group"><input value='.date("d-M-Y").' type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input value='.date("d-M-Y").' type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
                ],
                [
                    32,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'instalasi_id',
                        '',
                        ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '2'
                        ]
                    ))).'\'
                ],
                [
                    33,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'ruangan_id',
                        '',
                        ArrayHelper::map($api['response']['ruangan'], 'ruangan_id', 'ruangan_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '3'
                        ]
                    ))).'\'
                ],
                [
                    34,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'carabayar_id',
                        '',
                        ArrayHelper::map($api['response']['caraBayar'], 'carabayar_id', 'carabayar_nama'),
                        [
                            'id' => 'carabayar_id',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '3'
                        ]
                    ))).'\'
                ],
                [
                    35,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            DepDrop::widget(
                                [
                                    'name'=>'penjamin_id',
                                    'options'=>[
                                        'id'=>'penjamin_id',
                                        'class'=>'select2',
                                    ],
                                    'pluginOptions'=>[
                                        'depends'=>['carabayar_id'],
                                        'placeholder'=>\Yii::t('fe', '--pilih penjamin--'),
                                        'url'=>Url::to(['list-penjamin'])
                                    ]
                                ]
                            )

                        )
                    ).'\'
                ],
            ], {
                31:0,
                32:1,
                33:2,
                34:3,
                35:4,
            }, true
        );

        dateRangeHelper(".startDate", ".endDate", ".targetDate", true);

    });

', View::POS_END, 'b-index');
?>