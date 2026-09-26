<?php

use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

$instalasi_id = DocoHelpers::encrypt(Yii::$app->docoVars->workspace('instalasi_id'));
use app\components\Pelayanan\PelayananHelpers;

?>

<style>
    .tooltip-inner {
        white-space: nowrap !important;
        max-width: none !important;
    }
    .button-cara-bayar{
        border-radius: 2px;
        background-color: white;
        border: 2px solid <?= $data_pasien['carabayar_kode_warna']?>;
        padding: 2px;
        font-size: 12px;
    }

    .floating-sticky {
        position: fixed;
        top: 137px;
        width: 100%;
        padding: 5px 2% 0 2%;
        z-index: 25;
        left: 10px;
        background: #f5f5f5;
        box-shadow: 0 3px 5px -5px #000;
    }

    .margin-bottom-5 {
        margin-bottom: 5px !important
    }
    a.expand-data{
        color: #4fbfa3;
        font-weight: 700;
    }
    .button-alergi{
        border-radius: 2px;
        background-color: white;
        border: 2px solid rgb(214, 69, 65);
        padding: 2px 4px;
        font-size: 12px;
    }

</style>

<div class="row patient-informations">
    <div class="col-md-8">
        <div class="panel panel-default margin-bottom-5">
            <a id="info-heading" data-toggle="collapse" href="#patient-info-tab" role="button" aria-expanded="false" aria-controls="patient-info-tab">
                <div class="panel-heading flex-container ">
                    <h6 class="panel-title text-bold" style="padding-top: 3px">Informasi Pasien</h6>
                    <p class="p-data" id="data-pasien" style="padding-top: 3px">
                        <?= isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-' ?> -
                        <span class="text-bold"><?= isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-' ?></span>
                        (<?= ArrayHelper::getValue($data_pasien, 'umur', '-') ?>) - <?= isset($data_pasien['no_telepon_pasien']) ? $data_pasien['no_telepon_pasien'] : '-' ?>
                        <?php
                        if (array_key_exists('titipan', $data_pasien)) {
                            if ($data_pasien['titipan'] == true) {
                                echo ' - <b><span style="color:red;"> KELAS TAGIHAN : ' . $data_pasien['kelas_ditagihkan'] . '</span></b>';
                            }
                        }
                        ?>
                    </p>

                    <ul class="icons-list" style="padding-top: 3px">
                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                    </ul>
                </div>
            </a>

            <div class="panel-body collapse multi-collapse label-information" id="patient-info-tab">
                <div class="row">
                    <div class="col-md-3">
                        <label>Pasien</label>
                        <a data-toggle='modal' data-target="#modal-informasi-pasien" action="view-detail-patient?pasien_id=<?=$data_pasien['pasien_id']?>&instalasi_id=<?=$instalasi_id?>">
                            <p><?= $data_pasien['no_rekam_medik'] ?></p>
                            <p><?= $data_pasien['nama_pasien'] ?> - <?= $data_pasien['jenis_kelamin'] ?></p>
                        </a>
                        <p><?php
                          if (ArrayHelper::getValue($data_pasien, 'jenisidentitas_nama') != null && ArrayHelper::getValue($data_pasien, 'no_identitas_pasien') != null) {
                              echo ArrayHelper::getValue($data_pasien, 'jenisidentitas_nama', '-') .' - '. ArrayHelper::getValue($data_pasien, 'no_identitas_pasien');
                          } else {
                              echo ' - ';
                          }
                         ?></p>
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
                        <label>Nomor Telepon</label>
                        <p><?= isset($data_pasien['no_telepon_pasien']) && !empty($data_pasien['no_telepon_pasien']) ? $data_pasien['no_telepon_pasien'] : '-' ?></p>
                    </div>
                    <div class="col-md-6">
                        <label>Alamat Pasien</label>
                        <p><?= isset($data_pasien['alamat_pasien']) && !empty($data_pasien['alamat_pasien']) ? $data_pasien['alamat_pasien'] : '-' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default margin-bottom-5">
            <a id="heading-history-patient" data-toggle="collapse" href="#patient-history-tab" role="button" aria-expanded="false" aria-controls="patient-history-tab">
                <?php
                if (!empty($data_pasien['catatanpenting_pasien']) && $data_pasien['catatanpenting_pasien'] != '') :
                    $title = 'Catatan Penting';
                ?>
                    <div class="panel-heading flex-container ">
                        <h6 class="panel-title text-bold">
                            Riwayat Personal &nbsp; <button class="button-cara-bayar"><?= $data_pasien['carabayar_nama']?></button>
                            &nbsp;<?php if(isset($data_pasien['riwayat_pasien']['alergi']) && !empty($data_pasien['riwayat_pasien']['alergi'])): ?><button class="button-alergi">Alergi</button><?php endif; ?>
                            <span data-toggle="tooltip" title="Pasien Memiliki <?= $title ?>" rel="tooltip">
                                <i class="fa fa-info-circle" style="color: #FC8338;"></i>
                            </span>
                        </h6>
                        <ul class="icons-list">
                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                        </ul>
                    </div>
                <?php else : ?>
                    <div class="panel-heading flex-container ">
                        <h6 class="panel-title text-bold">
                            Riwayat Personal &nbsp; <button class="button-cara-bayar"><?= $data_pasien['carabayar_nama']?></button>
                            &nbsp;<?php if(isset($data_pasien['riwayat_pasien']['alergi']) && !empty($data_pasien['riwayat_pasien']['alergi'])): ?><button class="button-alergi">Alergi</button><?php endif; ?>
                        </h6>
                        <ul class="icons-list">
                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </a>

            <div class="panel-body collapse multi-collapse label-information" id="patient-history-tab">
                <div class="row">
                    <div class="col-md-12">
                        <label>Catatan Penting Pasien</label>
                        <p class="catatan-penting-pasien-text"><?= !empty($data_pasien['catatanpenting_pasien']) ? $data_pasien['catatanpenting_pasien'] : '-' ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Pekerjaan</label>
                        <p class="pekerjaan-nama-text"><?= !empty($data_pasien['pekerjaan_nama']) ? $data_pasien['pekerjaan_nama'] : '-' ?></p>
                    </div>
                    <div class="col-md-4">
                        <label>Riwayat Penyakit Keluarga</label>
                        <p class="riwayat-penyakit-keluarga-text"><?= isset($data_pasien['askep']) ? str_replace([',00', ', 00'],'',$data_pasien['askep']['riwayat_penyakit_keluarga'])  : '-' ?></p>
                    </div>
                    <div class="col-md-4">
                        <label>Diagnosa Dokter</label>
                        <p class="diagnosa-dokter-text"><?= isset($data_pasien['cppt']) && isset($data_pasien['cppt']['a_diag_utama']['text']) ? $data_pasien['cppt']['a_diag_utama']['text'] : '-' ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Status Merokok</label>
                        <p class="status-merokok-text"><?= isset($data_pasien['askep']) && $data_pasien['askep']['status_merokok'] ? 'Ya' : '-' ?></p>
                    </div>
                    <div class="col-md-4">
                        <label>Riwayat Sosial Ekonomi</label>
                        <p class="riwayat-sosial-ekonomi-text"><?= isset($data_pasien['askep']) ? ucwords(str_replace("_", " ", $data_pasien['askep']['status_ekonomi'])) : '-' ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Riwayat Penyakit</label>
                        <div class="can-expanded">
                            <?php
                                if(isset($data_pasien['riwayat_pasien']['penyakit_dahulu']) && !empty($data_pasien['riwayat_pasien']['penyakit_dahulu'])):
                                    foreach($data_pasien['riwayat_pasien']['penyakit_dahulu'] as $key => $value):
                                        echo strlen($value) > 16 ? PelayananHelpers::cutTextToTooltips($value, 16) : '<p class="riwayat-penyakit-dahulu">'. $value .'</p>';
                                    endforeach;
                                else:
                                    echo '<p class="riwayat-penyakit-dahulu"> - </p>';
                                endif;
                            ?>
                        </div>
                        <?php if(isset($data_pasien['riwayat_pasien']['penyakit_dahulu']) && count($data_pasien['riwayat_pasien']['penyakit_dahulu']) > 3):
                            echo '<a href="#" class="expand-data" data-text="+'. (count($data_pasien['riwayat_pasien']['penyakit_dahulu']) - 2 ).' Penyakit lainnya"> +'. (count($data_pasien['riwayat_pasien']['penyakit_dahulu']) - 2 ).' Penyakit lainnya</a>';
                        endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label>Riwayat Alergi</label>
                        <div class="can-expanded">
                            <?php
                                if(isset($data_pasien['riwayat_pasien']['alergi']) && !empty($data_pasien['riwayat_pasien']['alergi'])):
                                    foreach($data_pasien['riwayat_pasien']['alergi'] as $key => $value):
                                        echo strlen($value) > 16 ? PelayananHelpers::cutTextToTooltips($value, 16) : '<p class="riwayat-alergi">'. $value .'</p>';
                                    endforeach;
                                else:
                                    echo '<p class="riwayat-alergi"> - </p>';
                                endif;
                            ?>
                        </div>
                        <?php if(isset($data_pasien['riwayat_pasien']['alergi']) && count($data_pasien['riwayat_pasien']['alergi']) > 3):
                            echo '<a href="#" class="expand-data" data-text="+'. (count($data_pasien['riwayat_pasien']['alergi']) - 2) .' Alergi lainnya"> +'. (count($data_pasien['riwayat_pasien']['alergi']) - 2) .' Alergi lainnya</a>';
                        endif;?>
                    </div>
                    <div class="col-md-4">
                        <label>Riwayat Obat</label>
                        <div class="can-expanded">
                            <?php
                                if(isset($data_pasien['riwayat_pasien']['pengobatan']) && !empty($data_pasien['riwayat_pasien']['pengobatan'])):
                                    foreach($data_pasien['riwayat_pasien']['pengobatan'] as $key => $value):
                                        echo strlen($value) > 16 ? PelayananHelpers::cutTextToTooltips($value, 16) : '<p class="riwayat-pengobatan">'. $value .'</p>';
                                    endforeach;
                                else:
                                    echo '<p class="riwayat-pengobatan"> - </p>';
                                endif;
                            ?>
                        </div>
                        <?php if(isset($data_pasien['riwayat_pasien']['pengobatan']) && count($data_pasien['riwayat_pasien']['pengobatan']) > 3):
                            echo '<a href="#" class="expand-data" data-text="+'. (count($data_pasien['riwayat_pasien']['pengobatan']) - 2) .' Obat lainnya"> +'. (count($data_pasien['riwayat_pasien']['pengobatan']) - 2) .' Obat lainnya</a>';
                        endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
