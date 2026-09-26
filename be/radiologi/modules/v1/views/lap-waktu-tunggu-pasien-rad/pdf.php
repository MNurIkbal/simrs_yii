<?php
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use Doco\components\DocoHelpers;
?>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="0" width="100%" border="1">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?= \Yii::t("app", "Tanggal Rujukan"); ?></th>
                        <th><?= \Yii::t("app", "No Pendaftaran"); ?></th>
                        <th><?= \Yii::t("app", "Pasien"); ?></th>
                        <th><?= \Yii::t("app", "Dokter Radiologi"); ?></th>
                        <th><?= \Yii::t("app", "Jenis Pemeriksaan"); ?></th>
                        <th><?= \Yii::t("app", "Nama Pemeriksaan"); ?></th>
                        <th><?= \Yii::t("app", "Jenis Rujukan"); ?></th>
                        <th><?= \Yii::t("app", "Asal Rujukan / Nama RS"); ?></th>
                        <th><?= \Yii::t("app", "Tanggal Pendaftaran"); ?></th>
                        <th><?= \Yii::t("app", "Tanggal Persetujuan"); ?></th>
                        <th><?= \Yii::t("app", "Tanggal Ambil Foto"); ?></th>
                        <th><?= \Yii::t("app", "Tanggal Expertise"); ?></th>
                        <th><?= \Yii::t("app", "Waktu Tunggu Pendaftaran Expertise"); ?></th>
                        <th><?= \Yii::t("app", "Waktu Tunggu Pemeriksaan Radiologi"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $no = 1;
                    $temp = [];
                    $interval_sample = $interval_daftar = [];
                    foreach ($model as $value) :
                        $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
                        $jenisKelamin = ArrayHelper::getValue($value, 'jeniskelamin');
                        $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
                        $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
                        $tanggalLahir = ArrayHelper::getValue($value, 'tanggal_lahir');
                        $tglHasil = ArrayHelper::getValue($value, 'tgl_hasilrad');
                        $tglMasukPenunjang = ArrayHelper::getValue($value, 'tglmasukpenunjang');
                        $tglAmbilFoto = ArrayHelper::getValue($value, 'tgl_ambilfoto');
                        $tglPersetujuan = ArrayHelper::getValue($value, 'tglpersetujuan');
                        $temp[$noRekamMedik] = $noRekamMedik;
                        $diffTglExpertise = !empty($tglHasil) ? (new DocoHelpers)->getLamaTunggu($tglMasukPenunjang, $tglHasil) : '-';
                        $diffFotoExpertise = !empty($tglHasil) ? (new DocoHelpers)->getLamaTunggu($tglAmbilFoto, $tglHasil) : '-';
                        $interval_expertise[] = (new DocoHelpers)->timeToSeconds($diffTglExpertise);
                        $interval_foto[] = (new DocoHelpers)->timeToSeconds($diffFotoExpertise);

                        $instalasiAsalNama = ArrayHelper::getValue($value, 'instalasiasal_nama');
                        $rujukanDariNama = ArrayHelper::getValue($value, 'rujukandari_nama');
                        $asalRujukanNama = ArrayHelper::getValue($value, 'asalrujukan_nama');
                        $jenisRujukanId =  ArrayHelper::getValue($value, 'jenis_rujukan_id');
                        $asalRujukan = '';
                        if($jenisRujukanId == 4) {
                            $asalRujukan = $asalRujukanNama.'/'.$rujukanDariNama;
                        }
                        elseif($jenisRujukanId == 1) {
                            $asalRujukan = $asalRujukanNama;
                        }
                        elseif($jenisRujukanId == 2) {
                            $asalRujukan = $asalRujukanNama.'/'.$rujukanDariNama;
                        }

                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= date('d-M-Y H:i:s',strtotime($tglMasukPenunjang)) ?></td>
                            <td><?= $noPendaftaran ?></td>
                            <td><?= $namaPasien.'/'.$jenisKelamin.'/'.$noRekamMedik.'/'.$tanggalLahir ?></td>
                            <td><?= ArrayHelper::getValue($value, 'dokter') ?></td>
                            <td><?= ArrayHelper::getValue($value, 'jenispemeriksaanrad_nama') ?></td>
                            <td><?= ArrayHelper::getValue($value, 'daftartindakan_nama') ?></td>
                            <td><?= ArrayHelper::getValue($value, 'jenis_rujukan') ?></td>
                            <td><?= $asalRujukan ?></td>
                            <td><?= (new DocoHelpers)->convDateTime($tglMasukPenunjang, true, true) ?></td>
                            <td><?= !empty($tglPersetujuan) 
                                ? (new DocoHelpers)->convDateTime($tglPersetujuan, true, true) 
                                : (new DocoHelpers)->convDateTime($tglMasukPenunjang, true, true) ?></td>
                            <td><?= (new DocoHelpers)->convDateTime($tglAmbilFoto, false, true) ?></td>
                            <td><?= (new DocoHelpers)->convDateTime($tglHasil, false, true) ?></td>
                            <td><?= $diffTglExpertise ?></td>
                            <td><?= $diffFotoExpertise ?></td>
                        </tr>
                    <?php
                    $no++;
                    endforeach;
                    $find_average_second_expertise = (new DocoHelpers)->getAverage($interval_expertise);
                    $find_average_second_foto = (new DocoHelpers)->getAverage($interval_foto);
                    $average_lama_expertise = (new DocoHelpers)->secondsToTime($find_average_second_expertise);
                    $average_lama_foto = (new DocoHelpers)->secondsToTime($find_average_second_foto);
                    ?>

                     <tr>
                        <td colspan="4" align="center"><b>Jumlah pasien</b></td>
                        <td colspan="5" align="center"><b><?= count($temp) ?></b></td>
                        <td colspan="4" align="center"><b>Rata-rata waktu tunggu</b></td>
                        <td><b><?= $average_lama_expertise; ?></b></td>
                        <td><b><?= $average_lama_foto; ?></b></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
</div>