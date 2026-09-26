<?php if($isReferred) : ?>
<div class="col-md-12 panel panel-default" id="informasi" style="margin-top:10px;">
   <div class="panel-heading">
      <h6 class="panel-title text-bold"><?= Yii::t('fe', 'Rujukan Pemeriksaan Laboratorium') ?>
            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
      </h6>
   </div>
   <?php foreach($data_rujukan as $value) : 
      $daftarTindakanNama = !empty($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : '-';
      if(is_array($daftarTindakanNama)){
         $daftarTindakanNama = implode(", ",$daftarTindakanNama);
      }
      ?>
   <div class="panel-body">
      <div class="col-md-4">
         <table class="table borderless" width="30%">
            <tr>
               <td width="50%" class="text-bold"><?= Yii::t('fe', 'Tanggal Rujukan')  ?></td>
               <td>: <?= !empty($value['tgl_rujukan']) ? date('d M Y H:i', strtotime($value['tgl_rujukan'])) : '-' ?></td>
            </tr>
            <tr>
               <td class="text-bold"><?= Yii::t('fe', 'Alasan Rujukan')  ?></td>
               <td>: <?= isset($value['alasandirujuk']) ? $value['alasandirujuk'] : '-' ?></td>
            </tr>
         </table>
      </div>
      <div class="col-md-4">
         <table class="table borderless" width="30%">
            <tr>
               <td width="45%" class="text-bold"><?= Yii::t('fe', 'Tujuan RS / Klinik Rujukan')  ?></td>
               <td>: <?= isset($value['rumahsakit_rujukan']) ? $value['rumahsakit_rujukan'] : '-' ?></td>
            </tr>
            <tr>
               <td class="text-bold"><?= Yii::t('fe', 'Disetujui Oleh')  ?></td>
               <td>: <?= isset($value['dokter_perujuk_nama']) ? $value['dokter_perujuk_nama'] : '-' ?></td>
            </tr>
         </table>
      </div>
      <div class="col-md-4">
         <table class="table borderless" width="30%">
            <tr >
               <td width="20%" class="text-bold"><?= Yii::t('fe', 'Pemeriksaan')  ?></td>
               <td>: <?=$daftarTindakanNama?></td>
            </tr>
         </table>
      </div>
   </div>
   <?php endforeach; ?>
</div>
<?php endif; ?>
