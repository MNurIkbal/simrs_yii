<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-05-16 16:02:30
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-05-17 09:59:53
 */
?>
<br><br><br>

<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Tanggal Pembayaran</td>
            <td>No Pendaftaran</td>
            <td>Nama Pasien</td>
            <td>Total Pembayaran</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            $total = 0;
            foreach ($model->getModels() as $value) : $total = $total + $value['total_terbayar'];
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['tgl_pembayaran']) ? $value['tgl_pembayaran'] : '' ?></td>
                <td><?= isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '' ?></td>
                <td><?= isset($value['nama_pasien']) ? $value['nama_pasien'] : '' ?></td>
                <td style="text-align: right;"><?= isset($value['total_terbayar']) ? number_format($value['total_terbayar'], 0) : '' ?></td>
            </tr>
        <?php
            $no++;

            endforeach;

        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="text-align: right;"><strong>Total</td>
            <td style="text-align: right;"><?= number_format($total, 0) ?></strong></td>
        </tr>
    </tfoot>
</table>
<br><br>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Uang Pecahan</td>
            <td>Qty</td>
            <td>Jumlah</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            $totalPecahan = 0;
            foreach ($detail->getModels() as $details) : $totalPecahan = $totalPecahan + $details['jumlahuang'];
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($details['nilaiuang']) ? number_format($details['nilaiuang'], 0) : '' ?></td>
                <td><?= isset($details['banyakuang']) ? $details['banyakuang'] : '' ?></td>
                <td style="text-align: right;"><?= isset($details['jumlahuang']) ? number_format($details['jumlahuang'], 0) : '' ?></td>
            </tr>
        <?php
            $no++;
            endforeach;

        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" style="text-align: right;"><strong>Total</td>
            <td style="text-align: right;"><?= number_format($totalPecahan, 0) ?></strong></td>
        </tr>
    </tfoot>
</table>
<br><br><br>
<div align="right"> Bandung, <?= date('d-M-Y') ?> </div> <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Mengetahui <br>
<br><br><br><br>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?= $data->nama_pegawai ?> 