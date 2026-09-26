<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use Doco\components\DocoHelpers;
?>

<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            
            <table  border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Tanggal Rekomendasi");?></th>
                        <th><?=\Yii::t("app", "Nomor Rekomendasi");?></th>
                        <th><?=\Yii::t("app", "Nama Pegawai");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= date('d M Y', strtotime($value['tgl_rekomendasibarang'])) ?></td>
                            <td><?= $value['no_rekomendasibarang'] ?></td>
                            <td><?= $value['nama_pegawai'] ?></td>
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