<?php

/**
 * @author Naufal Ziyad L
 * @copyright 15 Februari 2018
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$tera = isset($isTera) ? 'Tera' : '';
?>
<style>
    .dataTables_wrapper {
        margin-top: 35px !important;
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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'back',
                ]); ?>
            </div>
            <div class="panel-body">
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title text-center"><?= Yii::t('fe', 'Riwayat Kunjungan Pasien ' . $tera) ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                    </div>
                    <div class="panel-body">
                        <table width="80%" cellpadding="10" class="tabel">
                            <tbody>
                                <tr>
                                    <td class="bold"><?= Yii::t('fe', 'No Rekam Medik') ?></td>
                                    <td class="header_namaPasien"><?= isset($infoPasien['no_rekam_medik']) ? $infoPasien['no_rekam_medik'] : "-" ?></td>
                                    <td class="bold"><?= Yii::t('fe', 'Nama Pasien') ?></td>
                                    <td class="header_namaPasien"><?= isset($infoPasien['nama_pasien']) ? $infoPasien['nama_pasien'] : "-" ?></td>
                                </tr>
                                <tr>
                                    <td class="bold"><?= Yii::t('fe', 'Tempat Tanggal Lahir') ?></td>
                                    <td class="header_namaPasien"><?= isset($infoPasien['tanggal_lahir']) ? date('d M Y H:i', strtotime($infoPasien['tanggal_lahir'])) : "-" ?></td>
                                    <td class="bold"><?= Yii::t('fe', 'Nama Ibu') ?></td>
                                    <td class="header_namaPasien"><?= isset($infoPasien['nama_ibu']) ? $infoPasien['nama_ibu'] : "-" ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="filter-form"></div>
                        <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-riwayat" style="width: 100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?= Yii::t('fe', 'No  Pendaftaran') ?></th>
                                    <th><?= Yii::t('fe', 'Tanggal Pendaftaran') ?></th>
                                    <th><?= Yii::t('fe', 'Tanggal Pulang') ?></th>
                                    <th><?= Yii::t('fe', 'Ruangan Nama / Instalasi') ?></th>
                                    <th><?= Yii::t('fe', 'Dokter DPJP') ?></th>
                                    <th><?= Yii::t('fe', 'Cara Bayar / Penjamin') ?></th>
                                    <th><?= Yii::t('fe', 'Kelas Pelayanan') ?></th>
                                    <th><?= Yii::t('fe', 'Cara Keluar') ?></th>
                                    <th><?= Yii::t('fe', 'Status Periksa') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <td class="text-center" colspan="7"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                            </tbody>
                        </table>

                        <div class="clear"><br></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
        var id = `$id`;
    " . $this->render('js/_riwayat-pasien.js'));
?>