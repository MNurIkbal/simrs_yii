<style>
   .tbl-bordered {
      border-collapse: collapse;
      font-size: 12px;
      font-family:Arial,Helvetica,sans-serif;
      word-wrap: break-all;
      table-layout: fixed;
   }
   .tbl-bordered thead th {
      border: 1px solid black;
      padding: 5px;
   }
   .tbl-bordered tbody td {
      border: 1px solid black;
      padding: 5px;
   }
   .tbl-bordered tfoot td {
      padding: 3px;
   }
</style>

<table width="100%" class="tbl-bordered">
   <thead>
      <tr>
         <th>No</th>
         <th>Nama Obat</th>
         <th>Jumlah</th>
         <th>Dosis</th>
         <th>Cara Pemberian</th>
      </tr>
   </thead>
   <tbody>
      <?php $no = 0; if(!empty($obat_dibawa_pulang)) : ?>
         <?php foreach ($obat_dibawa_pulang as $key => $value) : 
            $no++;
            $obatalkes_nama = !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '-';
            $qty_reseptur = !empty($value['qty_reseptur']) ? $value['qty_reseptur'] : '-';
            $signa_nama = !empty($value['signa_nama']) ? $value['signa_nama'] : '-';
            $satuan_kecil = !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : '-';
            $nama_rute = !empty($value['nama_rute']) ? $value['nama_rute'] : ' - ';
         ?>
         <tr>
            <td style="text-align: center;"><?= $no ?></td>
            <td><?= $obatalkes_nama ?></td>
            <td><?= $qty_reseptur.' '.$satuan_kecil ?></td>
            <td><?= $signa_nama ?></td>
            <td><?= $nama_rute ?></td>
         </tr>
         <?php endforeach; ?>
      <?php else: ?>
         <tr>
            <td style="text-align: center;">-</td>
            <td colspan="4" style="text-align: center;">data tidak ditemukan!</td>
         </tr>
      <?php endif ?>
   </tbody>
</table>
