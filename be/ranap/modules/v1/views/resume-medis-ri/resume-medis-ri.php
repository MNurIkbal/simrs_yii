<style>
   .tbl-borderless {
      border-collapse: collapse;
      font-size: 12px;
      font-family:Arial,Helvetica,sans-serif;
      word-wrap: break-all;
      table-layout: fixed;
   }
   .tbl-borderless thead th {
      border: 0px solid black;
      padding: 5px;
   }
   .tbl-borderless tbody td {
      border: 0px solid black;
      padding: 5px;
   }
   .tbl-borderless tfoot td {
      padding: 3px;
   }
</style>

<table width="80%" class="tbl-borderless">
   <tbody>
      <?php if(!empty($obat_dibawa_pulang)) : $no = 0;?>
         <?php foreach ($obat_dibawa_pulang as $key => $r_list) : $no++;
            $obatalkes_nama = [];
            $signa_nama = ''; 
            $catatan = ''; 
            $qty_reseptur = []; 
            foreach ($r_list as $key => $value) :
                if(!empty($value['obatalkes_nama'])) {
                    $obatalkes_nama[] = $value['obatalkes_nama'];
                    $qty =  !empty($value['qty_reseptur']) ? $value['qty_reseptur'] : '';
                    $satuan =  !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : '';
                    $qty_reseptur[] = $qty . ' ' . $satuan;
                }
                $signa_nama = !empty($value['signa_nama']) ? $value['signa_nama'] : $signa_nama;
                $catatan =  !empty($value['etiket']) ? $value['etiket'] : $catatan;
            endforeach;

            $obatalkes_nama = implode('<br>', $obatalkes_nama);
            $qty_reseptur = implode('<br>', $qty_reseptur);
         ?>
         <tr>
             <td style="vertical-align: top; text-align: left;"><?= $no; ?></td>
             <td style="vertical-align: top; text-align: left;">
                 <?= $obatalkes_nama; ?><br>
                 <?= $signa_nama; ?><br>
                 <?= $catatan; ?><br>
             </td>
             <td style="vertical-align: top; text-align: left;"><?= $qty_reseptur; ?></td>
         </tr>
         <?php endforeach; ?>
      <?php else: ?>
         <tr>
            <td colspan="3" style="text-align: left;">Pasien tidak memiliki obat untuk dibawa pulang!</td>
         </tr>
      <?php endif ?>
   </tbody>
</table>