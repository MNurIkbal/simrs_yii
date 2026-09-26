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
                        <th><?=\Yii::t("app", "Nama Klasifikasi");?></th>
                        <th><?=\Yii::t("app", "SIRS Online");?></th>
                        <th><?=\Yii::t("app", "EIS Covid");?></th>
                        <th><?=\Yii::t("app", "Applicare");?></th>
                        <th><?=\Yii::t("app", "SPGDT");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td align="center"><?= $no ?></td>
                            <td align="center"><?= $value['klasifikasikamar_nama'] ?></td>
                            <td align="center"><?= $value['sirsonline_nama'] ?></td>
                            <td align="center"><?= $value['eiscovid_nama'] ?></td>
                            <td align="center"><?= $value['applicare_nama'] ?></td>
                            <td align="center"><?= $value['spgdt_nama'] ?></td>
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