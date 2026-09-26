<?php 
    use Doco\components\DocoHelpers;
    $totalroomrent = 0; 
    $totalconsultation = 0; 
    $totalprocedures = 0; 
    $granTotPro = 0;
    $totaldrugs = 0; 
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
<table width="100%" class="tbl-header" style="font-size: 12px;">
    <tr>
        <td width="15%">SEP No</td>
        <td width="35%">: <?= $sep_no ?></td>
        <td width="15%">Age</td>
        <td width="35%">: <?= $umur ?></td>
    </tr>
    <tr>
        <td>Registrasi No</td>
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
        <td>Discharge Date</td>
        <td>: <?= $discharge_date ?></td>
        <td>Admission</td>
        <td>: <?= $admission ?></td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;</td>
        <td>Primary Doctor</td>
        <td>: <?= $primary_doctor ?></td>
    </tr>
</table><br>
<?php
    if (!empty($dataConsultation['label'])) :
?>
    <table width="100%" class="tbl-bordered">
        <thead>
            <tr>
                <th colspan="4" style="text-align: left;background-color: #F7DC6F;">
                    <?= $dataConsultation['label'] ?>
                </th>
            </tr>
            <tr>
                <th width="5%">No </th>
                <th width="15%">Trans Date</th>
                <th width="50%">Doctor's Name</th>
                <th width="25%">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no = 1;
                foreach ($dataConsultation['data'] as $key => $value_cons) :
                    $totalconsultation += $value_cons['tarif_tindakan'];
            ?>
                    <tr>
                        <td class="center"><?= $no ?></td>
                        <td><?= date('d/m/Y', strtotime($value_cons['tgl_pelayanan'])) ?></td>
                        <td><?= $value_cons['dokter'] ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($value_cons['tarif_tindakan']) ?></td>
                    </tr>
            <?php
                    $no++;
                endforeach;
            ?>
            <tr>
                <td colspan="3" class="number"><b>Total : </b></td>
                <td class="number" style="background-color: #D5D8DC;"><b><?= DocoHelpers::formatNumber($totalconsultation) ?></b></td>
            </tr>
        </tbody>
    </table>
<?php
    endif;
    if (!empty($dataProcedures)) :
        foreach ($dataProcedures as $key => $details) :
            $totalprocedures = 0;
?>
    <table width="100%" class="tbl-bordered">
        <thead>
            <tr>
                <th colspan="6" style="text-align: left;background-color: #F7DC6F;"><?= $key ?></th>
            </tr>
            <tr>
                <th width="5%">No </th>
                <th width="15%">Trans Date</th>
                <th width="35%">Service(s)</th>
                <th width="5%">Qty</th>
                <th width="15%">Price</th>
                <th width="25%">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no = 1;
                foreach ($details as $key => $value_proc) :
                    $granTotPro += $value_proc['tarif_tindakan'];
                    $totalprocedures += $value_proc['tarif_tindakan'];
            ?>
                    <tr>
                        <td class="center"><?= $no ?></td>
                        <td><?= date('d/m/Y', strtotime($value_proc['tgl_pelayanan'])) ?></td>
                        <td><?= $value_proc['tindakan_obat'] ?></td>
                        <td class="number"><?= $value_proc['qty'] ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($value_proc['tarif_satuan']) ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($value_proc['tarif_tindakan'])?></td>
                    </tr>
            <?php
                    $no++;
                endforeach;
            ?>
            <tr>
                <td colspan="5" class="number"><b>Total : </b></td>
                <td class="number" style="background-color: #D5D8DC;"><b><?= DocoHelpers::formatNumber($totalprocedures) ?></b></td>
            </tr>
        </tbody>
    </table>
<?php
        endforeach;
    endif;
    if (!empty($dataDrugs['label'])) :
?>
    <table width="100%" class="tbl-bordered">
        <thead>
            <tr>
                <th colspan="6" style="text-align: left;background-color: #F7DC6F;"><?= $dataDrugs['label'] ?></th>
            </tr>
            <tr>
                <th width="5%">No </th>
                <th width="15%">Trans Date</th>
                <th width="35%">Drugs & Consumables</th>
                <th width="5%">Qty UOM</th>
                <th width="15%">Price</th>
                <th width="25%">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no = 1;
                foreach ($dataDrugs['data'] as $value_drugs) :
                     $totaldrugs += $value_drugs['tarif_tindakan'];
            ?>
                <tr>
                    <td class="center"><?= $no ?></td>
                    <td><?= date('d/m/Y', strtotime($value_drugs['tgl_pelayanan'])) ?></td>
                    <td><?= $value_drugs['tindakan_obat'] ?></td>
                    <td class="number"><?= $value_drugs['qty'] ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value_drugs['tarif_satuan']) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value_drugs['tarif_tindakan']) ?></td>
                </tr>
            <?php
                    $no++;
                endforeach;
            ?>
            <tr>
                <td colspan="5" class="number"><b>Total : </b></td>
                <td class="number" style="background-color: #D5D8DC;"><b><?= DocoHelpers::formatNumber($totaldrugs) ?></b></td>
            </tr>
        </tbody>
    </table>
<?php
    endif;
?>
<table width="100%" class="tbl-bordered">
    <tbody>
        <tr>
            <td colspan="5" class="number" width="75%"><b>Grand Total : </b></td>
            <td class="number"  width="25%">
                <b><?= DocoHelpers::formatNumber($totalroomrent+$totalconsultation+$granTotPro+$totaldrugs) ?></b>
            </td>
        </tr>
    </tbody>
</table>
