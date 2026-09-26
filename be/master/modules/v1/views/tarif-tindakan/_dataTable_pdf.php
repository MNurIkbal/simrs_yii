<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use Doco\components\DocoHelpers;
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
    .bg-inverse th {
        background-color: #eee;
    }
</style>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="0" style="font-size: 11px;">
                <thead>
                    <tr class="bg-inverse">
                        <th><?= Yii::t('app', 'No') ?></th>
                        <th><?= Yii::t('app', 'Jenis Tindakan / Paket') ?></th>
                        <th><?= Yii::t('app', 'Nama Tindakan / Paket') ?></th>
                        <th><?= Yii::t('app', 'Kelas Pelayanan') ?></th>
                        <th><?= Yii::t('app', 'Cara Bayar') ?></th>
                        <th><?= Yii::t('app', 'Penjamin') ?></th>
                        <th><?= Yii::t('app', 'Perda / SK') ?></th>
                        <th><?= Yii::t('app', 'Persen Cyto (%)') ?></th>
                        <th><?= Yii::t('app', 'Persen Diskon (%)') ?></th>
                        <th><?= Yii::t('app', 'Harga (Rp.)') ?></th>
                        <th><?= Yii::t('app', 'Status') ?></th>
                    </tr>
                </thead>
                <tbody>
                        <?php
                            if ( count($result) > 0 ) {
                        ?>
                        <?php foreach ($result as $index => $value){ ?>
                        <tr class="td-inverse">
                            <td><?= $index + 1 ?></td>
                            <td><?= $value['jenis_tindakan_paket'] ?></td>
                            <td><?= $value['nama_tindakan_paket'] ?></td>
                            <td><?= $value['kelaspelayanan_nama'] ?></td>
                            <td><?= $value['carabayar_nama'] ?></td>
                            <td><?= $value['penjamin_nama'] ?></td>
                            <td><?= $value['perdanama_sk'] ?></td>
                            <td><?= str_replace('.', ',', $value['persencyto_tindakan']) ?></td>
                            <td><?= str_replace('.', ',', $value['persendiskon_tindakan'])  ?></td>
                            <td><?= !empty($value['harga_tariftindakan']) ? DocoHelpers::formatNumber($value['harga_tariftindakan']) : 0 ; ?></td>
                            <td><?= ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif' ?></td>
                        </tr>
                        <?php } 
                        } else {
                        ?>
                        <tr class="td-inverse">
                            <td colspan="11" align="center">Tidak ada Data.</td>
                        </tr>
                        <?php  
                         }
                        ?>
                </tbody>
            </table>
        </div>
    </div>
</div>