<?php
/**
 * 
 * Author: Dede Herdiana 
 * 
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\View;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DHtml;
use kartik\widgets\ActiveForm;

$this->title = DHtml::getTitleMenu('Form Tipe Diskon');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            
            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'class' => 'sp',
                                'data-options' => 'click',
                                'data-render' => 'tipe-diskon',
                                'data-tab' => 'tab-km',
                                'data-content' => 'content-td',
                                'id' => 'btn-back',
                            ]
                        ]
                    ]);?>
                </div>
            </div>
            <div class="panel-body">
                
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'tipe-diskon-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'role' => 'form',
                        ]
                    ]);
                ?>
                    
                <div class="col-md-12">
                        <?php
                            if ($is_edit) {
                                ?>
                                <div id="edit-tindakan">
                                    <?=Html::activeHiddenInput($model, 'tipediskon_id',[
                                        'class' => 'tipediskon-id'
                                    ])?>
                                </div>
                                <?php
                            }
                    ?>
                    <?= $form->field($model, 'tipediskon_nama', [
                        'labelOptions' => ['class' => 'text-left'],
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-qw',
                                'wrapper' => 'col-md-4'
                        ],
                        ])->textInput([
                            'placeholder' => Yii::t('fe', 'Tipe Diskon'),
                            'class' => 'form-control input-sm',
                            'autocomplete' => "off",
                            'id' => 'tipediskon_nama',
                        ])->label(Yii::t('fe', 'Tipe Diskon :'),[
                            'class'=>'text-left control-label col-md-1'
                            ]); 
                    ?>
                    <?=
                        $form->field($model, 'is_active', [
                            'labelOptions' => ['class' => 'text-left'],
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-md-1',
                                    'wrapper' => 'col-md-4'
                            ],
                        ])->checkbox([
                            'label' => 'Aktif',
                            'id' => 'is_active',
                            'class'=>'text-left control-label'
                        ])->label(Yii::t('fe', 'Status :'),[
                            'class'=>'text-left control-label col-md-1'
                            ]);
                    ?>
                    <br><br>
                </div>

                <div class="tabbable">
                    <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                        <li class="active" id="tab-kelas" data-id = ""><a href="#view-kelas" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Kelas')?></strong></a></li>
                        <li class="" id="tab-kategori"><a href="#view-kategori" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Kategori Tindakan')?></strong></a></li>
                        <li class="" id="tab-detail"><a href="#view-detail" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Detail Tindakan')?></strong></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="view-kelas">
                            <div id="content-kelas" data-jenislayananid="<?=DocoHelpers::encrypt(DocoConstants::TD_KELAS);?>">  </div>
                        </div>
                        <div class="tab-pane" id="view-kategori">
                            <div id="content-kategori" data-jenislayananid="<?=DocoHelpers::encrypt(DocoConstants::TD_KELOMPOK);?>">  </div>
                        </div>
                        <div class="tab-pane" id="view-detail">
                            <div id="content-detail" data-jenislayananid="<?=DocoHelpers::encrypt(DocoConstants::TD_TINDAKAN);?>" >  </div>
                        </div>
                    </div>
                </div>
                <!-- <hr> -->
                <div class="col-md-12">
                <!-- <button type="button" class="btn btn-info btn-labeled btn-xs" style="float:right;" data-options="click" id="btn-simpan-all"><b><i class="fa fa-floppy-o"></i></b>Simpan</button> -->
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
var action = "'.$action.'";
var is_edit = "'.$is_edit.'";
var tipediskon_id = null;

if(is_edit){
    tipediskon_id = $(".tipediskon-id").val();
}


', View::POS_END, 'e-index');
$this->registerJs($this->render('js/form.js'), View::POS_END);

?>