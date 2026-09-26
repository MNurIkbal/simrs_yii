<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use Doco\components\DocoHelpers;
?>

<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">

            <table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?= \Yii::t("app", "Tanggal PO"); ?></th>
                        <th><?= \Yii::t("app", "Nomor PO"); ?></th>
                        <th><?= \Yii::t("app", "Total Harga PO"); ?></th>
                        <th><?= \Yii::t("app", "Supplier"); ?></th>
                        <th><?= \Yii::t("app", "Rencana Terima"); ?></th>
                        <th><?= \Yii::t("app", "Status"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= date('d M Y', strtotime($value['tanggal_po'])) ?></td>
                            <td><?= $value['no_transaksi'] ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::rupiahDisplay($value['total_harga_po']) ?></td>
                            <td><?= $value['supplier_nama'] ?></td>
                            <td><?= !empty($value['tgl_rencanaterima'])
                                    ? date('d M Y', strtotime($value['tgl_rencanaterima'])) : null ?></td>
                            <td><?= $value['stat_penerimaan'] ?></td>
                        </tr>
                    <?php
                        $no++;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
