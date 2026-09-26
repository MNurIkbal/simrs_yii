<?php use yii\web\View; ?>
<hr>
<center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
<div class="progress" style="margin-left: 12px;">
   <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
   <span class="label-persentase">0</span>%</div>
</div>
<span class="help-block label-progress" style="margin-left: 12px;"></span>
<br><br>
<div class="table-wrapper table-scroll-x">
   <table id="detail-mcu" class="table table-condensed table-hover" style="width:100%;">
      <thead>
         <tr class="bg-inverse">
            <th width="1"></th>
            <th width="1"><?= Yii::t('fe', 'No') ?></th> 
            <th style="width: 300px !important;"><?= Yii::t('fe', 'Data Pasien') ?></th> 
            <th><?= Yii::t('fe', 'Status') ?></th> 
            <th><?= Yii::t('fe', 'No Pendaftaran') ?></th> 
            <th><?= Yii::t('fe', 'Tanggal Pendaftaran') ?></th> 
            <th><?= Yii::t('fe', 'Nomor Invoice') ?></th> 
         </tr>
      </thead>
      <tbody></tbody>
   </table>
</div>

<?php 
$this->registerJs('
var _status_reservasi = '.json_encode($status_reservasi).';
var no_order = "'.$no_order.'";
const _token = "'.$token.'";
', View::POS_END, 'b-index');
$this->registerJs($this->render('_listPasien.js'), View::POS_END);
?>