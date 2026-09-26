<style>
    .number {
        text-align: right
    }

    table thead tr th {
        font:arial !important;
        font-size:10px !important;
    }

    table tbody tr td {
        font:arial !important;
        font-size:10px !important;
    }

    table tfoot tr td {
        font:arial !important;
        font-size:10px !important;
    }
</style>
<?php 
use Doco\components\DocoHelpers;

foreach ($detail as $key => $value) :
    switch ($key) {
    case 'tindakan':
        foreach ($value as $k => $v) :
        ?>
            <table border="1" cellpadding="1" cellspacing="1" style="width:100%">
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
                            <td><?= $no ?></td>
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
        case 'pemeriksaan':
            foreach ($value as $k => $v) :
        ?>
            <table border="1" cellpadding="1" cellspacing="1" style="width:100%">
                <thead>
                    <tr>
                        <th colspan="7">Pemeriksaan - <?= $v['title'] ?></th>
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
                            // $total = ($items['qty'] * $items['tarif_satuan']) + $items['tarif_cyto'];
                            $tindakan = "";
                            if($v['tindakan_obat_nama'] !="" || $v['tipepaket_nama'] !==""):
                            
                                /*if($v['instalasi_pelayanan'] == 'Laboratorium'){
                                    $tindakan = $v['pemeriksaanlab_nama'];
                                }elseif($v['instalasi_pelayanan'] == 'Radiologi'){
                                    $tindakan = $v['pemeriksaanrad_nama'];
                                }
                                else{
                                    $tindakan = $v['tindakan_obat_nama'];
                                }*/
                                $tindakan = $v['tindakan_obat_nama'];
                                if($tindakan == ""):
                                    $tindakan = $v['tipepaket_nama'];
                                endIf;

                                $total = $v['jumlah_tarif'];
                                // if($v['instalasi_pelayanan'] == 'Bedah Sentral'){
                                //     $total = $total + $v['tarif_cyto'];
                                // }

                                $subTotal += $total;
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
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
        case 'penunjang':
            foreach ($value as $k => $v) :
        ?>
            <table border="1" cellpadding="1" cellspacing="1" style="width:100%">
                <thead>
                    <tr>
                        <th colspan="7">Penunjang - <?= $v['title'] ?></th>
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
                            <td><?= $no ?></td>
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
            <table border="1" cellpadding="1" cellspacing="1" style="width:100%">
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
                    // $total = $items['qty'] * $items['tarif_satuan'];
                    $total = $items['jumlah_tarif'];
                    $subTotal += $total;
                ?>
                        <tr>
                            <td><?= $no ?></td>
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