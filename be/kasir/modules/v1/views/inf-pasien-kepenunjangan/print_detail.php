<?php
// Author : Faidzin

use Doco\components\DocoHelpers;
use yii\web\View;

$this->title = \Yii::t('app', 'Rincian tagihan pasien penunjang');

$dataHeader = $model;
?>


<?php foreach ($detailPerRuangan as $key=>$details) : ?>
    <div class='panel panel-white'>
        <table width="100%" border=1>
            <thead>
                <tr>
                    <th colspan=8 class='success'><?= Yii::t('app','Pemeriksaan ' . $key); ?></th>
                </tr>
                <tr>
                    <th><?= Yii::t('app', 'No'); ?></th>
                    <th><?= Yii::t('app', 'Tgl pemeriksaan'); ?></th>
                    <th><?= Yii::t('app', 'Nama pemeriksaan'); ?></th>
                    <th><?= Yii::t('app', 'Tarif satuan'); ?></th>
                    <th><?= Yii::t('app', 'Cyto'); ?></th>
                    <th><?= Yii::t('app', 'Tarif satuan cyto'); ?></th>
                    <th><?= Yii::t('app', 'Qty'); ?></th>
                    <th><?= Yii::t('app', 'Jumlah'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            $no = 1;
            $total = 0;
            ?>
            <?php foreach ($details as $detail): ?>
                <tr>
                    <td><?= $no; ?></td>
                    <td><?= $detail['tglmasukpenunjang']; ?></td>
                    <td><?= $detail['daftartindakan_nama']; ?></td>
                    <td><?= DocoHelpers::rupiahDisplay($detail['tarif_satuan']); ?></td>
                    <td><?= $detail['cyto_tindakan']; ?></td>
                    <td><?= DocoHelpers::rupiahDisplay($detail['tarifcyto_tindakan']); ?></td>
                    <td><?= $detail['qty_tindakan']; ?></td>
                    <td><?= DocoHelpers::rupiahDisplay($detail['tarif_tindakan']); ?></td>
                </tr>
                <?php
                $no++; 
                $total += $detail['tarif_tindakan']; 
                ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan=7><?= Yii::t('app', 'Total'); ?></td>
                    <td><?= DocoHelpers::rupiahDisplay($total); ?></td>
                </tr>
            </tfoot>
        </table>
        <br />
    </div>
<?php endforeach; ?>
<?php 
$this->registerJs('
', View::POS_END, 'b-index');
?>
