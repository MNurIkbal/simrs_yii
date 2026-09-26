<?php 
    use yii\helpers\ArrayHelper;
    use Doco\components\DocoHelpers;
    $totalrent = 0;
    $total = 0; 
    $granTotalPayer = 0;
    $granTotalPatient = 0;
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
    .left {
        text-align: left
    }
    .border-bottom {
        border-bottom: 1px solid;
    }
</style>

<?php if (!empty($dataRoomRent)) : ?>
    <table width="100%" class="tbl-bordered">
        <thead>
            <tr>
                <th width="5%">No </th>
                <th width="10%">From Date</th>
                <th width="10%">To Date</th>
                <th width="10%">Bed Type</th>
                <th width="20%">Bed No</th>
                <th width="5%">Qty</th>
                <th width="10%">Price</th>
                <th width="10%">Disc</th>
                <th width="15%">Payer Amount</th>
            </tr>
            <?php foreach ($dataRoomRent as $key => $ruangan) : ?>
            <tr>
                <th colspan="9" style="text-align: left;background-color: #D5D8DC;"><strong><?= $key ?></strong></th>
            </tr>
            <tbody>
                <?php foreach ($ruangan as $keyKelompok => $kelompok) : ?>
                <tr>
                    <td colspan="9" style="text-align: left;background-color: #F7DC6F;"><strong><?= $keyKelompok ?></strong></td>
                </tr>
                <?php 
                    $no = 1;
                    $totalroomrent = 0; 
                    $totalPayer = $totalPatient = 0;
                    foreach ($kelompok as $value) :
                        $totalrent += $value['total_amount'];
                        $totalroomrent += $value['total_amount'];
                        $nominPayer = $value['total_amount'] - $value['tarif_dibayarkan'];
                        $nominPatient = $value['total_amount'] - $value['tarif_dijamin'];
                        $totalPayer += $nominPayer;
                        $totalPatient += $nominPatient;
                ?>
                <tr>
                    <td class="center"><?= $no; ?></td>
                    <td><?= date('d/m/Y', strtotime($value['min'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($value['max'])) ?></td>
                    <td><?= $value['kelas'] ?></td>
                    <td><?= $value['kamar'] ?> - <?= $value['no_bed'] ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value['qty']/100) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value['harga_satuan']) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value['tarif_diskon']) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($nominPayer) ?></td>
                </tr>
                <?php 
                        $no++; 
                    endforeach;
                    $granTotalPayer += $totalPayer;
                    $granTotalPatient += $totalPatient;
                ?>
                <tr>
                    <td colspan="8" class="number"><b>Total </b></td>
                    <td class="number">
                        <b><?=  DocoHelpers::formatNumber($totalPayer) ?></b>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <?php endforeach; ?>
        </thead>
    </table>
    <br>
<?php endif; ?>

<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <th width="5%"><strong>No</strong> </th>
            <th width="12%"><strong>Trans Date</strong></th>
            <th width="15%"><strong>Service(s)</strong></th>
            <th width="5%"><strong>Qty</strong></th>
            <th width="10%"><strong>Price</strong></th>
            <th width="15%"><strong>Disc</strong></th>
            <th width="15%"><strong>Payer Amount</strong></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            if($biaya_admin > 0)  :
                $biayaAdmin = ArrayHelper::getValue($biaya_admin, 'biaya_admin', 0);
                $penjamin_id = ArrayHelper::getValue($biaya_admin, 'penjamin_id', 0);
                $nominPayer = ArrayHelper::getValue($biaya_admin, 'nominPayer', 0);
                $nominPatient = ArrayHelper::getValue($biaya_admin, 'nominPatient', 0);
                $admAsuransi = ArrayHelper::getValue($additionalPembayaran, 'adm_asuransi', []);
                $discAdm = ArrayHelper::getValue($admAsuransi, 'nominal_diskon', 0);
                if($biayaAdmin > 0) {
                    $granTotalPayer += $nominPayer;
                    $granTotalPatient += $nominPatient;
                }
                
                if (!empty($nominPayer)) :
            ?>
            <tr>
                <td colspan="7" style="text-align: left;background-color: #F7DC6F;">
                    <strong>Biaya Administrasi</strong>
                </td>
            </tr>
            <tr>
                <td class="center">1</td>
                <td><?= date('d/m/Y', strtotime($tgl_pembayaran)) ?></td>
                <td><?= $tindakan_admin ?></td>
                <td class="number">1</td>
                <td><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($discAdm) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($nominPayer) ?></td>
            </tr>
        <?php
                endif;
            endif; 
        ?>
        <?php foreach ($dataTindakan as $ruangan => $kelompok) : ?>
        <tr>
            <td colspan="7" style="text-align: left;background-color: #D5D8DC;">
                <strong><?= $ruangan ?></strong>
            </td>
        </tr>
        <?php foreach ($kelompok as $key => $value) : $totalAll = 0; ?>
        <tr>
            <td colspan="7" style="text-align: left;background-color: #F7DC6F;">
                <strong><?= $key ?></strong>
            </td>
        </tr>
        <?php 
            $no = 1; 
            $totalPayer = $totalPatient = 0;
            foreach ($value as $val) : 
                $qty = ArrayHelper::getValue($val, 'qty', 1);
                $jenisRacikan = ArrayHelper::getValue($val, 'jenis_racikan');
                $tindakanObat = ArrayHelper::getValue($val, 'tindakan_obat');
                $tindakan = empty($jenisRacikan) ? $tindakanObat : $tindakanObat. " (".$jenisRacikan.")";
                $tglPelayanan = ArrayHelper::getValue($val, 'tgl_pelayanan');
                $dokter = ArrayHelper::getValue($val, 'dokter');
                $tarifDijamin = ArrayHelper::getValue($val, 'tarif_dijamin', 0);
                $tarifDibayarkan = ArrayHelper::getValue($val, 'tarif_dibayarkan', 0);
                $tarifDiskon = ArrayHelper::getValue($val, 'tarif_diskon', 0);
                $hargaSatuan = ArrayHelper::getValue($val, 'harga_satuan', 0);
                $tarifCito = ArrayHelper::getValue($val, 'tarifcyto_tindakan', 0);
                $tarifPenyulit = ArrayHelper::getValue($val, 'tarifpenyulit_tindakan', 0);
                $cito = ArrayHelper::getValue($val, 'cyto_tindakan', false);
                $cito = ($cito) ? 'Yes' : 'No';

                $nominPayer = $dijamin > 0 ? $tarifDijamin : 0;
                $nominPatient = $tarifDibayarkan;
                $tarif = $qty * $hargaSatuan;
                $totalPayer += $nominPayer;
                $totalPatient += $nominPatient;
                $grandTotal = ($tarif + $tarifCito - $tarifDiskon);
                $totalAll += $grandTotal;
        ?>
        <tr>
            <td class="center"><?= $no ?></td>
            <td><?= date('d/m/Y', strtotime($tglPelayanan)) ?></td>
            <td><?= $tindakan ?></td>
            <td class="number"><?= $qty ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($hargaSatuan) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($tarifDiskon) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($tarifDijamin) ?></td>
        </tr>
        <?php 
            $no++; 
        endforeach;
        $granTotalPayer += $totalPayer;
        $total += $totalAll; ?>
        <tr>
            <td colspan="6" class="number"><b>Total Tagihan <?= $key ?> </b></td>
            <td class="number"><b><?= DocoHelpers::formatNumber($totalPayer) ?></b></td>
        </tr>
        <?php endforeach; ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="6" class="number"><b>Total Amount : </b></td>
            <td class="number">
                <b><?= DocoHelpers::formatNumber($granTotalPayer) ?></b>
            </td>
        </tr>
    </tbody>
</table>

