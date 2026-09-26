<?php

use app\components\DocoHelpers;

?>

<style>
    .tooltip-inner {
        white-space: nowrap;
        max-width: none;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <a id="info-heading" data-toggle="collapse" href="#patient-info-tab" role="button" aria-expanded="false" aria-controls="patient-info-tab">
                <div class="panel-heading flex-container ">
                    <h6 class="panel-title text-bold">Informasi Pasien</h6>
                    <p class="p-data" id="data-pasien">
                        <?= isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-' ?> - 
                        <span class="text-bold"><?= isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-' ?></span>
                        (<?= isset($data_pasien['tanggal_lahir']) ? date('d-m-Y', strtotime($data_pasien['tanggal_lahir'])) : '-' ?>)
                        <?php
                            if (array_key_exists('titipan', $data_pasien)) {
                                if ($data_pasien['titipan'] == true) {
                                    echo ' - <b><span style="color:red;"> KELAS TAGIHAN : '.$data_pasien['kelas_ditagihkan'].'</span></b>';
                                }
                            }
                        ?>
                    </p>
                    
                    <ul class="icons-list">
                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                    </ul>
                </div>
            </a>

            <div class="panel-body collapse multi-collapse label-information" id="patient-info-tab">
                <div class="row">
                    <div class="col-md-3">
                        <label>Pasien</label>
                        <p><?= $data_pasien['no_rekam_medik'] ?></p>
                        <p><?= $data_pasien['nama_pasien'] ?> - <?= $data_pasien['jenis_kelamin'] ?></p>
                    </div>
                    <div class="col-md-3">
                        <label>Nomor Pendaftaran</label>
                        <p><?= $data_pasien['no_pendaftaran'] ?></p>
                        <p><?= date("d-m-Y H:i:s", strtotime($data_pasien['tgl_pendaftaran'])) ?></p>
                    </div>
                    <div class="col-md-3">
                        <label>Dokter DPJP</label>
                        <p><?= $data_pasien['nama_pegawai'] ?></p>
                    </div>
                    <div class="col-md-3">
                        <label>Kamar</label>
                        <p><?= isset($data_pasien['kamarruangan_nama']) ? $data_pasien['kamarruangan_nama'] : '-' ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label>Tanggal Lahir</label>
                        <p><?= DocoHelpers::convertDate($data_pasien['tanggal_lahir'], 'd-m-Y') ?></p>
                    </div>
                    <div class="col-md-3">
                        <label>Kelas Pelayanan</label>
                        <p><?= $data_pasien['kelaspelayanan_nama'] ?> - <?= $data_pasien['carabayar_nama'] ?> - <?= $data_pasien['penjamin_nama'] ?></p>
                    </div>
                    <div class="col-md-3">
                        <label>Kasus Penyakit</label>
                        <p><?= $data_pasien['jeniskasuspenyakit_nama'] ?></p>
                    </div>
                    <div class="col-md-3">
                        <label>Status</label>
                        <p><?= $data_pasien['status_periksa_nama'] ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label>Umur</label>
                        <p><?= $data_pasien['umur'] ?></p>
                    </div>
                    <div class="col-md-3">
                        <label>Alamat Pasien</label>
                        <p><?= isset($data_pasien['alamat_pasien']) && !empty($data_pasien['alamat_pasien']) ? $data_pasien['alamat_pasien'] : '-' ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label>Tanggal Masuk Kamar</label>
                        <p><?= date("d-m-Y H:i:s", strtotime($data_pasien['tgl_admisi'])) ?></p>
                    </div>
                    <div class="col-md-3">
                        <label>Tanggal Keluar Kamar</label>
                        <p><?= date("d-m-Y H:i:s", strtotime($modelPulang['tglpasienpulang'])) ?></p>
                    </div>
                    <div class="col-md-3">
                        <label>Lama Dirawat</label>
                        <p><?= $daysLamaRawat ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
