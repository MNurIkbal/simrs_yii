<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }
    
    tr.strikeout td:before {
        content: " ";
        position: absolute;
        top: 50%;
        left: 0;
        border-bottom: 1px solid #111;
        width: 100%;
    }

    td.label{
        vertical-align: text-top; 
        width: 150px;
        height: 25px;
    }

    td.koma{
        vertical-align: text-top; 
        width: 5px;
        height: 25px;
    }

    td.data{
        vertical-align: text-top; 
        text-align: left;
        height: 25px;
    }

    td.data-dosis{
        vertical-align: text-top; 
        text-align: left;
        height: 25px;
    }

    td.dosis{
        vertical-align: text-top; 
        width: 150px;
        height: 25px;
    }

</style>
<?php 
$tglProsedur = $data['data']['tgl_prosedure'];
if ($tglProsedur) {
    $tglProsedur = date('d-m-Y H:i:s', strtotime($tglProsedur));
}
    if($tipe == 'koroangiografi'){
?>
        <table width="100%">
            <tbody>
                <tr>
                    <td class="label">INDIKASI</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['indikasi']?></td>
                </tr>
                <tr>
                    <td class="label">APPROACH</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['approach']?></td>
                </tr>
                <tr>
                    <td class="label">LM</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['lm']?></td>
                </tr>
                <tr>
                    <td class="label">LAD</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['lad']?></td>
                </tr>
                <tr>
                    <td class="label">LCX</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['lcx']?></td>
                </tr>
                <tr>
                    <td class="label">RCA</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['rca']?></td>
                </tr>
                <tr>
                    <td class="label">Lain - lain</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['lain_lain']?></td>
                </tr>
                <tr>
                    <td class="label">KESIMPULAN</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['kesimpulan']?></td>
                </tr>
                <tr>
                    <td class="label">SARAN</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['saran']?></td>
                </tr>
                <tr>
                    <td class="label">DOSIS</td>
                    <td class="koma"> : </td>
                    <td class="data">
                        <table width="100%">
                            <tbody>
                                <tr>
                                    <td class="dosis">Cum Air Kerma</td>
                                    <td class="koma"> : </td>
                                    <td class="data-dosis"><?=$data['data']['cum_air_kerma']?></td>
                                    <td class="dosis"> /Mgy</td>
                                    <td class="dosis">Cum DAP</td>
                                    <td class="koma"> : </td>
                                    <td class="data-dosis"><?=$data['data']['cum_dap']?></td>
                                    <td class="dosis"> /MGycm2</td>
                                </tr>
                                <tr>
                                    <td class="dosis">Fluo Time</td>
                                    <td class="koma"> : </td>
                                    <td class="data-dosis"><?=$data['data']['fluo_time']?></td>
                                    <td class="dosis"> /Menit</td>
                                    <td class="dosis">Kontras</td>
                                    <td class="koma"> : </td>
                                    <td class="data-dosis"><?=$data['data']['kontras']?></td>
                                    <td class="dosis"> /CC</td>
                                </tr>
                                <tr>
                                    <td class="dosis">Procedure Time</td>
                                    <td class="koma"> : </td>
                                    <td class="data-dosis"><?=$data['data']['procedure_time']?></td>
                                    <td class="dosis"> /Menit</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="label">OPERATOR</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['operator_nama']?></td>
                </tr>
                <tr>
                    <td class="label">TGL PROSEDUR</td>
                    <td class="koma"> : </td>
                    <td class="data"><?= $tglProsedur ?></td>
                </tr>
                <tr>
                    <td class="label">CATATAN</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['koroner']?></td>
                </tr>
            </tbody>
        </table>
<?php
    }
    elseif($tipe == 'pci'){
    ?>
        <table width="100%">
            <tbody>
                <tr>
                    <td class="label">INDIKASI</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['indikasi']?></td>
                </tr>
                <tr>
                    <td class="label">APPROACH</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['approach']?></td>
                </tr>
                <tr>
                    <td class="label">TARGET</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['target']?></td>
                </tr>
                <tr>
                    <td class="label">LM</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['lm']?></td>
                </tr>
                <tr>
                    <td class="label">LAD</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['lad']?></td>
                </tr>
                <tr>
                    <td class="label">LCX</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['lcx']?></td>
                </tr>
                <tr>
                    <td class="label">RCA</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['rca']?></td>
                </tr>
                <tr>
                    <td class="label">LAPORAN PCI</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['laporan_pci']?></td>
                </tr>
                <tr>
                    <td class="label">KESIMPULAN</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['kesimpulan']?></td>
                </tr>
                <tr>
                    <td class="label">SARAN</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['saran']?></td>
                </tr>
                <tr>
                    <td class="label">DOSIS</td>
                    <td class="koma"> : </td>
                    <td class="data">
                        <table width="100%">
                            <tbody>
                                <tr>
                                    <td class="dosis">Cum Air Kerma</td>
                                    <td class="koma"> : </td>
                                    <td class="data-dosis"><?=$data['data']['cum_air_kerma']?></td>
                                    <td class="dosis"> /Mgy</td>
                                    <td class="dosis">Cum DAP</td>
                                    <td class="koma"> : </td>
                                    <td class="data-dosis"><?=$data['data']['cum_dap']?></td>
                                    <td class="dosis"> /MGycm2</td>
                                </tr>
                                <tr>
                                    <td class="dosis">Fluo Time</td>
                                    <td class="koma"> : </td>
                                    <td class="data-dosis"><?=$data['data']['fluo_time']?></td>
                                    <td class="dosis"> /Menit</td>
                                    <td class="dosis">Kontras</td>
                                    <td class="koma"> : </td>
                                    <td class="data-dosis"><?=$data['data']['kontras']?></td>
                                    <td class="dosis"> /CC</td>
                                </tr>
                                <tr>
                                    <td class="dosis">Procedure Time</td>
                                    <td class="koma"> : </td>
                                    <td class="data-dosis"><?=$data['data']['procedure_time']?></td>
                                    <td class="dosis"> /Menit</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="label">OPERATOR</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['operator_nama']?></td>
                </tr>
                <tr>
                    <td class="label">TGL PROSEDUR</td>
                    <td class="koma"> : </td>
                    <td class="data"><?= $tglProsedur ?></td>
                </tr>
            </tbody>
        </table>
<?php
    }
    elseif($tipe == 'dsa'){
    ?>
        <table width="100%">
            <tbody>
                <tr>
                    <td class="label">INDIKASI</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['indikasi']?></td>
                </tr>
                <tr>
                    <td class="label">APPROACH</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['approach']?></td>
                </tr>
                <tr>
                    <td class="label">LAPORAN DSA</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['laporan_dsa']?></td>
                </tr>
                <tr>
                    <td class="label">KESIMPULAN</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['kesimpulan']?></td>
                </tr>
                <tr>
                    <td class="label">SARAN</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['saran']?></td>
                </tr>
                <tr>
                    <td class="label">OPERATOR</td>
                    <td class="koma"> : </td>
                    <td class="data"><?=$data['data']['operator_nama']?></td>
                </tr>
                <tr>
                    <td class="label">TGL PROSEDUR</td>
                    <td class="koma"> : </td>
                    <td class="data"><?= $tglProsedur ?></td>
                </tr>
            </tbody>
        </table>
<?php
    }
?>

<hr>
