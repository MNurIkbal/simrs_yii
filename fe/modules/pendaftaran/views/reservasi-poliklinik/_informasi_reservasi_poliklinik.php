<?php

/**
 * @author Naufal Ziyad L
 * @copyright 18 January 2018
 * last modified Naufal @29/01/2018
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use app\widgets\filters\DropdownTenagaMedis\DHSelectTenagaMedis;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    .no-width{
        width: 19px
    }
    .status-badge{
        font-size:12px !important;
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
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search' => [
                       'attributes' => [
                           'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar',
                           'id' => 'btn-search-reservasi',
                           'data-options' => 'click',
                       ]
                    ],
                    'reset',
                    // 'pdf',
                    // 'excel',

                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/pendaftaran/reservasi-poliklinik/show-popup?tipe=1&',
                        ]
                    ],
                    'panggil' => [
                        'title' => \Yii::t('fe', 'Panggil Antrian'),
                        'icon' => 'fa fa-volume-up',
                        'attributes' => [
                            //'data-target' => '/pendaftaran/reservasi-poliklinik/panggil-antrian?id=',
                            'disabled' => false,
                            'class' => 'panggil_antrian',
                            'data-options' => 'click',
                            'action' => '/pendaftaran/daftar/panggil-ulang?antrian_id=',
                        ]
                    ],
                    'setujui' => [
                        'title' => \Yii::t('fe', 'Setujui'),
                        'icon' => 'fa fa-check',
                        'attributes' => [
                            'data-target' => '/pendaftaran/daftar/index?pendaftaranol_id=',
                            'id' => 'btn-setujui',
                            'data-type' => 'setujui',
                            'data-options' => 'click',
                            'disabled' => false
                        ]
                    ],
                    'tolak' => [
                        'title' => \Yii::t('fe', 'Tidak Hadir'),
                        'icon' => 'fa fa-close',
                        'attributes' => [
                            'action' => '/pendaftaran/reservasi-poliklinik/ditolak?id=',
                            'class' => 'btn-aksi',
                            'data-options' => 'click',
                            'data-type' => 'tolak',
                            'id' => 'btn-tolak',
                            'disabled' => false
                        ]
                    ],
                    'auto-daftar' => [
                        'title' => \Yii::t('fe', 'Auto Daftar'),
                        'icon' => 'fa fa-check',
                        'attributes' => [
                            'id' => 'btn-auto-daftar',
                            'data-options' => 'click',
                            'disabled' => true
                        ]
                    ],
                    'print-antrian'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Print Antrian Poli'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'btn-print-antrian-poli',
                            'disabled' => true,
                            'data-pages' => '_blank',
                            'data-target'=>'',
                        ]
                    ],
                ], '#table-reservasi-poliklinik')?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-body">
                            <div class="advanced-filter">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-body">
                        <?php if(isset($keteranganCetakTracer)) : ?>
                            <div class="col-md-7">
                                <div class='legend-index'>
                                    <div class='legend-header'>Keterangan</div>
                                        <div class="legend-wrapper">
                                        <?php
                                            foreach ($keteranganCetakTracer as $key => $value) {?>
                                                <div class="legend-information" id="<?= $value['id'];?>" data-type="<?= $value['id'];?>">
                                                    <div class="legend-information__color" style="background-color: <?= $value['kode_warna'];?>"></div>
                                                    <div class="legend-information__text" ><?= $value['text'];?></div>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                            <div class="col-md-12">
                            <table id="table-reservasi-poliklinik" class="table table-striped table-condensed table-hover" style="width:100%;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1" style="width: 19px">
                                            <input type="checkbox" id="table-reservasi-poliklinik-check" name="" value="">
                                        </th>
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "No. Antrian");?></th>
                                        <th>Info Pasien</th>
                                        <th><?=\Yii::t("fe", "No. Pendaftaran");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Lahir");?></th>
                                        <th><?=\Yii::t("fe", "Keterangan");?></th>
                                        <th><?=\Yii::t("fe", "No. Rekam Medik");?></th>
                                        <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                                        <th><?=\Yii::t("fe", "No. Asuransi");?></th>
                                        <th><?=\Yii::t("fe", "Poli / Dokter");?></th>
                                        <th><?=\Yii::t("fe", "Poli Tujuan");?></th>
                                        <th><?=\Yii::t("fe", "Dokter");?></th>
                                        <th><?=\Yii::t("fe", "Cara Bayar / Penjamin");?></th>
                                        <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                                        <th><?=\Yii::t("fe", "Penjamin");?></th>
                                        <th><?=\Yii::t("fe", "Jam Pelayanan");?></th>
                                        <th><?=\Yii::t("fe", "Jam Kunjungan");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Daftar");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Kunjungan");?></th>
                                        <th><?=\Yii::t("fe", "Status");?></th>
                                        <th><?=\Yii::t("fe", "Jenis");?></th>
                                        <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                                        <th><?=\Yii::t("fe", "No Telepon");?></th>
                                        <th><?=\Yii::t("fe", "No BPJS");?></th>
                                        <th><?=\Yii::t("fe", "Status Checkin");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Checkin");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    </tr>
                                </tbody>
                            </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_poli" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
            </div>
        </div>
    </div>

    <div id="modal_auto_register" class="modal modal-md fade" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>

</div>
<script src=""></script>
<?php
    $this->registerJs('
    const channelListenerName = `bulk-register`
    var table
    var reportEngineStatus = parseInt(`'.($reportEngine ? 1 : 0).'`)
    var userLogin = '. $userLogin .';
    var konfigBatalHadirReservasi = '.json_encode(ArrayHelper::getValue($result, 'konfig_batal_hadir_reservasi', false)).';
    var searchParams = new URLSearchParams(window.location.search)
    const colorTracer = "#05BBBE";
    let _extendedParams = {
        columnDefs: [ {
            orderable: false,
            className: "select-checkbox",
            targets: 0,
            checkboxes: {
                selectRow: true,
                selectAllPages: false
            }
        }],
        select: {
            style: "multi",
        }
    }
    const printAntrianPoliUrl = "'.Url::home().Yii::$app->controller->module->id.'/daftar-rajal/print-antrian-poli";

    // condition removed due to feature development at PCP-87 for handle multiple registration patient
    // if ( !reportEngineStatus ) { // when report engine is disabled, then user can use multiple checklist
    //     _extendedParams = {
    //         columnDefs: [ {
    //             orderable: false,
    //             className: "select-checkbox",
    //             targets: 0,
    //             checkboxes: {
    //                 selectRow: true,
    //                 selectAllPages: false
    //             }
    //         }],
    //         select: {
    //             style: "multi",
    //         }
    //     }
    // }

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#table-reservasi-poliklinik").docoTabel($.extend({
            filter: true,
            sorting: [[20, "DESC"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: {
                url: baseUrl+"pendaftaran/reservasi-poliklinik/get-data-informasi",
                data : function (param) {
                    param.columns = param.columns.map(function(obj){
                       if (obj.data == "is_executive") {
                            obj.search.value = searchParams.get("is_executive") == "true";
                            obj.searchable = true; //search true avoid generate search filter
                       }
                       return obj;
                    });
                    return param;
                }
            },
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "No Antrian")).'",
                    data: "no_antrian",
                    visible: true,
                    render: (data, type, row, meta) => {
                        return row.antrian ? row.antrian : ``;
                    }
                },
                {
                    title: "'.(\Yii::t('fe', 'Info Pasien')).'",
                    data: "info_pasien",
                    searchable: false,
                    orderable: false,
                },
                {title: "'.(\Yii::t("fe", "Status Check-in")).'", data: "is_checkin"},
                {
                    title: "'.(\Yii::t('fe', 'Tanggal Check-In')).'",
                    data: "tgl_checkin",
                    searchable: false,
                    orderable: true,
                },
                {title: "'.(\Yii::t("fe", "No. Pendaftaran")).'", data: "no_pendaftaranol", visible: false},
                {title: "'.(\Yii::t("fe", "Tanggal Lahir")).'", data: "tanggal_lahir", searchable: false},
                {title: "'.(\Yii::t("fe", "Keterangan")).'", data: "keterangan", searchable: false},
                {title: "'.(\Yii::t("fe", "No. Rekam Medik")).'", data: "no_rekam_medik", visible: false},
                {title: "'.(\Yii::t("fe", "Nama Pasien")).'",  data: "nama_pasien", visible: false},
                {title: "'.(\Yii::t("fe", "No. Asuransi")).'", data: "no_asuransi", searchable: false},
                {title: "'.(\Yii::t("fe", "Poli / Dokter")).'", data: "poli_dokter", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Poli Tujuan")).'", data: "ruangan_nama", name: "ruangan_id", visible: false},
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "nama_pegawai", name: "pegawai_id", visible: false},
                {title: "'.(\Yii::t("fe", "Cara bayar / Penjamin")).'", data: "carabayar_penjamin", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_nama",name: "carabayar_id", visible: false},
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama", name: "penjamin_id", visible: false},
                {title: "'.(\Yii::t("fe", "Jam Pelayanan")).'", data: "jam_mulai", visible: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Jam Kunjungan")).'", data: "jam_kunjungan",visible: false},
                {title: "'.(\Yii::t("fe", "Tanggal Daftar")).'", data: "tgl_pendaftaran",searchable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Kunjungan")).'", data: "tgl_kunjungan"},
                {title: "'.(\Yii::t("fe", "Status Daftar")).'", data: "status_daftar"},
                {title: "'.(\Yii::t("fe", "Jenis Daftar")).'", data: "jenis_reservasinama", searchable: true},
                {title: "'.(\Yii::t("fe", "Jenis Kelamin")).'", data: "jeniskelamin", visible: false},
                {title: "'.(\Yii::t("fe", "No Telepon")).'", data: "no_telepon_pasien", searchable: false},
                {
                    title: "'.(\Yii::t("fe", "No Bpjs")).'",
                    data: "no_bpjs",
                    render : function (data, type, row, meta) {
                        return data != null && data != "" ? data : " - "
                    }
                },
                {title: "'.(\Yii::t("fe", "No Telepon")).'", data: "no_pendaftaran", visible: false, searchable: false},
                {title: "'.(\Yii::t("fe", "Is Executive")).'", data: "is_executive", visible: false, searchable: false},
            ],
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                let is_cetaktracer = aData.is_cetaktracer

                if(is_cetaktracer) {
                    $("td:eq(2)", nRow).css("background-color", colorTracer);
                }
            }
        }, _extendedParams));

        $(".panggil_antrian").on("click",function(){
            var data = table.row(".selected").data();

            if(typeof data !== \'undefined\'){
                $.ajax({
                    type: "GET",
                    url: $(this).attr("action") + data.antrian_id,
                    dataType: "JSON",
                    success: function (res) {
                        $.ajax({
                            type: "GET",
                            url: "/pendaftaran/daftar/pilih?antrian_id=" + data.antrian_id,
                            dataType: "JSON",
                            success: function (res) {
                                
                            },
                        });
                    },
                });
            }else{

            }
        });

        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                21,
                "<div class=\'input-group\' style=\"margin-bottom: 0px !important;\"><input type=\'text\' value=\''.date('d-M-Y').'\' readonly=\'true\' id=\'rangeDemoStart\' class=\'form-control startDate\'/><span class=\'input-group-addon\' style=\'border-left: 0; border-right: 0;\'>-</span><input type=\'text\'  id=\'rangeDemoFinish\' readonly=\'true\' value=\''.date('d-M-Y').'\' class=\'form-control endDate\'/><input type=\'text\' style=\'display:none\' class=\'targetDate\'></div>",
            ],
            [
                14,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            DHSelectTenagaMedis::widget([

                            ])
                        )
                    ).'\'
            ],
            [
                13,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'ruangan_id',
                                '',
                                isset($result['poliklinik']) ? $result['poliklinik'] : [],
                                [
                                    'class' => 'form-control select2 select-multiple-tags',
                                    'id' => 'poliklinik_id',
                                    'multiple' => true,
                                    // 'prompt' => \Yii::t('fe', '--Semua Poliklinik--'),
                                ]
                            )
                        )
                    ).'\'
            ],
            [
                16,
                \''.(
                    preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        Html::dropDownList(
                            'carabayar_id',
                            '',
                            $result['carabayar'],
                            [
                                'class' => 'form-control select2',
                                'id' => 'carabayar_id',
                                'prompt' => \Yii::t('fe', '--Pilih Cara Bayar--')
                            ]
                        )
                    )
                ).'\'
            ],
            [
                17,
                \''.(
                    preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        DepDrop::widget(
                            [
                                'name'=>'penjamin_id',
                                'options'=>['id'=>'penjamin_id', 'class'=>'form-control select2'],
                                'pluginOptions'=>[
                                    'depends'=>['carabayar_id'],
                                    'placeholder'=>\Yii::t('fe', '--pilih penjamin--'),
                                    'url'=>Url::to(['/pendaftaran/end-point/list-penjamin'])
                                ]
                            ]
                        )
                    )
                ).'\'
            ],
            [
                22,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'statusdaftar',
                                '',
                                isset($result['statusdaftar']) ? $result['statusdaftar'] : [],
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'statusdaftar',
                                    'prompt' => \Yii::t('fe', '--Pilih Status Pendaftaran--')
                                ]
                            )
                        )
                    ).'\'
            ],
            [
                4,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'statuscheckin',
                                '',
                                isset($result['statuscheckin']) ? $result['statuscheckin'] : [],
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'statuscheckin',
                                    'prompt' => \Yii::t('fe', '--Pilih Status Check-in--')
                                ]
                            )
                        )
                    ).'\'
            ],
            [
                23,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'jenispendaftaran',
                                '',
                                isset($result['jenispendaftaran']) ? $result['jenispendaftaran'] : [],
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'jenispendaftaran',
                                    'prompt' => \Yii::t('fe', '--Pilih Jenis Pendaftaran--')
                                ]
                            )
                        )
                    ).'\'
            ], [
                18,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::textInput('jam_mulai', '',
                            [
                                'class' => 'form-control pickatime',
                                'prompt' => '',
                                'col-index'=>3,
                                'placeholder' => 'Jam Pelayanan'
                            ]
                        )
                )).'</div>\'
            ], [
                19,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::textInput('jam_kunjungan', '',
                            [
                                'class' => 'form-control pickatime',
                                'prompt' => '',
                                'col-index'=>3,
                                'placeholder' => 'Jam Kunjungan'
                            ]
                        )
                )).'</div>\'
            ],
            [
                24,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'jeniskelamin',
                                '',
                                isset($result['jeniskelamin']) ? $result['jeniskelamin'] : [],
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'jeniskelamin',
                                    'prompt' => \Yii::t('fe', '--Pilih Jenis Kelamin--')
                                ]
                            )
                        )
                    ).'\'
            ],
        ], {
            21:0,
            9:1,
            10:2,
            8:2,
            24:3,
            6:4,
        }, true, false);

        $(document).on("click", "#btn-search-reservasi", function(event) {
            event.preventDefault();
            $(".advancedFilterDo").click();
            return false;
        });

        $(document).on("keypress", ".advancedFilter", function(event) {
            if (event.which == "13") {
                event.preventDefault();
                $(".advancedFilterDo").click();
            }
        });

        dateRangeHelper(\'.startDate\',\'.endDate\',\'.targetDate\');

        $(".pickatime").pickatime({
            format: "HH:i"
        });
    });
    // Options
    var oneDay = 24*60*60*1000;
    var rangeDemoFormat = "%e-%b-%Y";
    var rangeDemoConv = new AnyTime.Converter({format:rangeDemoFormat});

    var primaryKey;
    //add for handle checkbox click
    $(document).on("click", "#table-reservasi-poliklinik tbody tr", selectedConditionEvent);

    $(document).on("click", "#btn-tolak", function(){
        var data = table.row(".selected").data();
        if(typeof data !== \'undefined\'){
            var primary = data.primary;
                target = $(this).attr("action");
                type = $(this).attr("data-type");
            $(this).docoForm("click", {
                url: target+primary,
                data: {noBoking: data.no_pendaftaranol},
                confirmMessage: (type == "setujui") ? "Apakah anda yakin untuk menerima reservasi pasien ini ?" : "Apakah anda yakin untuk menolak reservasi pasien ini ?",
                success: function(res){
                    if(type == "setujui"){
                        console.log("setujui");
                    }else{
                        table.draw();
                    }
                }
            })
        }
    });

    $("#btn-setujui").click(function() {
        var data = table.row(".selected").data();
        if(typeof data !== \'undefined\'){
            let tglDaftar = moment(new Date(data.tgl_kunjungan_formated)).locale("en").format("DD-MMM-YYYY");
            let tglhariIni = moment().locale("en").format("DD-MMM-YYYY")

            if(tglDaftar > tglhariIni) {
                confirmationDialog( "Tgl kunjungan pasien bukan hari ini. Lanjutkan pendaftaran?", (isConfirm) => {
                    if (isConfirm) {
                        window.open("/pendaftaran/daftar/index?pendaftaranol_id="+data.primary);
                    }
                });
            } else {
                window.open("/pendaftaran/daftar/index?pendaftaranol_id="+data.primary);
            }
        }
    });

    $("#btn-auto-daftar").click(function() {
        const selectedItems = table.rows(".selected").data();
        const reservationPatientInformation = selectedItems.toArray().map((item) => {
            return {
                info_pasien: item.info_pasien,
                penjamin_id: item.penjamin_id,
                penjamin_nama: item.penjamin_nama,
                carabayar_id: item.carabayar_id,
                no_pendaftaranol: item.no_pendaftaranol,
            }
        })
        const autoRegisterData = selectedItems.toArray().map((item) => {
            const tglKunjungan = new Date(item.tgl_kunjungan_formated).toISOString()
            return {
                carabayar_id: item.carabayar_id,
                penjamin_id: item.penjamin_id,
                asalrujukan_id: 1,
                antrian_id: item.antrian_id,
                no_rekam_medik: item.no_rekam_medik,
                no_asuransi: item.no_asuransi,
                pendaftaranol_id: item.primary,
                pendaftaran_id: null,
                dokter_perujuk: null,
                is_kolektif: false,
                is_multi_payer: "",
                tgl_pendaftaran: moment().format("YYYY-MM-DD HH:mm:ss"),
                ruangan_id: item.ruangan_id,
                jeniskasuspenyakit_id: item.jeniskasuspenyakit_id,
                dokter_id: item.pegawai_id,
                keadaan_masuk: "",
                transportasi: "",
                keterangan: item.keterangan,
                referal: "",
                jadwaldokter_id: item.jadwaldokter_id,
                no_pendaftaranol: item.no_pendaftaranol,
                nama_pasien: item.nama_pasien,
                jeniskelamin: item.jeniskelamin,
                pj_pengantar: 245,
                no_telepon_pasien: item.no_telepon_pasien,
                no_kartu_bpjs: item.no_bpjs,
                no_rujukan_bpjs: item.no_rujukan,
                jenis_kunjungan_bpjs: item.jenis_kunjungan_bpjs, // used for determine what kind of kunjungan bpjs
                nama_pengguna: item.logged_user,
                no_surat_kontrol: item.nomorreferensi,
            }
        })

        // manually show modal after post reservationItems
        $.ajax({
            type: "POST",
            url: "/pendaftaran/reservasi-poliklinik/show-auto-register-modal",
            data: {
                reservationItems: autoRegisterData,
                patientList: reservationPatientInformation,
            },
            dataType: "html",
            success: (page) => {
                $("#modal_auto_register").find(".modal-content").html(page)
                $("#modal_auto_register").modal("show")
            },
        })
    });

    // $("#btn-panggil").click(function() {
    //     var data = table.row(".selected").data();
    //     var id = data.primary;
    //     $.ajax({
    //         type: "GET",
    //         url: $(this).attr("action")+data.primary,
    //         // data: _data,
    //         dataType: "JSON",
    //         beforeSend: function () {
    //         },
    //         success: function (res) {

    //         },
    //     });
    // });

    $("#table-reservasi-poliklinik").on("draw.dt", function () {
        $("#table-reservasi-poliklinik-check").prop("checked", false).change(); // reset checked all
    });

    // Select all checkbox
    $("body").on("change", "#table-reservasi-poliklinik-check ", function() {
        let tableTr = $("#table-reservasi-poliklinik").find("tbody tr");
        let checked = $(this).prop("checked");
        $.each(tableTr, function(val) {
            if(checked){
              $(this).addClass("selected");
            }else{
              $(this).removeClass("selected");
            }
        });

        selectedConditionEvent();
    });

    const drawTables = () => {
        table.draw(false)
    }

    function selectedConditionEvent() {
        var data = table.row(".selected").data();
        const selectedItems = table.rows(".selected").data()
        const antrian_id = data?.antrian_id;
        const pendaftaran_id = data?.pendaftaran_id;
        const is_checkin = data?.is_checkin_origin;

        if (selectedItems.length > 1) {
            // disable all button due to the button doesnt have function for handle multiple items
            $("#btn-tolak").prop("disabled", true);
            $("#btn-setujui").prop("disabled", true);
            $(".panggil_antrian").prop("disabled", true);
            $("#btn-print-antrian-poli").prop("disabled", true);

            /* 
            * auto-daftar button will enable when the data meet certain conditions:
            * a. the registration date is higher than today
            * b. there need to be only one payment type can be available at one auto-registration transaction
            * c. make sure the status_daftar_ol is still 564
            */

            // filter for makesure that item selected is in same Payment Type
            const filterSamePaymentType = selectedItems.toArray().every(
                (item, _, array) => item.carabayar_id === array[0].carabayar_id
            )

            if (filterSamePaymentType) {
                const todayDate = moment().locale("in").format("YYYY-MM-DD")
                const filterByStatusAndDate = selectedItems.toArray().filter(item => item.status_daftar_ol === 564 && moment(item.tgl_kunjungan_formated, "YYYY-MM-DD").isSameOrAfter(todayDate))

                if (filterByStatusAndDate.length === selectedItems.toArray().length) {
                    $("#btn-auto-daftar").prop("disabled", false);
                } else {
                    $("#btn-auto-daftar").prop("disabled", true);
                }
            } else {
                $("#btn-auto-daftar").prop("disabled", true);
            }
        } else {
            $("#btn-auto-daftar").prop("disabled", true);
            if(typeof data !== \'undefined\'){
                let tglDaftar = moment(new Date(data.tgl_kunjungan_formated)).locale("en").format("DD-MMM-YYYY");
                let tglhariIni = moment().locale("en").format("DD-MMM-YYYY")

                if(data.status_daftar_ol == 564){
                    if(tglDaftar > tglhariIni) {
                        if (!konfigBatalHadirReservasi) {
                            $("#btn-tolak").prop("disabled", true);
                        } else {
                            $("#btn-tolak").prop("disabled", false);
                        }
                        $("#btn-setujui").prop("disabled", true);
                    }else{
                        $("#btn-tolak").prop("disabled", false);
                        $("#btn-setujui").prop("disabled", false);
                    }

                    // only enable auto daftar button when the registration date is same with current date
                    if (moment(tglDaftar).isSame(tglhariIni)) {
                        $("#btn-auto-daftar").prop("disabled", false);
                    }
                }else{
                    $("#btn-tolak").prop("disabled", true);
                    $("#btn-setujui").prop("disabled", true);
                }
            }else{
                $("#btn-tolak").prop("disabled", true);
                $("#btn-setujui").prop("disabled", true);
            }

            if (selectedItems.length == 0) {
                $("#btn-print-antrian-poli").prop("disabled", true);
            } else {
                if (is_checkin == true) {
                    $("#btn-print-antrian-poli").prop("disabled", false);
                    $("#btn-print-antrian-poli").attr("data-target", printAntrianPoliUrl+"?antrian_id="+antrian_id+"&pendaftaran_id="+pendaftaran_id);
                }
            }
        }
    }
', View::POS_END, 'js-kuning');

?>

<?php 
    $this->registerJs($this->render('js/informasi-reservasi.js'), View::POS_END);
?>

<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
