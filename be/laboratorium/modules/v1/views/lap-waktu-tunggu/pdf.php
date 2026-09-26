<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use Doco\components\DocoHelpers;
?>
<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
</style>
<h3 align="center">Laporan Waktu Tunggu Pemeriksaan Laboratorium</h3>
<h4 align="center"><?= $ruangan_nama ?></h4>
<h5 align="center">Periode <?= date('d M Y', strtotime($tgl_awal_bulan))?> - <?= date('d M Y', strtotime($tgl_akhir_bulan))?></h5>
<table cellSpacing="2" width="100%" border="1" class="tbl-bordered ">
    <thead>
        <tr class="bg-inverse">
            <th width="1">No</th>
            <th><?=Yii::t('app', 'No pendaftaran'); ?></th>
            <th><?=Yii::t('app', 'No rekam medik'); ?></th>
            <th><?=Yii::t('app', 'Nama pasien'); ?></th>
            <th><?=Yii::t('app', 'Dokter'); ?></th>
            <th><?=Yii::t('app', 'Specimen'); ?></th>
            <th><?=Yii::t('app', 'Pemeriksaan'); ?></th>
            <th><?=Yii::t('app', 'Tanggal pendaftaran'); ?></th>
            <th><?=Yii::t('app', 'Tanggal specimen'); ?></th>
            <th><?=Yii::t('app', 'Tanggal hasil pemeriksaan'); ?></th>
            <th><?=Yii::t('app', 'Tanggal expertise'); ?></th>
            <th><?=Yii::t('app', 'Waktu tunggu'); ?> <br>(Specimen - Expertise)</th>
            <th><?=Yii::t('app', 'Waktu tunggu'); ?> <br>(Pendaftaran - Expertise)</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            $temp = [];
            $interval_sample = $interval_daftar = [];
            foreach ($model as $value) :
                $temp[$value['no_rekam_medik']] = $value['no_rekam_medik'];
                $tgl_sample = $value['tgl_ambilsample'] . ' ' . $value['jam_ambilsample'];
                $diffPendaftaran = DocoHelpers::getLamaTunggu($value['tglmasukpenunjang'], $value['tgl_expertise']);
                $diffSample = DocoHelpers::getLamaTunggu($tgl_sample, $value['tgl_expertise']);
                $interval_sample[] = DocoHelpers::timeToSeconds($diffSample);
                $interval_daftar[] = DocoHelpers::timeToSeconds($diffPendaftaran);
        ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= $value['no_pendaftaran'] ?></td>
                    <td><?= $value['no_rekam_medik'] ?></td>
                    <td><?= $value['nama_pasien'] ?></td>
                    <td><?= $value['dokter'] ?></td>
                    <td><?= $value['nama_sample'] ?></td>
                    <td><?= $value['pemeriksaanlab_nama'] ?></td>
                    <td><?= date('d M Y H:i:s', strtotime($value['tglmasukpenunjang'])) ?></td>
                    <td><?= date('d M Y H:i:s', strtotime($tgl_sample)) ?></td>
                    <td><?= date('d M Y H:i:s', strtotime($value['tgl_hasilpemeriksaanlab'])) ?></td>
                    <td><?= date('d M Y H:i:s', strtotime($value['tgl_expertise'])) ?></td>
                    <td><?= $diffSample ?></td>
                    <td><?= $diffPendaftaran ?></td>
                </tr>
        <?php
            $no++;
            endforeach;
            $find_average_second_sample = DocoHelpers::getAverage($interval_sample);
            $find_average_second_daftar = DocoHelpers::getAverage($interval_daftar);
            $average_lama_sample = DocoHelpers::secondsToTime($find_average_second_sample);
            $average_lama_daftar = DocoHelpers::secondsToTime($find_average_second_daftar);
        ?>
        <tr>
            <td colspan="4" align="center"><b>Jumlah pasien</b></td>
            <td colspan="3" align="center"><b><?= count($temp) ?></b></td>
            <td colspan="4" align="center"><b>Rata-rata waktu tunggu</b></td>
            <td><b><?= $average_lama_sample; ?></b></td>
            <td><b><?= $average_lama_daftar; ?></b></td>
        </tr>
    </tbody>
</table>
