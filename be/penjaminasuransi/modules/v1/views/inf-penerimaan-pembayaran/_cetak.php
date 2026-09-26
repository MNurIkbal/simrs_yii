<?php

use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

?>
<style type="text/css">
    tr, th {
        font-family: Arial;
    }

    tr, td {
        font-family: Arial;
    }

    .tbl-bordered {
        border-collapse: collapse;
        font-family: Arial;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
        font-size: 11px;
    }

    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
        font-size: 11px;
    }
</style>

<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No</th>
            <th>Tanggal penerimaan</th>
            <th>No Pembayaran</th>
            <th>Cara Bayar / Penjamin</th>
            <th>Total Pembayaran</th>
            <th>Status Alokasi</th>
       </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1; 
            foreach ($data as $key => $value) : 
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date(' d M Y', strtotime($value['tgl_terimabayarklaim'])) ?></td>
                <td><?= $value['no_terimabayarklaim'] ?></td>
                <td><?= "<b>" . $value['carabayar_nama'] . "</b>" . "<br>" . $value['penjamin_nama'] ?></td>
                <td>Rp.<?= DocoHelpers::formatNumber($value['total_terimabayar'],0) ?></td>
                <td><?= isset(DocoConstants::$statusAlokasi[$value['final_alokasi']]) 
                    ? DocoConstants::$statusAlokasi[$value['final_alokasi']] : null ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>