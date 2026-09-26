<?php 
use Doco\components\DocoHelpers;

?>

<style>
    .number {
        text-align: right
    }

    table thead tr th {
        font:arial !important;
        font-size:12px !important;
    }

    table tbody tr td {
        font:arial !important;
        font-size:12px !important;
    }

    table tfoot tr td {
        font:arial !important;
        font-size:12px !important;
    }

</style>

<table border="1" style="width:100%; border-collapse: collapse;">
<thead>
    <tr>
        <th colspan="2">Riwayat Pembayaran</th>
    </tr>
    <tr>
        <td style="font-size:14px;">Total Tagihan</td>
        <td style="text-align: right;font-size:14px;"><?= $listHeader['total_tagihan'] ?></td>
    </tr>
    <tr>
        <td style="font-size:14px;">Subsidi Asuransi</td>
        <td style="text-align: right;font-size:14px;"><?= $listHeader['total_asuransi'] ?></td>
    </tr>
    <tr>
        <td style="font-size:14px;">Uang Masuk</td>
        <td style="text-align: right;font-size:14px;"><?= $listHeader['uang_masuk'] ?></td>
    </tr>
    <tr>
        <td style="font-size:14px;">Biaya Akomodasi Sementara</td>
        <td style="text-align: right;font-size:14px;"><?= $listHeader['total_akomodasi'] ?></td>
    </tr>
    <tr>
        <td style="font-size:14px;"><b>Total Sisa Tagihan</b></td>
        <td style="text-align: right;font-size:14px;"><b><?= $listHeader['sisa_tagihan'] ?></b></td>
    </tr>
</thead>
</table>
<br>
<?php 
foreach ($detail as $key => $value) :
    switch ($key) {
    case 'tindakan':
        foreach ($value as $k => $v) :
        ?>
            <table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th colspan="7">Tindakan - <?= $v['title'] ?></th>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal Tindakan</th>
                        <th>Nama Tindakan</th>
                        <th>Qty</th>
                        <th>Tarif Satuan</th>
                        <th>Tarif Cyto</th>
                        <th>Jumlah Tarif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $subTotal = 0;
                    foreach ($v['data'] as $items) :
                        foreach ($items as $k => $v): 
                            if($v['kelompoktindakan_id'] != $kelompok_visite) :
                            $tindakan = "";
                            if($v['tindakan_obat_nama'] !="" || $v['tipepaket_nama'] !==""):
                            $tindakan = $v['tindakan_obat_nama'];
                                if($tindakan == ""):
                                    $tindakan = $v['tipepaket_nama'];
                                endIf;

                            $total = $v['jumlah_tarif'];
                            $subTotal += $total;
                        ?>
                        <tr>
                            <td style="text-align: center;"><?= $no ?></td>
                            <td style="text-align: center;"><?php 
                            $res_tgl_pelayanan = ( !isset($v['tgl_pelayanan'])) ? '-' : date('d M Y H:i:s', strtotime($v['tgl_pelayanan'])); 
                            echo $res_tgl_pelayanan;
                            ?></td>
                            <td><?= $tindakan ?></td>
                            <td class='number'>
                                <?= DocoHelpers::formatNumber($v['qty']) ?>
                            </td>
                            <td class='number'>
                                <?= DocoHelpers::rupiahDisplay($v['tarif_satuan']) ?>
                            </td>
                            <td class='number'>
                                <?= DocoHelpers::rupiahDisplay($v['tarif_cyto']) ?>
                            </td>
                            <td class='number'><?= DocoHelpers::rupiahDisplay($total) ?></td>
                        </tr>
                        <?php
                        $no++;
                        endIf;
                        endIf;
                    endforeach;
                    ?>
                    <?php if($no < 2): ?>
                        <tr>
                            <td colspan="7" class="text-center"><i>Data tidak ditemukan</i></td>
                        </tr>
                    <?php endIf;
                    endforeach
                     ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6"><strong>Total</strong></td>
                        <td class='number'><strong><?= DocoHelpers::rupiahDisplay($subTotal) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <br>
        <?php
        endforeach;
        break;
        ?>
        <?php
        case 'pemeriksaan':
            foreach ($value as $k => $v) : 
        ?>
            <table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <?php
                        $titles = $v['title'];
                        ?>
                        <th colspan="7">Pemeriksaan - <?= $titles ?></th>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal Pemeriksaan</th>
                        <th>Nama Pemeriksaan</th>
                        <th>Qty</th>
                        <th>Tarif Satuan</th>
                        <th>Tarif Cyto</th>
                        <th>Jumlah Tarif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $subTotal = 0;
                    foreach ($v['data'] as $items) :
                        foreach ($items as $k => $v): 
                            $tindakan = "";
                            if($v['kelompoktindakan_id'] != $kelompok_visite) :
                            if($v['tindakan_obat_nama'] !="" || $v['tipepaket_nama'] !=="") :
                                $tindakan = $v['tindakan_obat_nama'];
                                if($tindakan == "") {
                                    $tindakan = $v['tipepaket_nama'];
                                }
                                    
                                $total = $v['jumlah_tarif'];
                                $subTotal += $total;
                                ?>
                                <tr>
                                    <td style="text-align: center;"><?= $no ?></td>
                                    <td style="text-align: center;"><?php 
                                    $res_tgl_pelayanan = ( !isset($v['tgl_pelayanan'])) ? '-' : date('d M Y H:i:s', strtotime($v['tgl_pelayanan'])); 
                                    echo $res_tgl_pelayanan;
                                    ?></td>
                                    <td><?= $tindakan ?></td>
                                    <td class='number'>
                                        <?= DocoHelpers::formatNumber($v['qty']) ?>
                                    </td>
                                    <td class='number'>
                                        <?= DocoHelpers::rupiahDisplay($v['tarif_satuan']) ?>
                                    </td>
                                    <td class='number'>
                                        <?= DocoHelpers::rupiahDisplay($v['tarif_cyto']) ?>
                                    </td>
                                    <td class='number'><?= DocoHelpers::rupiahDisplay($total) ?></td>
                                </tr>
                            <?php
                            $no++;
                        endIf;
                        endIf;
                    endforeach;
                    ?>
                    <?php if($no < 2): ?>
                        <tr>
                            <td colspan="7" class="text-center"><i>Data tidak ditemukan</i></td>
                        </tr>
                    <?php endIf;
                    endforeach
                     ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6"><strong>Total</strong></td>
                        <td class='number'><strong><?= DocoHelpers::rupiahDisplay($subTotal) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <br>
        <?php
        endforeach;
        break;
        ?>
        <?php
        case 'visite':
            foreach ($value as $k => $v) : 
        ?>
            <table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <?php
                        $titles = $v['title'];
                        ?>
                        <th colspan="7">Visite Dokter</th>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal Pemeriksaan</th>
                        <th>Nama Pemeriksaan</th>
                        <th>Qty</th>
                        <th>Tarif Satuan</th>
                        <th>Tarif Cyto</th>
                        <th>Jumlah Tarif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $subTotal = 0;
                    foreach ($v['data'] as $items) :
                            $tindakan = "";
                            if($items['kelompoktindakan_id'] == $kelompok_visite) :
                            if($items['tindakan_obat_nama'] !="" || $items['tipepaket_nama'] !=="") :
                                $tindakan = $items['tindakan_obat_nama'];
                                if($tindakan == "") {
                                    $tindakan = $items['tipepaket_nama'];
                                }
                                    
                                $total = $items['jumlah_tarif'];
                                $subTotal += $total;
                                ?>
                                <tr>
                                    <td style="text-align: center;"><?= $no ?></td>
                                    <td style="text-align: center;"><?php 
                                    $res_tgl_pelayanan = ( !isset($items['tgl_pelayanan'])) ? '-' : date('d M Y H:i:s', strtotime($items['tgl_pelayanan'])); 
                                    echo $res_tgl_pelayanan;
                                    ?></td>
                                    <td><?= $tindakan ?></td>
                                    <td class='number'>
                                        <?= DocoHelpers::formatNumber($items['qty']) ?>
                                    </td>
                                    <td class='number'>
                                        <?= DocoHelpers::rupiahDisplay($items['tarif_satuan']) ?>
                                    </td>
                                    <td class='number'>
                                        <?= DocoHelpers::rupiahDisplay($items['tarif_cyto']) ?>
                                    </td>
                                    <td class='number'><?= DocoHelpers::rupiahDisplay($total) ?></td>
                                </tr>
                            <?php
                            $no++;
                        endIf;
                        endIf;
                    ?>
                    <?php if($no < 2): ?>
                        <tr>
                            <td colspan="7" class="text-center"><i>Data tidak ditemukan</i></td>
                        </tr>
                    <?php endIf;
                    endforeach
                     ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6"><strong>Total</strong></td>
                        <td class='number'><strong><?= DocoHelpers::rupiahDisplay($subTotal) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <br>
        <?php
        endforeach;
        break;
        ?>
        <?php
        case 'penunjang':
            foreach ($value as $k => $v) :
        ?>
            <table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <?php
                        $titles = $v['title'];
                        ?>
                        <th colspan="7">Penunjang - <?= $titles ?></th>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal Penunjang</th>
                        <th>Nama Penunjang</th>
                        <th>Qty</th>
                        <th>Tarif Satuan</th>
                        <th>Tarif Cyto</th>
                        <th>Jumlah Tarif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $subTotal = 0;
                    foreach ($v['data'] as $items) :
                        foreach ($items as $k => $v): 
                            // $total = ($items['qty'] * $items['tarif_satuan']) + $items['tarif_cyto'];
                            $tindakan = "";
                            if($v['tindakan_obat_nama'] !="" || $v['tipepaket_nama'] !==""):
                            $tindakan = $v['tindakan_obat_nama'];
                                if($tindakan == ""):
                                    $tindakan = $v['tipepaket_nama'];
                                endIf;

                            $total = $v['jumlah_tarif'];
                            $subTotal += $total;
                        ?>
                        <tr>
                            <td style="text-align: center;"><?= $no ?></td>
                            <td style="text-align: center;"><?php 
                            $res_tgl_pelayanan = ( !isset($v['tgl_pelayanan'])) ? '-' : date('d M Y H:i:s', strtotime($v['tgl_pelayanan'])); 
                            echo $res_tgl_pelayanan;
                            ?></td>
                            <td><?= $tindakan ?></td>
                            <td class='number'>
                                <?= DocoHelpers::formatNumber($v['qty']) ?>
                            </td>
                            <td class='number'>
                                <?= DocoHelpers::rupiahDisplay($v['tarif_satuan']) ?>
                            </td>
                            <td class='number'>
                                <?= DocoHelpers::rupiahDisplay($v['tarif_cyto']) ?>
                            </td>
                            <td class='number'><?= DocoHelpers::rupiahDisplay($total) ?></td>
                        </tr>
                        <?php
                        $no++;
                        endIf;
                    endforeach;
                    ?>
                    <?php if($no < 2): ?>
                        <tr>
                            <td colspan="7" class="text-center"><i>Data tidak ditemukan</i></td>
                        </tr>
                    <?php endIf;
                    endforeach
                     ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6"><strong>Total</strong></td>
                        <td class='number'><strong><?= DocoHelpers::rupiahDisplay($subTotal) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <br>
        <?php
        endforeach;
        break;
        ?>
        <?php
        default:
            if ((count($value) > 0) && ($value[0]['tgl_pelayanan'] != NULL) ): ;
            ?>
            <table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th colspan="6">Obat</th>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal Order Obat</th>
                        <th>Nama Obat</th>
                        <th>Qty</th>
                        <th>Tarif Satuan</th>
                        <th>Jumlah Tarif</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $no = 1;
                $subTotal = 0;
                foreach ($value as $items) :
                    $total = $items['jumlah_tarif'];
                    $subTotal += $total;
                ?>
                        <tr>
                            <td style="text-align: center;"><?= $no ?></td>
                            <td style="text-align: center;"><?php 
                                $res_tgl_pelayanan = ( !isset($items['tgl_pelayanan'])) ? '-' : date('d M Y H:i:s', strtotime($items['tgl_pelayanan'])); 
                                echo $res_tgl_pelayanan;
                            ?>
                            </td>
                            <td><?= $items['tindakan_obat_nama'] ?></td>
                            <td class='number'><?= DocoHelpers::formatNumber($items['qty']) ?></td>
                            <td class='number'><?= DocoHelpers::rupiahDisplay($items['tarif_satuan']) ?></td>
                            <td class='number'><?= DocoHelpers::rupiahDisplay($total) ?></td>
                        </tr>
                        <?php
                        $no++;
                        endforeach;
                        ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"><strong>Total</strong></td>
                        <td class='number'><strong><?= DocoHelpers::rupiahDisplay($subTotal) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
        <br>
   <?php
    break;
}
endforeach;
?>