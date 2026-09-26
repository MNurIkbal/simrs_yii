<style>
    .number {
        text-align: right
    }
</style>
<?php 
use Doco\components\DocoHelpers;
$grandTotal = 0;

foreach ($detail as $key => $value) :
    switch ($key) {
    case 'tindakan':
        foreach ($value as $k => $v) :
        ?>
                        <table border="1" cellpadding="1" cellspacing="1" style="width:100%; border-collapse: collapse;">
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
                                    // $total = ($items['qty'] * $items['tarif_satuan']) + $items['tarif_cyto'];
                                    $total = $items['sub_total'];
                                    $subTotal += $total;
                                    $grandTotal += $total;
                                ?>
                                        <tr>
                                            <td><?= $no ?></td>
                                            <td><?= date('d M Y H:i:s', strtotime($items['tgl_pelayanan'])) ?></td>
                                            <td><?= $items['tindakan_obat_nama'] ?></td>
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
                                        endforeach;
                                        ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6">Total</td>
                                    <td class='number'><?= DocoHelpers::rupiahDisplay($subTotal) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                        <br>
                   <?php
                    endforeach;
                    break;
                case 'penunjang':
                    foreach ($value as $k => $v) :
                    ?>
                        <table border="1" cellpadding="1" cellspacing="1"  style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th colspan="8">Pemeriksaan Laboratorium </th>
                                </tr>
                                <tr>
                                    <th style="width:4%">No.</th>
                                    <th style="width:20%">Tanggal Pemeriksaan</th>
                                    <th>Nama Pemeriksaan</th>
                                    <th>Qty</th>
                                    <th>Tarif Satuan</th>
                                    <th>Cyto</th>
                                    <th>Tarif Cyto</th>
                                    <th>Jumlah Tarif</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $subTotal = 0;
                                ksort($v['data']);
                                foreach ($v['data'] as $urut=>$items) :
                                    // $total = ($items['qty'] * (!empty($items['tarif_cyto'])
                                    // ? $items['tarif_cyto'] : $items['tarif_satuan']));
                                    $total = $items['sub_total'];
                                    $subTotal += $total;
                                    $grandTotal += $total;
                                ?>
                                
                                        <tr>
                                            <td><?= $no ?></td>
                                            <td><?= date('d M Y H:i:s', strtotime($items['tgl_pelayanan'])) ?></td>
                                            <td><?= $items['tindakan_obat_nama']?></td>
                                            <td class='number'>
                                                <?= DocoHelpers::formatNumber($items['qty']) ?>
                                            </td>
                                            <td class='number'>
                                                <?= DocoHelpers::rupiahDisplay($items['tarif_satuan']) ?>
                                            </td>
                                            <td class='number'>
                                                <?= !empty($items['tarif_cyto']) ? 'Ya' : 'Tidak' ?>
                                            </td>
                                            <td class='number'>
                                                <?= DocoHelpers::rupiahDisplay($items['tarif_cyto']) ?>
                                            </td>
                                            <td class='number'><?= DocoHelpers::rupiahDisplay($total) ?></td>
                                        </tr>
                                        <?php
                                        $no++;
                                        endforeach;
                                        ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7">Total</td>
                                    <td class='number'><?= DocoHelpers::rupiahDisplay($subTotal) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                        <br>
                   <?php
                    endforeach;
                    break;
                default:
                    if (!count($value)) continue;
                    ?>
                <table border="1" cellpadding="1" cellspacing="1" style="width:100%; border-collapse: collapse;">
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
                        $total = $items['sub_total'];
                        $subTotal += $total;
                        $grandTotal += $total;
                    ?>
                            <tr>
                                <td><?= $no ?></td>
                                <td><?= date('d M Y H:i:s', strtotime($items['tgl_pelayanan'])) ?></td>
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
                            <td colspan="5">Total</td>
                            <td class='number'><?= DocoHelpers::rupiahDisplay($subTotal) ?></td>
                        </tr>
                    </tfoot>
                </table>
                <br>
               <?php
                break;
        }
        endforeach;
        ?>


<table border="0" cellpadding="1" cellspacing="1" style="width:100%; border-collapse: collapse;">
    <tr>
        <td><strong>TOTAL TAGIHAN:</strong></td>
        <td style="text-align:right;"><strong><?= DocoHelpers::rupiahDisplay($grandTotal); ?></strong></td>
    </tr>
</table>