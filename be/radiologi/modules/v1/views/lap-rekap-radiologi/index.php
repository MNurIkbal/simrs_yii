<?php
   use Doco\components\DocoHelpers;
   use yii\helpers\ArrayHelper;
   
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
<br>
<table width="100%" class="tbl-bordered">
    <thead>
        <tr style="font-size: 13px">
            <th width="1">No</th>
            <th><?=\Yii::t("app", "Tanggal Persetujuan");?></th>
            <th><?=\Yii::t("app", "Kelas Pelayanan");?></th>
            <th><?=\Yii::t("app", "Pemeriksaan");?></th>
            <th><?=\Yii::t("app", "Tipe Prosedur");?></th>
            <th><?=\Yii::t("app", "Jumlah Pemeriksaan");?></th>
            <th><?=\Yii::t("app", "Harga Total");?></th>
        </tr>
    </thead>
    <tbody style="font-size: 13px">
        <?php
         $no = 1; foreach($data as $value): 
            $tglPersetujuan = ArrayHelper::getValue($value, 'tgl_persetujuan');
            $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
            $jenisKelamin = ArrayHelper::getValue($value, 'jenis_kelamin');
            $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
            $tanggalLahir = ArrayHelper::getValue($value, 'tanggal_lahir');
            $tanggalLahir = !empty($tanggalLahir) ? date('d M Y', strtotime($tanggalLahir)) : '';
            $pasien = $namaPasien.'/'.$jenisKelamin.'/'.$noRekamMedik.'/'.$tanggalLahir;
            $instalasiAsalNama = ArrayHelper::getValue($value, 'instalasiasal_nama');
            $rujukanDariNama = ArrayHelper::getValue($value, 'rujukandari_nama');
            $asalRujukanNama = ArrayHelper::getValue($value, 'asalrujukan_nama');
            $jenisRujukanId =  ArrayHelper::getValue($value, 'jenis_rujukan_id');
         ?>
         <tr>
            <td><?=$no?></td>
            <td><?= !empty($tglPersetujuan) ? date('d M Y', strtotime($tglPersetujuan)) : '-' ?></td>
            <td><?= ArrayHelper::getValue($value, 'kelaspelayanan_nama') ?></td>
            <td><?= ArrayHelper::getValue($value, 'daftartindakan_nama') ?></td>
            <td><?= ArrayHelper::getValue($value, 'tipe_prosedur') ?></td>
            <td style="text-align:right;"><?= ArrayHelper::getValue($value, 'jumlah') ?></td>
            <td style="text-align:right;"><?= number_format(ArrayHelper::getValue($value, 'total_harga', 0)) ?></td>
        </tr>
        <?php
        $no++;
        endforeach;
        ?>
    </tbody>
</table>
