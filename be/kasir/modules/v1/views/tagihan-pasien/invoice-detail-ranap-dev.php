<?php 
    use Doco\components\DocoHelpers;
    use Doco\components\DocoConstants;
    $patientAmount = $pembayaran['patient_amount'] + $pembayaran['penggunaan_uangmuka'];
    $payerAmount = $pembayaran['total_dijamin'];
    $totalroomrent = 0; 
    $totalconsultation = 0; 
    $totalprocedures = 0; 
    $granTotPro = 0;
    $totaldrugs = 0; 
    $totalRoomPayer = 0;
    $totalRoomPatient = 0;
    $totalConsulPayer = 0;
    $totalConsulPatient = 0;
    $totalDrugsPayer = 0;
    $totalDrugsPatient = 0;
    $granTotProPayer = 0;
    $granTotProPatient = 0;
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
<?php
    if (!empty($roomrent)) :
?>
    <table width="100%" class="tbl-bordered">
            <?php
                foreach ($dataRoomRent as $key => $_detailRent) :
            ?>
                <thead>
                    <tr>
                        <th colspan="8" style="text-align: left;"><?= $key ?></th>
                    </tr>
                    <tr>
                        <th width="5%">No </th>
                        <th width="15%">From Date</th>
                        <th width="15%">To Date</th>
                        <th width="10%">Bed Type</th>
                        <th width="20%">Bed No</th>
                        <th width="5%">Qty</th>
                        <th width="15%">Payer Amount</th>
                        <th width="15%">Patient Amount</th>
                    </tr>
                </thead>
                <tbody>
            <?php
                    $no=1;
                    foreach ($_detailRent as $value) :
                        $totalroomrent += $value['total_amount'];
                        $totalRoomPayer += $value['tarif_dijamin'];
                        $totalRoomPatient += $value['tarif_dibayarkan'];
            ?>
                <tr>
                    <td class="center"><?= $no; ?></td>
                    <td><?= date('d/m/Y', strtotime($value['tanggal_akomodasi'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($value['tanggal_akomodasi'])) ?></td>
                    <td><?= $value['kelas'] ?></td>
                    <td><?= $value['kamar'] ?> - <?= $value['no_bed'] ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value['qty']) ?></td>
                    <td class="number">
                        <?= DocoHelpers::formatNumber($value['tarif_dijamin']) ?></td>
                    <td class="number">
                        <?= DocoHelpers::formatNumber($value['tarif_dibayarkan']) ?></td>
                </tr>
            <?php
                    $no++;
                    endforeach;
            ?>
                    <tr>
                        <td colspan="6" class="number"><b>Total : </b></td>
                        <td class="number"><b><?= DocoHelpers::formatNumber($totalRoomPayer) ?></b></td>
                        <td class="number"><b><?= DocoHelpers::formatNumber($totalRoomPatient) ?></b></td>
                    </tr>
                </tbody>
            <?php
                    $no++;
                endforeach;
            ?>
    </table>
<?php
    endif;
    
    if (!empty($dataConsultation['label'])) :
?>
    <table width="100%" class="tbl-bordered">
        <thead>
            <tr>
                <th colspan="5" style="text-align: left;">
                    <?= $dataConsultation['label'] ?>
                </th>
            </tr>
            <tr>
                <th width="5%">No </th>
                <th width="15%">Trans Date</th>
                <th width="50%">Doctor's Name</th>
                <th width="15%">Payer Amount</th>
                <th width="15%">Patient Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no = 1;
                foreach ($dataConsultation['data'] as $key => $value_cons) :
                    $totalconsultation += $value_cons['tarif'];
                    $totalConsulPayer += $value_cons['tarif_dijamin'];
                    $totalConsulPatient += $value_cons['tarif_dibayarkan'];
            ?>
                    <tr>
                        <td class="center"><?= $no ?></td>
                        <td><?= date('d/m/Y', strtotime($value_cons['tgl_pelayanan'])) ?></td>
                        <td><?= $value_cons['dokter'] ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($value_cons['tarif_dijamin']) ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($value_cons['tarif_dibayarkan']) ?></td>
                    </tr>
            <?php
                    $no++;
                endforeach;
            ?>
            <tr>
                <td colspan="3" class="number"><b>Total : </b></td>
                <td class="number"><b><?= DocoHelpers::formatNumber($totalConsulPayer) ?></b></td>
                <td class="number"><b><?= DocoHelpers::formatNumber($totalConsulPatient) ?></b></td>
            </tr>
        </tbody>
    </table>
<?php
    endif;
    if (!empty($dataProcedures)) :
        foreach ($dataProcedures as $key => $details) :
            $totalprocedures = 0;
            $totalProcedurePayer = 0;
            $totalProcedurePatient = 0;
?>
    <table width="100%" class="tbl-bordered">
        <thead>
            <tr>
                <th colspan="7" style="text-align: left;"><?= $key ?></th>
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
                foreach ($details as $key => $value_proc) :
                    $granTotPro += $value_proc['tarif'];
                    $totalprocedures += $value_proc['tarif'];
                    $totalProcedurePayer += $value_proc['tarif_dijamin'];
                    $totalProcedurePatient += $value_proc['tarif_dibayarkan'];
                    $granTotProPayer += $value_proc['tarif_dijamin'];
                    $granTotProPatient += $value_proc['tarif_dibayarkan'];
            ?>
                    <tr>
                        <td class="center"><?= $no ?></td>
                        <td><?= date('d/m/Y', strtotime($value_proc['tgl_pelayanan'])) ?></td>
                        <td><?= $value_proc['tindakan_obat'] ?></td>
                        <td class="number"><?= $value_proc['qty'] ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($value_proc['harga_satuan']) ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($value_proc['tarif_dijamin']) ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($value_proc['tarif_dibayarkan']) ?></td>
                    </tr>
            <?php
                    $no++;
                endforeach;
            ?>
            <tr>
                <td colspan="5" class="number"><b>Total : </b></td>
                <td class="number"><b><?= DocoHelpers::formatNumber($totalProcedurePayer) ?></b></td>
                <td class="number"><b><?= DocoHelpers::formatNumber($totalProcedurePatient) ?></b></td>
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
                <th colspan="7" style="text-align: left;"><?= $dataDrugs['label'] ?></th>
            </tr>
            <tr>
                <th width="5%">No </th>
                <th width="15%">Trans Date</th>
                <th width="35%">Drugs & Consumables</th>
                <th width="5%">Qty UOM</th>
                <th width="10%">Price</th>
                <th width="15%">Payer Amount</th>
                <th width="15%">Patient Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no = 1;
                foreach ($dataDrugs['data'] as $value_drugs) :
                     $totaldrugs += $value_drugs['tarif'];
                     $totalDrugsPayer +=  $value_drugs['tarif_dijamin'];
                     $totalDrugsPatient +=  $value_drugs['tarif_dibayarkan'];
            ?>
                <tr>
                    <td class="center"><?= $no ?></td>
                    <td><?= date('d/m/Y', strtotime($value_drugs['tgl_pelayanan'])) ?></td>
                    <td><?= $value_drugs['tindakan_obat'] ?></td>
                    <td class="number"><?= $value_drugs['qty'] ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value_drugs['harga_satuan']) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value_drugs['tarif_dijamin']) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value_drugs['tarif_dibayarkan']) ?></td>
                </tr>
            <?php
                    $no++;
                endforeach;
            ?>
            <tr>
                <td colspan="5" class="number"><b>Total : </b></td>
                <td class="number"><b><?= DocoHelpers::formatNumber($totalDrugsPayer) ?></b></td>
                <td class="number"><b><?= DocoHelpers::formatNumber($totalDrugsPatient) ?></b></td>
            </tr>
        </tbody>
    </table>
<?php
    endif;
?>
<?php 
$payerAdminFee = 0;
$patientAdminFee = 0;
$grandTotalPayer = $totalRoomPayer + $totalConsulPayer + $totalDrugsPayer + 
      $granTotProPayer; 

      $grandTotalPatient = $totalRoomPatient + $totalConsulPatient + $totalDrugsPatient + 
      $granTotProPatient; 

      if($grandTotalPayer > 0 || $grandTotalPatient > 0) :
?>

<table width="100%" class="tbl-bordered">
    <tbody>
        <?php if($jenis_invoice == DocoConstants::INV_PASIEN) : $patientAdminFee = $biaya_admin; ?>
        <tr>
            <td colspan="6" class="number" width="70%"><b>Administration Fee : </b></td>
            <td class="number" width="15%">
                <b>
                    <?= DocoHelpers::formatNumber($payerAdminFee) ?></b>
            </td>
            <td class="number" width="15%">
                <b><?= DocoHelpers::formatNumber($patientAdminFee) ?></b>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="6" class="number" width="70%"><b>Grand Total : </b></td>
            <td class="number" width="15%">
                <b>
                    <?= DocoHelpers::formatNumber($totalRoomPayer+$totalConsulPayer+$totalDrugsPayer+$granTotProPayer+$payerAdminFee) ?></b>
            </td>
            <td class="number" width="15%">
                <b><?= DocoHelpers::formatNumber($totalRoomPatient+$totalConsulPatient+$totalDrugsPatient+$granTotProPatient+$patientAdminFee) ?></b>
            </td>
        </tr>
    </tbody>
</table>
<?php
    endif;
?>