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
                        <th><?=\Yii::t("app", "Jenis Kasus Penyakit");?></th>
                        <th><?=\Yii::t("app", "Kode Diagnosa");?></th>
                        <th><?=\Yii::t("app", "Nama Diagnosa");?></th>
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
                            <td><?= $value['jeniskasuspenyakit_nama'] ?></td>
                            <td><?= $value['diagnosa_kode'] ?></td>
                            <td><?= $value['diagnosa_nama'] ?></td>
                            <td><?= $value['status'] ?></td>
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