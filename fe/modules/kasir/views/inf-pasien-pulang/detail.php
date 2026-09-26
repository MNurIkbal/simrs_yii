<?php

/**
 * @Author: Sigit
 * @Date:   2018-05-16 10:07:00
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-20 11:45:29
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Informasi Pasien Pulang');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Pasien Pulang'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

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
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'back',
                ]) ?>
                <?= Html::a(
                    '<b><i class="fa fa-print"></i></b>'.Yii::t('fe', 'Cetak'),
                    '/kasir/inf-pasien-pulang/print?id='.DocoHelpers::encrypt($data['pendaftaran_id']),
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'target' => '_blank',
                        'rel'=>'noopener',
                    ]
                ) ?>
            </div>
            <br>
            <div class="panel-body">
                <div class="row row-eq-height " style="margin-top:10px;">
                    <div class="col-md-8" id="informasi">
                        <div class="panel panel-default">
                            <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?></h6>

                                    <p class="p-data" id="data-pasien">
                                        <?= isset($data['no_rekam_medik']) ? $data['no_rekam_medik'] : '-' ?> -
                                        <b class="font" ><?= isset($data['nama_pasien']) ? $data['nama_pasien'] : '-' ?></b>
                                        (<?= isset($data['tanggal_lahir']) ? date('d M Y', strtotime($data['tanggal_lahir'])) : '-' ?>)
                                    </p>

                                    <ul class="icons-list">
                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                    </ul>

                                </div>
                            </a>

                            <div class="panel-body collapse multi-collapse info-card" id="infopasien">
                                <div class="col-xs-2">
                                    <div class="border-img">
                                        <?php
                                        $filename = isset($data['photopasien']) ? !empty($data['photopasien']) ? '/media/img/pasien/'.$data['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                                        ?>
                                        <?=Html::img($filename, [ 'style'=>'width: 100%;height: auto;max-width: 114px;', 'class'=>'img-responsive'])?>
                                        <?= Html::hiddenInput('pendaftaran_id', DocoHelpers::encrypt($data['pendaftaran_id']), ['id' => 'pendaftaran-id', 'readonly' => 'readonly']) ?>
                                    </div>
                                </div>

                                <div class="col-xs-9">
                                    <div class="row">
                                        <br>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pasien") ?></b>
                                            <br>
                                            <p>
                                                <?= isset($data['no_rekam_medik']) ? $data['no_rekam_medik'] : '-' ?> -
                                                <?= isset($data['nama_pasien']) ? $data['nama_pasien'] : '-' ?> -
                                                <?= isset($data['jenis_kelamin']) ? $data['jenis_kelamin'] : '-' ?>
                                            </p>

                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Lahir") ?></b>
                                            <br>
                                            <p>
                                                <?= isset($data['tanggal_lahir']) ? date('d M Y', strtotime($data['tanggal_lahir'])) : '-' ?> -
                                                (<?= isset($data['umur']) ? $data['umur'] : '-' ?>)
                                            </p>
                                        </div>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pendaftaran") ?></b>
                                            <p>
                                                <?= isset($data['no_pendaftaran']) ? $data['no_pendaftaran'] : '-' ?> -
                                                (<?= isset($data['tgl_pendaftaran']) ? date('d-M-Y', strtotime($data['tgl_pendaftaran'])) : '-' ?>)
                                            </p>

                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas pelayanan") ?></b>
                                            <p>
                                                <?= isset($data['kelaspelayanan_nama']) ? $data['kelaspelayanan_nama'] : '-' ?> -
                                                <?= isset($data['carabayar_nama']) ? $data['carabayar_nama'] : '-' ?> -
                                                <?= isset($data['penjamin_nama']) ? $data['penjamin_nama'] : '-' ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <a id="info-heading" data-toggle="collapse" href="#infodetail" role="button" aria-expanded="false" aria-controls="infopasien" >
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi Pasien'); ?></b></h6>
                                    <ul class="icons-list">
                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                    </ul>
                                </div>
                            </a>
                            <div class="panel-body column-info collapse multi-collapse info-card" id="infodetail">
                                <div class="row row-eq-height">
                                    <br>
                                    <div class="col-xs-6">
                                        <b class="text-left control-label font-design"><?= Yii::t("fe", " Penyakit") ?></b>
                                        <p>
                                            <?= isset($data['jeniskasuspenyakit_nama']) ? $data['jeniskasuspenyakit_nama'] : '-' ?>
                                        </p>

                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Dokter") ?></b>
                                        <p>
                                            <?= !empty($data['dokter']) ? $data['dokter'] : null ?>
                                        </p>


                                    </div>

                                    <div class="col-xs-6">
                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Ruangan") ?></b>
                                        <p>
                                            <?php $ruanganBayar = isset($data['ruangan_nama']) ? $data['ruangan_nama'] : ''?>
                                            <?= $ruanganBayar ?>
                                        </p>

                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Status Bayar") ?></b>
                                        <p>
                                            <?= isset($data['status_bayar']) ? $data['status_bayar'] : '-' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="id_tindakanAll" value="<?= !empty($dataTindakan) ? $dataTindakan['info']['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_tindakanRi" value="<?= !empty($dataTindakanRi) ? $dataTindakanRi[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_tindakanRj" value="<?= !empty($dataTindakanRj) ? $dataTindakanRj[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_tindakanRd" value="<?= !empty($dataTindakanRd) ? $dataTindakanRd[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_obat" value="<?= !empty($dataObat) ? $dataObat[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_lab" value="<?= !empty($dataLab) ? $dataLab[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_radiologi" value="<?= !empty($dataRadiologi) ? $dataRadiologi[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_gudang" value="<?= !empty($dataTindakanGudang) ? $dataTindakanGudang[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_rehab" value="<?= !empty($dataTindakanRehab) ? $dataTindakanRehab[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_rm" value="<?= !empty($dataTindakanRm) ? $dataTindakanRm[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_kasir" value="<?= !empty($dataTindakanKasir) ? $dataTindakanKasir[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_informasi" value="<?= !empty($dataTindakanInformasi) ? $dataTindakanInformasi[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_pendaftaran" value="<?= !empty($dataTindakanPendaftaran) ? $dataTindakanPendaftaran[0]['pendaftaran_id'] : '' ?>">
                <input type="hidden" id="id_bedah" value="<?= !empty($dataTindakanBedah) ? $dataTindakanBedah[0]['pendaftaran_id'] : '' ?>">

                <?php if ($dataTindakan):
                    foreach ($dataTindakan as $key => $value) {
                        if($key != 'info'){
                            ?>
                            <div class="col-md-12" style="margin-top:10px;">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h6 class="panel-title"><?= "Tindakan - ". str_replace('-', ' ', $key) ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                                        <div class="heading-elements">
                                            <ul class="icons-list">
                                                <li><a data-action="collapse"></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="test-footer"></div>
                                    <div class="panel-body">
                                        <table id="table-tindakan-<?=$key?>" class="table datatable-basic table-striped table-hover dataTable" style="width: 100%">
                                            <thead>
                                                <tr class="bg-inverse">
                                                <th width="1">No</th>
                                                <th><?= Yii::t('fe', 'Tanggal Tindakan') ?></th>
                                                <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                                <th><?= Yii::t('fe', 'Qty') ?></th>
                                                <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                                <th><?= Yii::t('fe', 'Tarif Cyto') ?></th>
                                                <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th><?= Yii::t('fe', 'Total') ?></th>
                                                <th></th>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                ?>
                <?php endif; ?>

                <!-- Info obat -->
                <?php if ($dataObat): ?>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Obat') ?>
                                <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                            </h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable" id="tb-obat" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Tanggal Order Obat') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Obat') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= Yii::t('fe', 'Total') ?></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info Pemeriksaan Laboratorium -->
                <?php if ($dataLab): ?>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Laboratorium') ?>
                                <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                            </h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable" id="tb-tindakan-lab" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Tanggal Pemeriksaan') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Pemeriksaan') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Cyto') ?></th>
                                        <th><?= Yii::t('fe', 'Jumlah') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= Yii::t('fe', 'Total') ?></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info Pemeriksaan Radiologi -->
                <?php if ($dataRadiologi): ?>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Radiologi') ?>
                                <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                            </h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="test-footer"></div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable" id="tb-tindakan-radiologi" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                      <th width="1">No</th>
                                      <th><?= Yii::t('fe', 'Tanggal Pemeriksaan') ?></th>
                                      <th><?= Yii::t('fe', 'Nama Pemeriksaan') ?></th>
                                      <th><?= Yii::t('fe', 'Qty') ?></th>
                                      <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                      <th><?= Yii::t('fe', 'Cyto') ?></th>
                                      <th><?= Yii::t('fe', 'Jumlah') ?></th>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= Yii::t('fe', 'Total') ?></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info tindakan gudang -->
                <?php if ($dataTindakanGudang): ?>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Gudang Farmasi') ?>
                                <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                            </h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable" id="tb-gudang" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Tanggal Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Cyto') ?></th>
                                        <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= Yii::t('fe', 'Total') ?></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info tindakan rehab -->
                <?php if ($dataTindakanRehab): ?>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Tindakan - Rehabilitasi Medik') ?>
                                <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                            </h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable" id="tb-rehab" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Tanggal Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Cyto') ?></th>
                                        <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= Yii::t('fe', 'Total') ?></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info tindakan rm -->
                <?php if ($dataTindakanRm): ?>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Rekam Medik') ?>
                                <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                            </h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable" id="tb-rm" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Tanggal Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Cyto') ?></th>
                                        <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= Yii::t('fe', 'Total') ?></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info tindakan kasir -->
                <?php if ($dataTindakanKasir): ?>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Kasir') ?>
                                <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                            </h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable" id="tb-kasir" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Tanggal Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Cyto') ?></th>
                                        <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= Yii::t('fe', 'Total') ?></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info tindakan info -->
                <?php if ($dataTindakanInformasi): ?>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Informasi') ?>
                                <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                            </h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable" id="tb-informasi" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Tanggal Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Cyto') ?></th>
                                        <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= Yii::t('fe', 'Total') ?></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info tindakan bedah -->
                <?php if ($dataTindakanBedah): ?>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Bedah Sentral') ?>
                                <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                            </h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable" id="tb-bedah" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Tanggal Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Cyto') ?></th>
                                        <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-center"></th>
                                    <th><?= Yii::t('fe', 'Total') ?></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info tindakan pendaftaran -->
                <?php if ($dataTindakanPendaftaran): ?>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Pendaftaran & Penjadwalan') ?>
                                <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                            </h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable" id="tb-pendaftaran" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Tanggal Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Cyto') ?></th>
                                        <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= Yii::t('fe', 'Total') ?></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($dataTindakanAmbulan): ?>
                <div class="col-md-12" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Tindakan - Ambulan') ?>
                                <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                            </h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable" id="table-tindakan-ambulan" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Tanggal Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Cyto') ?></th>
                                        <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?= Yii::t('fe', 'Total') ?></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // Global vars
    var arrRuangan = '.json_encode($arrRuangan).'
    var no = "'.(\Yii::t("fe", "Nomor")).'";
    var tanggalTindakan = "'.(\Yii::t("fe", "Tanggal Tindakan")).'";
    var tanggalPemeriksaan = "'.(\Yii::t("fe", "Tanggal Pemeriksaan")).'";
    var namaTindakan = "'.(\Yii::t("fe", "Nama Tindakan")).'";
    var namaPemeriksaan = "'.(\Yii::t("fe", "Nama Pemeriksaan")).'";
    var namaObat = "'.(\Yii::t("fe", "Nama Obat")).'";
    var qty = "'.(\Yii::t("fe", "Qty")).'";
    var tarifSatuan = "'.(\Yii::t("fe", "Tarif Satuan (Rp.)")).'";
    var tarifCyto = "'.(\Yii::t("fe", "Tarif Cyto (Rp.)")).'";
    var jumlahTarif = "'.(\Yii::t("fe", "Jumlah Tarif (Rp.)")).'";

    // Datatable language
    var emptyTable = "'.(\Yii::t("fe", "Tidak ada data yang tersedia")).'";
    var info = "'.(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")).'";
    var infoEmpty = "'.(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")).'";
    var infoFiltered = "'.(\Yii::t("fe", "(disaring dari _MAX_ total data)")).'";
    var lengthMenu = "'.(\Yii::t("fe", "Menampilkan _MENU_ data")).'";
    var loadingRecords = "'.(\Yii::t("fe", "Memuat...")).'";
    var processing = "'.(\Yii::t("fe", "Memproses...")).'";
    var search = "'.(\Yii::t("fe", "Cari:")).'";
    var zeroRecords = "'.(\Yii::t("fe", "Tidak ada data yang ditemukan")).'";
    var first = "'.(\Yii::t("fe", "Pertama")).'";
    var last = "'.(\Yii::t("fe", "Terakhir")).'";
    var next = "'.(\Yii::t("fe", "Selanjutnya")).'";
    var previous = "'.(\Yii::t("fe", "Sebelumnya")).'";
    var sortAscending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")).'";
    var sortDescending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")).'";
', View::POS_END, 'b-index');

// Register js file
$this->registerJs($this->render('js/detail.js'), View::POS_END);
?>
