<?php

/**
 * @Author: rizal
 * @Date:   2018-09-19 11:07:43
 * @Description:
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = isset($title) ? $title : Yii::t('fe', 'Laporan Pasien Rawat Darurat');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat darurat'), 'url' => ['/igd/lap-pasien-igd']];
$this->params['breadcrumbs'][] = $this->title;
?>

</style>
<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title">
                        <b>
                            <?php
                            // Yii::t('fe', 'Informasi Pasien').' '.Yii::$app->docoVars->workspace("modul_alias"); 
                            echo $this->title; 
                            ?>
                        </b>
                    </h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    // 'pdf' => [
                    //   'title' => Yii::t('fe', 'Cetak'),
                    //   'attributes'=>[
                    //       /*'data-target'=>Url::home().'igd/inf-pasien-igd/export-pdf?jenis=igd&'*/
                    //   ],
                    // ],
                    // 'rincian' => [
                    //     'title' => Yii::t('fe', 'Cetak Rincian'),
                    //     'icon' => 'fa fa-file-pdf-o',
                    //     'attributes' => [
                    //         'id' => 'cetak-rincian-tagihan',
                    //         'data-options' => 'link',
                    //         'class' => 'spa',
                    //         'data-target' => '',
                    //         'disabled' => 'true',
                    //         'target' => '_blank'
                    //     ]
                    // ],
                    // 'excel' => [
                    //   'title' => Yii::t('fe', 'Excel'),
                    //   'attributes'=>[
                    //      /* 'data-target'=>Url::home().'igd/inf-pasien-igd/export-excel?jenis=igd&'*/
                    //   ]
                    // ],
                    // 'pdf',
                    'export-excel-serconn' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'igd/lap-pasien-igd/show-popup-excel?',
                            'data-width' => '75%'
                        ]
                    ],
                    'export-pdf-serconn' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Cetak PDF'),
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            'id' => 'data-export-pdf-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'igd/lap-pasien-igd/show-popup-pdf?',
                            'data-width' => '75%'
                        ]
                    ],

                ], '#lap-igd');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="lap-igd" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                            <th><?=\Yii::t("fe", "Dokter Jaga");?></th>
                            <th><?=\Yii::t("fe", "Dokter Penanggung Jawab");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "No Telph 1");?></th>
                            <th><?=\Yii::t("fe", "No Telph 2");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="15"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
localStorage.clear();
// Event Ready
$(document).ready(function() {
    table = $("#lap-igd").docoTabel({
        filter: true,
        // columnDefs: [{
        //     orderable: false,
        //     className: "select-checkbox",
        //     targets: 0
        // }],
        select: {
            style: "os",
            selector: "tr"
        },
        sorting: [[2, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"igd/lap-pasien-igd/get-data",
        columns: [
            // {
            //      data: null,
            //      searchable: false,
            //      orderable: false,
            //      defaultContent: "",
            // },
            {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'", 
                data: "tgl_pendaftaran"
            },
            {
                title: "'.(\Yii::t("fe", "No Pendaftaran")).'", 
                data: "no_pendaftaran", 
            },
            {
                title: "'.(\Yii::t("fe", "No Rekam Medik")).'", 
                data: "no_rekam_medik"
            },
            {
                title: "'.(\Yii::t("fe", "Nama Pasien")).'", 
                data: "nama_pasien", 
            },
            {
                title: "'.(\Yii::t("fe", "Jenis Kelamin")).'", 
                data: "jenis_kelamin", 
                searchable: false
            },
            {
                title: "'.(\Yii::t("fe", "Cara Bayar")).'", 
                data: "carabayar_nama", 
                name : "carabayar_id",
                searchable: false
            },
            {
                title: "'.(\Yii::t("fe", "Penjamin")).'", 
                data: "penjamin_nama", 
                name: "penjamin_id",
                searchable: false
            },
            {
                title: "'.(\Yii::t("fe", "Jenis Kasus Penyakit")).'", 
                data: "jeniskasuspenyakit_nama", 
                name:"jeniskasuspenyakit_id",
                searchable: false
            },
            {
                title: "'.(\Yii::t("fe", "Dokter Jaga")).'", 
                data: "dokter_jaga", 
                name: "dokter_jaga_id", 
                searchable: false
            },
            {
                title: "'.(\Yii::t("fe", "Dokter Penanggung Jawab")).'", 
                data: "dokter", 
                name: "dokter_id", 
                searchable: false
            },
            {
                title: "'.(\Yii::t("fe", "Status")).'", 
                data: "status_periksa_nama", 
                name: "status_periksa"
            },
            {
                title: "'.(\Yii::t("fe", "No Telph 1")).'", 
                data: "no_telepon_pasien", 
                name: "no_telepon_pasien", 
                searchable: false
            },
            {
                title: "'.(\Yii::t("fe", "No Telph 2")).'", 
                data: "no_mobile_pasien", 
                name: "no_mobile_pasien", 
                searchable: false
            },
            {
                title: "'.(\Yii::t("fe", "Ruangan")).'", 
                data: "ruangan_nama", 
                name: "ruangan_id", 
                searchable: false,
                visible:false
            },
             // search from _id
            {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_id", visible:false}, // 13
            {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_id", visible:false}, // 14
            {title: "'.(\Yii::t("fe", "Dokter Penanggungjawab")).'", data: "dokter_id", visible:false}, // 15
            {title: "'.(\Yii::t("fe", "Jenis Kasus Penyakit")).'", data: "jeniskasuspenyakit_id", visible:false}, // 16
            {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_id", visible:false, searchable:false}, // 17
            {title: "'.(\Yii::t("fe", "Jenis Kelamin")).'", data: "jeniskelamin", visible:false}, // 19
        ],
    });
    $(".dataTables_filter").hide();


    $(".filter-form").datatableBootstrapFilter(table, 
        [
            [
                1, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="'. date('d-M-Y') .'"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate" value="'. date('d-M-Y') .'"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ],
            [
                13, 
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('carabayar_nama', '', 
                        ArrayHelper::map($cara_bayar, 'carabayar_id', 'carabayar_nama'), 
                        [
                            'id' => 'filter_carabayar', 
                            'class' => 'form-control select2 dep-to-child', 
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'data-url' =>  '/igd/lap-pasien-igd/get-penjamin',
                            'data-depend_id' => 'filter_penjamin',
                            'data-depend_prompt' => \Yii::t('fe', '-- Pilih --'),
                            'data-storage' => 'penjamin',
                            'data-key' => 'penjamin_id',
                            'data-value' => 'penjamin_nama',
                        ]
                    )
                )).'</div>\'
            ],
            [
                14, 
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('penjamin_nama', '',
                        ArrayHelper::map($penjamin, 'penjamin_id', 'penjamin_nama'), 
                        [
                            'id' => 'filter_penjamin',
                            'class' => 'form-control select2 dep-to-parent',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'data-url' =>  '/igd/lap-pasien-igd/get-carabayar',
                            'data-depend_id' => 'filter_carabayar',
                        ]
                    )
                )).'</div>\'
            ],
            [
                15, 
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('dokter', '',
                        ArrayHelper::map($resDokter, 'pegawai_id', 'nama_pegawai'),
                        [
                            'class' => 'form-control select2',
                            'id' => 'dokter_id',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                        ]
                    )
                )).'</div>\'
            ],
            [
                16, 
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('jenis_kasus_penyakit', '',
                        ArrayHelper::map($resKasuspenyakit, 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama'),
                        [
                            'class' => 'form-control select2',
                            'id' => 'jenis_kasus_penyakit',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                        ]
                    )
                )).'</div>\'
            ],
            [
                17, 
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('ruangan', '',
                        ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama'),
                        [
                            'id' => 'filter_ruangan',
                            'class' => 'form-control select2 dep-to-parent',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'data-url' =>  '/igd/lap-pasien-igd/get-instalasi',
                            'data-depend_id' => 'filter_instalasi',
                        ]
                    )
                )).'</div>\'
            ],
            [
                11,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status_periksa[]', [],
                        $listStatus,
                        [
                            'id' => 'filter_status_periksa',
                            'class' => 'form-control select2 word-wrapper',
                            'multiple' => 'multiple'
                        ]
                    )
                )).'</div>\'
            ],
            [
                19, 
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('jeniskelamin', '',
                        ArrayHelper::map($jenis_kelamin, 'lookup_id', 'lookup_name'),
                        [
                            'id' => 'filter_jenis_kelamin',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                        ]
                    )
                )).'</div>\'
            ],
        ], {
            1:0,
            2:1,
            3:2,
            11:3,
        }, true
    );

    $("#filter_status_periksa").change(function() {
        var listItems = $(".select2-selection__choice")

        listItems.each(function(idx, li) {
            var product = $(li).prop("title") == "";
            if($(li).prop("title") == ""){
                $(li).remove()
            }
        });
    });

    if($(".select2-selection__choice").prop("title") == ""){
        $(".select2-selection__choice").remove()
    }

    dateRangeHelper(".startDate",".endDate",".targetDate", true);
}); ', View::POS_END, 'b-index');
?>