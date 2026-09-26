<?php

/**
** @author yaya
**/

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Jenis Kertas');
$this->params['breadcrumbs'][] = ['label' => 'Dcms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">

page {
  background: white;
  display: block;
  margin: 0 auto;
  margin-bottom: 0.5cm;
  box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
}
page[size="A4"] {  
  width: 10.5cm;
  height: 14.85cm; 
}
/*page[size="A4"] {  
  width: 21cm;
  height: 29.7cm; 
}*/
/*page[size="A4"][layout="portrait"] {
  width: 29.7cm;
  height: 21cm;  
}
page[size="A3"] {
  width: 29.7cm;
  height: 42cm;
}
page[size="A3"][layout="portrait"] {
  width: 42cm;
  height: 29.7cm;  
}
page[size="A5"] {
  width: 14.8cm;
  height: 21cm;
}
page[size="A5"][layout="portrait"] {
  width: 21cm;
  height: 14.8cm;  
}*/
@media print {
  body, page {
    margin: 0;
    box-shadow: 0;
  }
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'save',
                        'back'
                    ]);?>
            
            </div>


            <div class="panel-body">
                <div class="col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Pengaturan Kertas</b></h6>
                        </div>
                        <div class="panel-body">
                            <?php 
                                $form = ActiveForm::begin([
                                    'id' => 'ajax-form', 
                                    'enableAjaxValidation'=>false, 
                                    'enableClientValidation'=>false,
                                    'type' => ActiveForm::TYPE_HORIZONTAL,
                                    'formConfig' => [
                                        'labelSpan' => 3, 
                                        'deviceSize' => ActiveForm::SIZE_SMALL
                                    ],
                                    'options' => [

                                    ]
                                ]); 
                            ?>
                           <?= $form->field($model, 'kertas_kode', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->textInput([
                                                'placeholder' => $model->getAttributeLabel('kertas_kode'),
                                                'class' => 'form-control input-sm',
                                                'autocomplete' => "off",
                                        ]); ?>
                            <?= $form->field($model, 'kertas_nama', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->textInput([
                                                'placeholder' => $model->getAttributeLabel('kertas_nama'),
                                                'class' => 'form-control input-sm',
                                                'autocomplete' => "off",
                                        ]); ?>
                            <?= $form->field($model, 'panjang', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-5'
                                            ],
                                        'addon' => ['append' => [
                                                        'content' => 'CM']]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('panjang'),
                                            'class' => 'form-control input-sm on-change',
                                            'autocomplete' => "off",
                                            'data-type' => 'panjang',
                                            'type' => 'number',
                                            'min' => 0,
                                            'value' => (empty($model->panjang) ? 29 : $model->panjang)
                                        ]); ?>
                            <?= $form->field($model, 'lebar', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-5'
                                            ],
                                        'addon' => ['append' => [
                                                        'content' => 'CM']]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('lebar'),
                                            'class' => 'form-control input-sm on-change',
                                            'autocomplete' => "off",
                                            'data-type' => 'lebar',
                                            'type' => 'number',
                                            'min' => 0,
                                            'value' => (empty($model->lebar) ? 21 : $model->lebar)
                                        ]); ?>
                            <?= $form->field($model, 'batas_kiri', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-5'
                                            ],
                                        'addon' => ['append' => [
                                                        'content' => 'CM']]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('batas_kiri'),
                                            'class' => 'form-control input-sm on-change',
                                            'autocomplete' => "off",
                                            'data-type' => 'batas-kiri',
                                            'type' => 'number',
                                            'min' => '0'
                                        ]); ?>
                            <?= $form->field($model, 'batas_kanan', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-5'
                                            ],
                                        'addon' => ['append' => [
                                                        'content' => 'CM']]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('batas_kanan'),
                                            'class' => 'form-control input-sm on-change',
                                            'autocomplete' => "off",
                                            'data-type' => 'batas-kanan',
                                            'type' => 'number',
                                            'min' => '0'
                                        ]); ?>
                            <?= $form->field($model, 'batas_atas', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-5'
                                            ],
                                        'addon' => ['append' => [
                                                        'content' => 'CM']]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('batas_atas'),
                                            'class' => 'form-control input-sm on-change',
                                            'autocomplete' => "off",
                                            'data-type' => 'batas-atas',
                                            'type' => 'number',
                                            'min' => '0'
                                        ]); ?>
                            <?= $form->field($model, 'batas_bawah', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-5'
                                            ],
                                        'addon' => ['append' => [
                                                        'content' => 'CM']]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('batas_bawah'),
                                            'class' => 'form-control input-sm on-change',
                                            'data-type' => 'batas-bawah',
                                            'autocomplete' => "off",
                                            'type' => 'number',
                                            'min' => '0'
                                        ]); ?>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Review Kertas</b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-styled-left alert-styled-custom alert-arrow-left alert-info alert-bordered">
                                <span class="text-semibold">Tampilan kertas berskala 1:2</span>
                            </div>
                            <center><table style="font-weight: bold;">
                                <tr>
                            <td><center><div class="panjang">  </div></center><td>
                            <td><center><div class="lebar"></center></div>
                            <page size="A4" id="layout-review"></page></td>
                        </tr>
                        </table></center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs('
    var _config = {
        ratio : 1,
        layout : {
            panjang : 0,
            lebar : 0,
            batas_atas : 0,
            batas_bawah : 0,
            batas_kanan : 0,
            batas_kiri : 0
        }
    };

    var _renderView = function () {
        var _target = $("#layout-review");
        var _td = _target.closest("td");
        var _setting = _config.layout;
        var _styleKertas = `width:${_setting.lebar/2}cm;height:${_setting.panjang/2}cm;`
        _target.attr({
            style : _styleKertas
        });
    }

    var _ready = function (event) {
        $(".on-change").trigger("click");
    }

    var _change = function (event) {
        event.preventDefault();
        var _type = $(this).data("type");
        var _value = $(this).val();
        _value = parseInt(_value) / _config.ratio;
        switch (_type) {
            case \'panjang\' :
                if (isNaN(_value)) {
                    _value = 0;
                }
                if (parseInt(_value) != 0 ) {
                    _config.layout.panjang = _value;
                    $(".panjang").html(_config.layout.panjang+"cm");
                }
                break;
            case \'lebar\' :
                if (isNaN(_value)) {
                    _value = 0;
                }
                if (parseInt(_value) != 0 ) {
                    _config.layout.lebar = _value;
                    $(".lebar").html(_config.layout.lebar+"cm");
                }
                break;
            case \'batas-atas\' :
                _config.layout.batas_atas = _value;
            break;
            case \'batas-bawah\' :
                _config.layout.batas_bawah = _value;
            break;
            case \'batas-kanan\' :
                _config.layout.batas_kanan = _value;
            break;
            case \'batas-kiri\' :
                _config.layout.batas_kiri = _value;
            break;
        }
        _renderView();
    }

    $("#ajax-form").docoForm("submit",{
        success : function (data) {
            setTimeout(function () {
                window.location.href = "/dcms/jenis-kertas"
            },1000);
        }
    });

    $(function(){
        $(document).on("keyup change click scroll",".on-change",_change);
        _ready();
    });
',View::POS_END,'document-tercetak');