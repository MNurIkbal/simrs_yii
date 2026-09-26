<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use Doco\components\DocoHelpers;
?>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table  border="1" style="width:100%; border-collapse: collapse;" cellspacing="2px">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Jenis Darah");?></th>
                        <th><?=\Yii::t("app", "Tanggal di Kirim");?></th>
                        <th><?=\Yii::t("app", "Jumlah");?></th>
                        <th><?=\Yii::t("app", "Harga (Rp.)");?></th>
                        <th><?=\Yii::t("app", "Sub Total (Rp.)");?></th>
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
                            <td><?= date('d-M-Y', strtotime($value['tgl_mintakirim'])).' '.$value['wkt_mintakirim'] ?></td>
                            <td style="text-align: right"><?= DocoHelpers::formatNumber($value['jumlah']) ?></td>
                            <td style="text-align: right;width: 20%"><?= DocoHelpers::formatNumber($value['harga_satuan']) ?></td>
                            <td style="text-align: right;width: 20%"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
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