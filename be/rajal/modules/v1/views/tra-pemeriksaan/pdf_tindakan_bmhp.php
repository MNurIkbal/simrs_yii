
<table width="100%" cellpadding="10" class="tabel">
    <tbody>
        <tr>
            <td class=""><?= Yii::t('app', 'Poliklinik') ?></td>
            <td class=""><?=!empty($data_header['ruangan_pendaftaran']) ? $data_header['ruangan_pendaftaran'] : '-' ?></td>
            <td class=""><?= Yii::t('app', 'Jenis kelamin') ?></td>
            <td class=""><?= !empty($data_header['jenis_kelamin']) ? $data_header['jenis_kelamin'] : '-' ?></td>
        </tr>
        <tr>
            <td class=""><?= Yii::t('app', 'No Pendaftaran') ?></td>
            <td class=""><?= !empty($data_header['no_pendaftaran']) ? $data_header['no_pendaftaran'] : '-' ?></td>
            <td class=""><?= Yii::t('app', 'Tanggal lahir') ?></td>
            <td class=""><?= !empty($data_header['tanggal_lahir']) ? date('d F Y', strtotime($data_header['tanggal_lahir'])) : '-' ?></td>
        </tr>
        <tr>
            <td class=""><?= Yii::t('app', 'No rekam medik') ?></td>
            <td class=""><?= !empty($data_header['no_rekam_medik']) ? $data_header['no_rekam_medik'] : '-' ?></td>
            <td class=""><?= Yii::t('app', 'Nama pasien') ?></td>
            <td class=""><?= !empty($data_header['nama_pasien']) ? $data_header['nama_pasien'] : '-' ?></td>
        </tr>
    </tbody>
</table>
<br>
<h4 style="text-align: center;"><?=Yii::t('app', 'TINDAKAN & PEMAKAIAN BMHP & ALKES')?></h4>		
<br>
<fieldset>
    <legend><?=Yii::t('app', 'Tindakan medis')?></legend>
    <table cellpadding="5" border="1">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?= Yii::t('app', 'Tanggal tindakan') ?></th>
                <th><?= Yii::t('app', 'Nama tindakan/paket') ?></th>
                <th><?= Yii::t('app', 'Dokter pemeriksaan') ?></th>
                <th><?= Yii::t('app', 'Dokter delegasi') ?></th>
                <th><?= Yii::t('app', 'Perawat 1') ?></th>
                <th><?= Yii::t('app', 'Perawat 2') ?></th>
                <th><?= Yii::t('app', 'Qty') ?></th>
                <th><?= Yii::t('app', 'Tarif satuan') ?></th>
                <th><?= Yii::t('app', 'Tarif cyto') ?></th>
                <th><?= Yii::t('app', 'Jumlah tarif') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $totalTindakan = 0;
            if (!empty($data_tindakan)) :
                foreach($data_tindakan as $value) :
                    $totalTindakan += $value['jumlah_tarif'];
                ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= date('d F Y H:i:s', strtotime($data_header['tgl_tindakan'])) ?></td>
                        <td><?= $value['tindakan_obat'] ?></td>
                        <td><?= $value['dokter_pemeriksa'] ?></td>
                        <td><?= $value['dokter_delegasi'] ?></td>
                        <td><?= $value['perawat_1'] ?></td>
                        <td><?= $value['perawat_2'] ?></td>
                        <td><?= $value['qty'] ?></td>
                        <td><?= "Rp. ".number_format($value['tarif_satuan'], 0, ',','.'); ?></td>
                        <td><?= $value['tarifcyto_tindakan'] ?></td>
                        <td><?= "Rp. ".number_format($value['jumlah_tarif'], 0, ',','.'); ?></td>
                    </tr>
                <?php 
                $no++;
                endforeach;
                ?>
                    <tr>
                        <td colspan="10">Total</td>
                        <td><?= "Rp. ".number_format($totalTindakan, 0, ',','.'); ?></td>
                    </tr>
            <?php
            else : 
            ?>
                <tr>
                    <td colspan="11" class="text-center">Data tidak ditemukan</td>
                </tr>
            <?php
            endif;
            ?>
        </tbody>  
    </table>
</fieldset>
<br>
<fieldset>
    <legend><?=Yii::t('app', 'Pemakaian alkes/obat')?></legend>
    <table cellpadding="5" border="1">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?= Yii::t('app', 'Tanggal tindakan') ?></th>
                <th><?= Yii::t('app', 'Nama tindakan') ?></th>
                <th><?= Yii::t('app', 'Obat/alkes') ?></th>
                <th><?= Yii::t('app', 'Perawat 1') ?></th>
                <th><?= Yii::t('app', 'Perawat 2') ?></th>
                <th><?= Yii::t('app', 'Qty') ?></th>
                <th><?= Yii::t('app', 'Ditagihkan') ?></th>
                <th><?= Yii::t('app', 'Jumlah tarif') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $totalBmhp = 0;
            if (!empty($data_bmhp)) :
                foreach($data_bmhp as $value) :
                    $ditagihkan = !empty($value['ditagihkan']) ? '<center>&#118;</center>' : '';
                    $totalBmhp += $value['jumlah_tarif'];
            ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= date('d F Y H:i:s', strtotime($data_header['tgl_tindakan'])) ?></td>
                        <td><?= $value['tindakan'] ?></td>
                        <td><?= $value['tindakan_obat'] ?></td>
                        <td><?= $value['perawat_1'] ?></td>
                        <td><?= $value['perawat_2'] ?></td>
                        <td><?= $value['qty'] ?></td>
                        <td><?= $ditagihkan ?></td>
                        <td><?= "Rp. ".number_format($value['jumlah_tarif'], 0, ',','.'); ?></td>
                    </tr>
                <?php 
                $no++;
                endforeach;
                ?>
                    <tr>
                        <td colspan="8">Total</td>
                        <td><?= "Rp. ".number_format($totalBmhp, 0, ',','.'); ?></td>
                    </tr>
            <?php
            else : 
            ?>
                <tr>
                    <td colspan="11" class="text-center">Data tidak ditemukan</td>
                </tr>
            <?php
            endif;
            ?>
        </tbody>  
    </table>
</fieldset>
<?php
    $total = $totalBmhp + $totalTindakan;
?>
<br>
<?= Yii::t('app', 'Total keseluruhan') ?> : <?= "Rp. ".number_format($total, 0, ',','.'); ?>