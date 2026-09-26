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
                        <th><?=\Yii::t("app", "Tanggal Permintaan di Kirim");?></th>
                        <th><?=\Yii::t("app", "Jumlah Pemesanan");?></th>
                        <th><?=\Yii::t("app", "Telah Diterima Sebelumnya");?></th>
                        <th><?=\Yii::t("app", "Nomor Kantong Yang Diterima");?></th>
                        <th><?=\Yii::t("app", "Tanggal Pengambilan Darah");?></th>
                        <th><?=\Yii::t("app", "Harga (Rp. )");?></th>
                    </tr>
                </thead>
                <tbody>
                	<?php
                        $no = 1;
                        $total = 0;
                        foreach ($data as $value) : 
                            $total = $total + $value['harga'];
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['jenisdarah_nama'] ?></td>
                            <td><?= $value['golongandarah_nama'].'-'.$value['rhesus'] ?></td>
                            <td><?= date('d-M-Y H:i:s', strtotime($value['tgl_mintakirim'])) ?></td>
                            <td style="text-align: right"><?= $value['jumlah_pemesanan'] ?></td>
                            <td style="text-align: right"><?= $value['jumlah_terima_sebelumnya'] ?></td>
                            <td><?= $value['no_kantongdarah'] ?></td>
                            <td><?= date('d-M-Y', strtotime($value['tgl_pengambilan'])) ?></td>
                            <td style="text-align: right"><?= DocoHelpers::formatNumber($value['harga']) ?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" style="text-align: right"><strong>Total</strong></td>
                        <td style="text-align: right"><strong><?= DocoHelpers::formatNumber($total) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>