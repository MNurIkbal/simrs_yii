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
                        <th><?=Yii::t('app', 'Tgl.Permintaan konsul')?></th>
                        <th><?=Yii::t('app', 'No.RM / No.Pendaftaran')?></th>
                        <th><?=Yii::t('app', 'Nama Pasien')?></th>
                        <th><?=Yii::t('app', 'Jenis Kelamin')?></th>
                        <th><?=Yii::t('app', 'Dokter DPJP')?></th>
                        <th><?=Yii::t('app', 'Cara Bayar / Penjamin')?></th>
                        <th><?=Yii::t('app', 'Hak Kelas / Kelas / Kelas Tagihan')?></th>
                        <th><?=Yii::t('app', 'Nama Ruangan No.Kamar-No.Bed')?></th>
                        <th><?=Yii::t('app', 'Hari Rawat')?></th>
                        <th><?=Yii::t('app', 'Jenis Konsul')?></th>
                        <th><?=Yii::t('app', 'Dokter Konsul')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['waktu_permintaan'] ?></td>
                            <td><?= $value['gab_noRmPdft'] ?></td>
                            <td><?= $value['nama_pasien'] ?></td>
                            <td><?= $value['jenis_kelamin'] ?></td>
                            <td><?= $value['dok_dpjp'] ?></td>
                            <td><?= $value['caraBayarPenjamin'] ?></td>
                            <td><?= $value['hakKelas'] ?></td>
                            <td><?= $value['noRuangan'] ?></td>
                            <td><?= $value['jenis_konsul_nama'] ?></td>
                            <td><?= $value['hariRawat'] ?></td>
                            <td><?= $value['dok_konsul'] ?></td>
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