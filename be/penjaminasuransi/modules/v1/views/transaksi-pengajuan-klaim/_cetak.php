<style type="text/css">
    w-full {
        width: 100%;
    }

    tr, th {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11pt;
    }

    tr, td {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11pt;
    }

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
    }

    .line-height {
        line-height: 1.5;
    }

    .text-ceter {
        text-align: center;
    }

</style>
<table class="tbl-bordered w-full line-height" autosize="1">
	<thead>
	   <tr>
            <th>No</th>
            <th width="20px">Data Pasien</th>
            <th>No Invoice</th>
            <th>Tanggal Masuk</th>
            <th>Tanggal Keluar</th>
            <th>No SEP</th>
            <th width="15px">Instalasi / Ruangan</th>
            <th width="10%">Tagihan</th>
            <th width="10%">Jumlah Dibayarkan Pasien</th>
            <th width="10%">Jumlah Discount</th>
            <th width="10%">Jumlah Pengajuan</th>
       </tr>
	</thead>
	<tbody>
    	<?php 
            $no = 1;
            foreach ($data as $key => $value) :
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= '<b>' . $value['nama_pasien'] . '</b>' . "<br>" . $value['no_rekam_medik'] . "<br>" . $value['no_pendaftaran'] ?></td>
                <td><?= $value['no_pembayaran'] ?></td>
                <td><?= date(' d M Y', strtotime($value['tgl_pendaftaran'])) ?></td>
                <td><?= !empty($value['tglpasienpulang']) ? date('d M Y', strtotime($value['tglpasienpulang'])) : '' ?></td>
                <td><?= $value['nosep'] ? $value['nosep'] : '-' ?></td>
                <td><?= '<b>' . $value['instalasi_nama'] . '</b>' . ' / ' . '<br>' . $value['ruangan_nama'] ?></td>
                <td><?= $value['total_tagihan_label'] ?></td>
                <td><?= $value['total_sdh_bayar_label'] ?></td>
                <td><?= $value['total_discount_label'] ?></td>
                <td><?= $value['total_pengajuan_label'] ?></td>
            </tr>
        <?php endforeach ?>
            <tr>
                <td colspan="7" style="text-align: right;"><b>Total</b></td>
                <td><?= $summaries['total_tagihan_label'] ?></td>
                <td><?= $summaries['total_sdh_bayar_label'] ?></td>
                <td><?= $summaries['total_discount_label'] ?></td>
                <td><?= $summaries['total_pengajuan_label'] ?></td>
            </tr>
    </tbody>
</table>