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
                        <th><?=\Yii::t("app", "Nama Barang");?></th>
                        <th><?=\Yii::t("app", "Reorder Point");?></th>
                        <th><?=\Yii::t("app", "Stok");?></th>
                        <th><?=\Yii::t("app", "Rekomendasi");?></th>
                        <th><?=\Yii::t("app", "Jumlah PO");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['barang_nama'] ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['nilai_ro']) ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['qty_tersedia']) ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['rekomendasi']) ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['on_po']) ?></td>
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