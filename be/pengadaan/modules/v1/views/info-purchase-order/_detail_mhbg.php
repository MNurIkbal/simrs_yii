<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use Doco\components\DocoHelpers;
?>

<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-family: 'Courier New', monospace;
        font-size: 10px;
    }

    .tbl-bordered th {
        border: 1px solid black;
        /*border-bottom: 1px solid black;*/
        padding: 5px;
    }

    .tbl-bordered tr td.border-bottom {
        border-bottom: 1px solid black;
    }

    .body-border td {
        border: 1px solid black;
    }

    .tbl-bordered tr.border-top td {
        border-top: 1px solid black;
    }

    .bold {
        font-weight: bold;
    }

    .header-right {
        padding-right: 15px;
    }

    .heading-bottom {
      background: linear-gradient(to bottom, transparent 3px, black 3px, black 6px, transparent 6px) no-repeat;
      border-bottom: 1px solid black;
      display: inline-block;
      vertical-align: bottom;
      padding-bottom: 10px;
    }

    .table-padding-top {
        padding-top: -50px;
    }
</style>

<div class="table-padding-top">
    <table width="100%" class="tbl-bordered">
        <thead  style="font-size: 13px">
            <tr class="bg-inverse">
                <th width="1"><?=\Yii::t("app", "No.");?></th>
                <th><?=\Yii::t("app", "Kode");?></th>
                <th><?=\Yii::t("app", "Nama");?></th>
                <th><?=\Yii::t("app", "Qty");?></th>
                <th><?=\Yii::t("app", "Satuan");?></th>
                <th><?=\Yii::t("app", "Harga(Rp)");?></th>
                <th><?=\Yii::t("app", "Sub Total");?></th>
                <th><?=\Yii::t("app", "Disc(%)");?></th>
                <th><?=\Yii::t("app", "PPN(%)");?></th>
                <th align="right"><?=\Yii::t("app", "Total");?></th>
            </tr>
        </thead>
        <tbody class="body-border">
            <?php
                $no = 1;
                $subTotal = 0;
                foreach ($data as $value) :
            ?>
                <tr>
                    <td align="center"><?= $no ?></td>
                    <td><?= $value['kode_item'] ?></td>
                    <td><?= $value['obat_barang_nama'] ?></td>
                    <td><?= DocoHelpers::formatNumber($value['qty_input']).' '.$value['satuan_besar'] ?></td>
                    <td><?= $value['satuan'] ?></td>
                    <td align="right"><?= DocoHelpers::formatNumber($value['harga']) ?></td>
                    <td align="right"><?= DocoHelpers::formatNumber($value['qty_input'] * $value['harga']) ?></td>
                    <td align="right"><?= DocoHelpers::formatNumber($value['discount'],2) ?></td>
                    <td align="right"><?= DocoHelpers::formatNumber($pajak_persen,2) ?></td>
                    <td align="right"><?= DocoHelpers::formatNumber($value['jumlah'],2) ?></td>
                </tr>
            <?php
                $no++;
                $subTotal += ($value['qty_input'] * $value['harga']);
                endforeach;

                $subTotalDiscount = $subTotal - $total_discount;
                $hargaPPN = $subTotalDiscount * $pajak_persen/100;
                $totalNet = $subTotalDiscount + $hargaPPN
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" align="right"><?=\Yii::t("app", "Sub Total : ");?></td>
                <td colspan="3" align="right"><b><?= DocoHelpers::formatNumber($subTotal, 0) ?></b></td>
            </tr>
            <tr>
                <td colspan="7" align="right"><?=\Yii::t("app", "Total Disc (-): ");?></td>
                <td class="border-bottom" colspan="3" align="right"><b><?= DocoHelpers::formatNumber($total_discount, 0) ?></b></td>
            </tr>
            <tr>
                <td colspan="7" align="right"></td>
                <td colspan="3" align="right"><b><?= DocoHelpers::formatNumber($subTotalDiscount, 0) ?></b></td>
            </tr>
            <tr>
                <td colspan="7" align="right"><?=\Yii::t("app", "PPN(%) : ");?></td>
                <td class="border-bottom" colspan="3" align="right"><b><?= DocoHelpers::formatNumber($hargaPPN, 0) ?></b></td>
            </tr>
            <tr>
                <td colspan="7" align="right"><?=\Yii::t("app", "Net Total (+): ");?></td>
                <td class="heading-bottom" colspan="3" align="right"><b><?= DocoHelpers::formatNumber($totalNet, 0) ?></b></td>
            </tr>
        </tfoot>
    </table>
</div>
