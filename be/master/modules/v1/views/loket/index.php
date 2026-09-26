<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<center><h5 class="modal-title"><?=$title;?></h5></center> 
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="1">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Nama Jenis Antrian");?></th>
                        <th><?=\Yii::t("app", "Kode Antrian");?></th>
                        <th><?=\Yii::t("app", "No Loket");?></th>
                        <th><?=\Yii::t("app", "Nama Loket");?></th>
                        <th><?=\Yii::t("app", "Jenis Pengambilan Antrian");?></th>
                        <th><?=\Yii::t("app", "Status");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($detail as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['jenis_name'] ?></td>
                            <td><?= $value['jenis_value'] ?></td>
                            <td><?= $value['loket_nama'] ?></td>
                            <td><?= $value['loket_namalain'] ?></td>
                            <td><?= $value['fungsi_name'] ?></td>
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