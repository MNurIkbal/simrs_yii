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
                        <th><?=\Yii::t("app", "Instalasi");?></th>
                        <th><?=\Yii::t("app", "Ruangan");?></th>
                        <th><?=\Yii::t("app", "No Rekam Medik");?></th>
                        <th><?=\Yii::t("app", "Nama Pasien");?></th>
                        <th><?=\Yii::t("app", "Alamat Pasien");?></th>
                        <th><?=\Yii::t("app", "Jenis Kelamin");?></th>
                        <th><?=\Yii::t("app", "Umur");?></th>
                        <th><?=\Yii::t("app", "Jenis kasus penyakit");?></th>
                        <th><?=\Yii::t("app", "Kelas pelayanan / Kelas Tagihan");?></th>
                        <th><?=\Yii::t("app", "Tanggal Pendaftaran");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        $statusTitipan = '-';
                        foreach ($data as $value) :
                        $statusTitipan = '-';
                            if($value['instalasi_id'] == 2 || $value['instalasi_id'] == 3){
                                if($value['carabayar_id'] == 6){
                                    $value['kelaspelayanan_nama'] =  $value['kelaspelayanan_nama'].' / '.$statusTitipan;
                                } else if (!empty($value['is_pasientitipan_pk'])) {
                                    if($value['is_pasientitipan_pk'] == true && $value['is_stoppasientitipan'] == false){
                                        $statusTitipan =  $value['kelas_ditagihkan_nama'];
                                    }
                                    $value['kelaspelayanan_nama'] =  $value['kelaspelayanan_nama'].' / '.$statusTitipan;
                                } else if (empty($value['is_pasientitipan_pk'])) {
                                    if($value['is_pasientitipan'] == true && $value['is_stoppasientitipan'] == false){
                                        $statusTitipan =  $value['kelas_ditagihkan_nama'];
                                    }
                                    $value['kelaspelayanan_nama'] =  $value['kelaspelayanan_nama'].' / '.$statusTitipan;
                                }
                            }
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['instalasi_nama'] ?></td>
                            <td><?= $value['ruangan_nama'] ?></td>
                            <td><?= $value['no_rekam_medik'] ?></td>
                            <td><?= $value['namadepan'] ?><?= $value['nama_pasien'] ?></td>
                            <td><?= $value['alamat_pasien'] ?></td>
                            <td><?= $value['jeniskelamin'] ?></td>
                            <td><?= $value['umur'] ?></td>
                            <td><?= $value['jeniskasuspenyakit_nama'] ?></td>
                            <td><?= $value['kelaspelayanan_nama'] ?></td>
                            <td><?= date('d-F-Y H:i:s',strtotime($value['tgl_pendaftaran'])) ?></td>
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