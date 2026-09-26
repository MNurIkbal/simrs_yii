<?php

/**
 * @Author: rizaal
 * @Date:   2018-07-19 
 */
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

<table border="0" style="width: 100%">
    <tr>
            <td valign='top'>
	        <?php foreach ($header1 as $key => $value) : ?>
                <?=$key?>
                <br>
    	    <?php endforeach; ?>
            </td>
            <td valign='top'>
	        <?php foreach ($header1 as $key => $value) : ?>
                <?=$value?>
                <br>
    	    <?php endforeach; ?>
            </td>

            <td valign='top'>
	        <?php foreach ($header2 as $key => $value) : ?>
                <?=$key?>
                <br>
    	    <?php endforeach; ?>
            </td>
            <td valign='top'>
	        <?php foreach ($header2 as $key => $value) : ?>
                <?=$value?>
                <br>
    	    <?php endforeach; ?>
            </td>
    </tr>
</table>

<br>
DATA OBAT SEBELUM DIRAWAT
<br>
<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <th rowspan=2 width="1">No</th>
            <th rowspan=2><?= \Yii::t("app", "Nama obat"); ?></th>
            <th rowspan=2><?= \Yii::t("app", "Jumlah"); ?></th>
            <th rowspan=2><?= \Yii::t("app", "Satuan kecil"); ?></th>
            <th rowspan=2><?= \Yii::t("app", "Signa"); ?></th>
            <th rowspan=2><?= \Yii::t("app", "Rute"); ?></th>
            <th rowspan=2><?= \Yii::t("app", "Waktu pemberian terakhir"); ?></th>
            <th colspan=2><?= \Yii::t("app", "Keputusan dokter"); ?></th>
            <th colspan=2><?= \Yii::t("app", "Kelayakan obat"); ?></th>
            <th rowspan=2><?= \Yii::t("app", "Terapi"); ?></th>
            <th rowspan=2><?= \Yii::t("app", "Signa"); ?></th>
            <th rowspan=2><?= \Yii::t("app", "Rute"); ?></th>
        </tr>
        <tr>
            <th><?= \Yii::t("app", "Lanjut/tidak?"); ?></th>
            <th><?= \Yii::t("app", "Catatan"); ?></th>
            <th><?= \Yii::t("app", "Layak"); ?></th>
            <th><?= \Yii::t("app", "Tidak"); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        foreach($detail as $value):
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $value['nama_obat'] ? : ''; ?></td>
            <td><?= $value['qty_obat'] ? : ''; ?></td>
            <td><?= $value['satuan_kecil'] ? : ''; ?></td>
            <td><?= $value['signa'] ? : ''; ?></td>
            <td><?= $value['rute_obat'] ? : ''; ?></td>
            <td><?= $value['waktu_pemberian'] ? : ''; ?></td>
            <td><?= $value['is_lanjut'] !== null ? Yii::t('app', ($value['is_lanjut'] === true ? 'Ya' : 'Tidak')) : ''; ?></td>
            <td><?= $value['catatan'] ? : ''; ?></td>
            <td><?= $value['qty_layak'] !== null ? ($value['qty_layak'] ? : 0) : ''; ?></td>
            <td><?= $value['qty_tidaklayak'] !== null ? ($value['qty_tidaklayak'] ? : 0) : ''; ?></td>
            <td><?= $value['terapi'] ? : ''; ?></td>
            <td><?= $value['signaterapi'] ? : ''; ?></td>
            <td><?= $value['rute_kelayakan'] ? : ''; ?></td>
        </tr>
        <?php 
        endforeach;
        ?>
    </tbody>
</table>