<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
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
    $count = count($data); 
    $page = 0;
    foreach($data_details as $key => $value) :
        $page++;
        $grandJasaMedisNetto = $grandTotalJasaBruto = 0;
        $penerimaanMedis = $penerimaanBruto = $penguranganMedis = $penguranganBruto = 0;
        echo "<p><b>TANDA TERIMA HONOR DOKTER</b></p>";
        echo "</br></br></br>";
        echo "<table width='70%' style='font-size:13px;'>";
        echo "<tr><td>Nama Dokter</td><td> : </td><td> ".$key." </td></tr>";
        echo "<tr><td>Periode Praktek</td><td> : </td><td> ".$periode." </td></tr>";
        echo "<tr><td>Cara Bayar</td><td> : </td><td> ".$carabayar_nama." </td></tr>";
        echo "<tr><td>Penjamin</td><td> : </td><td> ".$penjamin_nama." </td></tr>";
        echo "</table>";
        echo "<br>";
?>

<table width="100%" class="tbl-bordered">
  <thead  style="font-size: 13px">
        <tr class="bg-inverse">
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Tanggal') ?></th>
            <th><?= Yii::t('app', 'No Pendaftaran') ?></th>
            <th><?= Yii::t('app', 'Nama Pasien') ?></th>
            <th><?= Yii::t('app', 'Tindakan') ?></th>
            <th><?= Yii::t('app', 'Jasa Medis Netto') ?></th>
            <th><?= Yii::t('app', 'TOTAL JASA BRUTO') ?></th>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
      <?php 
        foreach ($value as $ke => $val) {
            echo "<tr><td colspan=7><b>".$ke."</b></td></tr>";
            $no=1;
            $totalJasaMedis = $totalJasaBruto = 0;
            foreach ($val as $k => $v) {
                $jasaMedisNetto = $v['total_jasanetto'];
                $totalBruto = $v['total_bruto'];
                $totalJasaMedis += $jasaMedisNetto;
                $totalJasaBruto += $totalBruto;
                echo "<tr>";
                echo '<td>'.$no++.'</td>';
                echo '<td>'.date('d M Y', strtotime($v['tgl_tindakan'])).'</td>';
                if ($ke == 'PENERIMAAN') {
                    echo '<td>'.$v['no_pendaftaran'].'</td>';
                    echo '<td>'.$v['nama_pasien'].'</td>';
                    echo '<td>'.$v['daftartindakan_nama'].'</td>';
                    echo '<td class="right">'.DocoHelpers::formatNumber($jasaMedisNetto).'</td>';
                    echo '<td class="right">'.DocoHelpers::formatNumber($totalBruto).'</td>';
                }else{
                    echo '<td>'.$v['daftartindakan_nama'].'</td>';
                    echo '<td>'.$v['no_pendaftaran'].' '.$v['nama_pasien'].'</td>';
                    echo '<td></td>';
                    echo '<td class="right">'.DocoHelpers::formatNumber($jasaMedisNetto).'</td>';
                    echo '<td></td>';
                }
                echo '</tr>';
            }
            echo "<tr>";
            echo '<td class="right" colspan=2><b>TOTAL '.$ke.' </b></td>';
            echo '<td></td>';
            echo '<td></td>';
            echo '<td></td>';
            echo '<td class="right"><b>'.DocoHelpers::formatNumber($totalJasaMedis).'</b></td>';
            if ($ke == 'PENERIMAAN') {
                echo '<td class="right"><b>'.DocoHelpers::formatNumber($totalJasaBruto).'</b></td>';
                $penerimaanMedis = $totalJasaMedis;
                $penerimaanBruto = $totalJasaBruto;
            }else{
                echo '<td></td>';
                $penguranganMedis = $totalJasaMedis;
                $penguranganBruto = 0;
            }
            echo '</tr>';
        }
        $grandJasaMedisNetto = $penerimaanMedis - $penguranganMedis;
        $grandTotalJasaBruto = $penerimaanBruto - $penguranganBruto;

        echo "<tr>";
        echo '<td class="right" colspan=2><b>TOTAL </b></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td class="right"><b>'.DocoHelpers::formatNumber($grandJasaMedisNetto).'</b></td>';
        echo '<td class="right"><b>'.DocoHelpers::formatNumber($grandTotalJasaBruto).'</b></td>';
        echo '</tr>';
      ?>
    </tbody>
</table>

<?php 
    if ($count != $page) :
?>
      <pagebreak />
<?php
    endif;
  endforeach;  
?>
