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
                        <th><?=\Yii::t("app", "Nama Instalasi");?></th>
                        <th><?=\Yii::t("app", "Nama Ruangan");?></th>
                        <th><?=\Yii::t("app", "Nama Ruangan Singkatan");?></th>
                        <th><?=\Yii::t("app", "Satu Sehat Location ID");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td align="center"><?= $no ?></td>
                            <td align="center"><?= $value['instalasi_nama'] ?></td>
                            <td align="center"><?= $value['ruangan_nama'] ?></td>
                            <td align="center"><?= $value['ruangan_singkatan'] ?></td>
                            <td align="center"><?= $value['satusehat_ruangan_id'] ?></td>
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