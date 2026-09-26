<?php
    use Doco\components\DocoConstants;

    $diperiksa = !empty($status_worklist[DocoConstants::WORKLIST_QC]['nama_pegawai']) ? $status_worklist[DocoConstants::WORKLIST_QC]['nama_pegawai'] : '';
    $diserahkan = !empty($status_worklist[DocoConstants::WORKLIST_SIAP_DISERAHKAN]['nama_pegawai']) ? $status_worklist[DocoConstants::WORKLIST_SIAP_DISERAHKAN]['nama_pegawai'] : '';
    $index_all = 0;
?>

<style type="text/css">
    .tbl-header {
        border: 1; 
        width: 100%;
        border-style:groove; 
        font-family: Tahoma;
    }

    .lbl-header {
        font-size: 10px;
    }

    .cn-header {
        font-size: 10px;
        padding-left: 3px;
    }

    .cn-highlight {
        font-size: 10px;
    }

    .tbl-resep tr td{
        font-size: 10px;
    }

    .tbl-resep {
        font-family: Tahoma;
        border-collapse: collapse;
    }

    .tbl-racikan {
        border-collapse: collapse;
        width: 100%;
    }

    .space{
        padding-top:90px;
    }

    .upper { text-transform: uppercase; }

    @media print {
        #spacer {height: 2em;} /* height of footer + a little extra */
        #footer {
            position: fixed;
            bottom: -50;
        }
    }
</style>

<!-- Repice -->
<div class="space"></div>
<table class="tbl-resep" width="100%">
   
    <?php
        if (!empty($data_obat['non_racikan'])) {
            $total = count($data_obat['non_racikan']) - 1;
            $total_nonracikan = count($data_obat['non_racikan']);
            
            foreach ($data_obat['non_racikan'] as $index => $value) :
            $index_all = +$index + 1;
    ?>
            <tr>
                <?php if(!empty($value['etiket'])) : ?>
                    <td style="width: 6%; font-size: 18px; vertical-align: top;" rowspan="3">
                <?php else : ?>
                    <td style="width: 6%; font-size: 18px; vertical-align: top;" rowspan="2">
                <?php endif ?>
                    <strong>R/</strong>
                </td>

                <td style="width: 61%">
                    <?= $value['obatalkes_nama'] ?>
                </td>

                <td>
                    <?= $value['qty_transaksi'] . ' ' . $value['satuan_input']; ?>
                </td>
            </tr>
            
            <tr>
                <td colspan="3"><small><i>s. </i><?= $value['signa_nama']; ?></small></td>
            </tr>
            
            <?php if(!empty($value['etiket'])) : ?>
                <tr>
                    <td colspan="3" class="upper"><?= !empty($value['etiket']) ? $value['etiket'] : '-'; ?></td>
                </tr>
            <?php endif; ?>
            
            <?php 
                if ($type == 'salinan') {
                    $det = !empty($value['det']) ? $value['det'] : null;
                    $qty = !empty($value['qty_transaksi']) ? $value['qty_transaksi'] : null;
                    $satuan_input = !empty($value['satuan_input']) ? $value['satuan_input'] : '';
                    $str_det = $det != 0 ? ($det < $qty ? '<i>det</i> ' . $det . ' ' . $satuan_input : "") : '<i>ne det </i>';
            ?>
                <tr>
                    <td style="width: 6%;"></td>
                    <td><?= ($qty != $det) ? "<hr style='border: none; height: 5px; background-color: #000; width:100%;' />" : "" ?></td>
                    <td style="width: 35%;">&nbsp;<strong> <?= $str_det; ?></strong></td>
                    <td>&nbsp;</td>
                </tr>
            <?php } else {?>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            <?php }?>
    <?php endforeach; } ?>
    
    <?php
        if (!empty($data_obat['racikan'])) {
            $total_racikan = count($data_obat['racikan']);
            
            foreach ($data_obat['racikan'] as $index => $value) :
    ?>
            <tr>
                <td style="width: 6%; font-size: 18px; vertical-align: top;">
                    <strong>R/</strong>
                </td>

                <td colspan="2">
                    <table class="tbl-racikan">
                        <?php if (!empty($value) && is_array($value)) : ?>
                            <tr>
                                <td style="width: 75.5%;">
                                    <?php foreach ($value as $i => $val) :
                                        $index_all = $index_all + 1;
                                    ?>
                                        <?= $val['obatalkes_nama'] ?>
                                        <br>
                                    <?php endforeach; ?>
                                </td>

                                <td>
                                    <?php foreach ($value as $i => $val) :
                                        $qty = !empty($val['qty_transaksi']) ? $val['qty_transaksi'] : null;
                                    ?>
                                        <?= $qty . ' ' . $val['satuan_input'] ?>
                                        <br>
                                    <?php endforeach; ?>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <strong><?= $value[0]['nama_racikan']; ?></strong>&nbsp;<br/>
                                    <small><i>s. </i><?= $value[0]['signa_nama']; ?></small>
                                </td>
                            </tr>

                            <?php if(!empty($value[0]['etiket'])) : ?>
                                <tr>
                                    <td colspan="2" class="upper"><?= !empty($value[0]['etiket']) ? $val['etiket'] : '-'; ?></td>
                                </tr>
                            <?php endif; ?>    
                        <?php endif; ?>
                    </table>
                </td>
            </tr>

            <tr>
                <td>&nbsp;</td>
            </tr>
        <?php endforeach;  ?>
    <?php } ?>

    <tfoot>
        <tr>
            <td id="spacer"></td>
        </tr>
    </tfoot>
</table>

<?php if ($type == 'salinan') : ?>
    <div id="footer">
        <div style="text-align: right; font-size: 10px; font-family: Tahoma, serif;">PCC</div>
        <table class="tbl-footer" width="100%" style="border:1; border-style:groove; font-size: 10px;">
            <tr>
                <td style="text-align:center;width: 33%; ">
                    <div class="row">Salin</div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="row" style="">(.....................)</div>
                </td>
                <td style="text-align:center; width: 33%;">
                    <div class="row">Verifikasi</div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="row">(.....................)</div>
                </td>
                <td style="text-align:center; width: 33%; ">
                    <div class="row">Serah</div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="row">(.....................)</div>
                </td>
            </tr>
            <tr>
                <td style="text-align:center;width: 33%;">
                    <div class="row" style=""><?=$pegawai_print; ?></div>
                </td>
                <td style="text-align:center; width: 33%; ">
                    <div class="row" style=""><?= $pegawai_approve ?></div>
                </td>
                <td style="text-align:center; width: 33%; ">
                    <div class="row" style=""><?=$diserahkan;?></div>
                </td>
            </tr>
        </table>
    </div>
<?php else: ?>
    <div id="footer">
        <div style="text-align: left; font-size: 10px;"><span>Diisi Farmasi : </span></div>
        <table class="tbl-footer" width="100%" style="border:1; border-style:groove; font-size: 10px;">
            <tr>
                <td style="text-align:center; ">
                    <div>Skrining</div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="row" style="">(..................)</div>
                </td>
                <td style="text-align:center; ">
                    <div class="row">Ambil</div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="row">(..................)</div>
                </td>
                <td style="text-align:center; ">
                    <div class="row">Label</div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="row">(..................)</div>
                </td>
                <td style="text-align:center; ">
                    <div class="row">Periksa</div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="row">(..................)</div>
                </td>
                <td style="text-align:center; ">
                    <div class="row">Serah</div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="row">(..................)</div>
                </td>
                <td style="text-align:center; ">
                    <div class="row">Terima</div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="row">(.....................)</div>
                </td>
            </tr>
            <tr>
                <td style="text-align:center; ">
                    <div class="row" style=""><?=$pegawai_print; ?></div>
                </td>
                <td style="text-align:center; ">
                    <div class="row" style=""></div>
                </td>
                <td style="text-align:center; ">
                    <div class="row" style=""></div>
                </td>
                <td style="text-align:center; ">
                    <div class="row" style=""><?=$diperiksa;?></div>
                </td>
                <td style="text-align:center; ">
                    <div class="row" style=""><?=$diserahkan;?></div>
                </td>
                <td style="text-align:center; ">
                    <div class="row" style=""></div>
                </td>
            </tr>
        </table>
    </div>
<?php endif; ?>
