<?php

/**
 * @Author: Ardi
 * @Date:   2018-10-05 16:10
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;
?>
<div id="view_only_kesimpulan" class="form-horizontal">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-sm-4">
                    Cara Keluar
                </label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?=@$infoKesimpulan['carakeluar_nama']?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4">
                    Dokter DPJP
                </label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= isset($infoKesimpulan['dokter_dpjp_pulang']) && !empty($infoKesimpulan['dokter_dpjp_pulang']) ? $infoKesimpulan['dokter_dpjp_pulang'] : '-'?></p>
                </div>
            </div>
            <div class="form-group <?= $infoKesimpulan['carakeluar_id'] != 5 ? 'hidden' : ''?>">
                <label class="control-label col-sm-4">
                    Jenis Kamar Tujuan
                </label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= isset($infoKesimpulan['kamarruangan_jenis_nama']) && !empty($infoKesimpulan['kamarruangan_jenis_nama']) ? $infoKesimpulan['kamarruangan_jenis_nama'] : '-'?></p>
                </div>
            </div>
            <div class="form-group <?= $infoKesimpulan['carakeluar_id'] != 5 ? 'hidden' : ''?>">
                <label class="control-label col-sm-4">
                    Kamar Ruangan Tujuan
                </label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= isset($infoKesimpulan['tempattidurtujuan_nama']) && !empty($infoKesimpulan['tempattidurtujuan_nama']) ? $infoKesimpulan['tempattidurtujuan_nama'] : '-'?></p>
                </div>
            </div>
            <div class="form-group <?= $infoKesimpulan['carakeluar_id'] != 5 ? 'hidden' : ''?>">
                <label class="control-label col-sm-4">
                    Dokter Tujuan
                </label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= isset($infoKesimpulan['dokter_tujuan']) && !empty($infoKesimpulan['dokter_tujuan']) ? $infoKesimpulan['dokter_tujuan'] : '-'?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4">
                    Tanggal Keluar / Pindah Ruangan
                </label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                    <?php
                        $tglpasienpulang = isset($infoKesimpulan['tglpasienpulang']) ? date('d F Y H:i:s', strtotime(@$infoKesimpulan['tglpasienpulang'])) :'-';
                        echo $tglpasienpulang;
                    ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-sm-4">
                    Kondisi Pasien
                </label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?=@$infoKesimpulan['kondisikeluar_nama']?></p>
                </div>
            </div>
            <div class="form-group <?= $infoKesimpulan['carakeluar_id'] != 5 ? 'hidden' : ''?>">
                <label class="control-label col-sm-4">
                Pemeriksaan / Pertolongan yang sudah / harus diberikan
                </label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= isset($infoKesimpulan['catatan_tindakan']) && !empty($infoKesimpulan['catatan_tindakan']) ? nl2br(htmlspecialchars($infoKesimpulan['catatan_tindakan'])) : '-'?></p>
                </div>
            </div>
            <div class="form-group <?= $infoKesimpulan['carakeluar_id'] != 5 ? 'hidden' : ''?>">
                <label class="control-label col-sm-4">
                    Catatan Lain
                </label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= isset($infoKesimpulan['catatan_lain']) && !empty($infoKesimpulan['catatan_lain']) ? $infoKesimpulan['catatan_lain'] : '-'?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4">
                    Tanggal Meninggal
                </label>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php
                    $tgl_meninggal = isset($infoKesimpulan['tgl_meninggal']) ? date('d F Y h:i:s', strtotime(@$infoKesimpulan['tgl_meninggal'])) :'-';
                    echo $tgl_meninggal;
                    ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?=Yii::t('fe','Pasien Pindah Rawat Inap')?></h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4">
                                Kondisi / Masalah
                            </label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=@$infoKesimpulan['kondisi']?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">
                                Detak Jantung (HR)
                            </label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=@$infoKesimpulan['hr']?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">
                                Pernapasan (RR)
                            </label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=@$infoKesimpulan['rr']?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">
                                Oksigen (SpO2)
                            </label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=@$infoKesimpulan['spo2']?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">
                                Temperatur (T)
                            </label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=@$infoKesimpulan['t']?></p>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <fieldset>
                            <legend><?=Yii::t('fe', 'Glasgow coma scale')?></legend>

                            <div class="form-group">
                                <label class="control-label col-sm-4">
                                    GCS Eye
                                </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=@$infoKesimpulan['gcs_eye_nama']?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">
                                    GCS Verbal
                                </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=@$infoKesimpulan['gcs_verbal_nama']?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">
                                    GCS Motorik
                                </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=@$infoKesimpulan['gcs_motorik_nama']?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">
                                    Hasil Metode Gcs
                                </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=@$infoKesimpulan['hasil_gcs']?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">
                                    Keterangan Kapitis
                                </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?php echo isset($infoKesimpulan['is_kapitis']) && $infoKesimpulan['is_kapitis'] == TRUE ? 'Ya':'Tidak' ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">
                                    Kategori Gcs
                                </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=@$infoKesimpulan['gcs_kategori']?></p>
                                </div>
                            </div>

                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?=Yii::t('fe','Pasien Pulang')?></h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4">
                                Instruksi Lanjutan
                            </label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=@$infoKesimpulan['instruksi_lanjutan']?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4">
                                Perawatan Lanjutan Tanggal
                            </label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                <?php
                                    $tgl_lanjut_rawat = isset($infoKesimpulan['tgl_lanjut_rawat']) ? date('d F Y H:i:s', strtotime(@$infoKesimpulan['tgl_lanjut_rawat'])) :'-';
                                    echo $tgl_lanjut_rawat;
                                ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">
                                Poliklinik
                            </label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=@$infoKesimpulan['poliklinik_nama']?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">
                                Dokter
                            </label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=@$infoKesimpulan['dokter_pulang']?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?=Yii::t('fe','Obat Saat Pulang')?></h5>
            </div>
            <div class="panel-body">
                <!-- <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4">Dokter</label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=@$infoResep['nama_pegawai']?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Tanggal Resep</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                <?php
                                    $tglreseptur = isset($infoResep['tglreseptur']) ? date('d F Y h:i:s', strtotime(@$infoResep['tglreseptur'])) :'-';
                                    echo $tglreseptur;
                                ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4">Depo Tujuan</label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=@$infoResep['ruangan_tujuan']?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Iterasi</label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=@$iter?></p>
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="row">
                    <div class="table-responsive">
                        <table id="tabel-reseptur" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No</th>
                                    <th><?=Yii::t('fe', 'Nama obat')?></th>
                                    <th><?=Yii::t('fe', 'Jumlah')?></th>
                                    <th><?=Yii::t('fe', 'Dosis')?></th>
                                    <th><?=Yii::t('fe', 'Cara Pemberian')?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $no = 0;
                            if (!empty($obat_pulang)) : ?>
                                <?php foreach ($obat_pulang as $key => $value) : $no++;
                                ?>
                                    <tr>
                                        <td><?= $no; ?></td>
                                        <td><?= !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : ''; ?></td>
                                        <td><?= $qty_reseptur = !empty($value['qty_reseptur']) ? $value['qty_reseptur'] : ''; ?> <?= $satuan_kecil = !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : ''; ?> </td>
                                        <td><?= $signa_nama = !empty($value['signa_nama']) ? $value['signa_nama'] : ''; ?></td>
                                        <td><?= $qty_reseptur = !empty($value['nama_rute']) ? $value['nama_rute'] : ''; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php else :?>
                                    <tr>
                                        <td colspan="9" class="text-center">Data Obat Tidak Tersedia</td>
                                    </tr>
                            <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if($datajenazah !== null){
        // var_dump($datajenazah['kondisipasien']); die();
        ?>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><b><?=Yii::t('fe', 'Pelayanan Jenazah')?></b></h5>
                </div>
                <div class="panel-body">
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><b><?=Yii::t('fe', 'Kondisi Pasien')?></b></h5>
                                </div>
                                <div class="panel-body">
                                    <br>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="col-sm-3" for="inputEmail"><b>Kondisi Pasien</b></label>
                                                <div class="col-sm-9">
                                                    <label><p><?= isset($datajenazah['kondisipasien']['kondisi']) ? $datajenazah['kondisipasien']['kondisi'] : '' ?></p></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-sm-4" for="inputEmail"><b><?=\Yii::t('fe', 'Nama Penanggung Jawab')?></b></label>
                                                <div class="col-sm-5">
                                                    <label><p><?= isset($datajenazah['kondisipasien']['penanggungjawab_nama']) ? $datajenazah['kondisipasien']['penanggungjawab_nama'] : '' ?></p></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-sm-4" for="inputEmail"><b><?=\Yii::t('fe', 'Jenis Kelamin')?></b></label>
                                                <div class="col-sm-5">
                                                    <label><p><?= isset($datajenazah['kondisipasien']['jenis_kelamin_pj']) ? $datajenazah['kondisipasien']['jenis_kelamin_pj'] : '' ?></p></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-sm-4" for="inputEmail"><b><?=\Yii::t('fe', 'Umur')?></b></label>
                                                <div class="col-sm-5">

                                                    <label><p><?= isset($datajenazah['kondisipasien']['umur_pj']) ? $datajenazah['kondisipasien']['umur_pj'] : '' ?></p></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-sm-4" for="inputEmail"><b><?=\Yii::t('fe', 'No. Telp / Hp')?></b></label>
                                                <div class="col-sm-5">
                                                    <label><p><?= isset($datajenazah['kondisipasien']['no_kontak']) ? $datajenazah['kondisipasien']['no_kontak'] : '' ?></p></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-sm-4" for="inputEmail"><b><?=\Yii::t('fe', 'Hubungan Keluarga')?></b></label>
                                                <div class="col-sm-5">
                                                    <label><p><?= isset($datajenazah['kondisipasien']['hubungan_kel']) ? $datajenazah['kondisipasien']['hubungan_kel'] : '' ?></p></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-sm-4" for="inputEmail"><b><?=\Yii::t('fe', 'Alamat')?></b></label>
                                                <div class="col-sm-5">
                                                    <label><p><?= isset($datajenazah['kondisipasien']['alamat']) ? $datajenazah['kondisipasien']['alamat'] : '' ?></p></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><b><?=Yii::t('fe', 'Tindakan dan Obat')?></b></h5>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <legend><?=Yii::t('fe', 'Tindakan')?></legend>
                                            <table class="table table-striped table-hover datatable-basic">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                        <th><?=Yii::t('fe', 'No')?></th>
                                                        <th><?=Yii::t('fe', 'Nama Tindakan')?></th>
                                                        <th><?=Yii::t('fe', 'Qty')?></th>
                                                        <th class="text-right"><?=Yii::t('fe', 'Harga (IDR)')?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php

                                                    if(isset($datajenazah['list_tindakan']['tindakan'])){
                                                        $no = 1;
                                                        foreach ($datajenazah['list_tindakan']['tindakan'] as $key => $value) {
                                                        ?>
                                                            <tr>
                                                                <td><?=$no?></td>
                                                                <td><?=$value['tindakan_obat']?></td>
                                                                <td><?=$value['qty']?></td>
                                                                <td class="text-right"><?=DocoHelpers::formatNumber($value['total_tarif'])?></td>
                                                            </tr>
                                                        <?php
                                                            $no++;
                                                        }
                                                    }else{
                                                        ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center">
                                                                <?=Yii::t('fe', 'Data Tidak Tersedia')?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <legend><?=Yii::t('fe', 'Obat')?></legend>
                                            <table class="table datatable-basic table-striped table-hover no-footer" id="tabel-obat" width="100%">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                        <th><?=Yii::t('fe', 'No')?></th>
                                                        <th><?=Yii::t('fe', 'Nama Obat Alkes')?></th>
                                                        <th><?=Yii::t('fe', 'Qty')?></th>
                                                        <th class="text-right"><?=Yii::t('fe', 'Harga (IDR)')?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php

                                                    if(isset($datajenazah['list_tindakan']['obat'])){
                                                        $no = 1;
                                                        foreach ($datajenazah['list_tindakan']['obat'] as $key => $value) {
                                                        ?>
                                                            <tr>
                                                                <td><?=$no?></td>
                                                                <td><?=$value['tindakan_obat']?></td>
                                                                <td><?=$value['qty']. ' '.$value['satuan']?></td>
                                                                <td class="text-right"><?=DocoHelpers::formatNumber($value['total_tarif'])?></td>
                                                            </tr>
                                                        <?php
                                                            $no++;
                                                        }
                                                    }else{
                                                        ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center">
                                                                <?=Yii::t('fe', 'Data Tidak Tersedia')?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><b><?=Yii::t('fe', 'Alat dan Linen')?></b></h5>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <legend><?=Yii::t('fe', 'Alat')?></legend>
                                            <table class="table table-striped table-hover datatable-basic dataTable" style="width:100%;" id="tabel-linen">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                        <th><?=Yii::t('fe', 'No')?></th>
                                                        <th><?=Yii::t('fe', 'Nama Linen')?></th>
                                                        <th><?=Yii::t('fe', 'Qty')?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(isset($datajenazah['list_linen']['linen'])){
                                                        $no = 1;
                                                        foreach ($datajenazah['list_linen']['linen'] as $key => $value) {
                                                        ?>
                                                            <tr>
                                                                <td><?=$no?></td>
                                                                <td><?=$value['nama_alat']?></td>
                                                                <td><?=$value['qty']?></td>
                                                            </tr>
                                                        <?php
                                                            $no++;
                                                        }
                                                    }else{
                                                        ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center">
                                                                <?=Yii::t('fe', 'Data Tidak Tersedia')?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <legend><?=Yii::t('fe', 'Linen')?></legend>
                                            <table class="table table-striped table-hover datatable-basic dataTable" style="width:100%;" id="tabel-alat">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                        <th><?=Yii::t('fe', 'No')?></th>
                                                        <th><?=Yii::t('fe', 'Nama Alat')?></th>
                                                        <th><?=Yii::t('fe', 'Qty')?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(isset($datajenazah['list_linen']['alat'])){
                                                        $no = 1;
                                                        foreach ($datajenazah['list_linen']['alat'] as $key => $value) {
                                                        ?>
                                                            <tr>
                                                                <td><?=$no?></td>
                                                                <td><?=$value['nama_alat']?></td>
                                                                <td><?=$value['qty']?></td>
                                                            </tr>
                                                        <?php
                                                            $no++;
                                                        }
                                                    }else{
                                                        ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center">
                                                                <?=Yii::t('fe', 'Data Tidak Tersedia')?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
    ?>
    <div class="row">
        <div class="col-md-12" style="margin-left: 5px">
            <?php
                if ($infoKesimpulan['carakeluar_id'] == 4 ){?>
                    <button type="button" id="cetak-keterangan-meninggal" class="btn bg-teal"><i class="fa fa-print"></i> Cetak</button>
                <?php } else { ?>
                    <button type="button" id="cetak-view-kesimpulan" class="btn bg-teal"><i class="fa fa-print"></i> Cetak</button>
                <?php }
            ?>


        </div>
    </div>
</div>
<?php
$this->registerJs("

    var encryptedId = '".$encryptedId."';
    $('#cetak-view-kesimpulan').on('click',function(e){
        e.preventDefault();
        var url='/igd/pemeriksaan-igd/cetak-pdf-kesimpulan?id='+encryptedId;
        window.open(url, '_blank');
    });

    $('#cetak-keterangan-meninggal').on('click',function(e){
        e.preventDefault();
        var url='/igd/pemeriksaan-igd/cetak-pdf-surat-kematian?id='+encryptedId;
        window.open(url, '_blank');
    });
", View::POS_END, 'js2');
?>
