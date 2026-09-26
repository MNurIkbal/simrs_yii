<div class="col-md-12">
   <div class="panel panel-default">
      <div class="panel-heading">
         <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien'); ?></b></h6>
      </div>
      <div class="panel-body">
         <div class="form-group">
            <br>
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nomor pendaftaran") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp; <?= isset($detail['no_pendaftaran']) ? $detail['no_pendaftaran'] : '-' ?> </p>
               </div>
            </div>
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No rekam medik") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp;<?= isset($detail['no_rekam_medik']) ? $detail['no_rekam_medik'] : '-' ?> </p>
               </div>
            </div>
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis kelamin") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp;<?= isset($detail['jenis_kelamin']) ? $detail['jenis_kelamin'] : '-' ?> </p>
               </div>
            </div>
         </div>
         <div class="form-group">
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Cara bayar") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp; <?= isset($detail['carabayar_nama']) ? $detail['carabayar_nama'] : '-' ?> </p>
               </div>
            </div>
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal pendaftaran") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp;<?= isset($detail['tgl_pendaftaran']) ? date('d F Y H:i:s', strtotime($detail['tgl_pendaftaran'])) : '-' ?> </p>
               </div>
            </div>
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nama pasien") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp; <?= isset($detail['nama_pasien']) ? $detail['nama_pasien'] : '-' ?> </p>
               </div>
            </div>
         </div>
         <div class="form-group">
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Umur") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp; <?= isset($detail['umur']) ? $detail['umur'] : '-' ?> </p>
               </div>
            </div>
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Penjamin") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp;<?= isset($detail['penjamin_nama']) ? $detail['penjamin_nama'] : '-' ?> </p>
               </div>
            </div>
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No rujukan") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp;<?= isset($detail['no_rujukan']) ? $detail['no_rujukan'] : '-' ?> </p>
               </div>
            </div>
         </div>
         <div class="form-group">
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Asal rujukan") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp;<?= isset($detail['asalrujukan_nama']) ? $detail['asalrujukan_nama'] : '-' ?> </p>
               </div>
            </div>
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kelas pelayanan") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp; <?= isset($detail['kelaspelayanan_nama']) ? $detail['kelaspelayanan_nama'] : '-' ?> </p>
               </div>
            </div>
            <div class="col-md-4">
               <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter Perujuk") ?></b></label>
               <div class="col-sm-7">
                  <p><b>:</b>&nbsp;<?= isset($detail['dokter_perujuk']) ? $detail['dokter_perujuk'] : '-' ?> </p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>