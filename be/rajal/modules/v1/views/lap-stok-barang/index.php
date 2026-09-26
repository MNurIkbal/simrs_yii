<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<center><h5 class="modal-title">Laporan Stok Barang</h5></center> 
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="1">
                <thead> 
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=Yii::t('app', 'Periode Stok')?></th>
                        <th><?=Yii::t('app', 'Ruangan')?></th>
                        <th><?=Yii::t('app', 'Nama Barang')?></th>
                        <th><?=Yii::t('app', 'Qty Masuk')?></th>
                        <th><?=Yii::t('app', 'Qty Keluar')?></th>
                        <th><?=Yii::t('app', 'Qty Dipesan')?></th>
                        <th><?=Yii::t('app', 'Tersedia')?></th>
                        <th><?=Yii::t('app', 'Stok')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['periodestok_nama'] ?></td>
                            <td><?= $value['ruangan_nama'] ?></td>
                            <td><?= $value['barang_nama'] ?></td>
                            <td><?= $value['qty_masuk'] ?></td>
                            <td><?= $value['qty_keluar'] ?></td>
                            <td><?= $value['qty_dipesan'] ?></td>
                            <td><?= $value['qty_tersedia'] ?></td>
                            <td><?= $value['qty_stok'] ?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
</div>