<?php
    use Doco\components\DocoHelpers;
?>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama obat alkes</td>
            <td>Tanggal Expired</td>
            <td>Qty</td>
            <td>Satuan Kecil</td>
            <td>Jumlah Harga Netto</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            if (count($detail)) :
                foreach ($detail as $value) :
        ?>
                    <tr>
                        <td>
                            <?= $no ?>
                        </td>
                        <td>
                            <?= $value['obatalkes_nama'] ?>
                        </td>
                        <td>
                            <?= date('d-m-Y',strtotime($value['tglkadaluarsa'])) ?>
                        </td>
                        <td>
                            <?= DocoHelpers::formatNumber($value['stok']) ?>
                        </td>
                        <td>
                            <?= $value['satuan_kecil'] ?>
                        </td>
                        <td class="text-right">
                            <?= DocoHelpers::formatNumber($value['jumlah_harganetto']) ?>
                        </td>
                    </tr>
        <?php
                $no++;
                endforeach;
            else :
        ?>
            <tr>
                <td colspan="6" style="text-align: center;">Data kosong</td>
            </tr>
        <?php
            endif;
        ?>
    </tbody>
</table>