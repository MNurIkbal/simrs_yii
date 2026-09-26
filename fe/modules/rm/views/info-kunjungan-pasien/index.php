<?php
// Author : Budi
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .my-legend .legend-title {
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 11px;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 10px;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        border: 1px solid #616161;
        padding: 4px 10px;
        color: #191919;
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
        color: #ffffff;
        padding: 5px 0 5px 10px;
        margin-right: 20px;
    }

    .square-batal {
        height: 30px;
        width: 70px;
        background-color: #D24D57;
        color: #ffffff;
        padding: 5px 0 5px 10px;
    }

    .belum-koreksi {
        background-color: #ffcccc !important;
        color: #484646;
        border-style: solid;
    }

    .sudah-koreksi {
        background-color: #b5e4b5 !important;
        color: #484646;
        border-style: solid;
    }

    .proses-klaim {
        background-color: #c2e6f8 !important;
        color: #484646;
    }

    .final-klaim {
        background-color: #b5e4b5 !important;
        color: #484646;
    }

    .dataTables_scroll {
        max-height: 99999em !important
    }

    table.dataTable td {
        border-bottom: 1px solid black;
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
                        'search'=>[
                            'attributes'=>[
                                'id'=>'btn-cari'
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'id' => 'btn-reset',
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'koreksi' => [
                            'title' => \Yii::t('fe', 'Koreksi'),
                            'icon' => 'fa fa-eye',
                            'attributes' => [
                                'id'=>'btn-koreksi',
                                'data-target' => '/rm/info-kunjungan-pasien/koreksi-diagnosa?id=',
                                'disabled' => 'disabled',
                                'data-conditions' => 'admisi',
                                'data-pages' => '_blank'
                            ]
                        ],
                    ],'#example');
                ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:#ffcccc;'>Belum Koreksi</span></li>
                                    <li><span style='background:#b5e4b5'>Sudah Koreksi</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Info Pasien");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar / Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                            <th><?=\Yii::t("fe", "Instalasi / Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Instalasi");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Dokter Penanggung Jawab");?></th>
                            <th><?=\Yii::t("fe", "Status Diagnosa");?></th>
                            <th><?=\Yii::t("fe", "Diagnosa Dokter");?></th>
                            <th><?=\Yii::t("fe", "Diagnosa Coding");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pulang Pasien");?></th>
                            <th><?=\Yii::t("fe", "Diagnosa Utama");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="14"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    // localStorage.clear();
    localStorage.setItem("penjamin", \''.json_encode($penjamin).'\');
    localStorage.setItem("ruangan", \''.json_encode($ruangan).'\');
    // Event Ready
    $(document).ready(function() {
        table = $("#example").docoTabel({
            filter: true,
            columnDefs: [{
                orderable: false,
                className: "select-checkbox",
                targets: 0
            }],
            select: {
                style: "os",
                selector: "tr"
            },
            sorting: [[19, "asc"]], 
            displayLength: 11,
            processing: true,
            serverSide: true,
            // scrollX: true,
            ajax: baseUrl+"rm/info-kunjungan-pasien/get-data",
            columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: "",
            },
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
                data: "info_pasien",
                name : "Info Pasien",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "No Rekam Medik")).'", 
                data: "no_rekam_medik",
                searchable: true,
                visible: false
            },
            {
                title: "'.(\Yii::t("fe", "Nama Pasien")).'", 
                data: "nama_pasien", 
                searchable: true,
                visible: false
            },
            {
                title: "'.(\Yii::t("fe", "Jenis Kelamin")).'", 
                data: "jenis_kelamin", 
                searchable: true,
                visible: false
            },
            {
                data: "info_penjamin",
                name : "Cara Bayar / Penjamin",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Cara Bayar")).'", 
                data: "carabayar_nama", 
                name : "carabayar_id",
                searchable: true,
                visible: false
            },
            {
                title: "'.(\Yii::t("fe", "Penjamin")).'", 
                data: "penjamin_nama", 
                name: "penjamin_id",
                searchable: true,
                visible: false
            },
            {
                title: "'.(\Yii::t("fe", "Jenis Kasus Penyakit")).'", 
                data: "jeniskasuspenyakit_nama", 
                name:"jeniskasuspenyakit_id",
                searchable: false,
                visible: false
            },
            {
                data: "info_instalasi",
                name : "Instalasi / Ruangan",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Instalasi")).'", 
                data: "instalasi_nama", 
                name: "instalasi_id", 
                visible: false
            },
            {
                title: "'.(\Yii::t("fe", "Ruangan")).'", 
                data: "ruangan_nama", 
                name: "ruangan_id", 
                visible: false
            },
            {
                title: "'.(\Yii::t("fe", "Dokter Penanggung Jawab")).'", 
                data: "dokter_dpjp", 
                name: "dokter_dpjp_id", 
                searchable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Status Diagnosa")).'", 
                data: "status_verif", 
                name : "status_verifikasi",
                visible: false
            },
            {
                title: "'.(\Yii::t("fe", "Diagnosa Dokter")).'", 
                data: "diagnosa_dokter", 
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Diagnosa Coding")).'", 
                data: "diagnosa_coding", 
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Pulang Pasien")).'", 
                data: "tglpasienpulang", 
                searchable: true,
            },
            {
                title: "'.(\Yii::t("fe", "Diagnosa Utama")).'", 
                data: "diagnosadokter_utama", 
                searchable: true,
                orderable: false,
                visible: false
            }
            ],
            drawCallback: function(e) {
                var api = this.api();

                for (var i = 0; api.rows().count() > i; i++) {
                    var rowData = api.row(i).data();
                    var rowNode = api.row(i).node();
                    
                    if(rowData.status_verifikasi == 550){
                        $(rowNode).removeClass("belum-koreksi");
                        $(rowNode).addClass("sudah-koreksi");
                    } else {
                        $(rowNode).removeClass("sudah-koreksi");
                        $(rowNode).addClass("belum-koreksi");
                    }
                }
            },
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    19, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="'. date('d-M-Y') .'"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate" value="'. date('d-M-Y') .'"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
                ],
                [
                    2, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart2" class="form-control startDate2" value="'. date('d-M-Y') .'"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish2" readonly class="form-control endDate2" value="'. date('d-M-Y') .'"/><input type="text" style="display:none" class="targetDate2" col-index=2></div>\'
                ],
                [
                    9, 
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('carabayar_nama', '', 
                            $cara_bayar, 
                            [
                                'id' => 'filter_carabayar', 
                                'class' => 'form-control select2 dep-to-child', 
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                                'data-url' =>  '/rm/info-kunjungan-pasien/get-penjamin',
                                'data-depend_id' => 'filter_penjamin',
                                'data-depend_prompt' => \Yii::t('fe', '-- Pilih --'),
                                'data-storage' => 'penjamin',
                                'data-key' => 'penjamin_id',
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    10, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        DepDrop::widget(
                            [
                                'name'=>'penjamin_id',
                                'options'=>[
                                    'id'=>'penjamin_id',
                                    'class'=>'select2 penjamin-depdrop',
                                ],
                                'pluginOptions'=>[
                                    'depends'=>['filter_carabayar'],
                                    'placeholder'=>\Yii::t('fe', '--pilih semua--'),
                                    'url'=>Url::to(['list-penjamin'])
                                ]
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    16, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('status_verifikasi', '',
                            $status_verivikasi,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    15, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('dokter', '',
                            [],
                            [
                                'class' => 'form-control select2',
                                'id' => 'dokter_id',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    14, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('ruangan', '',
                            $ruangan,
                            [
                                'id' => 'filter_ruangan',
                                'class' => 'form-control select2 dep-to-parent',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                                'data-url' =>  '/rm/info-kunjungan-pasien/get-instalasi',
                                'data-depend_id' => 'filter_instalasi',
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    13, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('instalasi', '',
                            $instalasi,
                            [
                                'id' => 'filter_instalasi', 
                                'class' => 'form-control select2 dep-to-child', 
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                                'data-url' =>  '/rm/info-kunjungan-pasien/get-ruangan',
                                'data-depend_id' => 'filter_ruangan',
                                'data-depend_prompt' => \Yii::t('fe', '-- Pilih --'),
                                'data-storage' => 'ruangan',
                                'data-key' => 'ruangan_id',
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    11, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('jenis_kasus_penyakit', '',
                            [],
                            [
                                'class' => 'form-control select2',
                                'id' => 'jenis_kasus_penyakit',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    7, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('jenis_kelamin', '',
                            $jenis_kelamin,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    20, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('diagnosadokter_utama', '',
                            [],
                            [
                                'class' => 'form-control select2',
                                'id' => 'diagnosadokter_utama',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
            ], {
                19:0,
                3:1,
                5:2,
                6:3,
                7:4,
                9:5,
                2:6
                // 4:2,
                // 5:3,
                // 10:4,
                // 11:5,
                // 13:6,
            }, true
        );
        dateRangeHelper(".startDate",".endDate",".targetDate");
        dateRangeHelper(".startDate2",".endDate2",".targetDate2");
        $("#jenis_kasus_penyakit").select2({
            placeholder: "-- Pilih --",
            minimumInputLength: 3, 
            ajax : {
                url: "/rm/info-kunjungan-pasien/get-jenis-penyakit",
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

        $("#dokter_id").select2({
            placeholder: "-- Pilih --",
            minimumInputLength: 3, 
            ajax : {
                url: "/rm/info-kunjungan-pasien/get-dokter",
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

        $("#diagnosadokter_utama").select2({
            placeholder: "-- Pilih --",
            minimumInputLength: 3,
            tags:true, 
            ajax : {
                url: "/api/rm/diagnosa/list",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                        type: "diagnosa_utama",
                        formatResponse: 1
                    };
                },
                templateResult: function(data) {
                    return data.text
                },
                templateSelection: function(data) {
                    return data.text
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            },
        });
    });
    $(document).on("click", "#example tbody tr", function (event) {
        event.preventDefault();
        try {
            primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
            status = table.row(".selected").data().status_verifikasi 
                    ? table.row(".selected").data().status_verifikasi : null;
            pasienpulang_id = table.row(".selected").data().pasienpulang_id 
                    ? table.row(".selected").data().pasienpulang_id : null;
        } catch (e) {
            primaryKey = status = pasienpulang_id = false;
        }
        $("#btn-koreksi").prop("disabled",true);
        if(table.row(".selected").data().carabayar_id == 6){
            if (pasienpulang_id) {
                $("#btn-koreksi").prop("disabled",false);
            }
        }else{
            if ( (status == 549 || status == 550 || status == 556 || status == 551) && pasienpulang_id ) {
                $("#btn-koreksi").prop("disabled",false);
            }
        }
    });

    $(document).on("click", ".data-reset", function() {
        $(".penjamin-depdrop").prop("disabled", true)
    });

;
', View::POS_END, 'b-index');
?>
