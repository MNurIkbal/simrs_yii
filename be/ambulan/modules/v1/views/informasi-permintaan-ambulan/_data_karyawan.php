<?php
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

<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIK</th>
            <th>Jabatan</th>
       </tr>
    </thead>
    <tbody>
        <?php if(count($dataKaryawan) > 0):
            $no = 1; 
            foreach ($dataKaryawan as $key => $value) : 
        ?>
            <tr>
                <td width="5%" style="text-align: center;"><?= $no++ ?></td>
                <td><?= isset($value->nama_pegawai) ? $value->nama_pegawai : '-' ?></td>
                <td><?= isset($value->nomorindukpegawai) ? $value->nomorindukpegawai : '-'?></td>
                <td><?= isset($value->jabatan_nama) ? $value->jabatan_nama : '-' ?></td>
            </tr>
        <?php endforeach ?>
        <?php else: ?>
            <tr>
                <td class="text-center" colspan="4"><?=\Yii::t("app", "Tidak ada data.");?></td>
            </tr>
        <?php endif ?>
    </tbody>
</table>
