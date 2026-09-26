<?php
    use Doco\components\DocoConstants;
    use Doco\components\DocoHelpers;
    
$i = 0;
foreach ($data as $key => $value):
    $nama_depan = ( isset($value['namadepan'])) ? $value['namadepan'] : '';

        $displayName = $nama_depan.$value['nama_pasien']; 
        $name = $nama_depan.$value['nama_pasien'];
        $displayName = DocoHelpers::cutSentence($name);
        
        $lookJk = isset($jenis_kelamin[$value['jeniskelamin']]['lookup_kode']) ? $jenis_kelamin[$value['jeniskelamin']]['lookup_kode'] : '-';
?>

<style>
body { 
    font-family: arial !important;
}
</style>

<?php if (($i % 2) == 0): ?>
    <div style="background-color: white">
        <div style="float:left;width:33%;background-color: white; margin-left: 2.5%; margin-top: 1%;">
            <table border="0" cellpadding="0.5" cellspacing="0.5" style="width:100%;">
                <tbody>
                    <tr>
                        <td colspan="2"><span style="font-size:12px;font-weight:bold;"><?= $displayName ?></span></td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><span style="font-size:10px">MR. No</span></td>
                        <td><span style="font-size:10px">: <?= $value['no_rekam_medik'] ?></span></td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><span style="font-size:10px">REG. No</span></td>
                        <td><span style="font-size:10px">: <?= $value['no_pendaftaran'] ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size:10px">DOB/SEX</span></td>
                        <td><span style="font-size:10px">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($value['tanggal_lahir'])), true, false) ?> / <?= $lookJk ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div style="background-color: white; width: 120%">
        <div style="width:40%;background-color: white; margin-left: 38%; margin-top: -8.3%; margin-right: -100px">
            <table border="0" cellpadding="0.5" cellspacing="0.5" style="width:100%;">
                <tbody>
                    <tr>
                        <td colspan="2"><span style="font-size:12px;font-weight:bold;"><?= $displayName ?></span></td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><span style="font-size:10px">MR. No</span></td>
                        <td><span style="font-size:10px">: <?= $value['no_rekam_medik'] ?></span></td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><span style="font-size:10px">REG. No</span></td>
                        <td><span style="font-size:10px">: <?= $value['no_pendaftaran'] ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size:10px">DOB/SEX</span></td>
                        <td><span style="font-size:10px">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($value['tanggal_lahir'])), true, false) ?> / <?= $lookJk ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div style="height: 30px">
        &nbsp;
    </div>
<?php endif ?>
<?php $i++; endforeach; ?>