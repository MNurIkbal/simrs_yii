<?php
use Doco\components\DocoConstants;

?>

<table style="width:100%" border="1" cellpadding="5" cellspacing="1">
    <thead>
        <tr>
            <td>No</td>
            <th><?=\Yii::t("app", "Tanggal Operasi");?></th>
            <th><?=\Yii::t("app", "No Pendaftaran");?></th>
            <th><?=\Yii::t("app", "Nama Pasien");?></th>
            <th><?=\Yii::t("app", "Ruangan Perujuk");?></th>
            <th><?=\Yii::t("app", "Nama Penjamin");?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($data as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['tgl_operasi']) ? date('d M Y', strtotime($value['tgl_operasi'])) : '' ?></td>
                <td><?= isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '' ?></td>
                <td><?= isset($value['nama_pasien']) ? $value['nama_pasien'] : '' ?></td>
                <td><?= isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '' ?></td>
                <td><?= isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '' ?></td>
        <?php
            $no++;
            endforeach;
        ?>
    </tbody>
</table>