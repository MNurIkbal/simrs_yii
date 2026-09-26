<?php
use Doco\components\DocoHelpers;
?>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <th><?=\Yii::t("app", "Ruangan");?></th>
            <th><?=\Yii::t("app", "Penjamin");?></th>
            <th><?=\Yii::t("app", "Kelompok Pemeriksaan");?></th>
            <th><?=\Yii::t("app", "Jenis Pemeriksaan");?></th>
            <th><?=\Yii::t("app", "Nama Pemeriksaan");?></th>
            <th><?=\Yii::t("app", "Kelas Pelayanan");?></th>
            <th><?=\Yii::t("app", "Tarif Total");?></th>
            <th><?=\Yii::t("app", "Cyto Tindakan (%)");?></th>
            <th><?=\Yii::t("app", "Diskon Tindakan (%)");?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($query as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '' ?></td>
                <td><?= isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '' ?></td>
                <td><?= isset($value['nama_kelompok']) ? $value['nama_kelompok'] : '' ?></td>
                <td><?= isset($value['jenispemeriksaanlab_nama']) ? $value['jenispemeriksaanlab_nama'] : '' ?></td>
                <td><?= isset($value['pemeriksaanlab_nama']) ? $value['pemeriksaanlab_nama'] : '' ?></td>
                <td><?= isset($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '' ?></td>
                <td>
                    <?= isset($value['harga_tariftindakan']) 
                                ? DocoHelpers::formatNumber($value['harga_tariftindakan']) : '' ?>
                </td>
                <td><?= isset($value['persencyto_tindakan']) ? $value['persencyto_tindakan'] : '' ?></td>
                <td><?= isset($value['persendiskon_tindakan']) ? $value['persendiskon_tindakan'] : '' ?></td>
            </tr>
        <?php
            $no++;
            endforeach;
        ?>
    </tbody>
</table>