<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    table {
        border-collapse: collapse;
    }
    .bg-inverse th, .td-inverse td {
        border: 1px solid #000000;
        padding: 10px;
        text-align: left;
    }
  /* tr:nth-child(even) {
    background-color: #eee;
  }
  tr:nth-child(odd) {
    background-color: #fff;
  }   */
</style>
<table id="lap-pelayanan-igd" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
            <th rowspan="2">NO</th>
                <th rowspan="2" align="center">JENIS PELAYANAN</th>
                <th colspan="2" align="center">PASIEN MASUK</th>
                <th colspan="5" align="center">TRIASE</th>
                <th colspan="7" align="center">TINDAKAN LANJUT</th>
            </tr>
            <tr class="bg-inverse">
                <th align="center" >RUJUKAN</th>
                <th align="center" >NON RUJUKAN</th>
                <th align="center" >RESUSITASI</th>
                <th align="center" >EMERGENT</th>
                <th align="center" >URGENT</th>
                <th align="center" >NON URGENT</th>
                <th align="center" >FALSE EMERGENCY</th>
                <th align="center">DIPULANGKAN</th>
                <th align="center">DIRUJUK KE RS LAIN</th>
                <th align="center">PULANG PAKSA</th>
                <th align="center" >MENINGGAL</th>
                <th align="center" >DIRUJUK RAWAT INAP</th>
                <th align="center" >LAIN-LAIN</th>
                <th align="center" >MELARIKAN DIRI</th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data as $value) :
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['jenis_pelayanan'] ?></td>
                    <td align="center"><?= $value['pasienmasukrujukan'] ?></td>
                    <td align="center"><?= $value['pasienmasuknonrujukan'] ?></td>
                    <td align="center"><?= $value['triase_resusitasi'] ?></td>
                    <td align="center"><?= $value['triase_emergent'] ?></td>
                    <td align="center"><?= $value['triase_urgent'] ?></td>
                    <td align="center"><?= $value['triase_nonurgent'] ?></td>
                    <td align="center"><?= $value['triase_falseemergency'] ?></td>
                    <td align="center"><?= $value['pasientindaklanjut_dipulangkan'] ?></td>
                    <td align="center"><?= $value['pasientindaklanjut_dirujukrslain'] ?></td>
                    <td align="center"><?= $value['pasientindaklanjut_pulangpaksa'] ?></td>
                    <td align="center"><?= $value['pasientindaklanjut_meninggal'] ?></td>
                    <td align="center"><?= $value['pasientindaklanjut_dirujukri'] ?></td>
                    <td align="center"><?= $value['pasientindaklanjut_lainlain'] ?></td>
                    <td align="center"><?= $value['pasientindaklanjut_melarikandiri'] ?></td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>