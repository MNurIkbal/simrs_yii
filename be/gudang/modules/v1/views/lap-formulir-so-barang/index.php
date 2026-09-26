<?php
    use Doco\components\DocoHelpers;
?>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Tanggal Formulir</td>
            <td>Periode Stok</td>
            <td>Nomer Formulir</td>
            <td>Harga Netto Sistem</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            $totalHarga = 0;
            if (count($detail)) :
                foreach ($detail as $value) :
                $periode_stok = date('d-M-Y',strtotime($value['periode_awal'])) 
                                        . ' <b>s/d</b> ' . date('d-M-Y',strtotime($value['periode_akhir']));
                $totalHarga += $value['total_harganetto'];
                $hargga_netto = DocoHelpers::formatNumber($value['total_harganetto']);
        ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= date('d-M-Y',strtotime($value['tglformulir'])) ?></td>
                        <td><?= $periode_stok ?></td>
                        <td><?= $value['noformulir'] ?></td>
                        <td style="text-align: right;"><?= $hargga_netto ?></td>
                    </tr>
        <?php
                $no++;
                endforeach;
            else :
        ?>
            <tr>
                <td colspan="4" style="text-align: center;">Data kosong</td>
            </tr>
        <?php
            endif;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">Total Harga Netto Sistem</th>
            <th style="text-align: right;"><?= DocoHelpers::formatNumber($totalHarga) ?></th>
        </tr>
    </tfoot>
</table>