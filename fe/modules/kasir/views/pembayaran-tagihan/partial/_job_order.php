<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use app\components\DocoHelpers;
?>
<div class="modal-header bg-inverse">
    <h5 class="modal-title"><strong><?= $title ?></strong></h5>
</div>

<style type="text/css">
.dataTables_scroll {
   /* height: 441px !important;
   max-height: 441px !important; */
   height: 330px !important;
   max-height: 330px !important;
   position: relative !important;
}

</style>
<div class="modal-body">
   <div class="panel-body">
      <table id="table-job-order" class="table table-striped table-condensed table-hover" style="width: 100%">
         <thead>
            <tr class="bg-inverse">
               <th width="1%">No</th>
               <th><?= Yii::t('fe', 'Tanggal') ?></th>
               <th><?= Yii::t('fe', 'Instalasi/Ruangan') ?></th>
               <th><?= Yii::t('fe', 'Tindakan/Obat') ?></th>
               <th><?= Yii::t('fe', 'Qty') ?></th>
               <th><?= Yii::t('fe', 'Instalasi Tujuan') ?></th>
               <th><?= Yii::t('fe', 'Pegawai Pengorder') ?></th>
            </tr>
         </thead>
         <tbody>
         </tbody>
      </table>
      <div style="text-align: right;">
         <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm btn-kembali']); ?>
      </div>
   </div>
</div>

<?php
$this->registerJs('
var table;
var pendaftaran_id = "' . $id . '";

', View::POS_END, 'b-index');
$this->registerJs($this->render('../js/_job_order.js'), View::POS_END);
?>
