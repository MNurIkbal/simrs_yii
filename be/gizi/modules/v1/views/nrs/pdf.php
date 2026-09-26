<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-20 17:29:28
 */
use Doco\components\DocoHelpers;
?>

<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    margin-bottom: 50px;
    }
    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered tr#colored {
        background-color: #fdfd96;
    }
    td:nth-child(1) {
        text-align:center;
    }
    h2{
        text-align: center;
    }
    .kesimpulan{
        padding: 10px;
        border: 1px solid black;
    }
</style>

<h2>Nutrional Risk Screening</h2>

<?php if($data_header['is_anak']): ?>
    <table width="100%" class="tbl-bordered">
        <thead>
           <tr>
                <th width="10%">No</th>
                <th width="40%">Variabel</th>
                <th width="10%">Skor</th>
                <th width="40%">Keterangan</th>
           </tr>
        </thead>
        <tbody>
            <?php foreach ($data_detail as $key => $value): ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= $value['skriningnrs_nama'] ?></td>
                    <td style="text-align:center;"><?= $value['skor']; ?></td>
                    <td><?= $value['additional_data']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4>Kesimpulan</h4>
    <div class="kesimpulan">
        <p>Total Skor: <b><?= $data_total_skor ?></b></p>
        <p>Keterangan: <b><?= $data_kesimpulan ? $data_kesimpulan->keterangan : ' - '?> </b></p>
    </div>

<?php else: ?>
    <h4>Skrining Gizi Awal</h4>

    <table width="100%" class="tbl-bordered">
        <thead>
           <tr>
                <th width="10%">No</th>
                <th width="65%">Variabel</th>
                <th width="25%">Keterangan</th>
           </tr>
        </thead>
        <tbody>
            <tr>
                <td>1.</td>
                <td>Apakah IMT < 20.5 atau LLA < 25 cm untuk wanita dan LLA < 26.3 cm untuk Pria</td>
                <td><?= $data_header['is_imt'] ? 'Ya' : 'Tidak' ?></td>
            </tr>
            <tr>
                <td>2.</td>
                <td>Apakah pasien kehilangan BB dalam 3 bulan terakhir?</td>
                <td><?= $data_header['is_berat_badan'] ? 'Ya' : 'Tidak' ?></td>
            </tr>
            <tr>
                <td>3.</td>
                <td>Apakah asupan makanan menurun 1 minggu terakhir?</td>
                <td><?= $data_header['is_asupan_makan'] ? 'Ya' : 'Tidak' ?></td>
            </tr>
            <tr>
                <td>4.</td>
                <td>Apakah pasien dengan penyakit berat? (ICU)</td>
                <td><?= $data_header['is_penyakit_berat'] ? 'Ya' : 'Tidak' ?></td>
            </tr>
        </tbody>
    </table>

    <?php if($data_header['is_imt'] || $data_header['is_berat_badan'] || $data_header['is_asupan_makan'] || $data_header['is_penyakit_berat']) { ?>

        <?php $total_skor=0; foreach($data_detail as $key => $value) : ?>

        <h4>Skrining Lanjutan <?= $key+1 ?></h4>

        <table width="100%" class="tbl-bordered">
            <thead>
               <tr>
                    <th width="10%">No</th>
                    <th width="65%">Variabel</th>
                    <th width="25%">Skor</th>
               </tr>
            </thead>
            <tbody>
                   <tr>
                        <td>1</td>
                        <td><?= $value['skriningnrs_nama'] ?></td>
                        <td><?= $value['skor']; ?></td>
                   </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Total Skor : <?= $value['skor']; ?></td>
                </tr>
            </tfoot>
        </table>

        <?php endforeach; ?>

        <h4>Kesimpulan</h4>
        <div class="kesimpulan">
            <?php foreach ($data_detail as $key => $value): ?>
                <span class="kesimpulan-skrining">Skrining Lanjut <?= $key + 1?>: <b><?= $value['skor'] ?></b>&nbsp;&nbsp;&nbsp;</span>
            <?php endforeach; ?>
            <p>Total Skor: <b><?= $data_total_skor ?></b></p>
            <p>Keterangan: <b><?= $data_kesimpulan ? $data_kesimpulan->keterangan : ' - '?> </b></p>
        </div>

    <?php } else {?>

        <p>Catatan: <b>Mohon lakukan asesmen ulang 1 minggu kemudian</b> </p>

    <?php }?>
<?php endif; ?>
