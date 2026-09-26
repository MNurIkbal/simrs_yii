<?php 
    use Doco\components\DocoHelpers;
    use app\components\object\GetTarifTindakanObject;
    use app\modules\v1\businessLogic\TagihanHelper;

    $tarifTindakan = GetTarifTindakanObject::getInstance();
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
        font-size: 10px;
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
<table width="100%" class="tbl-bordered">
    <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">TGL</th>
                <th style="width: 50%;">JENIS PELAYANAN</th>
                <th style="width: 15%;">HARGA</th>
                <th style="width: 15%;">#kelas_pembanding#</th>
            </tr>
    </thead>
    <tbody>
        <?php
            $totalTagihan = 0;
            $totalPembanding = 0;
            foreach ($newListTagihan as $key => $items) :
        ?>
            <tr>
                <td colspan="5" style="text-align: left;background-color: #F7DC6F;"><strong><?= $key ?></strong></td>
            </tr>
        <?php
                $no = 1;
                $subTotal = 0;
                foreach ($items as $val) :
                    $tagihan = $val->tindakanpelayanan_id ? $val->tarif_tindakan : $val->hargajual_oa;
                    $totalTagihan += $tagihan;
                    $idTagihan = $val->tindakanpelayanan_id ? $val->daftartindakan_id : $val->obatalkes_id;
                    $qty = $val->tindakanpelayanan_id ? $val->qty_tindakan : $val->qty_oa;
                    $isCyto = !empty($val->tarifcyto_tindakan) ? true : false;
                    $isPenyulit = !empty($val->tarifpenyulit_tindakan) ? true : false;
                    $val->jenis = ($val->instalasi_id == 12 && $val->is_akomodasi) ? $jenisAkomodasiBedah : $val->jenis; 
                    $tarifPembanding = $tarifTindakan->getTarif($val->jenis, $idTagihan, $qty, $isCyto, $isPenyulit, $val->dokterpenanggungjawab_id, $val->racikan_id, $countRacikan);
                    $subTotal += $tarifPembanding;
                    $totalPembanding += $tarifPembanding;
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $val->tgl_tindakan ? date('d-M-Y', strtotime($val->tgl_tindakan)) : null ?></td>
                <td><?= $val->tindakanpelayanan_id ? $val->daftartindakan_nama : $val->obatalkes_nama ?> 
                <?= '(' . ($val->kelaspelayanan_nama ? $val->kelaspelayanan_nama : $infoPasien->kelaspelayanan_nama)  . ')' ?></td>
                <td style="text-align: right"><?= DocoHelpers::formatNumber($tagihan) ?></td>
                <td style="text-align: right"><?= $tarifPembanding ? DocoHelpers::formatNumber($tarifPembanding) : '-' ?></td>
            </tr>
        <?php
                endforeach;
        ?>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
                <td style="text-align: right;background-color: #D5D8DC;">
                        <strong><?= DocoHelpers::formatNumber(isset($subTotalCateg[$key]) ? $subTotalCateg[$key] : 0) ?><strong>
                </td>
                <td style="text-align: right;background-color: #D5D8DC;">
                        <strong><?= DocoHelpers::formatNumber($subTotal) ?><strong>
                </td>
            </tr>
        <?php
            endforeach;
            $pendaftaranId = $infoPasien->pendaftaran_id;
            $penjaminId = $infoPasien->penjamin_id;
            $admisiId = $infoPasien->pasienadmisi_id;
            $kelasId = $payload->kelaspelayanan_id;
            $kelasPasienId = $infoPasien->kelaspelayanan_id;
            $biayaAdmin = 0;
            $biayaAdminPembanding = 0;
            if(!empty($admisiId)) {
                $biayaAdmin = TagihanHelper::getBiayaAdmin($pendaftaranId, $penjaminId, $kelasPasienId, $totalTagihan, $admisiId);
                $biayaAdminPembanding = TagihanHelper::getBiayaAdmin($pendaftaranId, $penjaminId, $kelasId, $totalPembanding, $admisiId);
            }
        ?>
        <tr>
            <td colspan="3" style="text-align: right"><b>Sub Total Tagihan</b></td>
            <td style="text-align: right"><strong><?= DocoHelpers::formatNumber($totalTagihan) ?></strong></td>
            <td style="text-align: right"><strong><?= DocoHelpers::formatNumber($totalPembanding) ?></strong></td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: right"><b>Biaya Administrasi</b></td>
            <td style="text-align: right"><strong><?= DocoHelpers::formatNumber($biayaAdmin) ?></strong></td>
            <td style="text-align: right"><strong><?= DocoHelpers::formatNumber($biayaAdminPembanding) ?></strong></td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: right"><b>Total Tagihan</b></td>
            <td style="text-align: right"><strong><?= DocoHelpers::formatNumber($totalTagihan + $biayaAdmin) ?></strong></td>
            <td style="text-align: right"><strong><?= DocoHelpers::formatNumber($totalPembanding + $biayaAdminPembanding) ?></strong></td>
        </tr>
    </tbody>
</table>
