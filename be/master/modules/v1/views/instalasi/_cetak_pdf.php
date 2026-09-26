<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    th, td {
        padding: 5px;
    }
</style>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="0" width="100%" border="1">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Nama Rumah Sakit");?></th>
                        <th><?=\Yii::t("app", "Nama Instalasi");?></th>
                        <th><?=\Yii::t("app", "Nama Singkatan Instalasi");?></th>
                        <th><?=\Yii::t("app", "Satu Sehat Organization ID");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td align="center"><?= $no ?></td>
                            <td align="center"><?= $value['nama_rumahsakit'] ?></td>
                            <td align="center"><?= $value['instalasi_nama'] ?></td>
                            <td align="center"><?= $value['instalasi_singkatan'] ?></td>
                            <td align="center"><?= $value['satusehat_instalasi_id'] ?></td>
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