<?php
    use Doco\components\DocoHelpers;
?>

<table border="1" cellpadding="1" cellspacing="0" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Barang</td>
            <td>Qty PO</td>
            <td>PO Ballance</td>
            <td>Satuan</td>
            <td>Qty Diterima</td>
            <td>Tanggal Kadaluarsa</td>
            <td>No Batch</td>
            <td>Keterangan</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($detail as $row) :
            $rowspan = count($row);
        ?>
            <?php foreach ($row as $key => $row_detail) : ?>
                <tr>
                    <?php if($key == 0) : ?>
                        <td rowspan="<?= $rowspan ?>"><?= $no ?></td>
                        <td rowspan="<?= $rowspan ?>"><?= isset($row_detail["barang_nama"]) ? $row_detail["barang_nama"] : "" ?></td>
                        <td rowspan="<?= $rowspan ?>" style="text-align: right;"><?= isset($row_detail["qty_po"]) 
                            ? DocoHelpers::formatNumber($row_detail["qty_po"]) : "" ?></td>
                        <td rowspan="<?= $rowspan ?>" style="text-align: right;"><?= isset($row_detail["po_balance"]) 
                            ? DocoHelpers::formatNumber($row_detail["po_balance"]) : "" ?></td>
                        <td rowspan="<?= $rowspan ?>"><?= isset($row_detail["satuan_besar"]) ? $row_detail["satuan_besar"] : "" ?></td>
                    <?php endif ?>
                    <td style="text-align: right;"><?= isset($row_detail["qty_diterima"]) ? $row_detail["qty_diterima"] : "" ?></td>
                    <td><?= isset($row_detail["tgl_kadaluarsa"]) ? date("d-M-Y", strtotime($row_detail["tgl_kadaluarsa"])) : "" ?></td>
                    <td><?= isset($row_detail["no_batch"]) ? $row_detail["no_batch"] : "" ?></td>
                    <td><?= isset($row_detail["keterangan"]) ? $row_detail["keterangan"] : "" ?></td>
                </tr>
            <?php endforeach ?>
       <?php $no++; endforeach; ?>
    </tbody>
</table>