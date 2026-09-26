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
                        <th><?=\Yii::t("app", "Kamar Tempat Tidur");?></th>
                        <th><?=\Yii::t("app", "Jenis Kamar");?></th>
                        <th><?=\Yii::t("app", "Warna Tempat Tidur");?></th>
                        <th><?=\Yii::t("app", "Status Kamar");?></th>
                        <th><?=\Yii::t("app", "Status");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td align="center"><?= $no ?></td>
                            <td align="center"><?= $value['kettempattidur_nama'] ?></td>
                            <td align="center"><?= $value['jenis_kamar'] ?></td>
                            <td align="center"><?= $value['kettempattidur_warna'] ?></td>
                            <td align="center"><?= $value['is_kosong'] ?></td>
                            <td align="center"><?= $value['is_active'] ?></td>
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