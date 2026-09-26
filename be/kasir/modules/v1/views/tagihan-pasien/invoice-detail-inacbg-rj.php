<?php 
    use Doco\components\DocoHelpers;
    $granTotPro = 0;
?>
<style>
    .tbl-header {
        border: 1px solid black;
    }

    .tbl-header td {
        padding: 3px;
    }
    .tbl-bordered {
        border-collapse: collapse;
        font-size: 12px;
    }

    .tbl-bordered thead th {
        border: 1px solid black;
        padding: 3px;
    }
    .tbl-bordered tbody td {
        border: 1px solid black;
        padding: 3px;
    }
    .number {
        text-align: right
    }
    .center {
        text-align: center
    }
    .border-bottom {
        border-bottom: 1px solid;
    }
</style>
<table width="100%" class="tbl-bordered" style="font-size: 12px;">
    <tr>
        <td>SEP No</td>
        <td>: <?= $sep_no ?></td>
        <td>Age</td>
        <td>: <?= $umur ?></td>
    </tr>
    <tr>
        <td>IP No</td>
        <td>: <?= $no_pendaftaran ?></td>
        <td>Gender</td>
        <td>: <?= $gender ?></td>
    </tr>
    <tr>
        <td>MR No</td>
        <td>: <?= $no_rekam_medik ?></td>
        <td>Ward</td>
        <td>: <?= $ward ?></td>
    </tr>
    <tr>
        <td>Patient Name</td>
        <td>: <?= $nama_pasien ?></td>
        <td>Bed Type</td>
        <td>: <?= $bed_type ?></td>
    </tr>
    <tr>
        <td>Address</td>
        <td>: <?= $address ?></td>
        <td>Bed No</td>
        <td>: <?= $bed_no ?></td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;</td>
        <td style="width: 40%">&nbsp;&nbsp;<?= $address2 ?></td>
        <td>Primary Doctor</td>
        <td>: <?= $primary_doctor ?></td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;</td>
        <td style="width: 40%">&nbsp;&nbsp;<?= $address3 ?></td>
        <td>&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;</td>
        <td style="width: 50%">&nbsp;&nbsp;<?= $address4 ?></td>
        <td>&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;</td>
    </tr>
    <tr>
        <td>Payer</td>
        <td>: <?= $payer ?></td>
        <td>Admission</td>
        <td>: <?= $admission ?></td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;</td>
        <td>Discharge Date</td>
        <td>: <?= $discharge_date ?></td>
    </tr>
</table><br>
<?php if (!empty($data)) : ?>
<table width="100%" class="tbl-bordered">
    <?php foreach ($data as $key => $value) : ?>
    <thead>
        <tr>
            <th colspan="7" style="text-align: left;">
                <?= $key ?>
            </th>
        </tr>
        <tr>
            <th width="5%">No </th>
            <th width="15%">Trans Date</th>
            <th width="35%">Service(s)</th>
            <th width="5%">Qty</th>
            <th width="10%">Price</th>
            <th width="15%">Payer Amount</th>
            <th width="15%">Patient Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            $total = 0;
            foreach ($value as $k => $v) :
                $sub_total = $v['sub_total'];
                $granTotPro += $v['sub_total'];
                $total = $total + $sub_total;
        ?>
                <tr>
                    <td class="center"><?= $no ?></td>
                    <td><?= date('d/m/Y', strtotime($v['tgl_pelayanan'])) ?></td>
                    <td><?= $v['tindakan_obat_nama'] ?></td>
                    <td class="number"><?= $v['qty'] ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($v['tarif_satuan']) ?></td>
                    <td class="number"><?= $dijamin > 0 ? DocoHelpers::formatNumber($v['sub_total']) : 0 ?></td>
                    <td class="number"><?= $dijamin < 1 ? DocoHelpers::formatNumber($v['sub_total']) : 0 ?></td>
                </tr>
        <?php
                $no++;
            endforeach;
        ?>
        <tr>
            <td colspan="5" class="number"><b>Total : </b></td>
            <td class="number"><b><?= $dijamin > 0 ? DocoHelpers::formatNumber($total) : 0 ?></b></td>
            <td class="number"><b><?= $dijamin < 1 ? DocoHelpers::formatNumber($total) : 0 ?></b></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
    endif;
?>
<?php if (!empty($data)) : ?>
<table width="100%" class="tbl-bordered">
    <tbody>
        <tr>
            <td colspan="6" class="number" width="70%"><b>Grand Total : </b></td>
            <td class="number" width="15%">
                <b><?= $dijamin > 0 ? DocoHelpers::formatNumber($granTotPro) : 0 ?></b>
            </td>
            <td class="number" width="15%">
                <b><?= $dijamin < 1 ? DocoHelpers::formatNumber($granTotPro) : 0 ?></b>
            </td>
        </tr>
    </tbody>
</table>
<?php
    endif;
?>
