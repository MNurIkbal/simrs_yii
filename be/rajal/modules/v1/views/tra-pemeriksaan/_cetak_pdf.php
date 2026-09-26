<?php use Doco\components\DocoHelpers;  ?>
<?php if ($detail): ?>
  <table border="1" cellpadding="5" cellspacing="1" style="font-size:10px; width:100%;" >
      <tr>
        <th>No</th>
        <th>No Antrian</th>
        <th>Tanggal Pendaftaran</th>
        <th>Nomor Rekam Medik</th>
        <th>Nama Pasien</th>
        <th>Ruangan Asal</th>
        <th>Jenis Kelamin</th>
        <th>Cara Bayar</th>
        <th>Penjamin</th>
        <th>Nama Dokter</th>
        <th>Status</th>
      </tr>
      <?php $counter=1; foreach ($detail as $value):
        $date1=date_create($value['tgl_pendaftaran']);
        $date2=date_create();
        $diff=date_diff($date1,$date2);
        $dataHariRawat = $diff->d;
      ?>
      <tr>
        <td><?= $counter++ ?></td>
        <td><?= $value['no_antrian'] ?></td>
        <td><?= date('d F Y H:i:s', strtotime($value['tgl_pendaftaran'])) ?></td>
        <td><?= $value['no_rekam_medik'] ?></td>
        <td><?= $value['nama_pasien'] ?></td>
        <td><?= $value['ruanganasal_nama'] ?></td>
        <td><?= $value['jenis_kelamin'] ?></td>
        <td><?= $value['carabayar_nama'] ?></td>
        <td><?= $value['penjamin_nama'] ?></td>
        <td><?= $value['nama_pegawai'] ?></td>
        <td><?= $value['status_periksa1'] ?></td>
      </tr>
      <?php endforeach; ?>
  </table><br>
<?php endif; ?>