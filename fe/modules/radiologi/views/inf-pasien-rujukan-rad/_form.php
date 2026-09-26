<?php 
use kartik\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin(
    [
        'action' => 'approve',
        'id' => 'form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]
) ?>

<div class="row">
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading">
            <h6 class="panel-title"><b><?= Yii::t('fe', 'Rencana Pemeriksaan Radiologi'); ?></b></h6>
         </div>
         <div class="panel-body">
            <div class="row">
               <div class="col-sm-6">
                  <?= $form->field($model, 'catatan_dokterpengirim')->textarea(["row"=>"6",'class' => 'catatan_dokter'])->label(Yii::t('fe', 'Catatan Dokter')) ?>
               </div>
            </div><br>
            <div class="row">
               <div class="col-md-12">
                  <table id="tableDetail" class="table table-striped table-condensed table-hover" style="width:100%">
                     <thead>
                           <tr class="bg-inverse">
                              <th width="1">No</th>
                              <th><?=\Yii::t("fe", "Jenis Pemeriksaan");?></th>
                              <th><?=\Yii::t("fe", "Nama Pemeriksaan");?></th>
                              <th><?=\Yii::t("fe", "Qty");?></th>
                              <th><?=\Yii::t("fe", "Cyto");?></th>
                              <th width="25%"><?=\Yii::t("fe", "Dokter");?></th>
                              <th><?=\Yii::t("fe", "Approve");?></th>
                           </tr>
                     </thead>
                     <tbody>
                           <tr>
                              <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                           </tr>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php ActiveForm::end(); ?>

