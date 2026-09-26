<?php

/**
 * @Author: Arief Saputra
 * @Date:   2018-02-22 11:40:04
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-02-23 15:16:36
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\Depdrop;
use kartik\widgets\ActiveForm;
use yii\web\JsExpression;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Kelas'), 'url' => ['/master/kelas#view-kelaspelayanan']];
$this->params['breadcrumbs'][] = $this->title;
?>

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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'back',
                        'save' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'class' => 'bg-teal simpan-pelayanan btn btn-info btn-labeled btn-xs',
                                'data-target' => '',
                                'onClick' => '',
                            ] 
                        ],
                        'reset',
                    ]);?>
            </div>
            <?php 
                $form = ActiveForm::begin([
                    'id' => 'kelas-pelayanan-form', 
                    'action' => '/master/kelas/set-list-pelayanan',
                    'enableAjaxValidation'=>false, 
                    'enableClientValidation'=>false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 3, 
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'skip-confirm' => "true"
                    ]
                ]); 
            ?>
            <div class="panel-body">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Input Kelas Pelayanan</b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="required">
                                <?= $form->field($model, 'jeniskelas_id', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->dropDownList($listJenisKelas,[
                                            'placeholder' => $model->getAttributeLabel('jeniskelas_id'),
                                            'class' => 'form-control input-sm select2',
                                            'id' => 'jeniskelas_id',
                                            'prompt' => Yii::t('fe','--Pilih--'),
                                    ]);
                                ?>
                            </div>
                            <hr>
                            <div class="required">
                                <?= $form->field($model, 'kelaspelayanan_nama', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('kelaspelayanan_nama'),
                                            'class' => 'form-control input-sm',
                                            'autocomplete' => "off",
                                    ]);
                                ?>
                            </div>
                           <?= $form->field($model, 'kelaspelayanan_namalainnya', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                                'placeholder' => $model->getAttributeLabel('kelaspelayanan_namalainnya'),
                                                'class' => 'form-control input-sm',
                                                'autocomplete' => "off",
                                        ]); ?>
                            <hr>
                            <div class="col-md-12">
                                <?= Html::submitButton(
                                    '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                        [
                                            'class' => 'btn btn-success btn-labeled btn-xs pull-right',
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Detail Kelas Pelayanan</b></h6>
                        </div>
                        <div class="panel-body">
                           <table class="table table-striped table-condensed table-hover data-table" 
                            style="width:100%" id="tablekelaspelayanan">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th class="text-center"><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                                        <th class="text-center"><?=\Yii::t("fe", "Nama Lainnya");?></th>
                                        <th class="text-center"><?=\Yii::t("fe", "Aksi");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="3">Data tidak ditemukan.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php 
$this->registerJs('
    $("#kelas-pelayanan-form").submit(function(event){
        event.preventDefault();
        $(this).docoForm("submit",{
            success : function (data) {
                $("#kelaspelayananform-kelaspelayanan_nama, #kelaspelayananform-kelaspelayanan_namalainnya").val("");
                table.draw();
            }
        });
    });

    $(document).on("click",".data-reset",function (event) {
        event.preventDefault();
        $("#kelas-pelayanan-form")[0].reset();
    })

    $(document).on("click",".simpan-pelayanan",function (event) {
        event.preventDefault();
        $(this).docoForm(\'click\',{
            url : baseUrl+"master/kelas/create-pelayanan",
            data : {
                jeniskelas_id : $("#jeniskelas_id").val(),
                status : true
            },
            method : "POST",
            success : function (data) {
                setTimeout(function () {
                    window.location.href = "/master/kelas"
                },1);
            }
        });
    });

    $(document).on(\'click\',\'.delete\', function(event) {
        event.preventDefault();
        $(this).docoForm(\'delete\',{
            success : function (data) {
                table.draw();
            }
        });
    });


    var table;
    $(function(){
        table = $("#tablekelaspelayanan").docoTabel({
            filter: false,
            displayLength: 20,
            lengthChange : false,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/kelas/get-list-pelayanan",
            columns: [
                {
                    title: "'.(\Yii::t("fe", "Kelas Pelayanan")).'", 
                    data: "kelaspelayanan_nama",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Lainnya")).'", 
                    data: "kelaspelayanan_namalainnya",
                    name: "kelaspelayanan_namalainnya",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
            ],
        });
    });
    ');
?>

<script type="text/javascript">
function addForm() {
    var template = '<tr>' + 
            '<td>' + 
                '<div class="col-lg-12">' + 
                    '<div class="form-group field-kelaspelayananform-kelaspelayanan_nama required">' + 
                    '<input type="text" id="kelaspelayananform-kelaspelayanan_nama" class="form-control" name="KelasPelayananForm[kelaspelayanan_nama][]">' + 
                '</div>' +
            '</td>' + 
            '<td>' + 
                '<div class="col-lg-12">' + 
                    '<div class="form-group field-kelaspelayananform-kelaspelayanan_namalainnya required">' + 
                    '<input type="text" id="kelaspelayananform-kelaspelayanan_namalainnya" class="form-control" name="KelasPelayananForm[kelaspelayanan_namalainnya][]">' + 
                
                '</div>' + 
            '</td>' +
            '<td>' + 
                    '<button type="button" class="btn btn-danger btn-sm btn-deletes"><i class="fa fa-trash"></i></button>'+
            '</td>' +
        '</tr>';
    $('#tablekelaspelayanan > tbody').append(template);
    $('.btn-deletes').on('click', function(){
        $(this).parent().parent().remove();
    });
}
</script>


