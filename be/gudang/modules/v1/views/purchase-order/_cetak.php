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
                <tfoot>
                	<tr>
                		<td colspan="7" style="text-align: right;"><b>Sub Total</b></td>
                		<td><b><?= DocoHelpers::formatNumber($sub_total, 0) ?></b></td>
                	</tr>
                	<tr>
                		<td colspan="7" style="text-align: right;"><b>Total Diskon</b></td>
                		<td><b><?= DocoHelpers::formatNumber($total_discount, 0) ?></b></td>
                	</tr>
                	<tr>
                		<td colspan="7" style="text-align: right;"><b>PPN(%)</b></td>
                		<td><b><?= DocoHelpers::formatNumber($ppn_nilai, 0) ?></b></td>
                	</tr>
                	<tr>
                		<td colspan="7" style="text-align: right;"><b>Total</b></td>
                		<td><b><?= DocoHelpers::formatNumber($total, 0) ?></b></td>
                	</tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>