<?php

/**
 * @author : Asri Nurul M
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Detail Obat Produksi</h6>
    </div>
    <div class="panel-body">
      <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-condensed table-hover" id="expand-produksi-<?= DocoHelpers::decrypt($id) ?>" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?= Yii::t('fe', 'Nama Item Obat') ?></th>
                            <th><?= Yii::t('fe', 'Qty') ?></th>
                            <th><?= Yii::t('fe', 'Satuan') ?></th>
                            <th><?= Yii::t('fe', 'Harga Per Item (Rp.)') ?></th>
                            <th><?= Yii::t('fe', 'Total Harga Item (Rp.)') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                            if(count($bahan_baku) > 0) {
                            foreach ($bahan_baku as $key => $value) {
                                foreach ($cekObat as $key => $cek) {
                                    if($cek['obatalkes_id'] == $value['obatalkes_id']){
                        ?>
                        <tr style="background-color:<?= $cek['warna'] ?>">
                            <td class="text-center"><?= $no ?></td>
                            <td><?= ArrayHelper::getValue($value, 'obatalkes_nama'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'qty_obat') ?></td>
                            <td><?= ArrayHelper::getValue($value, 'satuan_kecil') ?></td>
                            <td><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'harganetto_satuan')) ?></td>
                            <td><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'totalharga')) ?></td>
                        </tr>
                        <?php
                            $no++;
                            }}}}
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>