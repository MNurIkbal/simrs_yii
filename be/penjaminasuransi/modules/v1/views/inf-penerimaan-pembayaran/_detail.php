<?php
use Doco\components\DocoHelpers;
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
            <th>No.</th>
            <th>No Pengajuan</th>
            <th>Total Pengajuan</th>
            <th>Telah Bayar</th>
            <th>Pembayaran</th>
            <th>Sisa Piutang</th>
       </tr>
    </thead>
    <tbody>
        <?php $no = 1; 
            foreach ($data as $key => $value) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $value['no_pengajuanklaim'] ?></td>
                <td><?= DocoHelpers::rupiahDisplay($value['total_pengajuan']) ?></td>
                <td><?= DocoHelpers::rupiahDisplay($value['total_terbayar']) ?></td>
                <td><?= DocoHelpers::rupiahDisplay($value['pembayaran']) ?></td>
                <td><?= DocoHelpers::rupiahDisplay($value['total_sisapiutang']) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>