<?php

/**
 * @author Naufal Ziyad L
 * @copyright 15 Februari 2018
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;
use app\widgets\DHDatePickerWidget;
use app\widgets\DHDateRangePickerWidget;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$instalasi_id = DocoHelpers::encrypt(Yii::$app->docoVars->workspace('instalasi_id'));

?>
<style>
.dataTables_scroll {
    max-height: 510vh !important;
}
.row {
    padding-right: 5px;
}

#modal_backdrop {
    z-index: 1041 !important;
    max-height: calc(100vh);
    overflow-y: auto;
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
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'update' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-pencil',
                            'method' => '',
                            'attributes' => [
                                'id' => 'btn-update',
                                'disabled' => true,
                                'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/update?id=',
                            ],

                        ],
                        'riwayat' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Riwayat Kunjungan'),
                            'icon' => 'fa fa-eye',
                            'method' => '',
                            'attributes' => [
                                'data-pages' => '_blank',
                                'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/riwayat?id=',
                            ]
                        ],
                        'pdf'=> $cetakPdfButton,
                        'cetak-data-pasien' => $cetakDataPasienButton,
                        // 'excel',
                        'excel-bg' => $cetakExcelButton,
                        'cetak-kartu' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Cetak Kartu'),
                            'icon' => 'fa fa-print',
                            'method' => '',
                            'attributes' => [
                                'data-options'=>'click',
                                'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/cetak-kartu?',
                            ]
                        ],
                        'riwayat-pasien' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Riwayat Pasien'),
                            'icon' => 'fa fa-user',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'btn-riwayat-pasien',
                                'data-options' => 'click',
                                'data-target'=> ''
                            ]
                        ],
                        'terra-medik-soap' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Arsip Riwayat Pasien'),
                            'icon' => 'fa fa-history',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'btn-history-terra-medik',
                                'data-options' => 'click',
                                'data-target'=> ''
                            ]
                        ],
                        'buka-akses' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Buka Akses'),
                            'icon' => 'fa fa-pencil',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'btn-buka-akses',
                                'data-width'  => '90%',
                                'data-options'=>'modal',
                                'data-target'=>'#modal_backdrop',
                                'data-url' => '/pendaftaran/informasi-pencarian-pasien/buka-akses?pasien_id=',
                            ]
                        ],
                        'profil-ringkas-medis-rj' => $modalPrmjButton,
                        'upload-dokumen' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Upload Dokumen'),
                            'icon' => 'fa fa-upload',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'btn-upload-dokumen',
                                'disabled' => true,
                                'data-width'  => '95%',
                                'data-options'=>'modal',
                                'data-target'=>'#modal_backdrop',
                                'data-url' => '/pendaftaran/informasi-pencarian-pasien/upload-dokumen?id=',
                            ]
                        ],
                    ],'#table-informasi-pasien');?>
                <?= Html::button("hidden riwayat", [
                    'id'          => 'btn-hidden-riwayat-pasien',
                    'data-width'  => '90%',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'action'      => '',
                    'style'       => 'display: none;',
                ]) ?>
                <?= Html::button("hidden terra medik", [
                    'id'          => 'btn-hidden-history-terra-medik',
                    'data-width'  => '90%',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'action'      => '',
                    'style'       => 'display: none;',
                    'class'       => isset($thirdApp['terramedik']) && !$thirdApp['terramedik'] ? 'hidden' : ''
                ]) ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-body">
                            <div class="advanced-filter">
                            </div>
                            <div class="legend-index">
                                <div class="col-md-6">
                                    <div class="legend-header">Keterangan</div>
                                    <div class="legend-wrapper">
                                        <div class="legend-information">
                                            <div class="legend-information__color" style="background-color: #d64541"></div>
                                            <div class="legend-information__text">Catatan Penting</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="legend-index">
                        <div class="col-md-6">
                            <div class="legend-header">Keterangan</div>
                            <div class="legend-wrapper">
                                <div class="legend-information">
                                    <div class="legend-information__color" style="background-color: #d64541"></div>
                                    <div class="legend-information__text">Belum Isi SOAP</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="row">
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-body">
                            <table id="table-informasi-pasien" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1"></th>
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Tgl. Rekam Medik");?></th>
                                        <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                                        <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                                        <th><?=\Yii::t("fe", "Nama Pasien 2");?></th>
                                        <th><?=\Yii::t("fe", "Tgl. Lahir");?></th>
                                        <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                                        <th><?=\Yii::t("fe", "NIK");?></th>
                                        <th><?=\Yii::t("fe", "Alamat");?></th>
                                        <th><?=\Yii::t("fe", "Propinsi");?></th>
                                        <th><?=\Yii::t("fe", "Kabupaten");?></th>
                                        <th><?=\Yii::t("fe", "Kecamatan");?></th>
                                        <th><?=\Yii::t("fe", "Petugas");?></th>
                                        <th><?=\Yii::t("fe", "Alasan Perubahan");?></th>
                                        <th><?=\Yii::t("fe", "No. Handphone");?></th>
                                        <th><?=\Yii::t("fe", "No. BPJS");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    </tr>
                                    <!-- <tr>
                                        <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-preview-img" class="modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

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

    <div id="modal-show-konsul" class="modal">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-header bg-inverse" style="z-index: 1050">
                <button type="button" id="dismiss-preview-btn-konsul" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Konsul Poli</h5>
            </div>
            <div class="modal-content">
                <div class="preview-wrapper" style="position: relative;" id="preview-wrapper-konsul">
                    <!-- <div class="overlay-preview"></div> -->
                    <iframe frameborder="0" id="preview-content-konsul" style="width:100%;height:85vh"></iframe>
                </div>
            </div>
        </div>
    </div>
    
    <div id="modal_berkas_pasien" class="modal">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content">
            </div>
        </div>
    </div>

   
</div>

<?php
$pageVars = [
    'columnsLabels' => [
        'number' => 'No',
        'tglRekamMedik' => \Yii::t("fe", "Tgl. Rekam Medik"),
        'noRekamMedik' => \Yii::t("fe", "No Rekam Medik"),
        'namaPasien' => \Yii::t("fe", "Nama Pasien"),
        'tglLahir' => \Yii::t("fe", "Tgl. Lahir"),
        'jenisKelamin' => \Yii::t("fe", "Jenis Kelamin"),
        'nikPasien' => \Yii::t("fe", "NIK"),
        'alamatPasien' => \Yii::t("fe", "Alamat"),
        'provinsi' => \Yii::t("fe", "Propinsi"),
        'kabupaten' => \Yii::t("fe", "Kabupaten"),
        'kecamatan' => \Yii::t("fe", "Kecamatan"),
        'namaPetugas' => \Yii::t("fe", "Petugas"),
        'alasanPerubahan' => \Yii::t("fe", "Alasan Perubahan"),
        'noMobilePasien' => \Yii::t("fe", "No. Handphone"),
    ],
    'formFilters' => [
        // 'tglRekamMedikFilter' => DHDatePickerWidget::widget(['name_id' => 'tgl_rekam_medik']),
        'tglLahirFilter' => DHDatePickerWidget::widget(['name_id' => 'tanggal_lahir', 'defaultYears' => 200]),
        'jenisKelaminFilter' => Html::dropDownList('jeniskelamin', '', $ddlJenisKelamin, ['class' => 'form-control select2', 'id' => 'jeniskelamin', 'prompt' => Yii::t('fe', 'Jenis Kelamin') ]),
        'provinsiFilter' => Html::dropDownList('propinsi_id', '', $ddlPropinsi, ['class' => 'form-control select2', 'id' => 'propinsi_id', 'prompt' => Yii::t('fe', 'Propinsi') ]),
        'kabupatenFilter' => DepDrop::widget([
                    'name' => 'kabupaten_id',
                    'options' => [
                        'id' => 'kabupaten_id',
                        'class' => 'select2',
                    ],
                    'pluginOptions' => [
                        'depends' => ['propinsi_id'],
                        'placeholder' => \Yii::t('fe', 'Kabupaten'),
                        'url' => Url::to(['list-kabupaten'])
                    ]
                ]),
        'kecamatanFilter' => DepDrop::widget([
                    'name' => 'kecamatan_id',
                    'options' => [
                        'id' => 'kecamatan_id',
                        'class' => 'select2',
                    ],
                    'pluginOptions' => [
                        'depends' => ['kabupaten_id'],
                        'placeholder' => \Yii::t('fe', 'Kecamatan'),
                        'url' => Url::to(['list-kecamatan'])
                    ]
                ]),
    ],
    'options' => [
        'jenisKelaminOpt' => $ddlJenisKelamin,
        'provinsiOpt' => $ddlPropinsi,
        'instalasiId' => DocoHelpers::encrypt(Yii::$app->docoVars->workspace('instalasi_id'))
    ]
];
$this->registerJsVar('pageVars', $pageVars);
$this->registerJs("
        var isAksesUpdate = `$isAksesUpdate`;
    ".$this->render('js/_index.js'), View::POS_END);
?>
