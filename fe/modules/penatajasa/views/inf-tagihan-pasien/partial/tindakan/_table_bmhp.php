<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\widgets\DepDrop;
?>

<div id="tabel-bmhp">
   <table class="table-header-bmhp table-striped table-condensed table-hover" style="width:100%">
      <thead>
         <tr class="bg-inverse">
            <th><?=\Yii::t("fe", "No");?></th>
            <th><?=\Yii::t("fe", "Tindakan");?></th>
            <th><?=\Yii::t("fe", "Depo");?></th>
            <th><?=\Yii::t("fe", "BMHP");?></th>
            <th><?=\Yii::t("fe", "Stok");?></th>
            <th><?=\Yii::t("fe", "Qty");?></th>
            <th><?=\Yii::t("fe", "Satuan");?></th>
            <th><?=\Yii::t("fe", "Harga Satuan");?></th>
            <th><?=\Yii::t("fe", "Ditagihkan");?></th>
            <th><?=\Yii::t("fe", "Subtotal");?></th>
            <th><?=\Yii::t("fe", "Aksi");?></th>
         </tr>
         <tr>
            <td>
               <span>#</span>
            </td>                 
            <td>
               <?= $form->field($model, 'tindakan_obat_id')->dropDownList([],[
                  'class' => 'select2',
                  'id' => 'tindakan_obat_id',
                  'prompt' => '— Pilih —'
                  ])->label(false);
               ?>

            </td>                   
            <td>
               <?= $form->field($model, 'depo_id')->dropDownList($listDepo,[
                  'class' => 'select2',
                  'id' => 'depo_id',
                  'prompt' => '— Pilih —'
                  ])->label(false);
               ?>
            </td>                   
            <td>
               <?= $form->field($model, 'obatalkes_id')->dropDownList([],[
                  'class' => 'select2',
                  'id' => 'obatalkes_id',
                  'prompt' => '— Pilih —'
                  ])->label(false);
               ?>
            </td>                   
            <td>
               <div><span id="stok_obat"> - </span></div>
            </td>                    
            <td>
               <?= $form->field($model, 'qty_obat', [])->textInput([
                  'placeholder' => Yii::t('fe', 'Qty'),
                  'id' => 'qty_obat',
                  'class' => 'form-control text-left doco-number',
                  'style'=>'width:50px',
               ])->label(false); 
               ?>
            </td>                    
            <td>
               <div><span id = "satuan_bmhp"> - </span></div>
               <!-- < ? = $form->field($model, 'satuan_id')->widget(DepDrop::classname(), [
                  'options'=>['id'=>'satuan_id', 'class' => 'select2'],
                  'data' => [],
                  'pluginOptions'=>[
                     'depends'=>['obatalkes_id'],
                     'initialize' => true,
                     'loadingText' => Yii::t('fe', 'Memuat...'),
                     'placeholder'=>'--Pilih--',
                     'url' => Url::to(['/penatajasa/end-point/get-satuan']),
                     'style'=>'width:50px',
                     
                  ],
                  ])->label(false); 
               ? >                -->
            </td>
            <td>
               <div>Rp <span id="satuan_obat"> - </span></div>
            </td>                    
            <td>
               <?= $form->field($model, 'is_ditagihkan')->checkbox([
                     'id' => 'is_ditagihkan',
                     'class' => 'styled',
               ])->label(false); ?>
            </td>
            <td>
               <div>Rp <span id="subtotal_obat"> - </span></div>
            </td>
            <td>
               <div class="btn-group pull-right">
                  <?= Html::Button(
                     '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                        [
                        'class' => 'addrow btn btn-info btn-labeled btn-xs btn-block btn-labeled',
                        'id' => 'simpan-obat-bmhp'
                  ]) ?>
               </div>
            </td>
         </tr>
      </thead>
      <tbody>
      </tbody>
   </table>

   <table id="table-list-bmhp" class="table table-striped table-condensed table-hover" style="width:100%">
      <tbody>
         <tr class="isi-table">
            <td class="text-center" colspan="6">
               <?=\Yii::t("fe", "No data available in table.");?>
            </td>
         </tr>
      </tbody>
   </table>
   <hr>
</div>