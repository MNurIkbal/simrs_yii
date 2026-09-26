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
<table style="width: 100%">
    <tr>
        <td style="text-align: center;"><?=Yii::t('app', 'MASTER TARIF')?></td>
    </tr>
    <tr>
        <td style="text-align: center;"><?='Tanggal '.date("Y-m-d")?></td>
    </tr>
</table>
<br>
<table width="100%" class="tbl-bordered">
    <tr style="font-size: 13px">
        <th width="1">No</th>
        <th><?=\Yii::t("app", "Jenis Tindakan / Paket");?></th>
        <th><?=\Yii::t("app", "Nama Tindakan / Paket");?></th>
        <th><?=\Yii::t("app", "Kelas Pelayanan");?></th>
        <th><?=\Yii::t("app", "Cara bayar");?></th>
        <th><?=\Yii::t("app", "Penjamin");?></th>
        <th><?=\Yii::t("app", "Perda / SK");?></th>
        <th><?=\Yii::t("app", "Persen Cyto");?></th>
        <th><?=\Yii::t("app", "Persen Diskon");?></th>
        <th><?=\Yii::t("app", "Harga");?></th>
        <th><?=\Yii::t("app", "Status");?></th>
    </tr>
    <tbody style="font-size: 13px">
        <?php
        $no = 1;
        foreach($data as $value):
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?=$value['jenis_tindakan_paket']?></td>
            <td><?=$value['nama_tindakan_paket']?></td>
            <td><?=$value['kelaspelayanan_nama']?></td>
            <td><?=$value['carabayar_nama']?></td>
            <td><?=$value['penjamin_nama']?></td>
            <td><?=$value['perdanama_sk']?></td>
            <td><?=$value['persencyto_tindakan']?></td>
            <td><?=$value['persendiskon_tindakan']?></td>
            <td><?='Rp. '.number_format($value['harga_tariftindakan'], 0, ',', '.')?></td>
            <td><?= $var_aktif = ($value['is_active'] = 0 ? "Tidak Aktif" : "Aktif"); ?></td>
        </tr>
        <?php
        $no++;
        endforeach;
        ?>
    </tbody>
</table>
