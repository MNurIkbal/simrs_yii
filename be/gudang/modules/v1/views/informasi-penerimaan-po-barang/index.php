<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use Doco\components\DocoHelpers;
    use Doco\components\DocoConstants;
?>

<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            
            <table  border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th>Tanggal Penerimaan</th>
                        <th>Nomor Penerimaan</th>
                        <th>Nomor PO</th>
                        <th>Supplier</th>
                        <th>Nama Barang</th>
                        <th>Qty Terima</th>
                        <th>Total Harga (Rp.)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($detail as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= date('d-M-Y', strtotime($value['tgl_penerimaan'])) ?></td>
                            <td><?= $value['no_penerimaan'] ?></td>
                            <td><?= $value['nomor_po'] ?></td>
                            <td><?= $value['supplier_nama'] ?></td>
                            <td><?= $value['barang_nama'] ?></td>
                            <td><?= $value['qty_diterima'] ?> <?= $value['satuan_besar'] ?></td>
                            <td><?= DocoHelpers::rupiahDisplay($value['harga_total']) ?></td>
                            <td><?= DocoConstants::$statusPenerimaan[$value['status_invoice']] ?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>