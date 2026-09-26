<?php 
    use Doco\components\DocoHelpers;
    $totalrent = 0;
    $total = 0; 
    $granTotalPayer = $granTotalPatient = $diskonPayer = $diskonPasien = 0;
    $is_pembulatankeatas = $konfigSystem['is_pembulatankeatas'];
    $satuan_pembulatan = $konfigSystem['satuanpembulatan'];
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
                <th width="25%">Bed No</th>
                <th width="5%">Qty</th>
                <th width="15%">Price</th>
                <th width="15%">Payer Amount</th>
                <th width="15%">Patient Amount</th>
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
                        $nominPayer = ($dijamin > 0 && $jenis_invoice != 2) ? $value['tarif_dijamin'] : 0;
                        $nominPatient = ($jenis_invoice == 3) ?  0 : $value['tarif_dibayarkan'];
                        $tarifDiskon = isset($value['tarif_diskon']) ? $value['tarif_diskon'] : 0;
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
                    <td class="number"><?= DocoHelpers::formatNumber($nominPayer) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($nominPatient) ?></td>
                </tr>
                <?php 
                        $no++; 
                    endforeach;
                    $granTotalPayer += $totalPayer;
                    $granTotalPatient += $totalPatient;
                ?>
                <tr>
                    <td colspan="7" class="number"><b>Total </b></td>
                    <td class="number">
                        <b><?=  DocoHelpers::formatNumber($totalPayer) ?></b>
                    </td>
                    <td class="number">
                        <b><?= DocoHelpers::formatNumber($totalPatient) ?></b>
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
            <th width="18%"><strong>Service(s)</strong></th>
            <th width="19%"><strong>Doctor</strong></th>
            <th width="5%"><strong>Qty</strong></th>
            <th width="10%"><strong>Price</strong></th>
            <th width="10%"><strong>Cyto</strong></th>
            <th width="10%"><strong>Penyulit</strong></th>
            <th width="12%"><strong>Payer Amount</strong></th>
            <th width="12%"><strong>Patient Amount</strong></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $adm_diskon_pasien = 0;
            $no = 1; 
            $total = 0; 
            $subTotalPayer = $subTotalPatient = $data_dijamin = $round_penjamin = 0;
            $tgl_invoice_gabung = !empty($headerGabung['tgl_invoicegabung']) ? date('d/m/Y', strtotime($headerGabung['tgl_invoicegabung'])) : '-';
            if ($nominalAdm > 0) :
        ?>
            <tr>
                <td colspan="9" style="text-align: left;background-color: #F7DC6F;">
                    <strong>Biaya Administrasi</strong>
                </td>
            </tr>
            <tr>
                <td class="center">1</td>
                <td><?=$tgl_invoice_gabung; ?></td>
                <td>Biaya Administrasi</td>
                <td>&nbsp;</td>
                <td class="number">1</td>
                <td class="number"><?= DocoHelpers::formatNumber($nominalAdm) ?></td>
                <td class="number">0</td>
                <td class="number">0</td>
                <td class="number"><?= DocoHelpers::formatNumber($admPayer) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($admPatient) ?></td>
            </tr>
        <?php endif; ?>
        
        <?php foreach ($dataTindakan as $ruangan => $kelompok) : ?>
        <tr>
            <td colspan="10" style="text-align: left;background-color: #D5D8DC;">
                <strong><?= $ruangan ?></strong>
            </td>
        </tr>
        <?php foreach ($kelompok as $key => $value) : $totalAll = 0; ?>
        <tr>
            <td colspan="10" style="text-align: left;background-color: #F7DC6F;">
                <strong><?= $key ?></strong>
            </td>
        </tr>
        <?php 
            $no = 1; 
            $totalPayer = $totalPatient = 0;
            foreach ($value as $val) : 
                $qty = $val['qty'];
                $harga_satuan = $val['harga_satuan'];
                $tarif = $qty * $harga_satuan;
                $sub_total = isset($val['tarif']) ? $val['tarif'] : 0;
                $nominPayer = isset($val['tarif_dijamin']) ? $val['tarif_dijamin'] : 0;
                $nominPatient = isset($val['tarif_dibayarkan']) ? $val['tarif_dibayarkan'] : 0;
                $totalPayer += $nominPayer > 0 ? $nominPayer : 0;
                $totalPatient += $nominPatient > 0 ? $nominPatient : 0;
                $tarif_diskon = isset($val['tarif_diskon']) ? $val['tarif_diskon'] : 0;
                $tarifcyto_tindakan = $val['tarifcyto_tindakan'];
                $cyto_tindakan = ($val['cyto_tindakan']) ? 'Yes' : 'No';
                $tindakan = $val['tindakan_obat'];
                $tgl_pelayanan = $val['tgl_pelayanan'];
                $grandTotal = (($qty*$harga_satuan) - ($tarif_diskon + $tarifcyto_tindakan));
                $totalAll += $grandTotal;
                $dokter = isset($val['dokter']) ? $val['dokter'] : '';
                $diskonPasien += (!empty($nominPatient)) ? $tarif_diskon : 0;
                $diskonPayer += (!empty($nominPayer) && empty($nominPatient)) ? $tarif_diskon : 0;
        ?>
        <tr>
            <td class="center"><?= $no ?></td>
            <td><?= date('d/m/Y', strtotime($tgl_pelayanan)) ?></td>
            <td><?= $tindakan ?></td>
            <td><?= $dokter ?></td>
            <td class="number"><?= $qty ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($harga_satuan) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($tarifcyto_tindakan) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($val['tarifpenyulit_tindakan']) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($nominPayer) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($nominPatient) ?></td>
        </tr>
        <?php 
            $no++; 
        endforeach;
        $granTotalPayer += $totalPayer ;
        $granTotalPatient += $totalPatient; 
        $total += $totalAll; ?>
        <tr>
            <td colspan="8" class="number"><b>Total Tagihan <?= $key ?> </b></td>
            <td class="number"><b><?= DocoHelpers::formatNumber($totalPayer) ?></b></td>
            <td class="number"><b><?= DocoHelpers::formatNumber($totalPatient) ?></b></td>
        </tr>
        <?php endforeach; ?>
        <?php endforeach; 
            $granTotalPayer += $admPayer;
            $granTotalPatient += $admPatient;
        ?>
        <tr>
            <td colspan="8" class="number"><b>Total Amount : </b></td>
            <td class="number">
                <b><?= DocoHelpers::formatNumber($granTotalPayer + $diskonPayer) ?></b>
            </td>
            <td class="number">
                <b><?= DocoHelpers::formatNumber($granTotalPatient + $diskonPasien) ?></b>
            </td>
        </tr>
        <tr>
            <td colspan="8" class="number"><b>Discount : </b></td>
            <td class="number">
                <b><?= DocoHelpers::formatNumber($diskonPayer) ?></b>
            </td>
            <td class="number">
                <b>
                    <?= DocoHelpers::formatNumber($diskonPasien)  ?>
                </b>
            </td>
        </tr>
        <tr>
            <td colspan="8" class="number"><b>DP : </b></td>
            <td class="number">
                <b>0</b>
            </td>
            <td class="number">
                <b><?= DocoHelpers::formatNumber($penggunaan_uangmuka) ?></b>
            </td>
        </tr>
        <tr>
            <td colspan="8" class="number"><b>DEBT : </b></td>
            <td class="number">
                <b>0</b>
            </td>
            <td class="number">
                <b><?= DocoHelpers::formatNumber($total_sisatagihan) ?></b>
            </td>
        </tr>
        <tr>
            <td colspan="8" class="number"><b>Rounded Bill Amount : </b></td>
            <td class="number">
                <b><?= DocoHelpers::formatNumber($roundeBillAmountPayer) ?></b>
            </td>
            <td class="number">
                <b><?= DocoHelpers::formatNumber($roundeBillAmountPatient) ?></b>
            </td>
        </tr>
        <tr>
            <td colspan="8" class="number"><b>Amount : </b></td>
            <td class="number">
                <b><?= DocoHelpers::formatNumber($granTotalPayer + $roundeBillAmountPayer) ?></b>
            </td>
            <td class="number">
                <?php $pasienAmount = ($granTotalPatient - $penggunaan_uangmuka)  + $roundeBillAmountPatient; ?>
                <b><?= DocoHelpers::formatNumber($pasienAmount >= 0 ? $pasienAmount : 0 ) ?></b>
            </td>
        </tr>
    </tbody>
</table>

