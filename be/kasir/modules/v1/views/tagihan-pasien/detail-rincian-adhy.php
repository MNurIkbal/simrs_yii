<?php 
use Doco\components\DocoHelpers;

$kelompokNol = $countKelompokTidakNol = $tempPelayanan = $tempRuangan = $tempKelompok = [];


foreach($data as $pelayanan => $valPelayanan) {
    $indexPelayanan = strtolower($pelayanan);
    foreach ($valPelayanan as $ruangan => $valRuangan) {
        $tempPelayanan[$indexPelayanan] = !empty($tempPelayanan[$indexPelayanan]) ? $tempPelayanan[$indexPelayanan] : 0;
        $indexRuangan = strtolower(str_replace(" ", "-", $pelayanan."-".$ruangan));
        foreach ($valRuangan as $kelompok => $valKelompok) {
            $tempRuangan[$indexRuangan] = !empty($tempRuangan[$indexRuangan]) ? $tempRuangan[$indexRuangan] : 0;
            $indexKelompok = strtolower(strtolower(str_replace(" ", "-", $pelayanan."-".$ruangan."-".$kelompok)));
            foreach ($valKelompok as $key => $value ) {
                $tempKelompok[$indexKelompok] = !empty($tempKelompok[$indexKelompok]) ? $tempKelompok[$indexKelompok] : 0;
                if($value['tarif_satuan'] == 0 || $value['qty'] == 0) {
                    $kelompokNol[] = $value;
                    continue;
                } else {
                    $tempPelayanan[$indexPelayanan]++;
                    $tempRuangan[$indexRuangan]++;
                    $tempKelompok[$indexKelompok]++;
                }
            }
        }
    }
}
?>

<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
    }
    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
        font-size: 12px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
        font-size: 12px;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
</style>
<table class="tbl-bordered" style="width:100%;">
    <thead>
        <tr>
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Tanggal Tindakan') ?></th>
            <th><?= Yii::t('app', 'Nama Tindakan') ?></th>
            <th><?= Yii::t('app', 'Dokter') ?></th>
            <th><?= Yii::t('app', 'Qty') ?></th>
            <th><?= Yii::t('app', 'Tarif Satuan') ?></th>
            <th><?= Yii::t('app', 'Tarif Cito') ?></th>
            <th><?= Yii::t('app', 'Jumlah Tarif') ?></th>
        </tr>
    </thead>
    <tbody>
            <?php
            $no = 1;
            foreach ($data as $pelayanan => $valPelayanan) :  
            $indexPelayanan = strtolower($pelayanan);
            if($tempPelayanan[$indexPelayanan] < 1) {
                continue;
            }
            ?>
            <tr>
                <th colspan="8" style="text-align: left;background-color: #C2E3F9;"><?= strtoupper($pelayanan) ?></th>
            </tr>
            <?php foreach ($valPelayanan as $ruangan => $valRuangan) : 
            $indexRuangan = strtolower(str_replace(" ", "-", $pelayanan."-".$ruangan));
            if($tempRuangan[$indexRuangan] < 1) {
                continue;
            }
            ?>
            <tr>
                <th style="text-align: left;background-color: #D5D8DC;"></th>
                <th colspan="7" style="text-align: left;background-color: #D5D8DC;"><?= strtoupper($ruangan) ?></th>
            </tr>
            <?php foreach ($valRuangan as $kelompok => $valKelompok) : 
            $indexKelompok = strtolower(strtolower(str_replace(" ", "-", $pelayanan."-".$ruangan."-".$kelompok)));
            if($tempKelompok[$indexKelompok] < 1) {
                continue;
            }
            ?>
            <tr>
                <th style="text-align: left;background-color: #F7DC6F;"></th>
                <th colspan="7" style="text-align: left;background-color: #F7DC6F;"><?= strtoupper($kelompok) ?></th>
            </tr>
            <?php 
                $subTotal = 0;
                foreach ($valKelompok as $key => $value ) :
                    if($value['tarif_satuan'] == 0 || $value['qty'] == 0) {
                        continue;
                    }
                    $dokter = isset($value['dokterpenanggungjawab_nama']) ? $value['dokterpenanggungjawab_nama'] : '';
                    $subTotal += !empty($value['sub_total']) ? $value['sub_total'] : 0;
            ?>
            <tr>
                <td width="5%" class="text-center"><?= $no++ ?></td>
                <td width="15%"><?= date('d M Y', strtotime($value['tgl_pelayanan'])).'<br/>'.date('H:i:s', strtotime($value['tgl_pelayanan'])) ?></td>
                <td width="15%"><?= $value['tindakan_obat_nama'] ?>   
                <?= in_array($value['tindakan_obat_id'], $tindakan) 
                ? "/ ".$value['kelaspelayanan_nama']." - ".$value['dokterpenanggungjawab_nama'] : ''?>
                <?= ($value['is_akomodasi'] == true) ? " / ".$value['ruangan_pelayanan']." / ".$value['kelaspelayanan_nama'] : '' ?>
                </td>
                <td width="15%"><?= $dokter ?></td>
                <td width="5%" class="text-right"><?= $value['qty'] ?></td>
                <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($value['tarif_cyto']) ?></td>
                <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
            </tr>
            <?php if ($kelompok == 'Others' && !empty($value['detail_tindakan'])): ?>
                <?php foreach ($value['detail_tindakan'] as $detail_tindakan): ?>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="3"><?= $detail_tindakan['daftartindakan_nama'] ?></td>
                    <td class="text-right"><?= DocoHelpers::formatNumber($detail_tindakan['harga_tariftindakan']) ?></td>
                    <td colspan="2" class="text-right">&nbsp;</td>
                </tr>
                <?php endforeach ?>
            <?php endif; ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="7" class="number text-right"><b>TOTAL TAGIHAN <?= strtoupper($ruangan) ?>: </b></td>
                <td class="number text-right"><b><?= DocoHelpers::formatNumber($subTotal) ?></b></td>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
            <?php endforeach; ?>
            
        <?php 
            // $no = $no + 1;
            $tindakan_akomodasi = isset($data_akomodasi['tindakan_akomodasi']) ? $data_akomodasi['tindakan_akomodasi'] : [];
            if(!empty($tindakan_akomodasi)) : ?>
             <tr>
                <th style="text-align: left;background-color: #F7DC6F;"></th>
                <th colspan="7" style="text-align: left;background-color: #F7DC6F;">AKOMODASI SEMENTARA</th>
            </tr>
            <?php
                $subTotal = 0;
                foreach ($tindakan_akomodasi as $value ) : 
                $subTotal += $value['tarif_tindakan'];
            ?>
                <tr>
                    <td width="5%" class="text-center"><?= $no++ ?></td>
                    <td width="15%"><?= date('d M Y', strtotime($value['tgl_tindakan'])).'<br/>'.date('H:i:s', strtotime($value['tgl_tindakan'])) ?></td>
                    <td width="15%"><?= $value['daftartindakan_nama']." / ".$value['ruangan_nama']." / ".$value['kelaspelayanan_nama'] ?></td>
                    <td></td>
                    <td width="5%" class="text-right"><?= $value['qty_tindakan'] ?></td>
                    <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                    <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                    <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($value['tarif_tindakan']) ?></td>
                </tr>
            <?php endforeach; ?>
                <tr>
                    <td colspan="7" class="number text-right"><b>TOTAL AKOMODASI SEMENTARA : </b></td>
                    <td class="number text-right"><b><?= DocoHelpers::formatNumber($subTotal) ?></b></td>
                </tr>
            <?php endif; ?>
            <?php 
            if (!empty($data_admin)) :
            ?>
                <tr>
                    <td width="5%" class="text-center"><?= $no ?></td>
                    <td> </td>
                    <td><?= $data_admin ?></td>
                    <td> </td>
                    <td width="5%" class="text-right"></td>
                    <td width="15%" class="text-right"></td>
                    <td width="15%" class="text-right"></td>
                    <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
                </tr>
            <?php
        endif; ?>
            <?php if(!empty($kelompokNol)): ?>
                <tr><td colspan="8" style="text-align: left;background-color: #C2E3F9; text-transform: uppercase;"><strong>Tidak Ditagihkan</strong></td></tr>
                    <?php
                    $nomor = 1;
                    $subTotal = 0;
                    foreach ($kelompokNol as $key => $value ) :
                        $dokter = isset($value['dokterpenanggungjawab_nama']) ? $value['dokterpenanggungjawab_nama'] : '';
                        $subTotal += !empty($value['sub_total']) ? $value['sub_total'] : 0;
                        ?>
                        <tr>
                            <td width="5%" class="text-center"><?= $nomor++ ?></td>
                            <td width="15%"><?= date('d M Y', strtotime($value['tgl_pelayanan'])).'<br/>'.date('H:i:s', strtotime($value['tgl_pelayanan'])) ?></td>
                            <td width="15%"><?= $value['tindakan_obat_nama'] ?>
                                <?= in_array($value['tindakan_obat_id'], $tindakan)
                                    ? "/ ".$value['kelaspelayanan_nama']." - ".$value['dokterpenanggungjawab_nama'] : ''?>
                                <?= ($value['is_akomodasi'] == true) ? " / ".$value['ruangan_pelayanan']." / ".$value['kelaspelayanan_nama'] : '' ?>
                            </td>
                            <td width="15%"><?= $dokter ?></td>
                            <td width="5%" class="text-right"><?= $value['qty'] ?></td>
                            <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                            <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($value['tarif_cyto']) ?></td>
                            <td width="15%" class="text-right"><?= DocoHelpers::formatNumber(!empty($value['sub_total']) ? $value['sub_total'] : 0) ?></td>
                        </tr>
                    <?php
                    endforeach;
            endif;
            ?>
            <tr><td colspan="8" style="background-color: #4c4c4c;">&nbsp;</td></tr>
    </tbody>
    <?php
        $tagihanBelumBayar = !empty ($tagihan_belumbayar) ? $tagihan_belumbayar  : 0;
        $subsidiAsuransi = !empty ($nominal_dijamin) ? (int) $nominal_dijamin  : 0;
        $uangMuka = !empty ($sisa_uangmuka) ? (int) $sisa_uangmuka  : 0;
        $akomodasiSementara = !empty($data_akomodasi['total_akomodasi']) ? (int)$data_akomodasi['total_akomodasi'] : 0;
        $totalSisaTagihan = ($tagihanBelumBayar + $biayaAdmin) - $subsidiAsuransi - $uangMuka + $akomodasiSementara;
    ?>
    <tfoot>
        <tr>
            <td colspan="7" class="text-right"><strong>Total Tagihan :</strong></td>
            <td class="text-right"><b><?= DocoHelpers::formatNumber($tagihanBelumBayar) ?> </b></td>
        </tr>
        <tr>
            <td colspan="7" class="text-right"><strong>Biaya Administrasi :</strong></td>
            <td class="text-right"><b><?= DocoHelpers::formatNumber($biayaAdmin) ?> </b></td>
        </tr>
        <tr>
            <td colspan="7" class="text-right"><strong>Subsidi Asuransi :</strong></td>
            <td class="text-right"><b><?= DocoHelpers::formatNumber($subsidiAsuransi) ?> </b></td>
        </tr>
        <tr>
            <td colspan="7" class="text-right"><strong>Uang Masuk :</strong></td>
            <td class="text-right"><b><?= DocoHelpers::formatNumber($uangMuka) ?> </b></td>
        </tr>
        <tr>
            <td colspan="7" class="text-right"><strong>Biaya Akomodasi Sementara :</strong></td>
            <td class="text-right"><b><?= DocoHelpers::formatNumber((isset($data_akomodasi['total_akomodasi']) && $data_akomodasi['total_akomodasi'] != null)?$data_akomodasi['total_akomodasi'] : 0) ?> </b></td>
        </tr>
        <tr>
            <td colspan="7" class="text-right"><strong>Total Sisa Tagihan :</strong></td>
            <td class="text-right"><b><?= DocoHelpers::formatNumber($totalSisaTagihan) ?> </b></td>
        </tr>
    </tfoot>
</table>
