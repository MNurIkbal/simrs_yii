<?php
    use Doco\components\DocoHelpers;
?>

<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <tr>
        <td colspan="2" style="text-align: center; background-color:#3366cc;"><?=Yii::t('app', 'Tarif Karcis')?></td>     
    </tr>
    <tr>
        <td style="text-align: center;"><?=Yii::t('app', 'Karcis')?></td>
        <td style="text-align: center;"><?=Yii::t('app', 'Harga')?></td>
    </tr>
    <?php
        $total = 0;
        if (count($detail)) :
            foreach($detail as $value) :
                $total += $value['tarif_tindakan'];
        ?>
        <tr>
            <td><?= $value['daftartindakan_nama'] ?></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_tindakan']) ?></td>
        </tr>
        <?php
            endforeach;
        else :
    ?>
        <tr>
            <td colspan="2" style="text-align: center;">Data kosong</td>
        </tr>
    <?php
        endif;
    ?>
    <tr>
        <td><?=Yii::t('app', 'Total')?></td>
        <td style="text-align: right;"><?= DocoHelpers::formatNumber($total) ?></td>
    </tr>
</table>