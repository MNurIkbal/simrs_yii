<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use Doco\components\DocoHelpers;
?>

<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-family: Tahoma;
        font-size: 13px;
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
</style>

<table width="100%" class="tbl-bordered">
    <thead  style="font-size: 13px">
        <tr class="bg-inverse">
            <th width="1"><?=\Yii::t("app", "No.");?></th>
            <th><?=\Yii::t("app", "Kode");?></th>
            <th><?=\Yii::t("app", "Nama");?></th>
            <th><?=\Yii::t("app", "Qty");?></th>
            <th><?=\Yii::t("app", "Satuan");?></th>
            <th><?=\Yii::t("app", "Harga(Rp)");?></th>
            <th><?=\Yii::t("app", "Discount(%)");?></th>
            <th><?=\Yii::t("app", "PPN(%)");?></th>
            <th align="right"><?=\Yii::t("app", "Jumlah");?></th>
        </tr>
    </thead>
    <tbody class="body-border">
        <?php
            $no = 1;
            foreach ($data as $value) :
            $sum_qty_diterima = array_sum(array_column($value, "qty_diterima"));
            $sum_jumlah = $value[0]['harga'] * $sum_qty_diterima;
            $nama = isset($value[0]['obatalkes_nama']) ? $value[0]['obatalkes_nama'] : $value[0]['barang_nama'];
        ?>
            <tr>
                <td align="center"><?= $no ?></td>
                <td><?= $value[0]['kode_item'] ?></td>
                <td><?= $nama ?></td>
                <td><?= DocoHelpers::formatNumber($sum_qty_diterima).' '.$value[0]['satuan_besar'] ?></td>
                <td><?= $value[0]['satuanunit_nama'] ?></td>
                <td align="right"><?= DocoHelpers::formatNumber($value[0]['harga']) ?></td>
                <td align="right"><?= DocoHelpers::formatNumber($value[0]['discount'],2) ?></td>
                <td align="right"><?= DocoHelpers::formatNumber($pajak_persen,2 , true) ?></td>
                <td align="right"><?= DocoHelpers::formatNumber($sum_jumlah) ?></td>
            </tr>
        <?php
            $no++;
            endforeach;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" align="right"><?=\Yii::t("app", "Sub Total : ");?></td>
            <td colspan="2" align="right"><b><?= DocoHelpers::formatNumber($subTotal, 0) ?></b></td>
        </tr>
        <tr>
            <td colspan="7" align="right"><?=\Yii::t("app", "Total Diskon (-): ");?></td>
            <td class="border-bottom" colspan="2" align="right"><b><?= DocoHelpers::formatNumber($totalDiscount, 0) ?></b></td>
        </tr>
        <tr>
            <td colspan="7" align="right"></td>
            <td colspan="2" align="right"><b><?= DocoHelpers::formatNumber($subTotalDiscount, 0) ?></b></td>
        </tr>
        <tr>
            <td colspan="7" align="right"><?=\Yii::t("app", "PPN(%) : ");?></td>
            <td class="border-bottom" colspan="2" align="right"><b><?= DocoHelpers::formatNumber($hargaPPN, 0) ?></b></td>
        </tr>
        <tr>
            <td colspan="7" align="right"><?=\Yii::t("app", "Net Total (+): ");?></td>
            <td class="heading-bottom" colspan="2" align="right"><b><?= DocoHelpers::formatNumber($totalNet, 0) ?></b></td>
        </tr>
    </tfoot>
</table>
