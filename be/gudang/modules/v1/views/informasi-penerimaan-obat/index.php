<?php

use Doco\components\DocoHelpers;
?>

<table border="1" cellpadding="1" cellspacing="0" style="width:100%">
    <thead>
        <tr>
            <td>Tanggal Penerimaan</td>
            <td>Nomor Penerimaan</td>
            <td>Nomor PO</td>
            <td>Supplier</td>
            <td>Nama Obat</td>
            <td>Qty Diterima</td>
            <td>Total Harga (Rp.)</td>
            <td>Status</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            if (count($detail)) :
                foreach ($detail as $value) :
        ?>
                    <tr>
                        <td><?= isset($value['tgl_penerimaan']) ? date("d-M-Y", strtotime($value['tgl_penerimaan'])) : '' ?></td>
                        <td><?= isset($value['no_penerimaan']) ? $value['no_penerimaan'] : '' ?></td>
                        <td><?= isset($value['nomor_po']) ? $value['nomor_po'] : '' ?></td>
                        <td><?= isset($value['supplier_nama']) ? $value['supplier_nama'] : '' ?></td>
                        <td><?= isset($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '' ?></td>
                        <td><?= isset($value['qty_diterima']) ? $value['qty_diterima'] : '' ?> <?= isset($value['satuan_besar']) ? $value['satuan_besar'] : '' ?></td>
                        <td><?= isset($value['harga_total']) ? DocoHelpers::rupiahDisplay($value['harga_total']) : '' ?></td>
                        <?php
                                switch ($value["status_invoice"]) {
                                    case '0':
                                        $value["status_invoice"] = "Belum Diverifikasi";
                                        break;

                                    case '1':
                                        $value["status_invoice"] = "Sudah Diverifikasi";
                                        break;

                                    case '2':
                                        $value["status_invoice"] = "Dibatalkan";
                                        break;

                                    default:
                                        $value["status_invoice"] = "-";
                                        break;
                                }
                        ?>
                        <td><?= isset($value['status_invoice']) ? $value['status_invoice'] : '' ?></td>
                    </tr>
        <?php
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
</table>