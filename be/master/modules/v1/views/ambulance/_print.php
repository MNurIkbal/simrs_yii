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

<strong>Tarif Ambulan</strong><br>
<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No</th>
            <th>Nama Tindakan</th>
            <th>Biaya Tetap</th>
       </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($tindakan as $key => $value) : ?>
            <tr>
                <td width="5%" style="text-align: center;"><?= $no++ ?></td>
                <td><?= $value->daftartindakan_nama ?></td>
                <td><?= $value->biaya_tetap ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<br>
<strong>Default Obat Alkes Yang Dibawa</strong><br><br>
<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No</th>
            <th>Nama Obat Alkes</th>
            <th>Qty</th>
       </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($obat as $key => $value) : ?>
            <tr>
                <td width="5%" style="text-align: center;"><?= $no++ ?></td>
                <td><?= $value->obatalkes_nama ?></td>
                <td style="text-align: right;"><?= $value->qty.' '.$value['satuanunit_nama'] ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>