<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-08 17:47:28
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 13:52:05
 */
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\web\View;
?>
<?=Html::hiddenInput('pos',$posisi, ['class'=>'posisi-tab'])?>
<?=Html::hiddenInput('pos',$data['no_masukpenunjang'], ['class'=>'id-penunjang'])?>
<?php 
$prefix = '';
$state = 0;
if($status == 483){
    $prefix = '-view';
}
$noRegis = $data['pendaftaran_id'];
?>
<?=Html::hiddenInput('state',$state, ['class'=>'state-tab'])?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Operasi'); ?></b></h6>
    </div>
    <div class="panel-body">
        <div class="row">
            <div id="tab-operasi">
            <?php 
            $i = 0;
            foreach($activeTab as $index => $item){
                $url = $item['source'];
                if($url == 'verifikasi-tagihan') {
                    $prefix = '';
                }
                if($url == 'cppt'){
                    $param_id = $id;
                }else{
                    $param_id = $data['pasienmasukpenunjang_id'];
                }
                $i++
                ?>
                <fieldset title="<?=$i?>" class="stepy-step" onmouseover="this.title='';">
                    <legend class="stepy-legend"><?=$item['title']?></legend>
                    <div class="content" data-source="<?=Url::to([$item['source'].$prefix, 'id'=>$param_id, 'last' => $item['source'] == $lastOperasi ? true : false ])?>" data-noReg = "<?=$noRegis;?>" data-status="true">
                    </div>
                    <!-- inputs -->
                </fieldset>
                <?php
            }
            ?>
            
            <?=Html::button("<b><i class='fa fa-floppy-o'></i></b> Simpan", ['class'=>'stepy-finish btn-save-post btn btn-xs btn-labeled btn-info'])?>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs('
var roleBatalBtn = "'. $roleBatalBtn .'";
', View::POS_END);
$this->registerJs($this->render('../js/_tab.js'), View::POS_END, 'js');
?>