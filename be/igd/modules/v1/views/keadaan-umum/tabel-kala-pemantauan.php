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

<table width="100%" class="tbl-bordered">
  <thead  style="font-size: 13px">
        <tr class="bg-inverse">
            <th>Jam Ke</th>
            <th>Waktu</th>
            <th>Tekanan Darah /MmHg</th>
            <th>Nadi (Menit)</th>
            <th>Suhu (°C)</th>
            <th>Tinggi Fundus Uteri</th>
            <th>Kontraksi Uterus</th>
            <th>Kantung Kemih</th>
            <th>Darah yang Keluar</th>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
        <?php
            if (count($detail)) :
                foreach ($detail as $value) :
        ?>
            <tr>
                <td><?= $value['jam_ke'] ?></td>
                <td><?= date('d-M-Y H:i:s',strtotime($value['waktu'])) ?></td>
                <td><?= $value['td_systolic'] . '/' . $value['td_diastolic'] ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['detak_nadi']) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['suhu']) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tinggi_fundus']) ?></td>
                <td><?= $value['kontraksi_uterus'] ?></td>
                <td><?= $value['kandung_kemih'] ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['darah_keluar']) ?></td>
            </tr>
        <?php
                endforeach;
            else :
        ?>
            <tr>
                <td colspan="9" style="text-align: center;"><b>Data Kosong</b></td>
            </tr>
        <?php
            endif;
        ?>
    </tbody>
    <tfoot>

    </tfoot>
</table>