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
                        <th><?=\Yii::t("app", "Nama Obat Alkes");?></th>
                        <th><?=\Yii::t("app", "Qty Penerimaan");?></th>
                        <th><?=\Yii::t("app", "Qty Konversi");?></th>
                        <th><?=\Yii::t("app", "Tanggal Kadaluarsa");?></th>
                        <th><?=\Yii::t("app", "Harga Netto");?></th>
                        <th><?=\Yii::t("app", "Diskon (%)");?></th>
                        <th><?= \Yii::t("app", "No. Batch");?></th>
                        <th><?= \Yii::t("app", "Keterangan");?></th>
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
                            <td><?= $value['qty_besar'] ?> <?= $value['besar'] ?></td>
                            <td><?= $value['qty_kecil'] ?> <?= $value['kecil'] ?></td>
                            <td><?= !empty($value['tgl_kadaluarsa']) ? (new DocoHelpers)->convertDate($value['tgl_kadaluarsa'], 'd m Y') : '-' ?></td>
                            <td><?= DocoHelpers::formatNumber($value['harga_netto']) ?></td>
                            <td><?= DocoHelpers::formatNumber($value['diskon']) ?></td>
                            <td><?=  $value['no_batch'] ?></td>
                            <td><?=  $value['keterangan'] ?></td>
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