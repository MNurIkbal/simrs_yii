
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
<table class="tbl-bordered" style="width:100%" border="1" cellpadding="5" cellspacing="1">
    <thead>
        <tr>
            <th><?=\Yii::t('app', 'KODE_RS');?></th>
            <th><?=\Yii::t('app', 'KELAS_RS');?></th>
            <th><?=\Yii::t('app', 'KELAS_RAWAT');?></th>
            <th><?=\Yii::t('app', 'KODE_TARIF');?></th>
            <th><?=\Yii::t('app', 'PTD');?></th>
            <th><?=\Yii::t('app', 'ADMISSION_DATE');?></th>
            <th><?=\Yii::t('app', 'DISCHARGE_DATE');?></th>
            <th><?=\Yii::t('app', 'BIRTH_DATE');?></th>
            <th><?=\Yii::t('app', 'BIRTH_WEIGHT');?></th>
            <th><?=\Yii::t('app', 'SEX');?></th>
            <th><?=\Yii::t('app', 'DISCHARGE_STATUS');?></th>
            <th><?=\Yii::t('app', 'DIAGLIST');?></th>
            <th><?=\Yii::t('app', 'PROCLIST');?></th>
            <th><?=\Yii::t('app', 'ADL1');?></th>
            <th><?=\Yii::t('app', 'ADL2');?></th>
            <th><?=\Yii::t('app', 'IN_SP');?></th>
            <th><?=\Yii::t('app', 'IN_SR');?></th>
            <th><?=\Yii::t('app', 'IN_SI');?></th>
            <th><?=\Yii::t('app', 'IN_SD');?></th>
            <th><?=\Yii::t('app', 'INACBG');?></th>
            <th><?=\Yii::t('app', 'SUBACUTE');?></th>
            <th><?=\Yii::t('app', 'CHRONIC');?></th>
            <th><?=\Yii::t('app', 'SP');?></th>
            <th><?=\Yii::t('app', 'SR');?></th>
            <th><?=\Yii::t('app', 'SI');?></th>
            <th><?=\Yii::t('app', 'SD');?></th>
            <th><?=\Yii::t('app', 'DESKRIPSI_INACBG');?></th>
            <th><?=\Yii::t('app', 'TARIF_INACBG');?></th>
            <th><?=\Yii::t('app', 'TARIF_SUBACUTE');?></th>
            <th><?=\Yii::t('app', 'TARIF_CHRONIC');?></th>
            <th><?=\Yii::t('app', 'DESKRIPSI_SP');?></th>
            <th><?=\Yii::t('app', 'TARIF_SP');?></th>
            <th><?=\Yii::t('app', 'DESKRIPSI_SR');?></th>
            <th><?=\Yii::t('app', 'TARIF_SR');?></th>
            <th><?=\Yii::t('app', 'DESKRIPSI_SI');?></th>
            <th><?=\Yii::t('app', 'DESKRIPSI_SD');?></th>
            <th><?=\Yii::t('app', 'TARIF_SD');?></th>
            <th><?=\Yii::t('app', 'TOTAL_TARIF');?></th>
            <th><?=\Yii::t('app', 'TARIF_RS');?></th>
            <th><?=\Yii::t('app', 'TARIF_POLI_EKS');?></th>
            <th><?=\Yii::t('app', 'LOS');?></th>
            <th><?=\Yii::t('app', 'ICU_INDIKATOR');?></th>
            <th><?=\Yii::t('app', 'ICU_LOS');?></th>
            <th><?=\Yii::t('app', 'VENT_HOUR');?></th>
            <th><?=\Yii::t('app', 'NAMA_PASIEN');?></th>
            <th><?=\Yii::t('app', 'MRN');?></th>
            <th><?=\Yii::t('app', 'UMUR_TAHUN');?></th>
            <th><?=\Yii::t('app', 'UMUR_HARI');?></th>
            <th><?=\Yii::t('app', 'DPJP');?></th>
            <th><?=\Yii::t('app', 'SEP');?></th>
            <th><?=\Yii::t('app', 'NOKARTU');?></th>
            <th><?=\Yii::t('app', 'PAYOR_ID');?></th>
            <th><?=\Yii::t('app', 'CODER_ID');?></th>
            <th><?=\Yii::t('app', 'VERSI_INACBG');?></th>
            <th><?=\Yii::t('app', 'VERSI_GROUPER');?></th>
            <th><?=\Yii::t('app', 'C1');?></th>
            <th><?=\Yii::t('app', 'C2');?></th>
            <th><?=\Yii::t('app', 'C3');?></th>
            <th><?=\Yii::t('app', 'C4');?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($data as $value) :
        ?>
            <tr>
                <td><?=$value['KODE_RS']?></td>
                <td><?=$value['KELAS_RS']?></td>
                <td><?=$value['KELAS_RAWAT']?></td>
                <td><?=$value['KODE_TARIF']?></td>
                <td><?=$value['PTD']?></td>
                <td><?=$value['ADMISSION_DATE']?></td>
                <td><?=$value['DISCHARGE_DATE']?></td>
                <td><?=$value['BIRTH_DATE']?></td>
                <td><?=$value['BIRTH_WEIGHT']?></td>
                <td><?=$value['SEX']?></td>
                <td><?=$value['DISCHARGE_STATUS']?></td>
                <td><?=$value['DIAGLIST']?></td>
                <td><?=$value['PROCLIST']?></td>
                <td><?=$value['ADL1']?></td>
                <td><?=$value['ADL2']?></td>
                <td><?=$value['IN_SP']?></td>
                <td><?=$value['IN_SR']?></td>
                <td><?=$value['IN_SI']?></td>
                <td><?=$value['IN_SD']?></td>
                <td><?=$value['INACBG']?></td>
                <td><?=$value['SUBACUTE']?></td>
                <td><?=$value['CHRONIC']?></td>
                <td><?=$value['SP']?></td>
                <td><?=$value['SR']?></td>
                <td><?=$value['SI']?></td>
                <td><?=$value['SD']?></td>
                <td><?=$value['DESKRIPSI_INACBG']?></td>
                <td><?=$value['TARIF_INACBG']?></td>
                <td><?=$value['TARIF_SUBACUTE']?></td>
                <td><?=$value['TARIF_CHRONIC']?></td>
                <td><?=$value['DESKRIPSI_SP']?></td>
                <td><?=$value['TARIF_SP']?></td>
                <td><?=$value['DESKRIPSI_SR']?></td>
                <td><?=$value['TARIF_SR']?></td>
                <td><?=$value['DESKRIPSI_SI']?></td>
                <td><?=$value['DESKRIPSI_SD']?></td>
                <td><?=$value['TARIF_SD']?></td>
                <td><?=$value['TOTAL_TARIF']?></td>
                <td><?=$value['TARIF_RS']?></td>
                <td><?=$value['TARIF_POLI_EKS']?></td>
                <td><?=$value['LOS']?></td>
                <td><?=$value['ICU_INDIKATOR']?></td>
                <td><?=$value['ICU_LOS']?></td>
                <td><?=$value['VENT_HOUR']?></td>
                <td><?=$value['NAMA_PASIEN']?></td>
                <td><?=$value['MRN']?></td>
                <td><?=$value['UMUR_TAHUN']?></td>
                <td><?=$value['UMUR_HARI']?></td>
                <td><?=$value['DPJP']?></td>
                <td><?=$value['SEP']?></td>
                <td><?=$value['NOKARTU']?></td>
                <td><?=$value['PAYOR_ID']?></td>
                <td><?=$value['CODER_ID']?></td>
                <td><?=$value['VERSI_INACBG']?></td>
                <td><?=$value['VERSI_GROUPER']?></td>
                <td><?=$value['C1']?></td>
                <td><?=$value['C2']?></td>
                <td><?=$value['C3']?></td>
                <td><?=$value['C4']?></td>
            </tr>
        <?php
            $no++;
            endforeach;
        ?>
    </tbody>
</table>