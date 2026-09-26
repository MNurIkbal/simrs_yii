<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th width="15%"><?=\Yii::t("app", "Kode Barang");?></th>
                        <th width="25%"><?=\Yii::t("app", "Nama Barang");?></th>
                        <th><?=\Yii::t("app", "Kelompok");?></th>
                        <th><?=\Yii::t("app", "Sub Kelompok");?></th>
                        <th><?=\Yii::t("app", "Golongan");?></th>
                        <th><?=\Yii::t("app", "Status");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['barang_kode'] ?></td>
                            <td><?= $value['barang_nama'] ?></td>
                            <td><?= $value['kelompokbarang_nama'] ?></td>
                            <td><?= $value['subkelompok_nama'] ?></td>
                            <td><?= $value['golonganbarang_nama'] ?></td>
                            <td><?= ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif' ?></td>
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