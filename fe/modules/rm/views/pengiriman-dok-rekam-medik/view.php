<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = 'Pengiriman Dokumen Rekam Medik Masuk';
$this->params['breadcrumbs'][] = ['label' => 'Rm', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['informasi']];
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
                <?=DocoHelpers::generateToolbar([
                        'back' => [
                            'attributes' => [
                                'href' => '/rm/pengiriman-dok-rekam-medik/informasi'
                            ]
                        ],
                    ]);?>
            </div>

            <div class="panel-body">
                <div class="row text-center">
                    <h3><b>PENGIRIMAN DOKUMEN REKAM MEDIK</b><br><b><?= date('d-m-Y',strtotime($header['tgl_kirim'])) ?></b></h3>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label for="" class="col-lg-5 control-label">
                                <b><?= Yii::t('fe','Tanggal formulir') ?></b>
                            </label>
                            <div class="col-md-7 detail-pasien text-left">
                                <p>:&nbsp;<?= date('d-m-Y',strtotime($header['tgl_kirim'])) ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="" class="col-lg-5 control-label">
                                <b><?= Yii::t('fe','Instalasi asal') ?></b>
                            </label>
                            <div class="col-md-7 detail-pasien text-left">
                                <p>:&nbsp;<?= $header['instalasi_pengirim'] ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="" class="col-lg-5 control-label">
                                <b><?= Yii::t('fe','Instalasi tujuan') ?></b>
                            </label>
                            <div class="col-md-7 detail-pasien text-left">
                                <p>:&nbsp;<?= $header['instalasi_pemesan'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label for="" class="col-lg-5 control-label">
                                <b><?= Yii::t('fe','Nomor pengirim') ?></b>
                            </label>
                            <div class="col-md-7 detail-pasien text-left">
                                <p>:&nbsp;<?= $header['no_kirimdokrm'] ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="" class="col-lg-5 control-label">
                                <b><?= Yii::t('fe','Ruangan asal') ?></b>
                            </label>
                            <div class="col-md-7 detail-pasien text-left">
                                <p>:&nbsp;<?= $header['ruangan_pengirim'] ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="" class="col-lg-5 control-label">
                                <b><?= Yii::t('fe','Ruangan tujuan') ?></b>
                            </label>
                            <div class="col-md-7 detail-pasien text-left">
                                <p>:&nbsp;<?= $header['ruangan_pemesan'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th>Tanggal Rekam Medik</th>
                            <th>Lokasi Rak</th>
                            <th>Lokasi Sub Rak</th>
                            <th>Nomor Rekam Medik</th>
                            <th>Nama Pasien</th>
                            <th>Warna Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if (count($detail)) :
                                $no = 1;
                                foreach ($detail as $val) :
                                ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><?= date('d-m-Y',strtotime($val['tglrekammedis'])) ?></td>
                                        <td><?= $val['lokasirak_nama'] ?></td>
                                        <td><?= $val['subrak_nama'] ?></td>
                                        <td><?= $val['no_rekam_medik'] ?></td>
                                        <td><?= $val['nama_pasien'] ?></td>
                                        <td><?= $val['warnadokrm_namawarna'] ?></td>
                                    </tr>
                                <?php
                                $no++;
                                endforeach;
                            else :
                        ?>
                            <tr>
                                <td class="text-center" colspan="7">Data tidak ditemukan.</td>
                            </tr>
                        <?php
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

