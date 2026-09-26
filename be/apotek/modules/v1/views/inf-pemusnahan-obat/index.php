<?php
/**
 * @author Randy Vianda Putra
 * @copyright 8 Juni 2018 aweutist
 */
?>

<table style="width: 100%">
    <tr>
        <td style="text-align: center;"><b><?= strtoupper(Yii::t('app', 'Pemusnahan Obat Alkes Expired')) ?></b></td>
    </tr>
</table>
<br>
<table width="100%" cellpadding="10" class="tabel">
    <tbody>
        <tr>
            <td class="bold"><b><?= Yii::t('app', 'No pemusnahan') ?></b></td>
            <td class="header_nopemusnahan"><?=!empty($data['nopemusnahan']) ? $data['nopemusnahan'] : '-' ?></td>
            <td class="bold"><b><?= Yii::t('app', 'Instalasi pelaksana pemusnahan') ?></b></td>
            <td class="header_instalasi_nama"><?= !empty($data['instalasi_nama']) ? $data['instalasi_nama'] : '-' ?></td>
        </tr>
        <tr>
            <td class="bold"><b><?= Yii::t('app', 'Tanggal pemusnahan') ?></b></td>
            <td class="header_tglpemusnahan"><?= !empty($data['tglpemusnahan']) ? date('d-M-Y', strtotime($data['tglpemusnahan'])) : '-'?></td>
            <td class="bold"><b><?= Yii::t('app', 'Ruang pelaksana pemusnahan') ?></b></td>
            <td class="header_ruangan"><?= !empty($data['ruangan_nama']) ? $data['ruangan_nama'] : '-' ?></td>
        </tr>
    </tbody>
</table>
<br>
<table width="100%" border="1" cellspadding="5">
    <thead  style="font-size: 13px">
        <tr class="bg-inverse">
            <th width="1">No</th>
            <th><?= Yii::t('app', 'Nama Obat Alkes') ?></th>
            <th><?= Yii::t('app', 'Tanggal Expired') ?></th>
            <th><?= Yii::t('app', 'Qty') ?></th>
            <th><?= Yii::t('app', 'Satuan Kecil') ?></th>
            <th><?= Yii::t('app', 'Jumlah Harga Netto') ?></th>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
        <?php
        $no = 1;
        $total = 0;
        foreach ($data_pemusnahan as $value) :
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?=$value['obatalkes_nama']?></td>
            <td><?=date('d-M-Y', strtotime($value['tglkadaluarsa']));?></td>
            <td><?=$value['stok']?></td>
            <td><?=$value['satuan_kecil']?></td>
            <td><?="Rp. ".number_format($value['jumlah_harganetto'], 0, ',','.');?></td>
        </tr>
        <?php
        $no++;
        endforeach;
        ?>
    </tbody>
</table>
<br>
<br>
<table>
    <tr>
        <td><?= Yii::t('app', 'Pegawai Menyetujui') ?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><?= Yii::t('app', 'Pegawai Mengetahui') ?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><?= Yii::t('app', 'Pegawai Pelaksana') ?></td>
    </tr>
    <tr>
        <td colspan="8"><br></td>
    </tr>
    <tr>
        <td colspan="8"><br></td>
    </tr>
    <tr>
        <td colspan="8"><br></td>
    </tr>
    <tr>
        <td><?= $data['pegawai_menyetujui'] ?></td>
        <td></td>
        <td></td>
        <td><?= $data['pegawai_mengetahui'] ?></td>
        <td></td>
        <td></td>
        <td><?= $data['pegawai_pelaksana'] ?></td>
    </tr>
</table>
