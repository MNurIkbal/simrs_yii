<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;

?>

<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-family: <?= $font['family'] ?>;
        font-size: <?= $font['size'] ?>;
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

    .text-right {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
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
                <th><?=\Yii::t("app", "Konversi");?></th>
                <th><?=\Yii::t("app", "Harga(Rp)");?></th>
                <th><?=\Yii::t("app", "Sub Total(Rp)");?></th>
                <th><?=\Yii::t("app", "Disc(%)");?></th>
                <th><?=\Yii::t("app", "PPN(%)");?></th>
                <th><?=\Yii::t("app", "Total(Rp)");?></th>
            </tr>
        </thead>
        <tbody class="body-border">
            <?php
                $no = 1;
                $subTotal = 0;
                $totalDiscount = 0;
                $subTotalJumlah = 0;
                $ppnPersen = 0;
                foreach ($data as $value) :
                    
                    $obatalkes_id = $value['obat_barang_id'];
                    if(isset($hasil_konversi[$obatalkes_id][$value['s_konversiobt_id']])) {
                        $nilai_konversi = $hasil_konversi[$obatalkes_id][$value['s_konversiobt_id']];
                    } else {
                        $nilai_konversi = 1;
                    }

                    $satuan_konversi = "-";
                    if(isset($label_konversi[$obatalkes_id][$value['s_konversiobt_id']])) {
                        $satuan_besar = 1 . " " . $label_konversi[$obatalkes_id][$value['s_konversiobt_id']]["besar"];
                        $satuan_kecil = (1 * $nilai_konversi) . " " . $label_konversi[$obatalkes_id][$value['s_konversiobt_id']]["kecil"];
                        $satuan_konversi = $satuan_besar . " = " . $satuan_kecil;
                    }

                    $subtotal_item = $value['qty_input'] * $value['harga'];
            ?>
                <tr>
                    <td class="text-center"><?= $no ?></td>
                    <td><?= $value['kode_item'] ?></td>
                    <td><?= $value['obat_barang_nama'] ?></td>
                    <td class="text-right"><?= DocoHelpers::formatNumber($value['qty_input'])?></td>
                    <td><?= $value['satuan_besar'] ?></td>
                    <td><?= $satuan_konversi ?></td>
                    <td class="text-right"><?= DocoHelpers::formatNumber($value['harga']) ?></td>
                    <td class="text-right"><?= DocoHelpers::formatNumber($subtotal_item) ?></td>
                    <td class="text-right"><?= DocoHelpers::formatNumber($value['discount'],2) ?></td>
                    <td class="text-right"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'ppn_persen')) ?></td>
                    <td class="text-right"><?= DocoHelpers::formatNumber($value['jumlah_with_ppn'], 2) ?></td>
                </tr>
            <?php
                $no++;
                $subTotal += $subtotal_item;
                $totalDiscount += ArrayHelper::getValue($value, 'discount_rp', 0);
                $ppnPersen = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'ppn_persen',0));
                endforeach;

                $total_discount = $total_discount == $totalDiscount ? $total_discount : $totalDiscount;
                $subTotalDiscount = $subTotal - $total_discount;
                $hargaPPN = $subTotalDiscount * $ppnPersen/100;
                $totalNet = $subTotalDiscount + $hargaPPN
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="text-right"><?=\Yii::t("app", "Sub Total : ");?></td>
                <td colspan="3" class="text-right"><b><?= DocoHelpers::formatNumber($subTotal, 0) ?></b></td>
            </tr>
            <tr>
                <td colspan="8" class="text-right"><?=\Yii::t("app", "Total Disc (-): ");?></td>
                <td class="border-bottom text-right" colspan="3"><b><?= DocoHelpers::formatNumber($total_discount, 0) ?></b></td>
            </tr>
            <tr>
                <td colspan="8" class="text-right"></td>
                <td colspan="3" class="text-right"><b><?= DocoHelpers::formatNumber($subTotalDiscount, 0) ?></b></td>
            </tr>
            <tr>
                <td colspan="8" class="text-right"><?=\Yii::t("app", "PPN(Rp) : ");?></td>
                <td class="border-bottom text-right" colspan="3"><b><?= DocoHelpers::formatNumber($hargaPPN, 0) ?></b></td>
            </tr>
            <tr>
                <td colspan="8" class="text-right"><?=\Yii::t("app", "Nett Total (+): ");?></td>
                <td class="heading-bottom text-right" colspan="3"><b><?= DocoHelpers::formatNumber($totalNet, 0) ?></b></td>
            </tr>
        </tfoot>
    </table>
</div>
