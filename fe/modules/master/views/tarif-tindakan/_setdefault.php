<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-10 13:41:10
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-10 14:59:07
 */

    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
    use yii\widgets\ActiveForm;
    use app\components\DocoHelpers;
    use kartik\widgets\DepDrop;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'simpan' => [
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-save',
                            'attributes' => [
                                'class'=>'spa',
                                'data-type'=>'save',
                                'data-options'=>'click',
                                'data-target' => 'set-default-form',
                                'data-content' => 'tab-tarif',
                                'id' => 'btn-save'
                            ]
                        ],
                        'reset'=>[
                            'attributes'=>[
                                'data-parent'=>'#set-default-form',
                            ]
                        ],
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'class' => 'spa data-kembali',
                                'data-options' => 'click',
                                'data-content'=>'content-tarif',
                                'data-url' => '/master/tarif-tindakan/tarif',
                            ]
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <?php 
                $form = ActiveForm::begin([
                        'id' => 'set-default-form', 
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'options' => [
                                'class' => 'form-horizontal', 
                                'role' => 'form'
                            ],
                        ]); 
                ?>
                <div class="row">
                    <div class="col-md-5">
                        <h3><?=Yii::t('fe','Semua tarif dengan')?></h3>
                        <div class="form-group required">
                            <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Cara bayar')?></label>
                            <div class="col-lg-8">
                                <?=Html::activeDropDownList($model,'carabayar_awal', 
                                    $additional_data['carabayar'], 
                                    [
                                        'id'=>'carabayar-awal',
                                        'class'=>'form-control select2 dep-to-child', 
                                        'prompt'=>'',
                                        'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/tarif-tindakan/get-penjamin',
                                        'data-depend_id' => 'penjamin-awal',
                                        'data-depend_prompt' => '',
                                        'data-storage' => 'penjamin',
                                        'data-key' => 'penjamin_id',
                                    ])
                                ?>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Penjamin')?></label>
                            <div class="col-lg-8">
                                <?=Html::activeDropDownList($model,'penjamin_awal', 
                                        $penjamin, 
                                        [
                                            'id'=>'penjamin-awal',
                                            'class'=>'form-control select2 dep-to-parent', 
                                            'prompt'=>'',
                                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/tarif-tindakan/get-carabayar', 
                                            'data-depend_id' => 'carabayar-awal',
                                        ])
                                ?>
                            </div>
                        </div>
                        <h3><?=Yii::t('fe','Set default as')?></h3>
                        <div class="form-group required">
                            <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Cara bayar')?></label>
                            <div class="col-lg-8">
                                <?=Html::activeDropDownList($model,'carabayar_tujuan', 
                                    $additional_data['carabayar'], 
                                    [
                                        'id'=>'carabayar-tujuan',
                                        'class'=>'form-control select2 dep-to-child', 
                                        'prompt'=>'',
                                        'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/tarif-tindakan/get-penjamin',
                                        'data-depend_id' => 'penjamin-tujuan',
                                        'data-depend_prompt' => '',
                                        'data-storage' => 'penjamin',
                                        'data-key' => 'penjamin_id',
                                    ])
                                ?>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Penjamin')?></label>
                            <div class="col-lg-8">
                                <?=Html::activeDropDownList($model,'penjamin_tujuan', 
                                        $penjamin, 
                                        [
                                            'id'=>'penjamin-tujuan',
                                            'class'=>'form-control select2 dep-to-parent', 
                                            'prompt'=>'',
                                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/tarif-tindakan/get-carabayar', 
                                            'data-depend_id' => 'carabayar-tujuan',
                                        ])
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#set-default-form').docoForm('submit',{
        success : function(data) {
            $('.data-kembali').click();
        }
    });
</script>

<?php 

$this->registerJs("
    $(document).ready(function(){
        // save into localStorage
        localStorage.clear();
        localStorage.setItem('penjamin', '".json_encode($penjamin)."');
    })
    ", View::POS_END, 'js');

?>