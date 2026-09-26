<?php

/**
 * @Author: afil
 * @Date:   2018-02-26 07:58:44
 * @Last Modified by:   afil
 * @Last Modified time: 2018-02-26 07:58:44
 *
 * @Refactored by : Anggoro
 * @Refactored time : 2019-07-01
 *
 * @Description: Pendaftaran IGD / Rawat Darurat
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    .sweet-alert {
        z-index: 1065 !important;
    }
    .border-radius-top {
        border-top-right-radius: 26px !important;
        border-top-left-radius: 26px  !important;
    }
    .bg-indigo-300 {
        background-color: #49ce8e6b !important;
        border-color: #7986CB !important;
    }
</style>
<input type="hidden" name="" class="params-header" value="<?=isset($params) ? $params : '' ?>">
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumb -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias") ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                    <button type="button" class="btn btn-primary btn-icon btn-rounded pull-right" 
                        data-popup="popover-custom" 
                        data-placement="left" 
                        title="" 
                        data-html="true"
                        data-content="
                        <b>Alt + S</b> Simpan pasien / simpan kunjungan <br>
                        <b>F8</b> Pencarian Lanjutan<br>
                        <b>F9</b> Pasien Lama / Pasien Baru" 
                        data-original-title="Informasi Aplikasi"
                        data-trigger="focus"
                        style="padding:0px 8px!important;background-color: rgba(0, 0, 0, 0.5)!important;">
                        <i class="fa fa-info"></i>
                    </button>
                </div>
                <!-- end of breadcrumb -->

            </div>

            <div class="panel panel-white">
                <div class="panel-body no-border">

                    <!-- Data Pasien -->
                    <?php echo Yii::$app->controller->renderPartial('partial/_pasien', $render_pasien_data);?>
                    <!-- End of Data Pasien -->

                    <!-- Data Kunjungan -->
                        <?php
                            echo Yii::$app->controller->renderPartial(
                                'partial/_formkunjunganranap', array_merge($render_ranap_data, ['masterWarnaTempatTidur' => $masterWarnaTempatTidur])
                            );
                         ?>
                    <!-- End of Data Kunjungan -->

                    <div class="row">
                        <div class="col-md-12">
                            <?= Yii::$app->controller->renderPartial('partial/_tarifkarcis',[]);?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

&nbsp;
<div class="clearfix"></div>
<!-- GLOBAL DAFTAR 10 TERAKHIR (RJ, RD, RI) -->

<?= Yii::$app->controller->renderPartial('../daftar/partial/component/_sepuluhterakhir_global', [
    'param' => $params,
    'module' => $module,
]);?>

<div id="modal_pencarian_lanjutan" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<div id="modal_pencarian_identitas" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<button class="btn btn-primary grid-button btn-sm btn-form-bpjs" 
        data-toggle="modal"
        data-target="#modal_backdrop" 
        data-width="90%" 
        action="/pendaftaran/daftar-ranap/get-form-bpjs?pendaftaran_id=364&no_rekam_medik=00005"
        style="display: none">Tampilkan</button>

<button 
    class="btn btn-primary grid-button btn-sm btn-form-asuransi hidden"
    data-toggle="modal"
    data-target="#modal_backdrop" 
    data-width="75%">Tampilkan</button>

<?php
    $_rujukanDari = json_encode($rujukan_dari);
    $_rujukRanap = json_encode($rujukRanap);
    $this->registerJs("
    var _rujukanDari = $_rujukanDari
    var paramsPelayanan = '".$params."';
    var penjamin_umum = '".$penjamin_umum."';
    var rujukan_datang_sendiri = '".$rujukan_datang_sendiri."';
    var _rujukRanap = $_rujukRanap;
    var title_karcis = '".json_encode($render_kunjungan_data['data_lookup']['title_pendaftaran'][0]['lookup_value'])."';
    var defaultAsalRujukan = '".$getDefaultAsalRujukan."'
    var bpjsScenario = '".$render_pasien_data['modelBpjs']->scenario."'

    const oLanguage = {
        sLengthMenu: '".Yii::t('fe', 'dt_length_menu')."',
        sZeroRecords: '".Yii::t('fe', 'dt_zero_records')."',
        sEmptyTable: '".Yii::t('fe', 'dt_empty_table')."',
        sInfoFiltered: '".Yii::t('fe', 'dt_info_filtered')."',
        sInfoEmpty: '".Yii::t('fe', 'dt_info_empty')."',
        sInfo: '".Yii::t('fe', 'dt_info')."',
        oPaginate: {
            sFirst: '".Yii::t('fe', 'dt_first_page')."',
            sPrevious: '".Yii::t('fe', 'dt_previous_page')."',
            sNext: '".Yii::t('fe', 'dt_next_page')."',
            sLast: '".Yii::t('fe', 'dt_last_page')."'
        }
    }
    const columns = [
        { title: 'No', data: 'rowNum', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Jenis Kasus Penyakit')."', data: 'jeniskasuspenyakit_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Ruangan')."', data: 'ruangan_nama', searchable: false, orderable: false},
        {title: '".Yii::t('fe', 'Kamar')."', data: 'kamarruangan_nokamar', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Kelas')."', data: 'kelaspelayanan_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'No Tempat Tidur')."', data: 'datakamar', searchable: false, orderable: false },
    ]
    const columnKamarTitipan = [
        { title: 'No', data: 'rowNum', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Jenis Kasus Penyakit')."', data: 'jeniskasuspenyakit_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Ruangan')."', data: 'ruangan_nama', searchable: false, orderable: false},
        {title: '".Yii::t('fe', 'Kamar')."', data: 'kamarruangan_nokamar', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Kelas')."', data: 'kelaspelayanan_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Harga')."', data: 'harga_tariftindakan', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Aksi')."', data: 'datakamar', searchable: false, orderable: false },
    ]

    const columnKamarAps = [
        { title: 'No', data: 'rowNum', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Jenis Kasus Penyakit')."', data: 'jeniskasuspenyakit_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Ruangan')."', data: 'ruangan_nama', searchable: false, orderable: false},
        {title: '".Yii::t('fe', 'Kamar')."', data: 'kamarruangan_nokamar', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Kelas')."', data: 'kelaspelayanan_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'No Tempat Tidur')."', data: 'datakamar', searchable: false, orderable: false },
    ]

    const columnDaftarTerakhir = [
        {
            data: null,
            searchable: false,
            orderable: false,
            defaultContent: '',
        },
        {
            title: 'No',
            data: 'rowNum',
            searchable: false,
            orderable: false
        },
        {title: '".Yii::t('fe', 'Tanggal Pendaftaran')."',  data: 'tgl_pendaftaran'},
        {title: '".Yii::t('fe', 'No Pendaftaran')."',  data: 'no_pendaftaran'},
        {title: '".Yii::t('fe', 'No Rekam Medik')."', data: 'no_rekam_medik'},
        {title: '".Yii::t('fe', 'Nama Pasien')."', data: 'nama_pasien'},
        {title: '".Yii::t('fe', 'Umur')."', data: 'umur'},
        {title: '".Yii::t('fe', 'Jenis Kelamin')."', data: 'jenis_kelamin'},
        {title: '".Yii::t('fe', 'Poliklinik')."', data: 'ruangan_nama'},
        {title: '".Yii::t('fe', 'Dokter')."', data: 'nama_pegawai'},
        {title: '".Yii::t('fe', 'Cara Bayar')."', data: 'carabayar_nama'},
        {title: '".Yii::t('fe', 'Penjamin')."', data: 'penjamin_nama'},
        {
            data: 'primaryPasien',
            searchable: false,
            orderable: false,
            visible: false,
        },
        {
            data: 'primaryPendaftaran',
            searchable: false,
            orderable: false,
            visible: false,
        },
    ]
    ",View::POS_END, "js-kuning");
    $this->registerJs(""
        .$this->render('js/index.js')
        .$this->render('js/pasien.js')
    , View::POS_END, "js-index");
    $this->registerJs($this->render('js/ranap.js'), View::POS_END);
    $this->registerJs($this->render('js/datatable-kamar.js'), View::POS_END);
    $this->registerJs($this->render('js/datatable-kamar-titipan.js'), View::POS_END);
    $this->registerJs($this->render('/daftar/partial/component/js/shortcut-tab.js'), View::POS_END);
    $this->registerJs($this->render('/daftar/partial/component/js/bpjs-helper.js'), View::POS_END);

 ?>