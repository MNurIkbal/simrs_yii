<?php
use Doco\components\DocoHelpers;

?>

<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
</style>

<h4><?=Yii::t('app', 'Tindakan')?></h4>
<div class="table-responsive">
    <table class="table datatable-basic table-striped table-hover dataTable tbl-bordered" 
        id="data-riwayat-tindakan" 
        style="width:100%" border="1" 
        >
        <thead>
            <tr class="bg-inverse">
                <th>No</th>
                <th><?= Yii::t('app', 'Tanggal tindakan') ?></th>
                <th><?= Yii::t('app', 'Nama tindakan / paket') ?></th>
                <th><?= Yii::t('app', 'Dokter Pemeriksa') ?></th>
                <th><?= Yii::t('app', 'Dokter Delegasi') ?></th>
                <th><?= Yii::t('app', 'Perawat 1') ?></th>
                <th><?= Yii::t('app', 'Perawat 2') ?></th>
                <th><?= Yii::t('app', 'Qty') ?></th>
                <!-- <th><?php // Yii::t('app', 'Tarif Satuan') ?></th> -->
                <!-- <th><?php // Yii::t('app', 'Tarif CITO') ?></th> -->
                <!-- <th><?php // Yii::t('app', 'Jumlah Tarif') ?></th> -->
                <th><?= Yii::t('app', 'Keterangan') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0; ?>
            <?php if (!empty($data_tindakanbmhp)):
                $no = 1;
                $count = 0;
                foreach ($data_tindakanbmhp as $v_tindakan):
                    if ($v_tindakan['tipe_pelayanan'] == 'TINDAKAN' || $v_tindakan['tipe_pelayanan'] == 'PAKET'): ?>
                        <tr data-id='<?= $v_tindakan['tindakan_obat_id'] ?>'>
                            <td class='td-no'><?= $no ?></td>
                            <td><?= date('d-m-Y H:i:s', strtotime($v_tindakan['tgl_tindakan'])) ?></td>
                            <td class='daftartindakan-nama'><?= $v_tindakan['tipepaket_nama'] != '' ? $v_tindakan['tipepaket_nama'] : $v_tindakan['tindakan_obat'] ?></td>
                            <td class='dokterpenanggungjawab-nama'><?= $v_tindakan['dokter_pemeriksa'] ?></td>
                            <td><?= $v_tindakan['dokter_delegasi'] ?></td>
                            <td><?= $v_tindakan['perawat_1'] ?></td>
                            <td><?= $v_tindakan['perawat_2'] ?></td>
                            <td><?= $v_tindakan['qty'] ?></td>
                            <!-- <td style="text-align: right"><?php // DocoHelpers::rupiahDisplay($v_tindakan['tarif_satuan']) ?></td> -->
                            <!-- <td style="text-align: right"><?php // DocoHelpers::rupiahDisplay($v_tindakan['tarifcyto_tindakan']) ?></td> -->
                            <!-- <td style="text-align: right"><?php //  DocoHelpers::rupiahDisplay($v_tindakan['jumlah_tarif']) ?></td> -->
                            <?php if(isset($v_tindakan['tindakanbmhp_is_deleted']) && $v_tindakan['tindakanbmhp_is_deleted'] == TRUE){ ?>
                            <td><span class="label label-danger">Dibatalkan</span></td>
                            <?php }else if(isset($v_tindakan['tindakansudahbayar_id']) || isset($v_tindakan['pasienpulang_id'])){?>
                            <td><span class="label label-default">Sudah Dibayar/Pulang</span></td>
                            <?php }else{?>
                            <td>-</td>
                            <?php } ?>
                        </tr>
                    <?php $count = $count + 1; 
                        $no = $no + 1;
                        $total = $total + $v_tindakan['jumlah_tarif'];
                    endif;
                endforeach;
            endif; ?> 
        </tbody>
        <!-- <tfoot id="foot-tindakan">
            <tr class='tr-foot'>
                <th colspan='10' class='text-center'>Total</th>
                <th style="text-align: right"><?php //  DocoHelpers::rupiahDisplay($total) ?></th>
                <th></th>
            </tr>
        </tfoot> -->
    </table>
</div>
<br>
<h4><?=Yii::t('app', 'BMHP')?></h4>
<div class="table-responsive">
    <table class="table datatable-basic table-striped table-hover dataTable tbl-bordered" 
        id="data-riwayat-bmhp" 
        style="width:100%" border="1" 
        >
        <thead>
            <tr class="bg-inverse">
                <th><?=Yii::t('app', 'No')?></th>
                <th><?=Yii::t('app', 'Tanggal tindakan')?></th>
                <th><?=Yii::t('app', 'Nama tindakan')?></th>
                <th><?=Yii::t('app', 'Obat/alkes')?></th>
                <th><?=Yii::t('app', 'Nama perawat 1')?></th>
                <th><?=Yii::t('app', 'Nama perawat 2')?></th>
                <th><?=Yii::t('app', 'Qty')?></th>
                <th><?=Yii::t('app', 'Ditagihkan')?></th>
                <!-- <th><?php // Yii::t('app', 'Jumlah tarif')?></th> -->
                <th><?=Yii::t('app', 'Keterangan')?></th>
            </tr>
        </thead>
        <tbody> 
            <?php $total = 0; ?>
            <?php if (!empty($data_tindakanbmhp)):
                $no = 1;
                $count = 0;
                foreach ($data_tindakanbmhp as $key => $value):
                    if ($value['tipe_pelayanan'] == 'BMHP'): ?>
                        <tr data-id='<?= $value['tindakan_obat_id'] ?>'>
                            <td class='td-no-bmhp'><?= $no ?></td>
                            <td><?= date('d-m-Y H:i:s', strtotime($value['tgl_tindakan'])) ?></td>
                            <td class='daftartindakan-nama'><?= $value['tipepaket_nama'] != '' ? $value['tipepaket_nama'] : $value['tindakan'] ?></td>
                            <td><?= $value['tindakan_obat'] ?></td>
                            <td><?= $value['perawat_1'] != '' ? $value['perawat_1'] : '-' ?></td>
                            <td><?= $value['perawat_2'] != '' ? $value['perawat_2'] : '-' ?></td>
                            <td><?= $value['qty'] ?></td>
                            <td><?= $value['ditagihkan'] != null ? 'Ya' : 'Tidak' ?></td>
                            <!-- <td style="text-align: right"><?php // DocoHelpers::rupiahDisplay($value['jumlah_tarif']) ?></td> -->
                            <?php if(isset($value['tindakanbmhp_is_deleted']) && $value['tindakanbmhp_is_deleted'] == TRUE){ ?>
                            <td><span class="label label-danger">Dibatalkan</span></td>
                            <?php }else if(isset($value['tindakansudahbayar_id']) || isset($value['pasienpulang_id'])){?>
                            <td><span class="label label-default">Sudah Dibayar/Pulang</span></td>
                            <?php }else{?>
                            <td>-</td>
                            <?php } ?>
                        </tr>
                        <?php $count = $count + 1; 
                        $no = $no + 1;
                        $total = $total + $value['jumlah_tarif'];
                    endif;
                endforeach;
            endif; ?>
        </tbody>
        <!-- <tfoot id="foot-bmhp">
            <tr class='tr-foot'>
                <th colspan='8' class='text-center'>Total</th>
                <th style="text-align: right"><?php // DocoHelpers::rupiahDisplay($total) ?></th>
                <th></th>
            </tr>
        </tfoot> -->
    </table>
</div>
