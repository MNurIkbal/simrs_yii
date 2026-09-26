<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<center><h5 class="modal-title">Informasi Pembayaran Piutang Pasien</h5></center> 
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="1">
                <thead> 
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=Yii::t('app', 'Tanggal Pembayaran')?></th>
                        <th><?=Yii::t('app', 'Nama Pasien')?></th>
                        <th><?=Yii::t('app', 'No. RM')?></th>
                        <th><?=Yii::t('app', 'No. Pembayaran')?></th>
                        <th><?=Yii::t('app', 'No. Pendaftaran')?></th>
                        <th><?=Yii::t('app', 'Jumlah Bayar')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['tgl_pembayaranpiutang'] ?></td>
                            <td><?= $value['nama_pasien'] ?></td>
                            <td><?= $value['no_rekam_medik'] ?></td>
                            <td><?= $value['no_pembayaranpiutang'] ?></td>
                            <td><?= $value['no_pendaftaran'] ?></td>
                            <td><?= $value['total_bayarpiutang'] ?></td>
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