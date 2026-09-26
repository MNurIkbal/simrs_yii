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

                            <?php
                                $val_konversi = $value['qty'] * $value['nilai_konversi'];
                            ?>
                            <td align="right"><?= DocoHelpers::formatNumber($value['qty']).' '.$value['besar'] ?></td>
                            <td align="right"><?= DocoHelpers::formatNumber($val_konversi).' '.$value['kecil'] ?></td>
                            <?php if($tipe == 0) : ?>
                            <td align="center"><?= ($value['tgl_kadaluarsa'] == '2050-12-31 00:00:00') ? '-' : date('d M Y', strtotime($value['tgl_kadaluarsa']))  ?></td>
                            <td style="text-align: right;">Rp. <?= DocoHelpers::formatNumber($value['harga_netto']) ?></td>
                            <?php else : ?>
                            <td><?=$value['alasan'] ?></td>
                            <?php endif; ?>
                            
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