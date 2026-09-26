<?php

/**
 * @Author: Ilham pramono
 * @Date:   2021-08-05 15:24:43
 */

use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>

<style type="text/css">

.strikethrough {
  position: relative;
}
.strikethrough:before {
position: absolute;
content: "";
left: 0;
top: 50%;
right: 0;
border-top: 2px solid #666666!important;
border-color: inherit;
}

</style>
<div class="row">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Nursing Note')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-toolbar clearfix">
            <?php if($is_nurse){ ?>
            <?=DocoHelpers::generateToolbar([
                'add' => [
                    'attributes' => [
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-width' => '45%',
                        'action' => $url.'/create-nursing-note?id='.$pendaftaran_id.'&pasienadmisi_id='.$pasienadmisi_id,
                    ]
                ],
                'print' => [
                    'title' => Yii::t('fe', 'Cetak PDF'),
                    'icon' => 'fa fa-file-pdf-o',
                    'attributes' => [
                        'id'=>'cetak-pdf',
                        'data-options' => 'link',
                        'data-target' =>  $url.'/cetak-nursing-note?id='.$pendaftaran_id.'&pasienadmisi_id='.$pasienadmisi_id,
                        'target'=>'_blank',
                    ]
                ]
              
            ]);
            ?>
            <?php }?>
        </div>
       
       
        <div class="panel-body">
         
                 <table class="table table-bordered datatable-basic dataTable" style="width:100%" id="tabel-nursing-note">
                       <thead>
                            <tr class="bg-inverse">
                                <th><?=Yii::t('fe', 'No')?></th>
                                <th><?=Yii::t('fe', 'Tanggal')?></th>
                                <th><?=Yii::t('fe', 'Jam')?></th>
                                <th><?=Yii::t('fe', 'Kegiatan Perawat')?></th>
                                <th><?=Yii::t('fe', 'Catatan')?></th>
                                <th><?=Yii::t('fe', 'Nama Pegawai')?></th>
                                <th><?=Yii::t('fe', 'Aksi')?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <br>
                    <br>
 
        </div>
    </div>
</div>

<?php 

$this->registerJs("
    var pendaftaran_id = '".$pendaftaran_id."';
    var pasienadmisi_id = '".$pasienadmisi_id."';
    var url = '".$url."';
  
  ".$this->render('js/index.js'), View::POS_END, 'js');

?>