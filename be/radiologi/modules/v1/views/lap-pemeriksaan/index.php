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
            <th><?=\Yii::t("app", "Tanggal masuk");?></th>
            <th><?=\Yii::t("app", "No Pendaftaran");?></th>
            <th><?=\Yii::t("app", "No Rekam Medis");?></th>
            <th><?=\Yii::t("app", "Nama Pasien");?></th>
            <th><?=\Yii::t("app", "Nama dokter");?></th>
            <th><?=\Yii::t("app", "Nama Dokter Perujuk");?></th>
            <th><?=\Yii::t("app", "Kelas Pelayanan");?></th>
            <th><?=\Yii::t("app", "Kelompok pemeriksaan");?></th>
            <th><?=\Yii::t("app", "Jenis pemeriksaan");?></th>
            <th><?=\Yii::t("app", "Contrast");?></th>
            <th><?=\Yii::t("app", "Nama pemeriksaan");?></th>
            <th><?=\Yii::t("app", "Harga satuan (Rp.)");?></th>
            <th><?=\Yii::t("app", "Qty");?></th>
            <th><?=\Yii::t("app", "Cyto (Rp.)");?></th>
            <th><?=\Yii::t("app", "Total (Rp.)");?></th>
        </tr>
    </thead>
    <tbody style="font-size: 13px">
        <?php
        $no = 1;
        $kelompok = [];
        $jenispemeriksaan = [];
        $namapemeriksaan = [];
        $totalsatuan = 0;
        $qty = 0;
        $cyto = 0;
        $total = 0;
        foreach($data as $value):
            $tarif_satuan = ArrayHelper::getValue($value, 'tarif_satuan', 0);
            $tarifcyto_tindakan = ArrayHelper::getValue($value, 'tarifcyto_tindakan', 0);
            $qty_tindakan = ArrayHelper::getValue($value, 'qty_tindakan', 0);
            $nama_kelompok = ArrayHelper::getValue($value, 'nama_kelompok', '');
            $jenispemeriksaanrad_nama = ArrayHelper::getValue($value, 'jenispemeriksaanrad_nama', '');
            $daftartindakan_nama = ArrayHelper::getValue($value, 'daftartindakan_nama', '');
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?= isset($value['tglmasukpenunjang']) ? date('d M Y', strtotime($value['tglmasukpenunjang'])) : '' ?></td>
            <td><?= ArrayHelper::getValue($value, 'no_pendaftaran', '') ?></td>
            <td><?= ArrayHelper::getValue($value, 'no_rekam_medik', '') ?></td>
            <td><?= ArrayHelper::getValue($value, 'nama_pasien', '') ?></td>
            <td><?= ArrayHelper::getValue($value, 'dokter', '') ?></td>
            <td><?= ArrayHelper::getValue($value, 'dokter_perujuk_nama', '') ?></td>
            <td><?= ArrayHelper::getValue($value, 'kelaspelayanan_nama', '') ?></td>
            <td><?= $nama_kelompok ?></td>
            <td><?= $jenispemeriksaanrad_nama ?></td>
            <td><?= ArrayHelper::getValue($value, 'status_contrast', '') ?></td>
            <td><?= $daftartindakan_nama ?></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber($tarif_satuan)?></td>
            <td style="text-align: right;"><?=DocoHelpers::formatNumber($qty_tindakan)?></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber($tarifcyto_tindakan)?></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber(($tarif_satuan+$tarifcyto_tindakan)*$qty_tindakan)?></td>
        </tr>
        <?php
        $totalsatuan += $tarif_satuan;
        $cyto += $tarifcyto_tindakan;
        $total += ($tarif_satuan+$tarifcyto_tindakan)*$qty_tindakan;
        $qty += $qty_tindakan;
        if(!in_array($nama_kelompok, $kelompok)){
            array_push($kelompok, $nama_kelompok);
        }
        if(!in_array($jenispemeriksaanrad_nama, $jenispemeriksaan)){
            array_push($jenispemeriksaan, $jenispemeriksaanrad_nama);
        }
        if(!in_array($daftartindakan_nama, $namapemeriksaan)){
            array_push($namapemeriksaan, $daftartindakan_nama);
        }
        $no++;
        endforeach;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" style="text-align: center;"><b>Jumlah</b></td>
            <td><?=count($kelompok)?></td>
            <td><?=count($jenispemeriksaan)?></td>
            <td></td>
            <td><?=count($namapemeriksaan)?></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber($totalsatuan)?></td>
            <td style="text-align: right;"><?=DocoHelpers::formatNumber($qty)?></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber($cyto)?></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber($total)?></td>
        </tr>
    </tfoot>
</table>