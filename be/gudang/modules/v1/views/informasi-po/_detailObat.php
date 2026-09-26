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
                        <th><?=\Yii::t("app", "Qty diterima");?></th>
                        <th><?=\Yii::t("app", "Satuan");?></th>
                        <th><?=\Yii::t("app", "Tanggal Kadaluarsa");?></th>
                        <th><?=\Yii::t("app", "No Batch");?></th>
                        <th><?=\Yii::t("app", "Keterangan");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($detail as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['barang_nama'] ?></td>
                            <td><?= DocoHelpers::formatNumber($value['qty_diterima']) ?></td>
                            <td><?= $value['satuanunit_nama'] ?></td>
                            <td><?= date('d M Y', strtotime($value['tgl_kadaluarsa'])) ?></td>
                            <td><?= $value['no_batch'] ?></td>
                            <td><?= $value['keterangan'] ?></td>
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