<?php

/**
 * @Author: sunarko
 * @Date:   2018-06-05 14:00:43
 * @Last Modified by:   Doconb-Bandung
 * @Description:
 */

use app\components\DocoConstants;
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

$this->title = isset($title) ? $title : Yii::t('fe', 'Informasi Pasien Rawat Inap');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")) , 'url' => ['/ranap']];
//$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat inap'), 'url' => ['/ranap/inf-pasien-ranap']];
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
        width: 80px;
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
        width: 80px;
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
                    'pdf' => [
                      'title' => Yii::t('fe', 'Cetak'),
                      'attributes'=>[
                          'data-target'=>Url::home().'ranap/inf-pasien-ranap/export-pdf?jenis=ranap&'
                      ],
                    ],
                    // 'rincian' => [
                    //     'title' => Yii::t('fe', 'Rincian Tagihan'),
                    //     'icon'=> 'fa fa-file-pdf-o',
                    //     'attributes' => [
                    //         'data-target' => Url::home() . 'ranap/inf-pasien-ranap/export-rincian-tagihan-pdf?pendaftaran_id='
                    //     ],
                    // ],
                    'rincian' => [
                        'title' => Yii::t('fe', 'Cetak Rincian'),
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            'id'=>'cetak-rincian-tagihan',
                            'data-options' => 'link',
                            'class'=>'spa',
                            'data-target' => '/ranap/inf-pasien-ranap/export-rincian-tagihan-pdf?pendaftaran_id=',
                            'disabled'=>'true',
                            'target'=>'_blank'
                        ]
                    ],
                    'detail-rincian' => [
                        'title' => Yii::t('fe', 'Cetak Detail Rincian'),
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            'id'=>'cetak-detail-rincian',
                            'data-options' => 'link',
                            'class'=>'spa',
                            'data-target' => '/ranap/inf-pasien-ranap/export-detail-rincian?pendaftaran_id=',
                            'disabled'=>'true',
                            'target'=>'_blank'
                        ]
                    ],
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
                            'data-url' => '/ranap/inf-pasien-ranap/show-popup-excel?jenis=ranap&allruangan=' . $all_ruangan . '&',
                        ]
                    ],
                    'periksa' => [
                        'title' => \Yii::t('fe', 'Periksa'),
                        'icon' => 'fa fa-stethoscope',

                        'attributes' => [
                            'data-target'=> '/ranap/pemeriksaan-rawat-inap/periksa?id=',
                        ]
                    ],
                    'pindah' => [
                        'title' => \Yii::t('fe', 'Pindah'),
                        'icon' => 'fa fa-square',
                        'attributes' => [
                            'data-target'=> '/ranap/inf-pasien-ranap/pindah-kamar?id=',
                        ]
                    ],
                    'print-gelang'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cetak Gelang'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'btn-print-gelang',
                            'class'=>'btn-print-gelang',
                            'data-options'=>'click',
                            'data-target'=> '/ranap/inf-pasien-ranap/print-gelang?id=',
                            'style' => 'display:none'
                        ]
                    ],
                    'stop-pasientitipan'=>[
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Stop Pasien Titipan'),
                        'icon' => 'fa fa-ban',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-stop-pasientitipan',
                            'class' => 'btn-stop-pasientitipan',
                            'data-options' => 'click'
                        ]
                    ],
                    'stop-akomodasi'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Stop Akomodasi'),
                        'icon' => 'fa fa-times',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'btn-stop-akomodasi',
                            'class'=>'btn-stop-akomodasi',
                            'data-options'=>'click',
                            'disabled' => true,
                        ]
                    ],
                    'cetak-surat' => [
                        'title' => \Yii::t('fe', 'Cetak Surat Kelahiran'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id'=>'btn-cetak-surat-keterangan-kelahiran',
                            'data-options' => 'link',
                            'class'=>'spa',
                            'data-target' => '/ranap/inf-pasien-ranap/export-surat-keterangan-kelahiran?pendaftaran_id=',
                            'disabled'=>'true',
                            'target'=>'_blank'
                        ]
                    ],
                    'cetak-r2bbl' => [
                        'title' => \Yii::t('fe', 'Cetak Surat R2BBL'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id'=>'btn-cetak-surat-r2bbl',
                            'data-options' => 'link',
                            'class'=>'spa',
                            'data-target' => '/ranap/inf-pasien-ranap/export-surat-r2bbl?pendaftaran_id=',
                            'disabled'=>'true',
                            'target'=>'_blank'
                        ]
                    ],
                    'pulang'=>[
                        'title' => \Yii::t('fe', 'Pulang'),
                        'icon' => 'fa fa-home',
                        'attributes' => [
                            'data-target'=> '/ranap/inf-pasien-ranap/pasien-pulang?id=',
                            'id' => 'btn-pulang',
                            'disabled' => true
                        ]
                    ],

                    // 'edit' => [
                    //     'title' => \Yii::t('fe', 'Batal'),
                    //     'attributes' => [
                    //         'id' => 'data-batal',
                    //         'data-options'=>'modal',
                    //         'data-target'=>'#modal_backdrop',
                    //         'data-url' => '/ranap/inf-pasien-ranap/aksi-batal?id=',
                    //     ]
                    // ],
                ], '#example');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <!--<div class="col-md-12 filter-form"></div>-->
                </div>
                <div class="advanced-filter">
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                            <ul class='legend-labels'>
                                <li><span style='background:#F5D76E;'></span>PASIEN KONSUL</li>
                                <li><span style='background:#FFFfff;'></span>PASIEN NON KONSUL</li>
                                <li><span style='background:#FFCCCC;'></span>PASIEN TITIPAN</li>
                                <li><span style='background:#7efff5;'></span>PASIEN STOP AKOMODASI</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
                if($legend_cara_bayar){
                ?>
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan Cara Bayar</div>
                            <div class='legend-scale'>
                            <ul class='legend-labels'>
                                <?php 
                                foreach ($legend_cara_bayar as $key => $value) {
                                    echo "<li><span style='background:".$value['carabayar_kode_warna']."'></span>".$value['carabayar_nama']."</li>";
                                }
                                ?>
                            </ul>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
                <div class="col-md-12">
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed"
                    id="example"
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?=Yii::t('fe', 'Tgl.Admisi')?></th>
                            <th></th>
                            <th><?=Yii::t('fe', 'No.RM / No.Pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'No.RM')?></th>
                            <th><?=Yii::t('fe', 'No.Pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'Nama Pasien')?></th>
                            <th><?=Yii::t('fe', 'Jenis Kelamin')?></th>
                            <th><?=Yii::t('fe', 'Dokter DPJP')?></th>
                            <th></th>
                            <th><?=Yii::t('fe', 'Cara Bayar / Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Cara Bayar')?></th>
                            <th><?=Yii::t('fe', 'Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Hak Kelas / Kelas saat ini / Kelas Tagihan')?></th>
                            <th><?=\Yii::t("fe", "Status Kamar");?></th>
                            <th><?=Yii::t('fe', 'Hak Kelas')?></th>
                            <th><?=Yii::t('fe', 'Kelas saat ini')?></th>
                            <th><?=Yii::t('fe', 'Kasus Penyakit')?></th>
                            <th><?=Yii::t('fe', 'Nama Ruangan No.Kamar-No.Bed')?></th>
                            <th><?=Yii::t('fe', 'Nama Ruangan')?></th>
                            <th><?=Yii::t('fe', 'No.Kamar')?></th>
                            <th><?=Yii::t('fe', 'No.Bed')?></th>
                            <th><?=Yii::t('fe', 'Hari Rawat')?></th>
                            <th><?=Yii::t('fe', 'Tanggal Pindah')?></th>
                            <th><?=Yii::t('fe', 'Rencana Pulang')?></th>
                            <th></th>
                            <th></th>
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

<?php

$this->registerJs("
    var table;
    var _isPeriksa = '$_isPeriksa';
    var _allRuangan = '$all_ruangan';
    
    const stopAkomodasi = '/ranap/inf-pasien-ranap/stop-akomodasi?pendaftaran_id=';
    const cetakRincian = '/ranap/inf-pasien-ranap/export-rincian-tagihan-pdf?pendaftaran_id=';
    const cetakDetailRincian = '/ranap/inf-pasien-ranap/export-detail-rincian?pendaftaran_id=';
    const stopPasienTitipan = '/ranap/inf-pasien-ranap/stop-pasien-titipan?pendaftaran_id=';
    const cetakSuratKeteranganKelahiran = '/ranap/inf-pasien-ranap/export-surat-keterangan-kelahiran?pendaftaran_id=';
    const cetakSuratR2bbl = '/ranap/inf-pasien-ranap/export-surat-r2bbl?pendaftaran_id=';

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {

        const tgl_masuk = $('.tgl_masuk').val();
        let yesterday = new Date(tgl_masuk);
        yesterday.setDate(yesterday.getDate() - 1);

        $('.date').pickadate({
            formatSubmit: 'yyyy-mm-dd',
            format: 'dd mmmm yyyy',
            disable: [{
                from: [0, 0, 0],
                to: yesterday
            }],
            onStart: function () {
                var date = new Date();
                this.set('select', tgl_masuk)
            }
        });

        // Generate Table
        table = $('#example').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ 
                {
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0,
                    checkboxes: {
                        selectRow: true
                    }
                },
            ],
            select: {
                style: 'os',
                selector: 'tr'
            },
            sorting: [[3, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'ranap/inf-pasien-ranap/get-data?all_ruangan=' + _allRuangan,
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
                {title: '".(\Yii::t('fe', 'Tgl masuk'))."', data: 'tgl_admisi_format',searchable: false},
                {title: '".(\Yii::t('fe', 'Tanggal Masuk'))."', data: 'tgl_admisi',visible:false},
                {title: '".(\Yii::t('fe', 'Pasien'))."', data: 'gab_noRmPdft',searchable: false},
                {title: '".(\Yii::t('fe', 'No. Rekam Medik'))."', data: 'no_rekam_medik',visible:false},
                {title: '".(\Yii::t('fe', 'No. Pendaftaran'))."', data: 'no_pendaftaran',visible:false},
                {title: '".(\Yii::t('fe', 'Nama Pasien'))."',  data: 'nama_pasien' , render: (col, type, data) => {
                    return (data.is_bayi ? data.nama_depan+' ' : '') + col
                }, className: 'hidden'},
                {title: '".(\Yii::t('fe', 'Jenis Kelamin'))."', data: 'jenis_kelamin',searchable: false, className: 'hidden'},
                {title: '".(\Yii::t('fe', 'Dokter DPJP'))."', data: 'dokter_admisi',searchable: false},
                {title: '".(\Yii::t('fe', 'Dokter Penanggung Jawab'))."', data: 'dokter_admisi_id',visible:false}, // 10
                {title: '".(\Yii::t('fe', 'Cara Bayar / Penjamin'))."', data: 'carBay',searchable: false},
                {title: '".(\Yii::t('fe', 'Cara Bayar'))."', data: 'carabayar_nama',visible:false, name: 'carabayar_id'},
                {title: '".(\Yii::t('fe', 'Penjamin'))."', data: 'penjamin_nama',visible:false, name: 'penjamin_id'},
                {title: '".(\Yii::t('fe', 'Hak Kelas / Kelas Saat Ini / Kelas Tagihan'))."', data: 'hakKelas',searchable: false},
                {title: '".Yii::t('fe', 'Status Kamar')."', data: 'status_kamar', searchable: false},
                {title: '".(\Yii::t('fe', 'Hak Kelas'))."', data: 'hak_kelas',visible:false},
                {title: '".(\Yii::t('fe', 'Kelas Dirawat'))."', data: 'kelas_pelayanan',visible:false, name:'kelaspelayanan_id'},
                {title: '".(\Yii::t('fe', 'Kasus Penyakit'))."', data: 'jeniskasuspenyakit_nama', searchable:false},
                {title: '".(\Yii::t('fe', 'Nama Ruangan No.Kamar-No.Bed'))."', data: 'noRuangannya',searchable: false},
                {title: '".(\Yii::t('fe', 'Nama Ruangan'))."', data: 'ruangan_id',visible:false},
                {title: '".(\Yii::t('fe', 'Status Periksa'))."', data: 'stat_ranap',searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', 'No.Kamar'))."', data: 'kamarruangan_nokamar',searchable: false,visible:false}, // 20
                {title: '".(\Yii::t('fe', 'No.Bed'))."', data: 'no_tempattidur',searchable: false,visible:false},
                {title: '".(\Yii::t('fe', 'Hari Rawat'))."', data: 'hariRawat',searchable: false,visible:false},
                {title: '".(\Yii::t('fe', 'Tanggal Pindah'))."', data: 'tgl_pindahkamar_format',searchable: false},
                {title: '".(\Yii::t('fe', 'Rencana Pulang'))."', data: 'rencana_pulang_format',searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', 'Kasus Penyakit'))."', data: 'jeniskasuspenyakit_id', visible:false},
                {title: '".(\Yii::t('fe', 'Status Pasien'))."', data: 'is_stopakomodasi', visible:false},
            ],

            rowCallback: function(row, data, index) {
                if(data.is_konsul == true){
                    $(row).css('background-color', '#fdfd96');
                } else if (data.is_titip == true){
                    $(row).css('background-color', '#FFCCCC');
                } else if (data.is_stopakomodasi) {
                    $(row).css('background-color', '#7efff5');
                }
                var textColor = invertColor(data.carabayar_kode_warna,data.carabayar_kode_warna);
                $('td:eq(7)', row).css('background-color', data.carabayar_kode_warna);
                $('td:eq(7)', row).css('color', textColor);
            }
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [
            [
                3,
                \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                6,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::textInput('no_pendaftaran', '',
                            [
                                'class' => 'form-control no_pendaftaran',
                                'prompt' => 'No. Pendaftaran',
                                'placeholder'=> 'No. Pendaftaran'
                            ]
                        )
                    )
                )."<div>\"
            ],
            [
                5,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::textInput('no_rekam_medik', '',
                            [
                                'class' => 'form-control no_rekam_medik',
                                'prompt' => 'No. Rekam Medik',
                                'placeholder'=> 'No. Rekam Medik'
                            ]
                        )
                    )
                )."<div>\"
            ],
            [
                7,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::textInput('nama_pasien', '',
                            [
                                'class' => 'form-control nama_pasien',
                                'prompt' => 'Nama Pasien',
                                'placeholder'=> 'Nama Pasien'
                            ]
                        )
                    )
                )."<div>\"
            ],
            [
                10,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('dokter_admisi', '',$getlist_dokter,
                        [
                            'id' => 'filter_dokter_admisi',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Dokter Penanggung Jawab--'),
                        ]
                    )
                ))."<div>\"
            ], [
                12,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('carabayar_nama', '',$listcarabayar,
                        [
                            'id' => 'filter_carabayar',
                            'class' => 'form-control select2 dep-to-child',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Cara bayar--'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/inf-pasien-ranap/get-penjamin',
                            'data-depend_id' => 'filter_penjamin',
                            'data-depend_prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            'data-storage' => 'penjamin',
                            'data-key' => 'penjamin_nama',
                        ]
                    )
                ))."<div>\"
            ],
            [
                13,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('penjamin_nama', '',[],
                        [
                            'id' => 'filter_penjamin',
                            'class' => 'form-control select2 dep-to-parent',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            // 'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/inf-pasien-ranap/get-carabayar',
                            'data-depend_id' => 'filter_carabayar',
                        ]
                    )
                ))."<div>\"
            ],
            [
                16,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('hak_kelas', '',
                        ArrayHelper::map($lookup_kelas_bpjs, 'lookup_id', 'lookup_name'),
                        [
                            'id' => 'filter_hak_kelas',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                17,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kelas_pelayanan', '',$listkelaspelayanan,
                        [
                            'id' => 'filter_kelas_pelayanan',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                20,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('ruangan_id', '',$listruangan,
                        [
                            'id' => 'filter_ruangan_id',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                27,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('jenis_kasus_penyakit', '',$getjeniskasuspenyakit,
                        [
                            'id' => 'filter_jenis_kasus_penyakit',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                28,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('status_pasien', '', $statusPasien,
                        [
                            'id' => 'filter_status_pasien',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Status Pasien--'),
                        ]
                    )
                ))."<div>\"
            ],
        ],{
            3:0,
            6:1,
            5:2,
            7:3,
            10:4,
            12:5,
            13:6,
            16:7,
            27:8,
            20:9,
            17:10,
            28:11
        });

        dateRangeHelper('.startDate','.endDate','.targetDate',true);

        $(document).on('click', '#example tbody tr', function () {
            try {
                $('#cetak-rincian-tagihan').attr('disabled', true);
                $('#cetak-detail-rincian').attr('disabled', true);
                $('#tn-cetak-surat').attr('disabled', true);
                $('#data-batal').attr('disabled', true);
                status_ranap = table.row('.selected').data().status_ranap ? table.row('.selected').data().status_ranap : null;
                status_implementasi = table.row('.selected').data().status_implementasi;
                pendaftaran_id = table.row('.selected').data().pendaftaran_id ? table.row('.selected').data().pendaftaran_id : null;
                tagihan_rs = table.row('.selected').data().tagihan_rs ? table.row('.selected').data().tagihan_rs : 0;
                is_bayi = table.row('.selected').data().is_bayi ? table.row('.selected').data().is_bayi : 0;
                data = table.row('.selected').data() ? table.row('.selected').data() : null;
            } catch (e) {
                pendaftaran_id = false;
                status_ranap = null;
                tagihan_rs = 0
            }

            if (tagihan_rs > 0 ) {
                   $('#cetak-rincian-tagihan').attr('data-target',cetakRincian+pendaftaran_id);
                   $('#cetak-rincian-tagihan').attr('disabled', false);

                   $('#cetak-detail-rincian').attr('data-target',cetakDetailRincian+pendaftaran_id);
                   $('#cetak-detail-rincian').attr('disabled', false);
            }
            $('#btn-pulang').prop('disabled', !(data.is_stopakomodasi && data.pasienpulang_id == null && (data.penjamin_id != " . DocoConstants::VAR_P_Perseorangan . " || (data.penjamin_id == " . DocoConstants::VAR_P_Perseorangan . " && data.status_bayar == " . DocoConstants::STAT_BAYAR_LUNAS . "))))
            if(status_ranap == _isPeriksa){
                $('#data-batal').attr('disabled', true);
                $('#btn-stop-akomodasi').attr('data-implementasi', status_implementasi);
                $('#btn-stop-akomodasi').attr('data-target',stopAkomodasi+pendaftaran_id);
                $('#btn-stop-akomodasi').attr('disabled', data.is_stopakomodasi);
            }else{
                $('#data-batal').attr('disabled', false);
                $('#btn-stop-akomodasi').attr('disabled', true);
            }

            $('#btn-stop-pasientitipan').attr('data-target', stopPasienTitipan+pendaftaran_id);
            if(is_bayi == true){
                $('#btn-cetak-surat-keterangan-kelahiran').attr('data-target',cetakSuratKeteranganKelahiran+pendaftaran_id);
                $('#btn-cetak-surat-keterangan-kelahiran').attr('disabled', false);
                $('#btn-cetak-surat-r2bbl').attr('data-target',cetakSuratR2bbl+pendaftaran_id);
                $('#btn-cetak-surat-r2bbl').attr('disabled', false);
            }else{
                $('#btn-cetak-surat-keterangan-kelahiran').attr('disabled', true);
                $('#btn-cetak-surat-r2bbl').attr('disabled', true);
            }
        });

        $(document).on('click', '#btn-print-gelang', function(){
            window.open($(this).attr('data-target'), '_blank')
        });

        $(document).on('click', '#btn-stop-akomodasi', function(event){
            event.preventDefault();
            var _url = $(this).attr('data-target');
            var _status_implementasi = $(this).attr('data-implementasi');
            var _msg = (_status_implementasi == 'false') ? 'Terdapat instruksi yang belum di implementasi, apakah anda yakin?' : 'Apakah anda yakin akan menghentikan akomodasi ini ?';

            $(this).docoForm('click', {
                url: _url,
                method: 'GET',
                type: 'JSON',
                confirmMessage: _msg,
                success: function(data) {
                    table.draw();
                }
            });
        });

        $(document).on('click', '#btn-stop-pasientitipan', function(event) {
            event.preventDefault();
            var _url = $(this).attr('data-target');
            var _msg = 'Apakah anda yakin akan menghentikan kelas titipan pasien ini ?';

            try {
                pendaftaran_id = table.row('.selected').data().pendaftaran_id ? table.row('.selected').data().pendaftaran_id : null;
            } catch (e) {
                pendaftaran_id = false;
            }

            if (typeof _url === 'undefined') {
                docoNotification('warning', 'Perhatian!', 'Belum ada data yang dipilih.');
                return;
            }

            if (pendaftaran_id == false) {
                docoNotification('warning', 'Perhatian!', 'Belum ada data yang dipilih.');
                return;
            }

            $(this).docoForm('click', {
                url: _url,
                method: 'GET',
                type: 'JSON',
                confirmMessage: _msg,
                success: function (data) {
                    table.draw();
                }
            });
        });
    });

    ", View::POS_END, 'b-index');
?>