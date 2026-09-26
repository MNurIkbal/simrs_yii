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
                        <th><?=\Yii::t("app", "Nama Obat");?></th>
                        <th><?=\Yii::t("app", "Reorder Point");?></th>
                        <th><?=\Yii::t("app", "Stok");?></th>
                        <th><?=\Yii::t("app", "Rekomendasi");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['obatalkes_nama'] ?></td>
                            <td><?= $value['nilai_ro'] ?></td>
                            <td><?= $value['qty_tersedia'] ?></td>
                            <td><?= $value['rekomendasi'] ?></td>
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