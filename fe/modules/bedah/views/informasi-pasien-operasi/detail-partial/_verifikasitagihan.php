<?php

use yii\helpers\Html;
use yii\web\View;
use app\components\DocoHelpers;
use app\components\DocoConstants;
?>
<style>
   tr.odd td:first-child,
   tr.even td:first-child {
      padding-left: 4em;
   }

   a.btn-batal-verifikasi.btn.btn-xs.btn-labeled.btn-danger[disabled] {
      pointer-events: none;
      cursor: no-drop;
   }
</style>
<div class="row">
   <div class="col-md-12">
      <legend><?= Yii::t('fe', 'Verifikasi Tagihan') ?></legend>
   </div>
</div>
<div class="row">
   <input type="hidden" name="id" id="penunjang-id" value="<?= $pasienpenunjangId ?>">
   <div class="col-sm-6" style="font-size:14px;">
      <p style="font-weight: bold;">Total Tagihan : <span class="total_tagihan" style="font-weight: bold;"></span></p>
   </div>
   <?php if ($status_periksa != DocoConstants::VAR_P_SdhO) : ?>
    <div class="col-sm-6 text-right">
        <?= Html::button("<b><i class='fa fa-edit'></i></b> " . Yii::t('fe', 'Edit'), ['class' => 'btn btn-xs btn-labeled btn-info edit-intra-operasi']) ?>
        <?= Html::button("<b><i class='fa fa-floppy-o'></i> </b>" . Yii::t('fe', 'Verifikasi'), ['class' => 'btn btn-xs btn-labeled btn-info submit-verifikasi']) ?>
    </div>
   <?php endif; ?><br><br>
   <div class="col-md-12">
      <div class="advanced-filter">
      </div>
      <table class="table table-condensed" id="table-verifikasi" style="width:100%">
         <thead>
            <tr class="bg-inverse">
               <th width="1"><?= Yii::t('fe', 'No') ?></th>
               <th><?= Yii::t('fe', 'Tindakan Operasi') ?></th>
               <th><?= Yii::t('fe', 'Kegiatan Operasi') ?></th>
               <th><?= Yii::t('fe', 'Golongan Operasi') ?></th>
               <th><?= Yii::t('fe', 'Nama Pegawai') ?></th>
               <th><?= Yii::t('fe', 'Posisi Tim') ?></th>
               <th><?= Yii::t('fe', 'Quantity') ?></th>
               <th><?= Yii::t('fe', 'Harga') ?></th>
               <th><?= Yii::t('fe', 'Cyto') ?></th>
               <th><?= Yii::t('fe', 'Penyulit') ?></th>
               <th><?= Yii::t('fe', 'Persentase') ?></th>
               <th><?= Yii::t('fe', 'Sub Total') ?></th>
               <th><?= Yii::t('fe', 'Pegawai Input') ?></th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td class="text-center" colspan="12"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr>
         </tbody>
      </table>
   </div>
</div>

<?php
$this->registerJs('
var tableVerif;
var pasienpenunjangId = '.$pasienpenunjangId.';
var _statusPeriksa = ' . $status_periksa . ';
var _isStopAkomodasi = "' . $isStopAkomodasi . '";
', View::POS_END, 'b-index');

$this->registerJs($this->render('../js/verifikasitagihan.js'), View::POS_END, 'js')
?>