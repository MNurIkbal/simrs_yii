<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Detail Resep</h6>
    </div>
    <div class="panel-body">
      <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-condensed table-hover" id="expand-resep-<?= DocoHelpers::decrypt($id) ?>" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?= Yii::t('fe', 'R ke') ?></th>
                            <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                            <th><?= Yii::t('fe', 'Signa') ?></th>
                            <th><?= Yii::t('fe', 'Harga Satuan (Rp.)') ?></th>
                            <th><?= Yii::t('fe', 'Qty') ?></th>
                            <th><?= Yii::t('fe', 'Satuan') ?></th>
                            <th><?= Yii::t('fe', 'Kronis') ?></th>
                            <th><?= Yii::t('fe', 'Catatan') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php
                            $no = 1;
                            if(count($data_racikan) > 0) {
                                foreach ($data_racikan as $key => $value) { 
                            ?>
                                    <tr>
                                        <td class="text-center"><?= $no ?></td>
                                        <?php if(isset($value['type'])){ ?>
                                            <td><?= $value['type'] == 'OR' ? 'Racikan' : '-' ?></td>
                                            <td><?= $value['type'] == 'NR' ? "OTHERS<br>".$value['racikan'] : $value['racikan'] ?></td>
                                            <td colspan="6"></td>
                                        <?php }else{ ?>
                                            <td>Racikan</td>
                                            <td><?= $value['rke'] ?></td>
                                            <td colspan="6"><?= nl2br($value['racikan']) ?></td>
                                        <?php } ?>
                                    </tr>
                            <?php
                                    $no++;
                                }
                            }

                            if(count($detail_resep) > 0) {
                                foreach ($detail_resep as $key => $value) {
                                    $qty_resep = isset($value['det_transaksi']) ? $value['det_transaksi'] : $value['qty_transaksi'];
                                    $namaRc = ArrayHelper::getValue($value, 'nama_racikan', null);
                                    $qtRc = ArrayHelper::getValue($value, 'qty_racikan', null);
                                    $kronis = ArrayHelper::getValue($value, 'is_kronis') == true ? 'Ya' : 'Tidak';
                                    $stRcNama = ArrayHelper::getValue($value, 'satuan_racikan_nama', null);
                                    $namaRcJmlRc = $namaRc . " - " . $qtRc . " " .$stRcNama;
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $no ?></td>
                                        <td><?= ArrayHelper::getValue($value, 'rke', "-").$namaRcJmlRc ?></td>
                                        <td><?= ArrayHelper::getValue($value, 'obatalkes_nama'); ?></td>
                                        <td><?= ArrayHelper::getValue($value, 'signa_nama') ?></td>
                                        <td class="text-right"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value ,'hargajual_satuan',0)) ?></td>
                                        <td class="text-right"><?= $qty_resep ?></td>
                                        <td><?= ArrayHelper::getValue($value, 'satuan_input') ?></td>
                                        <td><?= $kronis ?></td>
                                        <td><?= ArrayHelper::getValue($value, 'etiket') ?></td>
                                    </tr>
                                <?php
                                    $no++;
                                }
                            }
                            ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
