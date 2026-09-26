<?php
    use Doco\components\DocoConstants;
    use Doco\components\DocoHelpers;
?>

<style>
   td {
      vertical-align: top;
   }
   .fz-6 {
      font-size: 6px;
   }
   .fz-7 {
      font-size: 7px;
   }

   .fz-9 {
      font-size: 9px;
   }

   .fz-10 {
      font-size: 10px;
   }

   .fz-11 {
      font-size: 11px;
   }

   .fw-b {
      font-weight: bold;
      font-family: Arial, Helvetica, sans-serif;
   }
   .center {
      text-align : center;
   }

   .tglcetak {
      padding-bottom: 5px;
   }
</style>
<?php for ($i = 0; $i < $jumlah; $i++) { ?>
<?php
   $displayNama = DocoHelpers::cutSentence($nama_pasien, 20);
   $pos = ($i == 0) ? $margin_top_1 : $margin_top_2;
?>
<div style="background-color: white;" class="div-border-red">
   <div style="background-color: white; margin-left: 5%;margin-top: <?= $pos ?>" class="div-border-green label-size-half">
      <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
         <tbody>
            <tr>
               <td colspan="3" class="center"><span class="fz-7 fw-b">Breakfest/Lunch/Dinner</span></td>
            </tr>
            <tr>
               <td colspan="3" class="center tglcetak"><span class="fz-6 fw-b"><?= $tgl_cetak ?></span></td>
            </tr>
            <tr>
               <td colspan="1"><span class="fz-6 fw-b">Nama Lengkap</span></td>
               <td class="fz-6 fw-b"> : </td>
               <td class="fz-6 fw-b"><?= !empty($displayNama) ? $displayNama : '-' ?> </td>
            </tr>
            <tr>
               <td colspan="1"><span class="fz-6 fw-b">DOB</span></td>
               <td class="fz-6 fw-b"> : </td>
               <td class="fz-6 fw-b"><?= !empty($tgl) ? $tgl : '-' ?> </td>
            </tr>
            <tr>
               <td colspan="1"><span class="fz-6 fw-b">Umur</span></td>
               <td class="fz-6 fw-b"> : </td>
               <td class="fz-6 fw-b"><?= !empty($umur) ? $umur : '-' ?> </td>
            </tr>
            <tr>
               <td colspan="1"><span class="fz-6 fw-b">Jenis Kelamin</span></td>
               <td class="fz-6 fw-b"> : </td>
               <td class="fz-6 fw-b"><?= !empty($jk) ? $jk : '-' ?> </td>
            </tr>
            <tr>
               <td colspan="1"><span class="fz-6 fw-b">Agama</span></td>
               <td class="fz-6 fw-b"> : </td>
               <td class="fz-6 fw-b"><?= !empty($agama) ? $agama : '-' ?> </td>
            </tr>
            <tr>
               <td colspan="1"><span class="fz-6 fw-b">No MR</span></td>
               <td class="fz-6 fw-b"> : </td>
               <td class="fz-6 fw-b"><?= !empty($norm) ? $norm : '-' ?> </td>
            </tr>
            <tr>
               <td colspan="1"><span class="fz-6 fw-b">Ruangan/No Kamar</span></td>
               <td class="fz-6 fw-b"> : </td>
               <td class="fz-6 fw-b"><?= !empty($ruangan) ? $ruangan : '-' ?> </td>
            </tr>
            <tr>
               <td colspan="1"><span class="fz-6 fw-b">Diet</span></td>
               <td class="fz-6 fw-b"> : </td>
               <td class="fz-6 fw-b"><?= !empty($diet) ? $diet : '-' ?> </td>
            </tr>
            <tr>
               <td colspan="1"><span class="fz-6 fw-b">Catatan Diet</span></td>
               <td class="fz-6 fw-b"> : </td>
               <td class="fz-6 fw-b"><?= !empty($catatan) ? $catatan : '-' ?> </td>
            </tr>
         </tbody>
      </table>
      <?php if($jumlah - $i !== 1) { ?>
         <pagebreak>
      <?php } else { ?>
      <?php } ?>
   </div>
</div>

<?php } ?>
