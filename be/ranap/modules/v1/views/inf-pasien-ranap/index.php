<?php use Doco\components\DocoHelpers;  ?>
<?php if ($detail): ?>
  <table border="1" cellpadding="5" cellspacing="1" style="font-size:10px; width:100%;" >
      <tr>
        <th>No</th>
        <th>Tanggal Admisi</th>
        <th>No.Rm / No.Pendaftaran</th>
        <th>Nama Pasien</th>
        <th>Jenis Kelamin</th>
        <th>Dokter DPJP</th>
        <th>Cara Bayar / Penjamin</th>
        <th>Hak Kelas / Kelas Saat Ini / Kelas Tagihan</th>
        <th>Kasus Penyakit</th>
        <th>Nama Ruangan No.Kamar-No.Bed</th>
        <th>Hari Rawat</th>
        <th>Tanggal Pindah</th>
        <th>Rencana Pulang</th>
      </tr>
      <?php $counter=1; foreach ($detail as $value):
        $date1=date_create($value['tgl_admisi']);
        $date2=date_create();
        $diff=date_diff($date1,$date2);
        $dataHariRawat = $diff->d;
        $statusTitipan = '-';
        if (!empty($value['is_pasientitipan_pk'])) {
            if($value['is_pasientitipan_pk'] == true && $value['is_stoppasientitipan'] == false){
                $statusTitipan = $value['kelas_ditagihkan_nama'];
            }
        } else if (empty($value['is_pasientitipan_pk'])) {
            if($value['is_pasientitipan'] == true && $value['is_stoppasientitipan'] == false){
                $statusTitipan = $value['kelas_ditagihkan_nama'];
            }
        }

      ?>
      <tr>
        <td><?= $counter++ ?></td>
        <td><?= date('d F Y H:i:s', strtotime($value['tgl_admisi'])) ?></td>
        <td><?= $value['no_rekam_medik'].' / '.$value['no_pendaftaran'] ?></td>
        <td><?= $value['nama_pasien'] ?></td>
        <td><?= $value['jenis_kelamin'] ?></td>
        <td><?= $value['dokter_admisi'] ?></td>
        <td><?= $value['carabayar_nama'].' / '.$value['penjamin_nama'] ?></td>
        <td><?= $value['hak_kelas'].' / '.$value['kelas_pelayanan'].' / '.$statusTitipan ?></td>
        <td><?= $value['jeniskasuspenyakit_nama'] ?></td>
        <td><?= $value['ruangan_nama'].' <br> '.$value['kamarruangan_nokamar'].' / '.$value['no_tempattidur'] ?></td>
        <td><?= ($dataHariRawat == 0 ) ? 1 : $dataHariRawat ?></td>
        <td><?= !empty($value['tgl_pindahkamar']) ?  
            date('d M Y H:i:s', strtotime($value['tgl_pindahkamar'])) : ' - ' ?></td>
        <td><?= !empty($value['rencana_pulang']) ?  
            date('d M Y H:i:s', strtotime($value['rencana_pulang'])) : ' - ' ?></td>
      </tr>
      <?php endforeach; ?>
  </table><br>
<?php endif; ?>
