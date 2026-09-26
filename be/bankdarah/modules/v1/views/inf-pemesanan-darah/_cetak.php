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
                        <th><?= Yii::t("app", "Tanggal Pemesanan") ?></th>
                        <th><?= Yii::t("app", "Nomor Pemesanan") ?></th>
                        <th><?= Yii::t("app", "Nama PMI") ?></th>
                        <th><?= Yii::t("app", "Jumlah Pemesanan") ?></th>
                        <th><?= Yii::t("app", "Jumlah Diterima") ?></th>
                        <th><?= Yii::t("app", "Sisa") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= date('d-M-Y', strtotime($value['tgl_pesandarahpmi'])) ?></td>
                            <td><?= $value['no_pesandarahpmi'] ?></td>
                            <td><?= $value['supplier_nama'] ?></td>
                            <td style="text-align: right"><?= $value['qty_pesan'] ?></td>
                            <td style="text-align: right"><?= $value['qty_diterima'] ?></td>
                            <td style="text-align: right"><?= $value['qty_sisa'] ?></td>
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