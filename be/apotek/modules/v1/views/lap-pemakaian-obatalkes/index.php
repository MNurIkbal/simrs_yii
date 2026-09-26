<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
</style>
<table style="width: 100%">
    <tr>
        <td style="text-align: center;"><?=Yii::t('app', 'Laporan Pemakaian Obat Alkes')?></td>     
    </tr>
    <tr>
        <td style="text-align: center;"><?= Yii::t('app', 'Apotek Farmasi')?></td>
    </tr>
    <tr>
        <td style="text-align: center;"><?='Periode '.$filter?></td>
    </tr>
</table>
<br>
<table width="100%" class="tbl-bordered">
    <tr style="font-size: 13px">
        <th width="1">No</th>
        <th><?=\Yii::t("app", "Tanggal Pemakaian");?></th>
        <th><?=\Yii::t("app", "Nomor Pemakaian");?></th>
        <th><?=\Yii::t("app", "Nama Penginput");?></th>
        <th><?=\Yii::t("app", "Nama Obat Alkes");?></th>
        <th><?=\Yii::t("app", "Qty Pemakaian");?></th>
        <th><?=\Yii::t("app", "Qty Konversi");?></th>
        <th><?=\Yii::t("app", "Keterangan");?></th>
    </tr>
    <tbody style="font-size: 13px">
        <?php 
        $no = 1;
        $total = 0;
        $no_pemakaian = '';
        foreach($data as $value):
            $qty_besar = $value['jumlah_input'] . ' ' . $value['satuanbesar_nama'];
            $qty_kecil = $value['qty_satuanpakai'] . ' ' . $value['satuankecil_nama'];
        if($no_pemakaian == $value['nopemakaian_obat']) : ?>
        <tr>
            <td>No Pemakaian : </td>
        </tr>
        <?php else : ?>
        <tr>
            <td><?=$no?></td>
            <td><?= date('d M Y', strtotime($value['tglpemakaianobat'])) ?></td>
            <td><?=$value['nopemakaian_obat']?></td>
            <td><?=$value['nama_pegawai']?></td>
            <td><?=$value['obatalkes_nama']?></td>
            <td><?= $qty_besar ?></td>
            <td><?= $qty_kecil ?></td>
            <td><?=$value['keterangan_pemakaianobat']?></td>
        </tr>
        <?php 
        $no++;
        endif;
        endforeach;
        ?>
    </tbody>  
</table>