<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    table {
        border-collapse: collapse;
    }
   .bg-inverse th, .td-inverse td {
    border: 1px solid #000000;
    padding: 10px;
    text-align: left;
  }
  tr:nth-child(even) {
    background-color: #eee;
  }
  tr:nth-child(odd) {
    background-color: #fff;
  }  
</style>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="0">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Tanggal");?></th>
                        <th><?=\Yii::t("app", "Pasien");?></th>
                        <th><?=\Yii::t("app", "No Telepon");?></th>
                        <th><?=\Yii::t("app", "Kelas");?></th>
                        <th><?=\Yii::t("app", "Ruangan");?></th>
                        <th><?=\Yii::t("app", "Dokter Penanggung Jawab");?></th>
                        <th><?=\Yii::t("app", "Cara Bayar");?></th>
                        <th><?=\Yii::t("app", "Status");?></th>
                        <th><?=\Yii::t("app", "Dipulangkan Oleh");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                            $carabayar_nama = isset($value['carabayar_nama']) ? $value['carabayar_nama'] : ' - ';
                            $penjamin_nama =  isset($value['penjamin_nama']) ? $value['penjamin_nama'] : ' - ';
                            $cara_kondisipulang = (is_null($value['pasienpulang_id']) && $value['is_stopakomodasi'] == true) ? 'STOP AKOMODASI' : $value['carakeluar_nama'] .' - '. $value['kondisikeluar_nama'];
                    ?>
                        <tr class="td-inverse">
                            <td align="center"><?= $no ?></td>
                            <td align="center"><?= isset($value['tgl_admisi']) ? $value['tgl_admisi'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['no_telpon']) ? $value['no_telpon'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['ruangan_nama']) ? $value['ruangan_nama'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['dokter']) ? $value['dokter'] : ' - ' ?></td>
                            <td align="center"><?=  $carabayar_nama.'<br>'.$penjamin_nama ?></td>
                            <td align="center"><?= $cara_kondisipulang ?></td>
                            <td align="center"><?= isset($value['petugas_pemulang_nama']) ? $value['petugas_pemulang_nama'] : ' - ' ?></td>
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