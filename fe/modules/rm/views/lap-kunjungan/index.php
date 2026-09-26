<?php

/**
 * @Author: aris
 * @Date:   2019-07-05 10:00:00
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

$this->title = isset($title) ? $title : Yii::t('fe', 'Rekam Medis');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$uid = isset($_GET['uid']) ? $_GET['uid'] : null;
?>

<style>
    /* .dataTables_scroll {
        max-height: 99999em !important
    } */

    #modal_backdrop {
        z-index: 1041 !important;
        max-height: calc(100vh);
        overflow-y: auto;
    }
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
                            // echo Yii::$app->docoVars->workspace("modul_alias");
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
                'export-excel-serconn' => [
                    'type' => 'button',
                    'title' => \Yii::t('fe', 'Excel'),
                    'icon' => 'fa fa-file-excel-o',
                    'attributes' => [
                        'id' => 'data-export-excel-serconn',
                        'data-options' => 'excel-serconn',
                        'data-target' => '#modal_backdrop',
                        'data-url' => Url::home() . 'rm/lap-kunjungan/export-excel-serconn?',
                        'data-width' => '75%'
                    ]
                ],
                'riwayat-pasien'=>[
                    'type'=>'button',
                    'title' => \Yii::t('fe', 'Riwayat Pasien'),
                    'icon' => 'fa fa-user',
                    'method' => 'not-exist',
                    'attributes' => [
                        'id' => 'btn-riwayat-pasien',
                        'data-options'=>'click',
                        'data-target' => '',
                        // 'data-width' => '75%',
                        // 'data-url' => Url::home() . 'igd/riwayat-pasien/history-patient?modal=is_modal&norm=',
                    ]
                ],
                // 'riwayat-pasien' => [
                //     'type' => 'button',
                //     'title' => \Yii::t('fe', 'Riwayat Pasien'),
                //     'icon' => 'fa fa-user',
                //     'method' => 'not-exist',
                //     'attributes' => [
                //         'id' => 'btn-riwayat-pasien',
                //         'data-options' => 'click',
                //         'data-target'=> ''
                //     ]
                // ],
                'koreksi-diagnosa' => [
                    'type' => 'button',
                    'title' => \Yii::t('fe', 'Koreksi Diagnosa'),
                    'icon' => 'fa fa-cogs',
                    'method' => 'not-exist',
                    'attributes' => [
                        'id' => 'btn-koreksi-diagnosa',
                        'data-options' => 'click',
                        'data-target'=> '',
                        'disabled' => false
                    ]
                ],
        ], '#lap-kunjung');?>
        <?= Html::button("hidden riwayat", [
            'id'          => 'btn-hidden-riwayat-pasien',
            'data-width'  => '90%',
            'data-toggle' => 'modal',
            'data-target' => '#modal_backdrop',
            'action'      => '',
            'style'       => 'display: none;',
        ]) ?>
        </div>

        <div class="panel-body">
            <div class="row">
                <!--<div class="col-md-12 filter-form"></div>-->
            </div>
            <div class="advanced-filter">
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-kunjung" class="table table-striped table-condensed table-hover" style="width:100%">
                       <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Data Pasien");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Instalasi");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Lahir");?></th>
                            <th><?=\Yii::t("fe", "Alamat");?></th>
                            <th><?=\Yii::t("fe", "Nomor Identitas");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar / Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                            <th><?=\Yii::t("fe", "Instalasi / Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Dokter DPJP");?></th>
                            <th><?=\Yii::t("fe", "Diagnosa Utama");?></th>
                            <th><?=\Yii::t("fe", "Diagnosa Penyerta");?></th>
                            <th><?=\Yii::t("fe", "Status Periksa");?></th>
                            <th><?=\Yii::t("fe", "No Telepon Pasien");?></th>
                            <th><?=\Yii::t("fe", "Kondisi Pulang");?></th>
                            <th><?=\Yii::t("fe", "Cara Pulang");?></th>
                            <th><?=\Yii::t("fe", "Status Kunjungan");?></th>
                            <th><?=\Yii::t("fe", "Kondisi Pulang");?></th>
                            <th><?=\Yii::t("fe", "Cara Pulang");?></th>
                            <th><?=\Yii::t("fe", "Status Kunjungan");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Lahir");?></th>
                            <th><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Asal Rujukan");?></th>
                            <th><?=\Yii::t("fe", "Rujukan Dari");?></th>
                            <th><?=\Yii::t("fe", "Nama Perujuk");?></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
    <div id="modal_riwayat" class="modal">
        <div class="modal-dialog modal-xl" style="width: 90%;">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div id="modal-preview" class="modal">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-header bg-inverse" style="z-index: 1050">
                <button type="button" id="dismiss-preview-btn" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Preview</h5>
            </div>
            <div class="modal-content">
                <div class="preview-wrapper" style="position: relative;" id="preview-wrapper">
                    <!-- <div class="overlay-preview"></div> -->
                    <iframe frameborder="0" id="preview-content" style="width:100%;height:85vh"></iframe>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-gambar-radiologi" class="modal">
        <div class="modal-dialog modal-xl" style="height: 100%;width: 99%;margin: 2px;">
            <div class="modal-content" style=" height: 100%;">
                <div class="modal-header bg-inverse">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h5 class="modal-title">Gambar Radiologi</h5>
                </div>
                <div class="modal-body" style="height: 100%;">
                    <iframe  style="width: 100%; height: 95%;" src=""></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs("
    var table;
    var uid = '".$uid."';

    $(document).ready(function(){

    // Generate Table
        table = $('#lap-kunjung').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style: 'os',
                selector: 'tr'
            },
            sorting: [[2, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/lap-kunjungan/get-data',
            columns: [
                {
                    title: '',
                    data: null,
                    defaultContent: '',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },

                {title: '".(\Yii::t('fe', "Tanggal Pendaftaran"))."', data: 'tgl_pendaftaran'},
                {title: '".(\Yii::t('fe', "Data Pasien"))."', data: 'data_pasien', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', "No Rekam Medik"))."', data: 'no_rekam_medik', visible: false, searchable: true},
                {title: '".(\Yii::t('fe', "Nama Pasien"))."', data: 'nama_pasien', visible: false, searchable: true},
                {title: '".(\Yii::t('fe', "Jenis Kelamin"))."', data: 'jenis_kelamin', visible: false, searchable: true},
                {title: '".(\Yii::t('fe', "Cara Bayar"))."', data: 'carabayar_id', visible: false, searchable: true},
                {title: '".(\Yii::t('fe', "Penjamin"))."', data: 'penjamin_id', visible: false, searchable: true},
                {title: '".(\Yii::t('fe', "Instalasi"))."', data: 'instalasi_id', visible: false, searchable: true},
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', visible: false, searchable: true}, //10
                {title: '".(\Yii::t('fe', "Tanggal Lahir"))."', data: 'tgl_lahir', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', "Alamat"))."', data: 'alamat_pasien', searchable: false},
                {title: '".(\Yii::t('fe', "Nomor Identitas"))."', data: 'no_identitas', visible: true, searchable: true},
                {title: '".(\Yii::t('fe', "Cara Bayar / Penjamin"))."', data: 'carabayar_penjamin', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', "Kelas Pelayanan"))."', data: 'kelaspelayanan_nama', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', "Jenis Kasus Penyakit"))."', data: 'jeniskasuspenyakit_nama', searchable: true},
                {title: '".(\Yii::t('fe', "Instalasi / Ruangan"))."', data: 'instalasi_ruangan', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', "Dokter DPJP"))."', data: 'dokterdpjp_nama', searchable: true},
                {title: '".(\Yii::t('fe', "Diagnosa Utama"))."', data: 'diagnosa_utama', searchable: true},
                {title: '".(\Yii::t('fe', "Diagnosa Penyerta"))."', data: 'diagnosa_penyerta', searchable: true}, //20
                {title: '".(\Yii::t('fe', "Status Periksa"))."', data: 'status_periksa', searchable: true},
                {title: '".(\Yii::t('fe', "No Telepon Pasien"))."', data: 'no_telepon_pasien', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', "Kondisi Pulang"))."', data: 'kondisikeluar_nama', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', "Cara Pulang"))."', data: 'carakeluar_nama', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', "Status Kunjungan"))."', data: 'kunjungan_nama', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', "Kondisi Pulang"))."', data: 'kondisikeluar_id', visible: false, searchable: true},
                {title: '".(\Yii::t('fe', "Cara Pulang"))."', data: 'carakeluar_id', visible: false, searchable: true},
                {title: '".(\Yii::t('fe', "Status Kunjungan"))."', data: 'kunjungan_id', visible: false, searchable: true},
                {title: '".(\Yii::t('fe', "Tanggal Lahir"))."', data: 'tanggal_lahir', visible: false, searchable: true},
                {title: '".(\Yii::t('fe', "Kelas Pelayanan"))."', data: 'kelaspelayanan_id', visible: false, orderable: false}, //30
                {title: '".(\Yii::t('fe', "Asal Rujukan"))."', data: 'asalrujukan_nama', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', "Rujukan Dari"))."', data: 'rujukandari_nama', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', "Nama Perujuk"))."', data: 'nama_perujuk', searchable: false, orderable: false},
            ],
        });

        $('.dataTables_filter').hide();

        //Filter berdasarkan Tanggal Pendaftaran, Instalasi, Ruangan, No RM, Nama Pasien, Jenis Kelamin
        $('.filter-form').datatableBootstrapFilter(table,
        [
            [
                2,
                \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate inputDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate inputDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                6,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('jenis_kelamin', '',
                    ArrayHelper::map($api['response']['jenis_kelamin'], 'lookup_id', 'lookup_name'),
                        [
                            'id' => 'filter_jenis_kelamin',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                7,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('carabayar_nama', '',
                    ArrayHelper::map($api['response']['cara_bayar'], 'carabayar_id', 'carabayar_nama'),
                        [
                            'id' => 'filter_carabayar_nama',
                            'class' => 'form-control select2 selectCaraBayar',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                8,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    DepDrop::widget([
                            'name' => 'penjamin_nama',
                            //'data'=> [
                            //    Yii::$app->docoVars->workspace("ruangan_id") => Yii::$app->docoVars->workspace("ruangan_name")
                            //],
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2 selectPenjamin'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_carabayar_nama'],
                               'placeholder' => '--Pilih Semua--',
                               'url' => Url::to(['/rm/lap-kunjungan/get-penjamin'])
                            ]
                        ])
                ))."<div>\"
            ],
            [
                16,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('jeniskasuspenyakit_nama', '',
                    ArrayHelper::map($api['response']['kasus_penyakit'], 'jeniskasuspenyakit_nama', 'jeniskasuspenyakit_nama'),
                        [
                            'id' => 'filter_jeniskasuspenyakit_nama',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                9,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('instalasi_nama', '',
                    ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'),
                        [
                            'id' => 'filter_instalasi_nama',
                            'class' => 'form-control select2 selectInstalasi',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                10,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        DepDrop::widget([
                            'name' => 'ruangan_nama',
                            //'data'=> [
                            //    Yii::$app->docoVars->workspace("ruangan_id") => Yii::$app->docoVars->workspace("ruangan_name")
                            //],
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2 selectRuangan'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi_nama'],
                               'placeholder' => '--Pilih Semua--',
                               'url' => Url::to(['/rm/lap-kunjungan/get-ruangan'])
                            ]
                        ])
                ))."<div>\"
            ],

            [
                29,
                    \"<div class='input-group' style='margin-bottom:0px !important;'><input  type='text'  class='form-control picker__input' data-mask='99-99-9999' id='dateOfBirth'/><label class='input-group-addon btn' style='padding:5px;' id='btn-date-of-birth'><span class='fa fa-calendar'></span></label>\"
            ],

            [
                18,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('nama_pegawai', '',
                    ArrayHelper::map($api['response']['dokter'], 'nama_pegawai', 'nama_pegawai'),
                        [
                            'id' => 'filter_dokter_admisi',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                19,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('diagnosa_utama', '',[],
                        [
                            'id' => 'select-diagnosa-utama',
                            'class' => 'form-control select2 search-diag-utama',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                20,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('diagnosa_penyerta', '',[],
                        [
                            'id' => 'select-diagnosa-penyerta',
                            'class' => 'form-control select2 search-diag-penyerta',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                21,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('status_periksa', '',
                    ArrayHelper::map($api['response']['statusPeriksa'], 'lookup_id', 'lookup_name'),
                        [
                            'id' => 'filter_status_periksa',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                26,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kondisikeluar_id', '',
                    ArrayHelper::map($api['response']['kondisi_keluar'], 'kondisikeluar_id', 'kondisikeluar_nama'),
                        [
                            'id' => 'filter_kondisikeluar_id',
                            'class' => 'form-control select2 selectKondisiKeluar',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                27,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('carakeluar_id', '',
                    ArrayHelper::map($api['response']['cara_keluar'], 'carakeluar_id', 'carakeluar_nama'),
                        [
                            'id' => 'filter_carakeluar_id',
                            'class' => 'form-control select2 selectCaraKeluar',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                28,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kunjungan', '',
                    ArrayHelper::map($api['response']['kunjungan'], 'lookup_id', 'lookup_name'),
                        [
                            'id' => 'filter_kunjungan',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                30,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kelas_pelayanan', '',
                    ArrayHelper::map($api['response']['kelas_pelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama'),
                        [
                            'id' => 'filter_kelaspelayanan',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
        ],
        {
            //posisi kolom dan grid
            2 : 0,
            4 : 1,
            5 : 2,
            13: 3,
            29: 4,
            6 : 5,
            7 : 6,
            8 : 7,
            30: 8,
            9 : 9,
            10: 10,
            16: 11,
            18: 12,
            19: 13,
            20: 14,
            21: 15,
            26: 16,
            27: 17,
            28: 18,
        });

        dateRangeHelper('.startDate','.endDate','.targetDate');

        $(document).on('click', '#btn-date-of-birth', function() {
            var endDates = $('#dateOfBirth').pickadate({
                editable: true,
                format:'dd-mm-yyyy',
                formatSubmit:'dd-mm-yyyy',
                selectMonths: true,
                selectYears: true,
                // onClose: function() {
                //     $('.datepicker').focus();
                // }
            });


            var picker_endDate = endDates.pickadate('picker');
            if (picker_endDate.get('open')) {
                picker_endDate.close();
            } else {
                picker_endDate.open();
            }
        });


        $('.search-diag-utama').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '".Url::to(['get-icd'])."',
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                  params.type_icd = 'ICD X';
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
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            },
        });

        $('.search-diag-penyerta').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '".Url::to(['get-icd'])."',
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                  params.type_icd = 'ICD X';
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
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            },
        });

        $(document).on('click', '#lap-kunjung tbody tr', function () {
            var norm = (typeof table.row('.selected').data() != 'undefined') ? table.row('.selected').data().primary : null;
            var id_status_periksa = (typeof table.row('.selected').data() != 'undefined') ? table.row('.selected').data().id_status_periksa : null;
            var enc_pendaftaran_id = (typeof table.row('.selected').data() != 'undefined') ? table.row('.selected').data().enc_pendaftaran_id : null;
            var admisi = (typeof table.row('.selected').data() != 'undefined') ? table.row('.selected').data().admisi : null;
            var is_status_periksa = (typeof table.row('.selected').data() != 'undefined') ? table.row('.selected').data().is_status_periksa : null;

            // if (norm) {
            //     $('#btn-riwayat-pasien').attr('data-url', '".Url::home()."'+'igd/riwayat-pasien/index?modal=is_modal&norm=');
            // } else {
            //     $('#btn-riwayat-pasien').attr('data-url', null);
            // }

            if (id_status_periksa) {
                if (is_status_periksa == true || is_status_periksa == 't') {
                    $('#btn-koreksi-diagnosa').attr('data-target', '".Url::home()."'+'rm/info-kunjungan-pasien/koreksi-diagnosa?id='+enc_pendaftaran_id+'&admisi='+admisi+'&is_koreksi=true');
                    $('#btn-koreksi-diagnosa').prop('disabled',false);
                } else {
                    $('#btn-koreksi-diagnosa').attr('data-target', null);
                    $('#btn-koreksi-diagnosa').prop('disabled',true);
                }
            } else {
                $('#btn-koreksi-diagnosa').attr('data-target', null);
                $('#btn-koreksi-diagnosa').prop('disabled',true);
            }

        });

        $(document).on('click', '#btn-riwayat-pasien', function() {
            if (typeof table.row('.selected').data() !== 'undefined') {
                let norm = table.row('.selected').data().no_rekam_medik;
                if (norm) {
                        $('#btn-hidden-riwayat-pasien').attr('action', '/igd/riwayat-pasien/history-patient?norm=' + norm + '&modal=is_modal');
                        $('#btn-hidden-riwayat-pasien').trigger('click');
                    } else {
                        $('#btn-hidden-riwayat-pasien').attr('action', '');
                    }
            } else {
                docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
                return true;
            }
        });

        // $(document).on('click', '#btn-riwayat-pasien', function() {
        //     if (typeof table.row('.selected').data() === 'undefined') {
        //         docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');

        //         return true;
        //     }

        //     window.open($(this).attr('data-target'), '_blank')
        // });

        // koreksi diagnosa

        $(document).on('click', '#btn-koreksi-diagnosa', function() {
            if (typeof table.row('.selected').data() === 'undefined') {
                docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');

                return true;
            }

            window.open($(this).attr('data-target'),'_self')
        });

        $(document).on('change', '.inputDate', function() {
            const btnExcel = $('#data-export-excel-serconn');
            const startDate = new Date($('.startDate').val());
            const endDate = new Date($('.endDate').val());
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const maxDate = 31;

            if(diffDays > maxDate) {
                btnExcel.prop('disabled', true);
                docoNotification('warning', 'Perhatian', 'Tidak dapat mengunduh laporan lebih dari 31 hari');

                return true;
            } else {
                btnExcel.prop('disabled', false);
            }
        });

        if(uid != '') {
            docoNotification('warning', 'Perhatian', 'Data excel sedang diproses! <br/> Mohon tunggu beberapa saat!', false);
            var getLinkExcel = setInterval(
                function(){
                    $.ajax({
                        type: 'GET',
                        url: '/rm/lap-kunjungan/download-excel?uid=' + uid,
                        dataType: 'JSON',
                        success: function (res) {
                            var download_url = res.download_url;

                            if(download_url != null && download_url != '' && download_url != false){
                                clearInterval(getLinkExcel);
                                window.open(download_url, '_blank');
                                window.close();
                            }
                        },
                        error: function (err) {
                            console.log(err);
                            clearInterval(getLinkExcel);
                        }
                    });
                },
                5000  /* 5 sec */
            );
        }

        });
        ",View::POS_END)

        ?>
