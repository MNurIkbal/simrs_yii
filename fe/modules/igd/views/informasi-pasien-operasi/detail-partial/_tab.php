<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-08 17:47:28
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 13:52:05
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>
<?=Html::hiddenInput('pos',$posisi, ['class'=>'posisi-tab'])?>
<?php 
$prefix = '';
$state = 0;
if($status == 483){
    $prefix = '-view';
    $state = 1;
}
?>
<?=Html::hiddenInput('state',$state, ['class'=>'state-tab'])?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Operasi'); ?></b></h6>
    </div>
    <div class="panel-body">
        <div class="row">
            <div id="tab-operasi">
            <fieldset title="1" class="stepy-step" onmouseover="this.title='';">
                <legend class="stepy-legend"><?=Yii::t('fe', 'Intra operasi')?></legend>
                <div class="content" data-source="<?=Url::to(['intra-operasi'.$prefix, 'id'=>$data['pasienmasukpenunjang_id']])?>" data-status="true">
                    
                </div>
                <!-- inputs -->
            </fieldset>
            <fieldset title="2" class="stepy-step" onmouseover="this.title='';">
                <legend class="stepy-legend"><?=Yii::t('fe', 'Post operasi')?></legend>
                <div class="content" data-source="<?=Url::to(['post-operasi'.$prefix, 'id'=>$data['pasienmasukpenunjang_id']])?>" data-status="true">
                    
                </div>
                <!-- inputs -->
            </fieldset>
            <?php /* jangan dulu dihapus ya
            <fieldset title="1" class="form-pra-operasi stepy-step disabled" onmouseover="this.title='';">
                <legend class="stepy-legend"><?=Yii::t('fe', 'Pra operasi')?></legend>
                <div class="content" data-source="<?=Url::to(['pra-operasi'])?>" data-status="true">
                    
                </div>
                <!-- inputs -->
            </fieldset>
            <fieldset title="2" class="form-checklist-operasi stepy-step" onmouseover="this.title='';">
                <legend class="stepy-legend"><?=Yii::t('fe', 'Checklist persiapan operasi')?></legend>
                <div class="content" data-source="<?=Url::to(['checklist-operasi'])?>" data-status="true">
                    
                </div>
                <!-- inputs -->
            </fieldset>
            <fieldset title="3" class="stepy-step" onmouseover="this.title='';">
                <legend class="stepy-legend"><?=Yii::t('fe', 'Intra operasi')?></legend>
                <div class="content" data-source="<?=Url::to(['intra-operasi'])?>" data-status="true">
                    
                </div>
                <!-- inputs -->
            </fieldset>
            <fieldset title="4" class="stepy-step" onmouseover="this.title='';">
                <legend class="stepy-legend"><?=Yii::t('fe', 'Post operasi')?></legend>
                <div class="content" data-source="<?=Url::to(['post-operasi'])?>" data-status="true">
                    
                </div>
                <!-- inputs -->
            </fieldset>
            <fieldset title="5" class="stepy-step" onmouseover="this.title='';">
                <legend class="stepy-legend"><?=Yii::t('fe', 'Pemeriksaan jaringan')?></legend>
                <div class="content" data-source="<?=Url::to(['periksa-jaringan'])?>" data-status="false">
                    
                </div>
                <!-- inputs -->
            </fieldset>
            <fieldset title="6" class="stepy-step" onmouseover="this.title='';">
                <legend class="stepy-legend"><?=Yii::t('fe', 'Surgical safety checklist')?></legend>
                <div class="content" data-source="<?=Url::to(['surgical-safety'])?>" data-status="false">
                    
                </div>
                <!-- inputs -->
            </fieldset>
            <fieldset title="7" class="stepy-step" onmouseover="this.title='';">
                <legend class="stepy-legend"><?=Yii::t('fe', 'Surgical record')?></legend>
                <div class="content" data-source="<?=Url::to(['surgical-record'])?>" data-status="true">
                    
                </div>
                <!-- inputs -->
            </fieldset>
            */ ?>
            <?=Html::button("<b><i class='fa fa-floppy-o'></i></b> Simpan", ['class'=>'stepy-finish btn-save-post btn btn-xs btn-labeled btn-info'])?>
            </div>
        </div>
    </div>
</div>
<?php 

$this->registerJs($this->render('../js/_tab.js'), View::POS_END, 'js');

?>