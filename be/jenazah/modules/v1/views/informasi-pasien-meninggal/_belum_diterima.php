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

<strong>C.Pelayanan Yang Diharapkan Terhadap Jenazah</strong><br><br>
<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No.</th>
            <th>Nama Tindakan</th>
            <th>Qty</th>
            <th>Harga</th>
       </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($tindakan as $key => $value) : ?>
            <tr>
                <td width="5%" style="text-align: center;"><?= $no++ ?></td>
                <td><?= $value->tindakan_obat ?></td>
                <td style="text-align: right;"><?= $value->qty ?></td>
                <td style="text-align: right;">Rp. <?= DocoHelpers::formatNumber($value->tarif_satuan, 0) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table><br>
<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No.</th>
            <th>Nama Obat Alkes</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Harga</th>
       </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($obat as $key => $value) : ?>
            <tr>
                <td width="5%" style="text-align: center;"><?= $no++ ?></td>
                <td><?= $value->tindakan_obat ?></td>
                <td style="text-align: right;"><?= $value->qty ?></td>
                <td><?= $value->satuan ?></td>
                <td style="text-align: right;">Rp. <?= DocoHelpers::formatNumber($value->tarif_satuan, 0) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
