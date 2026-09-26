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
use dosamigos\ckeditor\CKEditor;
use kartik\widgets\DepDrop;

$this->title = \Yii::t('fe', $this->context->_title);
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
                <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                </div>
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
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
                <div class="col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Pengaturan Dokumen</b></h6>
                        </div>
                        <div class="panel-body">
                           <?= $form->field($model, 'kode_doc', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->textInput([
                                                'placeholder' => $model->getAttributeLabel('kode_doc'),
                                                'class' => 'form-control input-sm',
                                                'autocomplete' => "off",
                                        ]); ?>
                           <?= $form->field($model, 'nama_doc', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->textInput([
                                                'placeholder' => $model->getAttributeLabel('nama_doc'),
                                                'class' => 'form-control input-sm',
                                                'autocomplete' => "off",
                                        ]); ?>
                            <?= $form->field($model, 'kertas_id', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->dropDownList($jenisKertas,['class' => 'select2']); ?>
                            <?= $form->field($model, 'docheader_id', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->dropDownList($docHeader,['class' => 'select2']); ?>
                            <?= $form->field($model, 'docfooter_id', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->dropDownList($docFooter,['class' => 'select2']); ?>
                            <?= $form->field($model, 'modul_id', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->dropDownList($listApi,[
                                            'class' => 'select2',
                                            'id' => 'modul_id',
                                            'prompt' => Yii::t('fe',"--Pilih--")
                                        ]); ?>
                            <?= $form->field($model, 'menu_id', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->widget(DepDrop::classname(), [
                                            'options'=>[
                                                'id'=>'menu_id',
                                                'class' => 'select2',
                                                'data-value' => $model->menu_id,
                                            ],
                                            'pluginOptions'=>
                                            [
                                                'depends'=> [
                                                    'modul_id'
                                                ],
                                                'placeholder'=>'Select...',
                                                'url'=>Url::to(['/dcms/dokumen-tercetak/get-controller'])
                                            ],
                                            'pluginEvents' => [
                                                'depdrop:afterChange' => "function (event, id, value, jqXHR, textStatus) {
                                                    var _data = textStatus.responseJSON;
                                                    var _value = $('#menu_id').attr('data-value');
                                                    $('#menu_id').val(_value).trigger('depdrop:change');
                                                    table.draw()
                                                    if (typeof _data != 'undefined') {
                                                        dataCollect = _data.data_collect;
                                                    } else {
                                                        dataCollect = {};
                                                    }
                                                }"
                                            ]
                                        ]);
                            ?>
                            <?= $form->field($model, 'sub_menu_id', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->widget(DepDrop::classname(), [
                                            'options'=>[
                                                'id'=>'sub_menu_id',
                                                'class' => 'select2',
                                                'data-value' => $model->sub_menu_id
                                            ],
                                            'pluginOptions'=>
                                            [
                                                'depends'=> [
                                                    'menu_id'
                                                ],
                                                'placeholder'=>'Select...',
                                                'url'=>Url::to(['/dcms/dokumen-tercetak/get-dokumen'])
                                            ],
                                            'pluginEvents' => [
                                                'depdrop:afterChange' => "function (event, id, value, jqXHR, textStatus) {
                                                    var _value = $('#sub_menu_id').attr('data-value');
                                                    $('#sub_menu_id').val(_value);
                                                    $('#sub_menu_id').trigger('change')
                                                }"
                                            ]
                                ]);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Konten</b></h6>
                        </div>
                        <div class="panel-body">
                            <center>
                                <?= $form->field($model, 'docbody_text')->widget(CKEditor::className(), [
                                        'options' => ['rows' => 30],
                                        'preset' => 'custom',
                                         'clientOptions' => [
                                              'extraPlugins' => '',
                                              'height' => 400,
                                              'width' => 650,
                                              'filebrowserUploadUrl' => '/master/header-kertas/uploads',
                                          ]
                                    ])->label(false) ?>
                            </center>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Informasi Konten</b></h6>
                        </div>
                        <div class="panel-body">
                            <table id="table-dokumen" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Attribute");?></th>
                                        <th><?=\Yii::t("fe", "Fungsi");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs('
    var table;
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

    $("#sub_menu_id").on("change",function (event) {
        event.preventDefault();
        var _value = $(this).val();
        if (_value) {
            $.ajax({
                url: "/dcms/dokumen-tercetak/set-session",
                type: "POST",
                dataType : "json",
                data : {id : _value},
                success : function (data) {
                    table.draw()
                }
            });
        }
    });

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
        setTimeout(function () {
            $(\'#modul_id\').trigger(\'depdrop:change\');
        },1);
    }

    var _change = function (event) {
        event.preventDefault();
        var _type = $(this).data("type");
        var _value = $(this).val();
        _value = parseInt(_value) / _config.ratio;
        switch (_type) {
            case \'panjang\' :
                _config.layout.panjang = _value;
                $(".panjang").html(_config.layout.panjang+"cm");
            break;
            case \'lebar\' :
                _config.layout.lebar = _value;
                $(".lebar").html(_config.layout.lebar+"cm");
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
                window.location.href = "/dcms/dokumen-tercetak"
            },1000);
        }
    });

    $(function(){
        $(document).on("keyup change click scroll",".on-change",_change);
        _ready();
        table = $("#table-dokumen").docoTabel({
            filter: true,
            lengthChange: false,
            displayLength: 30,
            processing: true,
            serverSide: true,
            ordering: false,
            stateSave: true,
            ajax: baseUrl+"dcms/dokumen-tercetak/get-data-attributes",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Attribute")).'", data: "attribute"},
                {title: "'.(\Yii::t("fe", "Fungsi")).'", data: "fungsi"},
            ],
        });
        $(".dataTables_filter").hide();
    });
',View::POS_END,'document-tercetak');