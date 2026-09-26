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
      <th><b>No</b></th>
      <th><b>Diagnosa Keperawatan</b></th>
      <th><b>Tujuan Terukur</b></th>
      </tr>
   </thead>
   <tbody>
      <?php $no = 0; if(!empty($diagnosa_keperawatan)) : ?>
         <?php foreach ($diagnosa_keperawatan as $key => $value) : 
            $no++;
            $diagnosa_keperawatan = !empty($value['diagnosa_keperawatan']) ? $value['diagnosa_keperawatan'] : '';
            $tujuan_terukur = !empty($value['tujuan_terukur']) ? $value['tujuan_terukur'] : '';
         ?>
         <tr>
            <td style="text-align: center;"><?= $no ?></td>
            <td><?= $diagnosa_keperawatan ?></td>
            <td><?= $tujuan_terukur ?></td>
         </tr>
         <?php endforeach; ?>
      <?php endif ?>
   </tbody>
</table>
