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
                        <th><?=\Yii::t("app", "Jenis Darah");?></th>
                        <th><?=\Yii::t("app", "Golongan Darah");?></th>
                        <th><?=\Yii::t("app", "Rhesus");?></th>
                        <th><?=\Yii::t("app", "Tanggal Permintaan di Kirim");?></th>
                        <th><?=\Yii::t("app", "Waktu Permintaan di Kirim");?></th>
                        <th><?=\Yii::t("app", "Jumlah");?></th>
                        <th><?=\Yii::t("app", "Harga");?></th>
                        <th><?=\Yii::t("app", "Sub Total");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['jenisdarah_nama'] ?></td>
                            <td><?= $value['golongandarah_nama'] ?></td>
                            <td><?= ($value['rhesus'] == 1) ? "Positif" : "Negatif" ?></td>
                            <td><?= date('d-M-Y', strtotime($value['tgl_mintakirim'])) ?></td>
                            <td><?= $value['wkt_mintakirim'] ?></td>
                            <td style="text-align: right"><?= DocoHelpers::formatNumber($value['qty_pesan']) ?></td>
                            <td style="text-align: right"><?= DocoHelpers::formatNumber($value['harga_satuan']) ?></td>
                            <td style="text-align: right"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                </tbody>
                <footer>
                    <tr>
                        <td colspan="8" style="text-align: right;"><strong> Total : </strong></td>
                        <td style="text-align: right;"><strong>
                            Rp <?= DocoHelpers::formatNumber($total_harga) ?></strong></td>
                    </tr>
                     <tr>
                        <td colspan="8" style="text-align: right;"><strong> Total Kantong Darah : </strong></td>
                        <td style="text-align: right;"><strong><?= $total_kantongdarah ?></strong></td>
                    </tr>
                </footer>
            </table>
        </div>
    </div>
</div>