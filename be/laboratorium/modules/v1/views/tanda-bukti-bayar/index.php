<?php 
use Doco\components\DocoHelpers;
?>

<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th><?= Yii::t('app', 'Tanggal pembayaran'); ?></th>
            <th><?= Yii::t('app', 'No pendaftaran'); ?></th>
            <th><?= Yii::t('app', 'Nama pasien'); ?></th>
            <th><?= Yii::t('app', 'Total pembayaran'); ?></th>
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
                    <?= !empty($value['tglbuktibayar']) 
                                ? date('d-M-Y',strtotime($value['tglbuktibayar'])) : '' ?>
                </td>
                <td>
                    <?= !empty($value['no_pendaftaran']) 
                            ? $value['no_pendaftaran'] : '' ?>
                </td>
                <td>
                    <?= !empty($value['nama_pasien']) 
                            ? $value['nama_pasien'] : '' ?>
                </td>
                <td style="text-align:right">
                    <?= !empty($value['uangditerima']) ? DocoHelpers::formatNumber($value['uangditerima']) : '' ?>
                </td>
            </tr>
            <?php 
            $no++; 
            $total += $value['uangditerima'];
            ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>Total Closing</td>
            <td style="text-align:right"><?= DocoHelpers::formatNumber($total); ?></td>
        </tr>
    </tfoot>
</table>