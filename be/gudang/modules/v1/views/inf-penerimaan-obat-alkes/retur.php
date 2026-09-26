<?php
    use Doco\components\DocoHelpers;
?>
<table border="1" cellspacing="0" style="width:100%;">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Obat Alkes</td>
            <td>Tanggal Kadaluarsa</td>
            <td>Qty Terima</td>
            <td>Satuan Besar</td>
            <td>Qty Retur</td>
            <td>Qty Sisa</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            if (count($detail)) :
                foreach ($detail as $value) :
        ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td width="25%"><?= $value['obatalkes_nama'] ?></td>
                        <td><?= date('d-M-Y', strtotime($value['tgl_kadaluarsa'])) ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['qty_besar']) ?></td>
                        <td><?= $value['satuanunit_nama'] ?></td>
                        <td><?= $value['qty_retur'] ?></td>
                        <td><?= $value['qty_sisa'] ?></td>
                    </tr>
        <?php
                $no++;
                endforeach;
            else :
        ?>
            <tr>
                <td colspan="7" style="text-align: center;">Data kosong</td>
            </tr>
        <?php
            endif;
        ?>
    </tbody>
</table>