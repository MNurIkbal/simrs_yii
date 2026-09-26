<table border="0" cellspacing="5" cellpadding="3" style="font-size:10px; width:100%;">
        <tr>
            <td>Tanggal Pendaftaran</td>
            <td>: <?= date('d F Y', strtotime($detailtindakan[0]['tgl_pendaftaran']))   ?></td>
            <td>Cara Bayar</td>
            <td>: <?= $detailtindakan[0]['carabayar_pelayanan']  ?></td>
            <td>Penjamin</td>
            <td>: <?= $detailtindakan[0]['penjamin_pelayanan']  ?></td>
        </tr>
        <tr>
            <td>No Rekam Medik</td>
            <td>: <?= $detailtindakan[0]['no_rekam_medik']    ?></td>
            <td>Jenis Kasus Penyakit</td>
            <td>: <?= $detailtindakan[0]['jeniskasuspenyakit_nama']    ?></td>
            <td>Kelas Pelayanan</td>
            <td>: <?= $detailtindakan[0]['kelaspelayanan_nama']  ?></td>
        </tr>
        <tr>
            <td>No Pendaftaran</td>
            <td>: <?= $detailtindakan[0]['no_pendaftaran']   ?></td>
            <td>Dokter</td>
            <td>: <?= $detailtindakan[0]['dokter_pendaftaran']   ?></td>
            <td>Status Bayar</td>
            <td>: <?= $detailtindakan[0]['status_bayar']   ?></td>
        </tr>
        <tr>
            <td>Nama Pasien</td>
            <td>:  <?= $detailtindakan[0]['nama_pasien']   ?></td>
            <td>Ruangan</td>
            <td colspan="2">: <?= $detailtindakan[0]['ruangan_pelayanan']  ?></td>
        </tr>
</table><hr>
<table border="1" cellspacing="1" cellpadding="5" style="font-size:10px; width:50%;">
      <tr >
        <td colspan="2" style="">Riwayat Pembayaran</td>
      </tr>
      <tr>
        <td>Total Tagihan</td>
        <td><?= $detailtindakan[0]['total_tagihan']   ?></td>
      </tr>
      <tr>
        <td>Total Uang Muka</td>
          <td><?= $detailtindakan[0]['total_uang_muka']   ?></td>
      </tr>
      <tr>
        <td>Total Sudah Dibayarkan</td>
          <td><?= $detailtindakan[0]['total_sudah_dibayarkan']   ?></td>
      </tr>
      <tr>
        <td>Total Sisa Tagihan</td>
          <td><?= $detailtindakan[0]['total_sisatagihan']   ?></td>
      </tr>
</table><br>

<table border="1" cellpadding="5" cellspacing="1" style="font-size:10px; width:100%;" >
    <tr>
          <td colspan="7" >Tindakan <?= $detailtindakan[0]['ruangan_pelayanan'] ?></td>
    </tr>
    <tr>
      <td>No</td>
      <td>Tanggal Tindakan</td>
      <td>Nama Tindakan</td>
      <td>Qty</td>
      <td>Tarif Satuan</td>
      <td>Tarif Cyto</td>
      <td>Jumlah Tarif</td>
    </tr>
    <?php $counter=1; foreach ($detailtindakan as $value): ?>
    <tr>
      <td><?= $counter++ ?></td>
      <td><?= date('d F Y H:i:s', strtotime($value['tgl_pelayanan'])) ?></td>
      <td><?= $value['tindakan_obat_nama']  ?></td>
      <td><?= $value['qty']  ?></td>
      <td><?= $value['tarif_satuan']   ?></td>
      <td><?= $value['tarifcyto_tindakan']   ?></td>
      <td><?= $value['sub_total']   ?></td>
    </tr>
    <?php  $totaltindakan = $totaltindakan+$value['sub_total'];?>
    <?php endforeach; ?>
    <tr>
      <td colspan="6">Total</td>
      <td><?= $totaltindakan   ?></td>
    </tr>
</table><br>


<table border="1" cellpadding="5" cellspacing="1" style="font-size:10px; width:100%;" >
    <tr>
          <td colspan="7" style="">Obat</td>
    </tr>
    <tr>
      <td>No</td>
      <td>Tanggal Order Obat</td>
      <td>Nama Obat</td>
      <td>Qty</td>
      <td>Tarif Satuan</td>
      <td>Jumlah Tarif</td>
    </tr>
    <?php $counter=1; foreach ($detailobat as $value): ?>
    <tr>
      <td><?= $counter++ ?></td>
      <td><?= date('d F Y  H:i:s', strtotime($value['tgl_pelayanan'])) ?></td>
      <td><?= $value['tindakan_obat_nama']  ?></td>
      <td><?= $value['qty']  ?></td>
      <td><?= $value['tarif_satuan']  ?></td>
      <td><?= $value['sub_total']   ?></td>
    </tr>
    <?php  $totalobat = $totalobat+$value['sub_total'];?>
    <?php endforeach; ?>
    <tr>
      <td colspan="5">Total</td>
      <td><?= $totalobat   ?></td>
    </tr>
</table><br>

<?php if ($detaillab): ?>
  <table border="1" cellpadding="5" cellspacing="1" style="font-size:10px; width:100%;" >
      <tr>
            <td colspan="8" style="">Pemeriksaan Laboratorium</td>
      </tr>
      <tr>
        <td>No</td>
        <td>Tanggal Pemeriksaan</td>
        <td>Nama Pemeriksaan</td>
        <td>Tarif Satuan</td>
        <td>Cyto ?</td>
        <td>Tarif Satuan Cyto</td>
        <td>Qty</td>
        <td>Jumlah</td>
      </tr>
      <?php $counter=1; foreach ($detaillab as $value): ?>
      <tr>
        <td><?= $counter++ ?></td>
        <td><?= date('d F Y  H:i:s', strtotime($value['tgl_pelayanan'])) ?></td>
        <td><?= $value['tindakan_obat_nama']  ?></td>
        <td><?= $value['tarif_satuan']  ?></td>
        <td>
            <?php if ($value['tarifcyto_tindakan']): ?>
              Ya
            <?php else: ?>
              Tidak
            <?php endif; ?>
        </td>
        <td><?= $value['tarifcyto_tindakan']   ?></td>
        <td><?= $value['qty']  ?></td>
        <td><?= $value['sub_total']   ?></td>
      </tr>
      <?php  $totallab = $totallab+$value['sub_total'];?>
      <?php endforeach; ?>
      <tr>
        <td colspan="7">Total</td>
        <td><?= $totallab   ?></td>
      </tr>
  </table><br>
<?php endif; ?>

<?php if ($detailradiologi): ?>
  <table border="1" cellpadding="5" cellspacing="1" style="font-size:10px; width:100%;" >
      <tr>
            <td colspan="8" style="">Pemeriksaan Radiologi</td>
      </tr>
      <tr>
        <td>No</td>
        <td>Tanggal Pemeriksaan</td>
        <td>Nama Pemeriksaan</td>
        <td>Tarif Satuan</td>
        <td>Cyto ?</td>
        <td>Tarif Satuan Cyto</td>
        <td>Qty</td>
        <td>Jumlah</td>
      </tr>
      <?php $counter=1; foreach ($detailradiologi as $value): ?>
      <tr>
        <td><?= $counter++ ?></td>
        <td><?= date('d F Y  H:i:s', strtotime($value['tgl_pelayanan'])) ?></td>
        <td><?= $value['tindakan_obat_nama']  ?></td>
        <td><?= $value['tarif_satuan']  ?></td>
        <td>
            <?php if ($value['tarifcyto_tindakan']): ?>
              Ya
            <?php else: ?>
              Tidak
            <?php endif; ?>
        </td>
        <td><?= $value['tarifcyto_tindakan']   ?></td>
        <td><?= $value['qty']  ?></td>
        <td><?= $value['sub_total']   ?></td>
      </tr>
      <?php  $totalradiologi = $totalradiologi+$value['sub_total'];?>
      <?php endforeach; ?>
      <tr>
        <td colspan="7">Total</td>
        <td><?= $totalradiologi   ?></td>
      </tr>
  </table><br>
<?php endif; ?>
