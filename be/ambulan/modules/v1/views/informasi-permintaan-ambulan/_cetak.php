<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use Doco\components\DocoHelpers;
?>

<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <?php
                if (!empty($dataObat)) :
            ?>
                <table  border="1" style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("app", "Nama Obat Alkes");?></th>
                            <th><?=\Yii::t("app", "Qty");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                            foreach ($dataObat as $value) :
                        ?>
                            <tr>
                                <td><?= $no ?></td>
                                <td><?= $value['obatalkes_nama'] ?></td>
                                <td><?= $value['qty_obat'].' '.$value['satuan_kecil'] ?></td>
                            </tr>
                        <?php
                            $no++;
                            endforeach;
                        ?>
                    </tbody>
                </table><br><br>
            <?php
                endif;
            ?>
            <table  border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Tindakan");?></th>
                        <th><?=\Yii::t("app", "Kelompok Biaya");?></th>
                        <th><?=\Yii::t("app", "Qty");?></th>
                        <th><?=\Yii::t("app", "Tarif Satuan (Rp.)");?></th>
                        <th><?=\Yii::t("app", "Jumlah Tarif (Rp.)");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        $total = 0;
                        foreach ($data as $value) :
                            $total += $value['jumlah_tarif'];
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['daftartindakan_nama'] ?></td>
                            <td><?= $value['kelompok_biaya'] ?></td>
                            <td style="text-align: right;"><?= $value['qty_tindakan'] ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['jumlah_tarif']) ?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: right;"><b>Total Tarif</b></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber($total) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>