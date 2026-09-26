<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<center><h5 class="modal-title">Klasifikasi Pasien</h5></center> 
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="1">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Nama Klasifikasipasien");?></th>
                        <th><?=\Yii::t("app", "Kode Klasifikasipasien");?></th>
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
                            <td><?= $value['klasifikasipasien_nama'] ?></td>
                            <td><?= $value['klasifikasipasien_kode'] ?></td>
                            <td><?= ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif' ?></td>
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