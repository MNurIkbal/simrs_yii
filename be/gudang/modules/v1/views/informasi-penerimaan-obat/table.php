<?php
    use Doco\components\DocoHelpers;
?>

<table border="1" cellpadding="1" cellspacing="0" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Obat</td>
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
            foreach ($detail as $obatId => $row) :
                $qty[$obatId] = $poBalance[$obatId] = 0;
                foreach ($row as $key => $row_detail) : 
                    $qty[$obatId] += $row_detail['qty_po'];
                    $poBalance[$obatId] += $row_detail['po_balance'];
                endforeach;
            endforeach;
            ?>

        <?php 
            $no = 1;
            foreach ($detail as $obatId => $row) :
            $rowspan = count($row);
        ?>

            <?php foreach ($row as $key => $row_detail) : ?>
                <tr>
                    <?php if($key == 0) : ?>
                        <td rowspan="<?= $rowspan ?>"><?= $no ?></td>
                        <td rowspan="<?= $rowspan ?>"><?= isset($row_detail["obatalkes_nama"]) ? $row_detail["obatalkes_nama"] : "" ?></td>
                        <td rowspan="<?= $rowspan ?>" style="text-align: right;"><?= $qty[$obatId] ?></td>
                        <td rowspan="<?= $rowspan ?>" style="text-align: right;"><?= $poBalance[$obatId] ?></td>
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