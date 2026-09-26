<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use Doco\components\DocoHelpers;
?>

<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            
            <table  border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Nama");?></th>
                        <th><?=\Yii::t("app", "Qty");?></th>
                        <th><?=\Yii::t("app", "Satuan");?></th>
                        <th><?=\Yii::t("app", "Harga");?></th>
                        <th><?=\Yii::t("app", "Discount(%)");?></th>
                        <th><?=\Yii::t("app", "Discount(Rp)");?></th>
                        <th><?=\Yii::t("app", "Jumlah");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['obat_barang_nama'] ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['qty_input']) ?></td>
                            <td><?= $value['satuan'] ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['harga']) ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['discount']) ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['discount_rp']) ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['jumlah']) ?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" style="text-align: right;"><b>Sub Total</b></td>
                        <td style="text-align: right;"><b><?= DocoHelpers::formatNumber($sub_total, 0) ?></b></td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: right;"><b>Total Diskon</b></td>
                        <td style="text-align: right;"><b><?= DocoHelpers::formatNumber($total_discount, 0) ?></b></td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: right;"><b>PPN(%)</b></td>
                        <td style="text-align: right;"><b><?= DocoHelpers::formatNumber($ppn_nilai, 0) ?></b></td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: right;"><b>Total</b></td>
                        <td style="text-align: right;"><b><?= DocoHelpers::formatNumber($total, 0) ?></b></td>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>