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
                        <th><?=\Yii::t("app", "Golongan Darah & Rhesus");?></th>
                        <th><?=\Yii::t("app", "Tanggal di Kirim");?></th>
                        <th><?=\Yii::t("app", "Jumlah");?></th>
                        <th><?=\Yii::t("app", "Harga (Rp. )");?></th>
                        <th><?=\Yii::t("app", "Sub Total (Rp. )");?></th>
                    </tr>
                </thead>
                <tbody>
                	<?php
                        $no = 1;
                        $total = 0;
                        foreach ($data as $value) : 
                            $total = $total + $value['sub_total'];
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['jenisdarah_nama'] ?></td>
                            <td><?= $value['golongandarah_nama'].'-'.$value['rhesus'] ?></td>
                            <td><?= date('d-M-Y H:i:s', strtotime($value['tgl_mintakirim'])) ?></td>
                            <td style="text-align: right"><?= $value['qty_pesan'] ?></td>
                            <td style="text-align: right"><?= DocoHelpers::formatNumber($value['harga_satuan']) ?></td>
                            <td style="text-align: right"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align: right"><strong>Total</strong></td>
                        <td style="text-align: right"><strong><?= DocoHelpers::formatNumber($total) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>