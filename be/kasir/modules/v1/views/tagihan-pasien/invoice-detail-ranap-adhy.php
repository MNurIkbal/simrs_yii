<?php 
    use Doco\components\DocoHelpers;
    $totalrent = 0;
    $total = 0; 
    $granTotalPayer = $granTotalPatient = 0;
    $str_jenis_pembayaran = '';
    $metode_bayar = '';
    if($pembayaran->total_nontunai >0 ){
        $additional_pembayaran = json_decode($pembayaran->additional_data, true);
        $jenis_pembayaran = !empty($additional_pembayaran['pembayaran_jenis_pembayaran']) ? $additional_pembayaran['pembayaran_jenis_pembayaran'] : [];
        if(!empty($jenis_pembayaran)){
            foreach($jenis_pembayaran as $val){
                $str_operarotmb = !empty($metode_bayar) ? ', ' : '';
                $metode_bayar = !empty($val['metode_bayar']) ? $val['metode_bayar'] : '';
                $str_jenis_pembayaran .= $str_operarotmb . $metode_bayar;
            }
        }
    }
    $str_tunai = $pembayaran->total_tunai > 0 ? "Tunai" : "";
    $str_nontunai = $pembayaran->total_nontunai > 0 ? "Non Tunai (" .$str_jenis_pembayaran.")" : "";
    $str_penjamin = $pembayaran->total_dijamin > 0 ? "Penjamin" : "";
    $str_operatortnt = ($pembayaran->total_tunai > 0 &&  $pembayaran->total_nontunai > 0) ? "/" : "";
    $str_tunai_nontunai = $str_tunai . $str_operatortnt . $str_nontunai;
    $str_tunai_nontunai_penjamin = (!empty($str_tunai_nontunai) && !empty($str_penjamin)) ? '/'.$str_penjamin : (!empty($str_penjamin) ? '-'.$str_penjamin : $str_penjamin);
    $str_tunai_nontunai =  !empty($str_tunai_nontunai) ? ' - '.$str_tunai_nontunai . $str_tunai_nontunai_penjamin : $str_tunai_nontunai_penjamin;
    $jumlahTunainontunai = $pembayaran->total_tunai + $pembayaran->total_nontunai ;
    $total_dijamin = $pembayaran->total_dijamin;
    $total_ditagihkan = $pembayaran->total_ditagihkan;
    $patient_amount = $pembayaran->patient_amount;
    $penggunaan_uangmuka = $pembayaran->penggunaan_uangmuka;
    $total_kekurangan = $pembayaran->total_ditagihkan - ($total_dijamin + $jumlahTunainontunai);
    $discAdm = 0;
?>
<style>
    .tbl-header {
        border: 0px solid black;
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
        border: 0px solid black;
        padding: 3px;
    }
    .table-detail-paket tbody td {
        border: 0px solid black;
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
        border-bottom: 0px solid;
    }
    hr { 
        border : 0px solid black;
        color : black;
    }
    .detail-paket{
        padding-right:0px ;
    }
</style>

<?php if ($pasienadmisi_id) : ?>
    <p style="text-align:center"><span style="font-size:18px"><strong>Perincian Biaya Rawat Inap</strong></span></p>
    <table border="0" cellpadding="1" cellspacing="1" style="width:100%">
        <tbody>
            <tr>
                <td><span style="font-size:12px">No Rekam Medik</span></td>
                <td><span style="font-size:12px">:&nbsp;<?=!empty($modelHeader->no_rekam_medik) ? $modelHeader->no_rekam_medik : '-';?></span></td>
                <td><span style="font-size:12px">Jenis Pasien</span></td>
                <td><span style="font-size:12px">:&nbsp;<?=!empty($modelHeader->carabayar_nama) ? $modelHeader->carabayar_nama : '-';?></span></td>
            </tr>
            <tr>
                <td><span style="font-size:12px">No. Register</span></td>
                <td><span style="font-size:12px">:&nbsp;<?=!empty($modelHeader->no_pendaftaran) ? $modelHeader->no_pendaftaran : '-';?></span></td>
                <td><span style="font-size:12px">Penanggung</span></td>
                <td><span style="font-size:12px">:&nbsp;<?=!empty($modelHeader->penjamin_nama) ? $modelHeader->penjamin_nama : '-';?></span></td>
            </tr>
            <tr>
                <td><span style="font-size:12px">Nama Pasien</span></td>
                <td><span style="font-size:12px">:&nbsp;<?=!empty($modelHeader->nama_pasien) ? $modelHeader->nama_pasien : '-';?></span></td>
                <td><span style="font-size:12px">Tindakan</span></td>
                <td><span style="font-size:12px">:&nbsp;</span></td>
            </tr>
            <tr>
                <td><span style="font-size:12px">Tanggal perawatan</span></td>
                <td><span style="font-size:12px">:&nbsp;<?=$tgl_admisi . ' s/d ' . $tgl_stopakomodasi?></span></td>
                <td><span style="font-size:12px"></td>
                <td><span style="font-size:12px"></span></td>
            </tr>
        </tbody>
    </table>

    <table width="100%" class="tbl-bordered">
        <thead>
            <tr>
                <td v-align="bottom" colspan="8"> <hr> </td>
            </tr>
            <tr class = "">
                <th style=" text-align: left; " width="55%" colspan="4"><strong>Keterangan</strong> </th>
                <th width="5%"><strong>Qty</strong></th>
                <th width="13%"><strong>Tarif</strong></th>
                <th width="12%"><strong>Diskon</strong></th>
                <th style=" text-align: right;" width="20%"><strong>Total</strong></th>
            </tr>
            <tr>
                <td v-align="top" colspan="8"> <hr> </td>
            </tr>
        </thead>
            <tbody>
                <?php 
                $no =1;
                if(!empty($dataRoomRent)) : 
                    ?> 
                    <tr>
                        <td colspan="8" style="text-align: left;">
                            <strong>Kamar Perawatan <hr></strong>
                        </td>
                    </tr>
                    <?php
                    $total_room = 0;
                    foreach ($dataRoomRent as $key => $ruangan) : ?>
                        <?php 
                        foreach ($ruangan as $keyKelompok => $kelompok) :
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
                                $tarif_dibayarkan = !empty($value['tarif_dibayarkan']) ? $value['tarif_dibayarkan'] : 0;
                                $tarif_dijamin = !empty($value['tarif_dijamin']) ? $value['tarif_dijamin'] : 0;
                                $subtotal = $tarif_dijamin + $tarif_dibayarkan;
                                $total_room += $subtotal;
                                ?>
                                <tr>
                                    <td width = "1%"></td>
                                    <td width = "5%"><?=$no?>.</td>
                                    <td colspan="2"><?= $key . ' - ' .$value['kamar'] ?></td>
                                    <td class="number"><?= DocoHelpers::formatNumber($value['qty']/100) ?></td>
                                    <td class="number"><?= DocoHelpers::formatNumber($value['harga_satuan']) ?></td>
                                    <td class="number"><?= DocoHelpers::formatNumber($value['tarif_diskon']) ?></td>
                                    <td class="number"><?= DocoHelpers::formatNumber($subtotal) ?></td>
                                </tr>
                                <?php
                                $no++;
                            endforeach;
                            $granTotalPayer += $totalPayer;
                            $granTotalPatient += $totalPatient;
                            ?>
                        <?php 
                        endforeach; ?>
                    <?php 
                    endforeach;?>
                    <tr>
                        <td class="number">&nbsp;&nbsp;</td>
                        <td class="number">&nbsp;&nbsp;</td>
                        <td class="number">&nbsp;&nbsp;</td>
                        <td colspan ="4" class="number"  style="text-align: left;"><b>Total : Kamar Perawatan</b></td>
                        <td class="number">
                            <hr><b><?= DocoHelpers::formatNumber($total_room) ?></b>
                        </td>
                    </tr>
                    <tr></tr>
                <?php 
                endif; ?>
                    <tr>
                        <td colspan="8" style="text-align: left;">
                            <strong>Biaya Pemeriksaan/Tindakan/Obat<hr></strong>
                        </td>
                    </tr>
                    
                    <?php 
                    if($biaya_admin > 0 && $jenis_invoice == 1)  : 
                        $addJson = json_decode($pembayaran['additional_data'], true);
                        $addAdm = isset($addJson['adm_asuransi']) ? $addJson['adm_asuransi'] : [];
                        $dijaminAdm = isset($addAdm['dijamin']) ? $addAdm['dijamin'] : 0;
                        $discAdm = isset($addAdm['nominal_diskon']) ? $addAdm['nominal_diskon'] : 0;
                        $payarAdm = isset($addAdm['harusbayar']) ? $addAdm['harusbayar'] : 0;
                        $nominPayer = $dijamin > 0 ? $biaya_admin - $payarAdm : 0;
                        $nominPatient = $dijamin > 0 ? $payarAdm : $biaya_admin;
                        $granTotalPayer += $nominPayer;
                        $granTotalPatient += $nominPatient;
                        $subtotal = ($nominPayer + $nominPatient) - $discAdm;
                        ?>
                        <tr>
                            <td width = "1%"></td>
                            <td colspan="7" style="text-align: left;">
                                <strong>Biaya Administrasi</strong>
                            </td>
                        </tr>
                        <tr>
                            <td width = "1%"></td>
                            <td width = "5%">1.</td>
                            <td colspan="2">Biaya Administrasi</td>
                            <td class="number">1</td>
                            <td class="number"><?=DocoHelpers::formatNumber($biaya_admin)?></td>
                            <td class="number"><?=DocoHelpers::formatNumber($discAdm)?></td>
                            <td class="number"><?= DocoHelpers::formatNumber($subtotal) ?></td>
                        </tr>
                    <?php 
                    endif; ?>

                    <?php 
                    foreach ($dataTindakan as $ruangan => $kelompok) : $total_ruangan = 0; ?>
                        <tr>
                            <td width = "1%"></td>
                            <td colspan="7" style="text-align: left;">
                                <strong><p style="margin-left:60px;"><?= $ruangan ?> </p> </strong>
                            </td>
                        </tr>
                        <?php 
                        $no = 1;
                        foreach ($kelompok as $key => $value) : $totalAll = 0; ?>
                            <?php 
                                $totalPayer = $totalPatient = 0;
                                $total_diskon = 0;
                                $total_tarif = 0;
                                foreach ($value as $val) : 
                                    $qty = $val['qty'];
                                    $nominPayer = ($dijamin > 0 && $jenis_invoice != 2) ? $val['tarif_dijamin'] : 0;
                                    $nominPatient = ($jenis_invoice == 3) ?  0 : $val['tarif_dibayarkan'];
                                    if($jenis_invoice == 3) {
                                        $nominPayer = $val['tarif_dijamin'];
                                        $nominPatient = $val['tarif_dibayarkan'];
                                    }
                                    
                                    $totalPayer += $nominPayer;
                                    $totalPatient += $nominPatient;

                                    $harga_satuan = $val['harga_satuan'];
                                    $tarif_diskon = $val['tarif_diskon'];
                                    $tarif = $val['tarif'];
                                    $tarifcyto_tindakan = $val['tarifcyto_tindakan'];
                                    $cyto_tindakan = ($val['cyto_tindakan']) ? 'Yes' : 'No';
                                    $tindakan = $val['tindakan_obat'];
                                    $tgl_pelayanan = $val['tgl_pelayanan'];
                                    $grandTotal = (($qty*$harga_satuan) - ($tarif_diskon + $tarifcyto_tindakan));
                                    $totalAll += $grandTotal;
                                    $tarif_dibayarkan = !empty($val['tarif_dibayarkan']) ? $val['tarif_dibayarkan'] : 0;
                                    $tarif_dijamin = !empty($val['tarif_dijamin']) ? $val['tarif_dijamin'] : 0;
                                    $subtotal = $tarif_dijamin + $tarif_dibayarkan;
                                    $total_ruangan += $subtotal; 
                                    $total_diskon += $tarif_diskon;
                                    $total_tarif += $tarif;

                                    if($ruangan != "FARMASI") : 
                                        ?>
                                        <tr>
                                            <td width = "1%"></td>
                                            <td width = "5%"><?=$no?>.</td>
                                            <td colspan="2"><?= $tindakan ?></td>
                                            <td class="number"><?= $qty ?></td>
                                            <td class="number"><?= DocoHelpers::formatNumber($harga_satuan) ?></td>
                                            <td class="number"><?= DocoHelpers::formatNumber($tarif_diskon) ?></td>
                                            <td class="number"><?= DocoHelpers::formatNumber($subtotal) ?></td>
                                        </tr>
                                    <?php 
                                    endif;
                                    $no++; 
                                endforeach;
                            $granTotalPayer += $totalPayer;
                            $granTotalPatient += $totalPatient; 
                            $total += $totalAll;
                            
                            if($ruangan == "FARMASI") :
                                $total_farmasi = $totalPayer + $totalPatient;
                                ?>
                                <tr>
                                    <td width = "1%"></td>
                                    <td width = "5%">1.</td>
                                    <td colspan="2">Obat/Alat Kesehatan</td>
                                    <td class="number">&nbsp;</td>
                                    <td class="number">&nbsp;<?= DocoHelpers::formatNumber($total_tarif) ?></td>
                                    <td class="number">&nbsp;<?= DocoHelpers::formatNumber($total_diskon) ?></td>
                                    <td class="number"><?= DocoHelpers::formatNumber($total_tarif-$total_diskon) ?></td>
                                </tr>
                                <?php
                            endif; 
                        endforeach;
                        ?>
                            <tr>
                                <td class="number">&nbsp;&nbsp;</td>
                                <td class="number">&nbsp;&nbsp;</td>
                                <td class="number">&nbsp;&nbsp;</td>
                                <td colspan="4" class="number" style="text-align: left;"><b>Total : <?= $ruangan ?></b></td>
                                <td style="text-align: right;" class="number">
                                    <b> Rp. <?= DocoHelpers::formatNumber($total_ruangan);?></b>
                                </td>
                            </tr>
                        <?php
                    endforeach; 
                        $grandTotalAll = ($granTotalPayer + $granTotalPatient) - $discAdm;
                    ?>
        
                    <tr>
                        <td colspan="8" style="text-align: left;">
                            <strong>Pembayaran</strong> <hr>
                        </td>
                    </tr>
                    <?php foreach ($list_cara_bayar as $key => $val) : 
                        $metode_bayar = !empty($val['metode_bayar']) ? $val['metode_bayar'] : '-';
                        $total_dibayar = !empty($val['total_dibayar']) ? $val['total_dibayar'] : '-';
                        ?>
                        <tr>
                            <td width = "1%"></td>
                            <td width = "5%"><?=$key+1?>.</td>
                            <td colspan="3">Perlunasan : <?=$pembayaran->no_pembayaran . ' - ' . $metode_bayar?> </td>
                            <td colspan="2" style="text-align: right;"> <?=date('d-m-y ', strtotime($pembayaran->tgl_pembayaran))?> </td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($total_dibayar);?></td>
                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="8" style="text-align: left;">
                            <hr>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="number">&nbsp;&nbsp;</td>
                        <td class="number">&nbsp;&nbsp;</td>
                        <td class="number">&nbsp;&nbsp;</td>
                        <td colspan="4" class="number" style="text-align: left;"><b>Total Biaya: Rp. </b></td>
                        <td style="text-align: right;" class="number">
                            <b> <?= DocoHelpers::formatNumber($grandTotalAll);?></b>
                        </td>
                    </tr>
                    
                    <tr>
                        <td width = "1%"></td>
                        <td width = "5%"></td>
                        <td colspan ="5" style="text-align: left;">
                            <table border="0" cellpadding="0" cellspacing="0" width = "50%" >
                                <tr>
                                    <td width = "5%"></td>
                                    <td style="text-align: right;">Perusahaan/Asuransi: Rp.</td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($total_dijamin);?></td>
                                </tr>
                                <tr>
                                    <td width = "5%"></td>
                                    <td style="text-align: right;">Subsidi RS: Rp.</td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber(0);?></td>
                                </tr>
                                <tr>
                                    <td width = "5%"></td>
                                    <td style="text-align: right;">Pribadi: Rp.</td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($patient_amount);?></td>
                                </tr>
                                <tr>
                                    <td width = "5%"></td>
                                    <td style="text-align: right;"></td>
                                    <td style="text-align: right;"><hr></td>
                                </tr>
                                <tr>
                                    <td width = "5%"></td>
                                    <td style="text-align: right;"> <b> Total kekurangan : Rp</b></td>
                                    <td style="text-align: right;">0</td>
                                </tr>
                            </table>
                        </td>
                        <td></td>
                       
                    </tr>
            </tbody>
    </table>
    <br>
    <table border="0" cellpadding="0" cellspacing="0" style="width:100%">
        <tbody>
            <tr>
                <td style="width:40%"><span style="font-size:12px">Penanggung jawab pasien</span></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="height:65px; vertical-align:bottom; width:30%"><br />
                <br />
                <br />
                <span style="font-size:12px"><hr></span></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><span style="font-size:12px">Tgl. cetak </spab></td>
                <td><span style="font-size:12px"><?=date('d/m/y H:i:s')?> </span></td>
            </tr>
        </tbody>
    </table>


<?php else: ?>
    
    <p style="text-align:center"><span style="font-size:18px"><strong>Perincian Biaya</strong></span></p>
    <table border="0" cellpadding="1" cellspacing="1" style="width:100%">
        <tbody>
            <tr>
                <td><span style="font-size:12px">No Rekam Medik</span></td>
                <td><span style="font-size:12px">:&nbsp;<?=!empty($modelHeader->no_rekam_medik) ? $modelHeader->no_rekam_medik : '-';?></span></td>
                <td><span style="font-size:12px">No. Register</span></td>
                <td><span style="font-size:12px">:&nbsp;<?=!empty($modelHeader->no_pendaftaran) ? $modelHeader->no_pendaftaran : '-';?></span></td>
            </tr>
            <tr>
                <td><span style="font-size:12px">Nama Pasien</span></td>
                <td><span style="font-size:12px">:&nbsp;<?=!empty($modelHeader->nama_pasien) ? $modelHeader->nama_pasien : '-';?></span></td>
                <td><span style="font-size:12px">Tgl. Masuk/Keluar</span></td>
                <td><span style="font-size:12px">:&nbsp;<?=!empty($tglMasuk) ? DocoHelpers::convDateTime($tglMasuk, false, false) : '-';?></span></td>
            </tr>
        </tbody>
    </table>
    <table width="100%" class="tbl-bordered" border="0">
        <thead>
            <tr>
                <td colspan="8"> <hr> </td>
            </tr>
            <tr class = "border-bottom">
                <th style=" text-align: left; " width="50%" colspan="4"><strong>Keterangan</strong> </th>
                <th width="5%"><strong>Qty</strong></th>
                <th style=" text-align: right;" width="12%"><strong>Tarif</strong></th>
                <th style=" text-align: right;" width="13%"><strong>Diskon</strong></th>
                <th style=" text-align: right;" width="20%"><strong>Subtotal</strong></th>
            </tr>
            <tr>
                <td colspan="8"> <hr> </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="8" style="text-align: left;">
                    <strong>A. Transaksi</strong>
                </td>
            </tr>
            <?php 
                if($biaya_admin > 0 && $jenis_invoice == 1)  : 
                    $addJson = json_decode($pembayaran['additional_data'], true);
                    $addAdm = isset($addJson['adm_asuransi']) ? $addJson['adm_asuransi'] : [];
                    $dijaminAdm = isset($addAdm['dijamin']) ? $addAdm['dijamin'] : 0;
                    $discAdm = isset($addAdm['nominal_diskon']) ? $addAdm['nominal_diskon'] : 0;
                    $payarAdm = isset($addAdm['harusbayar']) ? $addAdm['harusbayar'] : 0;
                    $nominPayer = $dijamin > 0 ? $biaya_admin - $payarAdm : 0;
                    $nominPatient = $dijamin > 0 ? $payarAdm : $biaya_admin;
                    $granTotalPayer += $nominPayer;
                    $granTotalPatient += $nominPatient;
                    $subtotal = $nominPayer + $nominPatient;
                    ?>
                    <tr>
                        <td width = "1%"></td>
                        <td colspan="7" style="text-align: left;">
                            <strong>Biaya Administrasi</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width = "1%"></td>
                        <td width = "1%"></td>
                        <td colspan ="2">Biaya Administrasi</td>
                        <td class="number">1</td>
                        <td class="number"><?=DocoHelpers::formatNumber($biaya_admin)?></td>
                        <td class="number"><?=DocoHelpers::formatNumber($discAdm)?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($payarAdm) ?></td>
                    </tr>
            <?php endif; ?>

            <?php foreach ($dataTindakan as $ruangan => $kelompok) : $total_ruangan = 0; ?>
                <tr>
                    <td width = "1%"></td>
                    <td colspan="7" style="text-align: left;">
                        <strong><p style="margin-left:60px;"><?= $ruangan ?> </p> </strong>
                    </td>
                </tr>
                <?php foreach ($kelompok as $key => $value) : $totalAll = 0; ?>
                    <?php 
                        $no = 1; 
                        $totalPayer = $totalPatient = 0;
                        $total_diskon = 0;
                        $total_tarif = 0;

                        foreach ($value as $val) : 
                            $qty = $val['qty'];
                            $nominPayer = ($dijamin > 0 && $jenis_invoice != 2) ? $val['tarif_dijamin'] : 0;
                            $nominPatient = ($jenis_invoice == 3) ?  0 : $val['tarif_dibayarkan'];
                            if($jenis_invoice == 3) {
                                $nominPayer = $val['tarif_dijamin'];
                                $nominPatient = $val['tarif_dibayarkan'];
                            }
                            
                            $totalPayer += $nominPayer;
                            $totalPatient += $nominPatient;

                            $harga_satuan = $val['harga_satuan'];
                            $tarif_diskon = $val['tarif_diskon'];
                            $tarif = $val['tarif'];
                            $tarifcyto_tindakan = $val['tarifcyto_tindakan'];
                            $cyto_tindakan = ($val['cyto_tindakan']) ? 'Yes' : 'No';
                            $tindakan = $val['tindakan_obat'];
                            $tgl_pelayanan = $val['tgl_pelayanan'];
                            $grandTotal = (($qty*$harga_satuan) - ($tarif_diskon + $tarifcyto_tindakan));
                            $totalAll += $grandTotal;
                            $tarif_dibayarkan = !empty($val['tarif_dibayarkan']) ? $val['tarif_dibayarkan'] : 0;
                            $tarif_dijamin = !empty($val['tarif_dijamin']) ? $val['tarif_dijamin'] : 0;
                            $subtotal = $tarif_dijamin + $tarif_dibayarkan;
                            $total_diskon += $tarif_diskon;
                            $total_tarif += $tarif;

                            if($ruangan != "FARMASI") : 
                                ?>
                                <tr>
                                    <td width = "1%"></td>
                                    <td width = "1%"></td>
                                    <td colspan="2">
                                        <?php if( is_null($val['kelompoktindakan_id'])): ?>
                                            <table border="0" style="width: 100%;" class="table-detail-paket">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2" ><?= $tindakan ?></td>
                                                    </tr>
                                                    <?php 
                                                        if( is_null($val['kelompoktindakan_id'])):
                                                            foreach ($val['detail_tindakan'] as $x) : ?> 
                                                            <tr>
                                                                <td ></td>
                                                                <td > <?= $x['daftartindakan_nama'];  ?>  </td>
                                                            </tr>
                                                    <?php endforeach;
                                                            endif; 
                                                    ?> 
                                                    <tr><td colspan="2" class="detail-paket">&nbsp;</td></tr>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <?= $tindakan ?>
                                        <?php endif; ?>                                        
                                    </td>
                                    <td class="number" valign="top">
                                        <?= $qty ?>
                                    </td>
                                    <td class="number" valign="top">
                                        <?= DocoHelpers::formatNumber($harga_satuan) ?>
                                    </td>
                                    <td class="number" valign="top">
                                        <?php if( is_null($val['kelompoktindakan_id'])): ?>
                                            <table border="0" style="width: 100%;" class="table-detail-paket">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2"><?= DocoHelpers::formatNumber($tarif_diskon)  ?></td>
                                                    </tr>
                                                    <?php 
                                                        if( is_null($val['kelompoktindakan_id'])):
                                                            foreach ($val['detail_tindakan'] as $x) : ?> 
                                                            <tr>
                                                                <td >&nbsp;</td>
                                                                <td >&nbsp; </td>
                                                            </tr>
                                                    <?php endforeach;
                                                            endif; 
                                                    ?> 
                                                    <tr><td ></td><td>Total</td></tr>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                           <?= DocoHelpers::formatNumber($tarif_diskon) ?>
                                        <?php endif; ?> 
                                    </td>
                                    <td class="number" valign="bottom">
                                        <?php if( is_null($val['kelompoktindakan_id'])): ?>
                                            <table border="0" style="width: 100%;" class="table-detail-paket">
                                                <tbody>
                                                    <tr><td >&nbsp;</td></tr>
                                                    <?php 
                                                        if( is_null($val['kelompoktindakan_id'])):
                                                            foreach ($val['detail_tindakan'] as $x) : ?> 
                                                            <tr>
                                                                <td >&nbsp;</td>
                                                                <td > <?= DocoHelpers::formatNumber($x['harga_tariftindakan']);  ?>  </td>
                                                            </tr>
                                                    <?php endforeach;
                                                            endif; 
                                                    ?> 
                                                    <tr><td ></td><td><b><?= DocoHelpers::formatNumber($subtotal) ?></b></td></tr>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <b><?= DocoHelpers::formatNumber($subtotal) ?></b>
                                        <?php endif; ?>                                
                                    </td>
                                </tr>
                            <?php 
                            endif;
                            $no++; 
                        endforeach;

                        $granTotalPayer += $totalPayer;
                        $granTotalPatient += $totalPatient; 
                        $total += $totalAll; 
                        $total_ruangan += $totalAll; 
                    
                    if($ruangan == "FARMASI") :
                        $total_farmasi = $totalPayer + $totalPatient;
                        ?>
                        <tr>
                            <td width = "1%"></td>
                            <td width = "1%"></td>
                            <td colspan="2">Obat/Alat Kesehatan</td>
                            <td class="number">&nbsp;</td>
                            <td class="number">&nbsp;<?= DocoHelpers::formatNumber($total_tarif) ?></td>
                            <td class="number">&nbsp;<?= DocoHelpers::formatNumber($total_diskon) ?></td>
                            <td class="number"><?= DocoHelpers::formatNumber($total_tarif-$total_diskon) ?></td>
                        </tr>
                        <?php
                    endif; 
                endforeach;
            endforeach; 
                $grandTotalAll = $granTotalPayer + $granTotalPatient;
            ?>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="6" class="number"><b>Total Transaksi : </b></td>
                <td class="number"></td>
                <td class="number">
                    <hr> <b>Rp. <?= DocoHelpers::formatNumber($grandTotalAll) ?></b>
                </td>
            </tr>
            
            <tr>
                <td colspan="8" style="text-align: left;">
                    <strong>B. Pembayaran</strong>
                </td>
            </tr>
            
            <?php foreach ($list_cara_bayar as $key => $val) : 
                $metode_bayar = !empty($val['metode_bayar']) ? $val['metode_bayar'] : '-';
                $total_dibayar = !empty($val['total_dibayar']) ? $val['total_dibayar'] : '-';
                ?>
                <tr>
                    <td width = "3%"><?=$key+1?>. </td>
                    <td colspan="4"> <?=$pembayaran->no_pembayaran . ' - ' . $metode_bayar?> </td>
                    <td colspan="2" style="text-align: center;"> <?=date('d-m-y ', strtotime($pembayaran->tgl_pembayaran))?> </td>
                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($total_dibayar);?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="6" class="number"><b>Total Pembayaran : </b></td>
                <td class="number">&nbsp;&nbsp;</td>
                <td style="text-align: right;" class="number">
                    <b>Rp. <?= DocoHelpers::formatNumber($grandTotalAll);?></b>
                </td>
            </tr>
            <tr>
                <td colspan="8"> <hr> </td>
            </tr>
            <tr>
                <td colspan="6" class="number"><b>Kurang Bayar : </b></td>
                <td class="number">&nbsp;&nbsp;</td>
                <td style="text-align: right;" class="number">
                    <b>Rp. <?= DocoHelpers::formatNumber($pembayaran['total_sisatagihan']);?></b>
                </td>
            </tr>
            
        </tbody>
    </table>
    <table border="0" cellpadding="0" cellspacing="0" style="width:100%">
        <tbody>
            <tr>
                <td style="width:40%"><span style="font-size:12px"><?=$lokasi?><br />
                Petugas</span></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="height:65px; vertical-align:bottom; width:30%"><br />
                <br />
                <br />
                <span style="font-size:12px"><?=$printed_by?></span></td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>