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
                        <th><?=\Yii::t("app", "Nama Suku");?></th>
                        <th><?=\Yii::t("app", "Nama Lainnya");?></th>
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
                            <td align="center"><?= $value['suku_nama'] ?></td>
                            <td align="center"><?= $value['suku_namalainnya'] ?></td>
                            <td align="center"><?= $value['status'] ?></td>
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