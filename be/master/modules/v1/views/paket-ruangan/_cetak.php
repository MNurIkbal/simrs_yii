<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
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
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table class="tbl-bordered" width="100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Nama Ruangan");?></th>
                        <th><?=\Yii::t("app", "Kode Paket");?></th>
                        <th><?=\Yii::t("app", "Nama Paket");?></th>
                        <th><?=\Yii::t("app", "Nama Lainnya");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($model as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['ruangan_nama'] ?></td>
                            <td><?= $value['tipepaket_kode'] ?></td>
                            <td><?= $value['tipepaket_nama'] ?></td>
                            <td><?= $value['tipepaket_namalainnya'] ?></td>
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