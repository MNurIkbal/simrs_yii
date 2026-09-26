<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-10-29 16:59:52
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-22 16:02:34
 */

use Doco\components\DocoHelpers;

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
        font-size: 11px;
    }
    .right {
      text-align: right;
    }
</style>

<?php  
  $group = '';
  $count = count($data); 
  $no = 0;
  foreach($data as $key => $value) :
    $no++;
    echo "<table width='70%' style='font-size:13px;'>";
    echo "<tr><td>Nama Dokter</td><td> : </td><td> ".$key." </td></tr>";
    echo "<tr><td>Periode Praktek</td><td> : </td><td> ".$periode." </td></tr>";
    echo "<tr><td>Cara Bayar</td><td> : </td><td> ".$carabayar_nama." </td></tr>";
    echo "<tr><td>Penjamin</td><td> : </td><td> ".$penjamin_nama." </td></tr>";
    echo "</table>";
    echo "<br>";
    $grandTotalNettoNonBpjs = $grandTotalNettoBpjs = $grandTotalNetto = 0;
    $grandTotalBruto = $grandTotalDpp = 0;
?>

<table width="100%" class="tbl-bordered">
  <thead  style="font-size: 13px">
        <tr class="bg-inverse">
            <th><?= Yii::t('app', 'Keterangan') ?></th>
            <th><?= Yii::t('app', 'Jasa Medis Netto (Asuransi & Umum)') ?></th>
            <th><?= Yii::t('app', 'Jasa Medis Netto (BPJS)') ?></th>
            <th><?= Yii::t('app', 'Total Jasa Netto') ?></th>
            <th><?= Yii::t('app', 'Total Jasa Bruto') ?></th>
            <th><?= Yii::t('app', 'DPP (Dasar Perhitungan Pajak)') ?></th>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
      <?php 
        $totalInNettoNonBpjs = $totalOutNettoNonBpjs = 0;
        foreach ($value as $keterangan => $val) {
          $totalNettoNonBpjs = $totalNettoBpjs = $totalNettoPenjamin = 
          $totalBruto = $totalDpp = 0;

          if($group != $keterangan) {
              echo "<tr><td colspan=6><b>$keterangan</b></td></tr>";
          }

          foreach ($val as $instalasi => $vv) {
            $total_jasanetto = $vv['total_jasanetto'];
            $total_bruto = $vv['total_bruto'];
            $total_dpp = $vv['total_dpp'];
            $nettoNonBpjs = $vv['total_jasanetto_non_bpjs'];
            $nettoBpjs = $vv['total_jasanetto_bpjs'];

            if($keterangan == 'PENERIMAAN') {
              $totalNettoNonBpjs += $nettoNonBpjs;
              $totalNettoBpjs += $nettoBpjs;
              $totalBruto += $total_bruto;
              $totalDpp += $total_dpp;
            }
            else {
              if($nettoNonBpjs == 0) {
                $totalNettoNonBpjs += $nettoNonBpjs;
              }
              else {
                $totalNettoNonBpjs -= $nettoNonBpjs;
              }

              if($nettoBpjs == 0) {
                $totalNettoBpjs += $nettoBpjs;
              }
              else {
                $totalNettoBpjs -= $nettoBpjs;
              }

              if($total_bruto == 0) {
                $totalBruto += $total_bruto;
              }
              else {
                $totalBruto -= $total_bruto;
              }

              if($total_dpp == 0) {
                $totalDpp += $total_dpp;
              }
              else {
                $totalDpp -= $total_dpp;
              }
            }

            $labeltotalNettoNonBpjs = abs($totalNettoNonBpjs);
            $labeltotalNettoBpjs = abs($totalNettoBpjs);
            $labelBruto = abs($totalBruto);
            $labelDpp = abs($totalDpp);

            $totalNetto = $nettoNonBpjs + $nettoBpjs;
            $totalNettoPenjamin += $totalNetto;
            
            echo "<tr>";
            echo '<td>'.$instalasi.'</td>';
            echo '<td class="right">'.DocoHelpers::formatNumber($nettoNonBpjs).'</td>';
            echo '<td class="right">'.DocoHelpers::formatNumber($nettoBpjs).'</td>';
            echo '<td class="right">'.DocoHelpers::formatNumber($totalNetto).'</td>';
            echo '<td class="right">'.DocoHelpers::formatNumber($total_bruto).'</td>';
            echo '<td class="right">'.DocoHelpers::formatNumber($total_dpp).'</td>';
            echo '</tr>';
          }
          
          echo "<tr>";
          echo '<td class="right" colspan=1><b>TOTAL '.$keterangan.' </b></td>';
          echo '<td class="right"><b>'.DocoHelpers::formatNumber($labeltotalNettoNonBpjs).'</b></td>';
          echo '<td class="right"><b>'.DocoHelpers::formatNumber($labeltotalNettoBpjs).'</b></td>';
          echo '<td class="right"><b>'.DocoHelpers::formatNumber($totalNettoPenjamin).'</b></td>';
          echo '<td class="right"><b>'.DocoHelpers::formatNumber($labelBruto).'</b></td>';
          echo '<td class="right"><b>'.DocoHelpers::formatNumber($labelDpp).'</b></td>';
          echo '</tr>';

          $grandTotalNettoNonBpjs += $totalNettoNonBpjs;
          $grandTotalNettoBpjs += $totalNettoBpjs;
          $grandTotalNetto = $grandTotalNettoNonBpjs + $grandTotalNettoBpjs;
          $grandTotalBruto += $totalBruto;
          $grandTotalDpp += $totalDpp;
        }

        echo "<tr>";
        echo '<td class="right" colspan=1><b>TOTAL</b></td>';
        echo '<td class="right"><b>'.DocoHelpers::formatNumber($grandTotalNettoNonBpjs).'</b></td>';
        echo '<td class="right"><b>'.DocoHelpers::formatNumber($grandTotalNettoBpjs).'</b></td>';
        echo '<td class="right"><b>'.DocoHelpers::formatNumber($grandTotalNetto).'</b></td>';
        echo '<td class="right"><b>'.DocoHelpers::formatNumber($grandTotalBruto).'</b></td>';
        echo '<td class="right"><b>'.DocoHelpers::formatNumber($grandTotalDpp).'</b></td>';
        echo '</tr>';
      ?>
    </tbody>
</table>
<?php 
    if ($count != $no) :
?>
      <pagebreak />
<?php
    endif;
  endforeach;  
?>

