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
    .bold {
        font-weight: bold;
    }
    .header-right {
        padding-right: 15px;
    }
</style>
<table width="100%" class="tbl-bordered">
    <thead  style="font-size: 13px">
        <tr class="bg-inverse">
            <th width="1">No</th>
            <th><strong><?= Yii::t('app', 'Racikan / Non Racikan') ?></strong></th>
            <th><strong><?= Yii::t('app', 'R ke') ?></strong></th>
            <th><strong><?= Yii::t('app', 'Signa') ?></strong></th>
            <th><strong><?= Yii::t('app', 'Nama Obat Alkes') ?></strong></th>
            <th style="text-align: right"><strong><?= Yii::t('app', 'Harga Satuan (Rp.)') ?></strong></th>
            <th><strong><?= Yii::t('app', 'Qty') ?></strong></th>
            <th style="text-align: right"><strong><?= Yii::t('app', 'Sub Total (Rp.)') ?></strong></th>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
        <?php
        $no = 1;
        $total = 0;
        foreach($data_obat as $value):
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?=$value['jenis_racikan']?></td>
            <td><?=$value['rke']?></td>
            <td><?=$value['signa_oa']?></td>
            <td><?=$value['obatalkes_nama']?></td>
            <td style="text-align: right"><?=$value['hargajual_oa']?></td>
            <td style="text-align: right"><?=$value['qty']?></td>
            <td style="text-align: right"><?=$value['sub_total']?></td>
        </tr>
        <?php
        $no++;
        endforeach;
        ?>
        <tr>
            <td style="text-align: right" colspan="6">Total Obat</td>
            <td style="text-align: right">Rp.</td>
            <td style="text-align: right"><?=$totalharga?></td>
        </tr>
        <tr>
            <td style="text-align: right" colspan="6">Biaya Administrasi</td>
            <td style="text-align: right">Rp.</td>
            <td style="text-align: right"><?=$biaya_admin?></td>
        </tr>
        <tr>
            <td style="text-align: right" colspan="6">Jasa Racik</td>
            <td style="text-align: right">Rp.</td>
            <td style="text-align: right"><?=$jasa_racik?></td>
        </tr>
        <tr>
            <td style="text-align: right" colspan="6">Pembulatan</td>
            <td style="text-align: right">Rp.</td>
            <td style="text-align: right"><?=$pembulatan?></td>
        </tr>
        <tr>
            <td style="text-align: right" colspan="6">Total Tagihan</td>
            <td style="text-align: right">Rp.</td>
            <td style="text-align: right"><?=$total_tagihan?></td>
        </tr>
    </tbody>
</table>
