<div class="col-md-12 panel panel-default" id="informasi" style="margin-top:10px;">
   <div class="panel-heading">
      <h6 class="panel-title text-bold"><?= Yii::t('fe', 'Rujukan Pemeriksaan Radiologi') ?>
            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
      </h6>
   </div>
   <div class="panel-body">
      <div class="col-md-4">
         <table class="table borderless" width="50%">
            <tr>
               <td class="text-bold"><?= Yii::t('fe', 'Tanggal Rujukan')  ?></td>
               <td>: <?= isset($hasilRad['tgldirujuk']) ? date('d M Y H:i', strtotime($hasilRad['tgldirujuk'])) : '-' ?></td>
            </tr>
            <tr>
               <td class="text-bold"><?= Yii::t('fe', 'Alasan Rujukan')  ?></td>
               <td>: <?= isset($hasilRad['alasandirujuk']) ? $hasilRad['alasandirujuk'] : '-' ?></td>
            </tr>
         </table>
      </div>
      <div class="col-md-4">
         <table class="table borderless" width="50%">
            <tr>
               <td class="text-bold"><?= Yii::t('fe', 'RS Tujuan / Klinik Rujukan')  ?></td>
               <td>: <?= isset($hasilRad['rumahsakit_rujukan']) ? $hasilRad['rumahsakit_rujukan'] : '-' ?></td>
            </tr>
            <tr>
               <td class="text-bold"><?= Yii::t('fe', 'Disetujui Oleh')  ?></td>
               <td>: <?= isset($hasilRad['pegawai_nama_perujuk']) ? $hasilRad['pegawai_nama_perujuk'] : '-' ?></td>
            </tr>
         </table>
      </div>
   </div>
</div>