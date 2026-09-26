<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="1">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Nama Ruangan");?></th>
                        <th><?=\Yii::t("app", "Kode Tindakan");?></th>
                        <th><?=\Yii::t("app", "Nama Tindakan");?></th>
                        <th><?=\Yii::t("app", "Nama Lainnya");?></th>
                        <th><?=\Yii::t("app", "Kategori");?></th>
                        <th><?=\Yii::t("app", "Kelompok");?></th>
                        <th><?=\Yii::t("app", "Kegiatan");?></th>
                        <th><?=\Yii::t("app", "Group INA CBGS");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['ruangan_nama'] ?></td>
                            <td><?= $value['daftartindakan_kode'] ?></td>
                            <td><?= $value['daftartindakan_nama'] ?></td>
                            <td><?= $value['daftartindakan_namalainnya'] ?></td>
                            <td><?= $value['kategoritindakan_nama'] ?></td>
                            <td><?= $value['kelompoktindakan_nama'] ?></td>
                            <td><?= $value['jeniskegiatantindakan_nama'] ?></td>
                            <td><?= $value['groupinacbg_nama'] ?></td>
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