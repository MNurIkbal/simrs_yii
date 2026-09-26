
<table style="width: 100%">
    <tr>
        <td style="text-align: center;"><?=Yii::t('app', 'Rincian Tagihan Penjualan Obat Alkes')?></td>		
    </tr>
    <tr>
        <td style="text-align: center;"><?=Yii::t('app', 'Apotek Farmasi')?></td>		
    </tr>
</table>
<br>
<table width="100%" cellpadding="10" class="tabel">
    <tbody>
        <tr>
            <td class="bold"><b><?= Yii::t('app', 'No reseptur') ?></b></td>
            <td class="header_noResep"><?=!empty($data['noresep']) ? $data['noresep'] : '-' ?></td>
            <td class="bold"><b><?= Yii::t('app', 'Nama pasien') ?></b></td>
            <td class="header_namaPasien"><?= !empty($data['nama_pasien']) ? $data['nama_pasien'] : '-' ?></td>
            <td class="bold"><b><?= Yii::t('app', 'Cara bayar') ?></b></td>
            <td class="header_instalasi"><?= !empty($data['carabayar_nama']) ? $data['carabayar_nama'] : '-'?></td>
            <td class="bold"><b><?= Yii::t('app', 'Instalasi') ?></b></td>
            <td class="header_dokter"><?= !empty($data['instalasi_reseptur']) ? $data['instalasi_reseptur'] : '-'?></td>
        </tr>
        <tr>
            <td class="bold"><b><?= Yii::t('app', 'No Pendaftaran') ?></b></td>
            <td class="header_noPendaftaran"><?= !empty($data['no_pendaftaran']) ? $data['no_pendaftaran'] : '-' ?></td>
            <td class="bold"><b><?= Yii::t('app', 'Dokter resep') ?></b></td>
            <td class="header_dokter"><?= !empty($data['nama_pegawai']) ? $data['nama_pegawai'] : '-' ?></td>
            <td class="bold"><b><?= Yii::t('app', 'Penjamin') ?></b></td>
            <td class="header_ruangan"><?= !empty($data['penjamin_nama']) ? $data['penjamin_nama'] : '-'?></td>
            <td class="bold"><b><?= Yii::t('app', 'Ruangan') ?></b></td>
            <td class="header_ruangan"><?= !empty($data['ruangan_reseptur']) ? $data['ruangan_reseptur'] : '-'?></td>
        </tr>
    </tbody>
</table>
<br>
<table width="100%" border="1">
    <thead  style="font-size: 13px">
        <tr class="bg-inverse">
            <th width="1">No</th>
            <th><?= Yii::t('app', 'Nama Obat Alkes') ?></th>
            <th><?= Yii::t('app', 'Signa') ?></th>
            <th><?= Yii::t('app', 'Harga Satuan') ?></th>
            <th><?= Yii::t('app', 'Qty') ?></th>
            <th><?= Yii::t('app', 'Sub Total') ?></th>
        </tr>                                
    </thead>
    <tbody  style="font-size: 13px">
        <?php 
        $no = 1;
        $total = 0;
        foreach($data_obat as $value): 
            // $value['ppn'] = ($value['hargasatuan_oa'] *$value['ppn_persen'])/100;
            $subtotal = $value['qty_reseptur'] * $value['hargajual_satuan'];
            $total += $subtotal;
            // $value['ppn'] = "Rp. ".number_format($value['ppn'], 0, ',','.');
            $value['totaltagihan'] = "Rp. ".number_format($total, 0, ',','.');                                       
            $value['hargasatuan_oa'] = "Rp. ".number_format($value['hargajual_satuan'], 0, ',','.');    		
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?=$value['obatalkes_nama']?></td>
            <td><?=$value['signa_nama']?></td>
            <td><?=$value['hargajual_satuan']?></td>
            <td><?=$value['qty_reseptur']?></td>
            <td><?=$subtotal?></td>
        </tr>
        <?php 
        $no++;
        endforeach;
        ?>
        <tr>
            <td colspan="5">Total</td>
            <td><?=$total?></td>
        </tr>
    </tbody>  
</table>
