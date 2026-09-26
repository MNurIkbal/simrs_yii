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
                        <th><?=\Yii::t("app", "Kode Obat Alkes");?></th>
                        <th><?=\Yii::t("app", "Nama Obat Alkes");?></th>
                        <?php if($tipe == 0) : ?>
                        <th><?=\Yii::t("app", "Qty Penerimaan");?></th>
                        <th><?=\Yii::t("app", "Qty Konversi ");?></th>
                        <th><?=\Yii::t("app", "Tanggal Kadaluarsa");?></th>
                        <th><?=\Yii::t("app", "Harga Netto");?></th>
                        <?php else : ?>
                        <th><?=\Yii::t("app", "Qty Pengeluaran");?></th>
                        <th><?=\Yii::t("app", "Qty Konversi ");?></th>
                        <th><?=\Yii::t("app", "Alasan Pengeluaran");?></th>
                        <?php endif; ?>
                        <th><?=\Yii::t("app", "No Batch");?></th>
                        <th><?=\Yii::t("app", "Keterangan");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['obatalkes_kode'] ?></td>
                            <td><?= $value['obatalkes_nama'] ?></td>
                            <?php
                                $val_konversi = $value['qty'] * $value['nilai_konversi'];
                            ?>
                            <td align="right"><?= DocoHelpers::formatNumber($value['qty']).' '.$value['besar'] ?></td>
                            <td align="right"><?= DocoHelpers::formatNumber($val_konversi).' '.$value['kecil'] ?></td>
                            <?php if($tipe == 0) : ?>
                            <td><?= date('d M Y', strtotime($value['tgl_kadaluarsa'])) ?></td>
                            <td align="right"><?= DocoHelpers::formatNumber($value['harga_netto']) ?></td>
                            <?php else : ?>
                            <td><?=$value['alasan'] ?></td>
                            <?php endif; ?>
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