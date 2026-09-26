<?php use Doco\components\DocoHelpers; ?>
<style>
    .tbl-bordered {
        /*border: 1px solid black;*/
        border-collapse: collapse;
        font-size: 12px;
        font-family: Tahoma;
    }
    .tbl-bordered th, .tbl-bordered td {
        /*border: 1px solid black;*/
        font-family: Tahoma;
    }

    .tbl-bordered td {
        padding: 3px;
    }
    .number {
        text-align: right
    }
    .center {
        text-align: center
    }
</style>
<table width="100%" class="tbl-bordered" style="margin-top: 10px;">
    <tr>
        <td>&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;</td>
    </tr>
    <tr>
        <td style="width: 60%;">&nbsp;&nbsp;</td>
        <td class="number"><strong><?= $no_mr; ?></strong></td>
    </tr>
    <tr>
        <td style="width: 60%;">&nbsp;&nbsp;</td>
        <td class="number"><strong><?= $no_reg; ?></strong></td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;</td>
        <td class="number" style="height: 10px; vertical-align: bottom;"><strong><?= $no_kwt; ?></strong></td>
    </tr>
    <tr>
        <td style="width: 80%;">&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;</td>
    </tr>
</table>
<table width="100%" class="tbl-bordered" style="margin-top: 25px">
    <tr>
        <td style="width: 23%;"></td>
        <td style="height: 15px;vertical-align: bottom;"><strong><?= $diterima_dari; ?></strong></td>
    </tr>
    <tr>
        <td style="width: 23%; "></td>
        <td style="height: 10px; vertical-align: bottom;"><strong><?= $terbilang; ?></strong></td>
    </tr>
    <tr>
        <td style="width: 23%;"></td>
        <td rowspan="2" style="height: 20px; vertical-align: bottom;"><strong><?= $keterangan; ?></strong></td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;</td>
    </tr>
</table>
<table width="100%" class="tbl-bordered" style="margin-right: 30px;">
    <tr>
        <td class="number"><strong><?= $kasir ?></strong></td>
    </tr>
</table>
<table width="100%" class="tbl-bordered" style="margin-right: 30px;">
    <tr>
        <td style="width: 8%;">&nbsp;&nbsp;</td>
        <td style="height: 36px; vertical-align: bottom;font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?= $jumlah_diterima; ?></strong></td>
        <td class="number"><strong><?= date('d-M-Y') ?></strong></td>
    </tr>
</table>
