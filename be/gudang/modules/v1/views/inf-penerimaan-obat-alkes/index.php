<?php
    use Doco\components\DocoHelpers;
?>

<style type="text/css">
.table {
    font-size: 9pt;
    border-collapse: collapse;
}
</style>

<table class="table" border="1" cellspacing="0" cellpadding="4" style="width:100%;">
    <thead>
        <tr>
            <td>No</td>
            <td>Kode Obat Alkes</td>
            <td>Nama Obat Alkes</td>
            <td>Qty</td>
            <td>Satuan</td>
            <td style="width:95px">Tgl Kadaluarsa</td>
            <td style="width:110px">Harga Netto (Rp.)</td>
            <td style="width:55px">PPn (%)</td>
            <td style="width:55px">Disc (%)</td>
            <td style="width:55px">Keterangan</td>
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
                        <td><?= !empty($value['obatalkes_kode']) ? $value['obatalkes_kode'] : "-" ?></td>
                        <td><?= $value['obatalkes_nama'] ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['qty_besar']) ?></td>
                        <td><?= $value['satuan_besar'] ?></td>
                        <td><?= date('d-M-Y', strtotime($value['tgl_kadaluarsa'])) ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['harga_netto_satuan']) ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['ppn']) ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['diskon']) ?></td>
                        <td><?= !empty($value['keterangan']) ? $value['keterangan'] : "-" ?></td>
                    </tr>
        <?php
                $no++;
                endforeach;
            else :
        ?>
            <tr>
                <td colspan="8" style="text-align: center;">Data kosong</td>
            </tr>
        <?php
            endif;
        ?>
    </tbody>
</table>