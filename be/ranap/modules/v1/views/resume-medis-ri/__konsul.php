<?php

use Doco\components\DocoHelpers;

?>
<table class="tbl tbl-bordered" width="100%">
    <tr>
        <th>No</th>
        <th width="15%">Tanggal Konsul</th>
        <th width="15%">Tanggal Selesai Konsul</th>
        <th>Nama Dokter</th>
        <th>Catatan Dokter</th>
        <th>Hasil Konsul</th>
    </tr>
    <?php
    if (!empty($data)) :
        $counter = 0;
        foreach ($data as $index => $record) :
        $counter++;
    ?>
            <tr>
                <td width="1"><?= $counter ?></td>
                <td class="text-center"><?= (new DocoHelpers)->convertDate($record['tgl_konsulpoli'], 'd-m-Y H:i') ?></td>
                <td class="text-center"><?= !empty($record['tgl_selesaikonsul']) ? (new DocoHelpers)->convertDate($record['tgl_selesaikonsul'], 'd-m-Y H:i') : '-'?></td>
                <td><?= $record['dok_mengkonsul'] ?></td>
                <td><?= $record['catatan_dokter_konsul'] ?></td>
                <td><?= !empty($record['jawaban_konsul']) ? $record['jawaban_konsul'] : '-' ?></td>
            </tr>
        <?php
        endforeach;
    else :
        ?>
        <tr>
            <td colspan="6">Data tidak tersedia</td>
        </tr>
    <?php
    endif;
    ?>
</table>
