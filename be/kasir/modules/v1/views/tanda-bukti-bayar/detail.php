<?php 
use Doco\components\DocoHelpers;
?>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th><?= Yii::t('app', 'Uang pecahan'); ?></th>
            <th><?= Yii::t('app', 'Qty'); ?></th>
            <th><?= Yii::t('app', 'Jumlah'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        $totalPecahan = 0;
        $totalQty = 0;
        ?>
        <?php foreach ($detailPecahan as $value): ?>
            <tr>
                <td><?= $no; ?></td>
                <td style="text-align:right"><?= !empty($value['nilaiuang']) 
                            ? DocoHelpers::formatNumber($value['nilaiuang']) : '' ?></td>
                <td style="text-align:right"><?= !empty($value['banyakuang']) 
                            ? DocoHelpers::formatNumber($value['banyakuang']) : '' ?></td>
                <td style="text-align:right"><?= !empty($value['jumlahuang']) 
                            ? DocoHelpers::formatNumber($value['jumlahuang']) : '' ?></td>
            </tr>
            <?php 
            $no++; 
            $totalPecahan += $value['jumlahuang'];
            $totalQty += $value['banyakuang'];
            ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><?= Yii::t('app', 'Total Pecahan'); ?></td>
            <td style="text-align:right"><?= DocoHelpers::formatNumber($totalQty); ?></td>
            <td style="text-align:right"><?= DocoHelpers::formatNumber($totalPecahan); ?></td>
        </tr>
    </tfoot>
</table>