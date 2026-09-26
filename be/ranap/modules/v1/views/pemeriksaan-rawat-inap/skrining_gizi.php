<?php

/**
 * @Author: rizal
 * @Date:   2018-11-23 10:20:03
 * @Last Modified by:
 * @Last Modified time:
 */
use Doco\components\DocoConstants;

?>

<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
        vertical-align: text-top;
    }
    .tbl-main td {
        vertical-align: text-top;
        border: 0px;
        padding: 5px;
    }
    .tbl-detail {
        border: 0px;
        /*padding: 5px;*/
    }
    .tbl-detail td {
        border: 0px;
        /*padding: 5px;*/
    }
</style>

<table width="100%" class="tbl-main">
    <tbody>
        <tr>
            <td width='55%'>Penurunan Berat Badan yang Tidak Direncanakan</td>
            <td>: <?= 
            $data_skrininggizi['bb_ygdirencanakan'] 
            ? (DocoConstants::BB_DIRENCANAKAN[$data_skrininggizi['bb_ygdirencanakan']] . (
                $data_skrininggizi['bb_ygdirencanakan'] != 2 
                ? ' (Skor' . $data_skrininggizi['bb_turun_skor'] . '). ' 
                : '. Penurunan BB : ' . $data_skrininggizi['bb_turun_nama'] . ' (Skor ' . $data_skrininggizi['bb_turun_skor'] . ')'
            )) 
            : ''; ?></td>
        </tr>
        <tr>
            <td>Hanya Mampu menghabiskan 1/4 Porsi Makanan</td>
            <td>: <?= @$data_skrininggizi['porsi_makan_nama'] . ($data_skrininggizi['porsi_makan_skor'] ? ' (Skor ' . $data_skrininggizi['porsi_makan_skor']. ')' : ''); ?></td>
        </tr>
        <tr>
            <td>Menderita Sakit Berat</td>
            <td>: <?= @$data_skrininggizi['sakit_berat_nama'] . ($data_skrininggizi['sakit_berat_skor'] ? ' (Skor ' . $data_skrininggizi['sakit_berat_skor']. ')' : ''); ?></td>
        </tr>
        <tr>
            <td><strong>Total Skor</strong></td>
            <td>: <strong><?= @$data_skrininggizi['skor']; ?></strong></td>
        </tr>
    </tbody>
</table>