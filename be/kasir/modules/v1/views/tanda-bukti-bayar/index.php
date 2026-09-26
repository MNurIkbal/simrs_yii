<?php 
use Doco\components\DocoHelpers;
?>

<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th><?= Yii::t('app', 'Tanggal pembayaran'); ?></th>
            <th><?= Yii::t('app', 'No pendaftaran'); ?></th>
            <th><?= Yii::t('app', 'Cara Bayar'); ?></th>
            <th><?= Yii::t('app', 'Penjamin'); ?></th>
            <th><?= Yii::t('app', 'Nama pasien'); ?></th>
            <th><?= Yii::t('app', 'Total tagihan (Rp.)'); ?></th>
            <th><?= Yii::t('app', 'Uang diterima (Rp.)'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        $total = 0;
        ?>
        <?php foreach ($detail as $value): ?>
            <tr>
                <td>
                    <?= $no; ?>
                </td>
                <td>
                    <?= !empty($value['tgl_closingkasir']) 
                                ? date('d-M-Y',strtotime($value['tgl_closingkasir'])) : '' ?>
                </td>
                <td>
                    <?= !empty($value['no_pendaftaran']) 
                            ? $value['no_pendaftaran'] : '' ?>
                </td>
                <td>
                    <?= !empty($value['carabayar_nama']) 
                            ? $value['carabayar_nama'] : '' ?>
                </td>
                <td>
                    <?= !empty($value['penjamin_nama']) 
                            ? $value['penjamin_nama'] : '' ?>
                </td>
                <td>
                    <?= !empty($value['nama_pasien']) 
                            ? $value['nama_pasien'] : '' ?>
                </td>
                <td style="text-align:right">
                    <?= !empty($value['total_terbayar']) ? DocoHelpers::formatNumber($value['total_terbayar']) : 0 ?>
                </td>
                <td style="text-align:right">
                    <?= !empty($value['total_setoran']) ? DocoHelpers::formatNumber($value['total_setoran']) : 0 ?>
                </td>
            </tr>
            <?php 
            $no++; 
            $total += $value['total_setoran'];
            ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">Total Closing</td>
            <td style="text-align:right"><?= DocoHelpers::formatNumber($total); ?></td>
        </tr>
    </tfoot>
</table>