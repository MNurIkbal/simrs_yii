<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use Doco\components\DocoHelpers;
?>

<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-family: Tahoma;
        font-size: 11px;
    }

    .tbl-bordered th {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
        /*border-bottom: 1px solid black;*/
        padding: 5px;
    }

    .tbl-bordered tr td.border-bottom {
        border-bottom: 1px solid black;
    }

    .body-border td {
        border-bottom: 1px solid black;
    }

    .tbl-bordered tr.border-top td {
        border-top: 1px solid black;
    }

    .bold {
        font-weight: bold;
    }

    .header-right {
        padding-right: 15px;
    }

    .heading-bottom {
        background: linear-gradient(to bottom, transparent 3px, black 3px, black 6px, transparent 6px) no-repeat;
        border-bottom: 1px solid black;
        display: inline-block;
        vertical-align: bottom;
        padding-bottom: 10px;

    }
</style>

<table width="100%" class="tbl-bordered">
    <tr class="bg-inverse" style="font-size: 13px">
        <th width="1" rowspan="2">No.</th>
        <th rowspan="2">Kode</th>
        <th rowspan="2">Nama</th>
        <th colspan="3">Pemakaian (Hari)</th>
        <th rowspan="2">DOI</th>
        <th rowspan="2">SSMin</th>
        <th rowspan="2">Kriteria</th>
        <th colspan="3">Stok</th>
        <th colspan="3">Qty</th>
        <th rowspan="2">Satuan</th>
        <th rowspan="2">Konversi</th>
        <th rowspan="2">Catatan</th>
        <th rowspan="2">Status</th>
        <th rowspan="2">Nomer PO </th>
    </tr>
    <tr>
        <th>7</th>
        <th>14</th>
        <th>30</th>
        <th>Store</th>
        <th>Pharm</th>
        <th>R. Lain</th>
        <th>Outs. PO</th>
        <th>Sgst</th>
        <th>PR</th>
    </tr>
    <tbody>
        <?php
        foreach ($content as $value) :
        ?>
            <tr>
                <td align="center"><?= $value['no'] ?></td>
                <td><?= $value['kode'] ?></td>
                <td><?= $value['nama'] ?></td>
                <td align="right"><?= $value['last_7'] ?></td>
                <td align="right"><?= $value['last_14'] ?></td>
                <td align="right"><?= $value['last_30'] ?></td>
                <td align="right"><?= $value['doi'] ?></td>
                <td align="right"><?= $value['ssmin'] ?></td>
                <td align="center"><?= $value['criteria'] ?></td>
                <td align="right"><?= $value['stok_gudang'] ?></td>
                <td align="right"><?= $value['stok_farmasi'] ?></td>
                <td align="right"><?= $value['stok_ruanganlain'] ?></td>
                <td align="right"><?= $value['qty_outstanding'] ?></td>
                <td align="right"><?= $value['qty_sugesstion'] ?></td>
                <td align="right"><?= $value['qty_final'] ?></td>
                <td align="center"><?= $value['satuan'] ?></td>
                <td><?= $value['konversi'] ?></td>
                <td><?= $value['catatan'] ?></td>
                <td><?= $value['status'] ?></td>
                <td><?= $value['nomor_po'] ?></td>
            </tr>
        <?php
        endforeach;
        ?>
    </tbody>
</table>
