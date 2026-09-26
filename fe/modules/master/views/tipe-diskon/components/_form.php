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

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-body" style="margin-top: 20px;">
                
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'tipe-diskon-detail-form-'.$jenis_layanan,
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
                    <div class="col-md-5" style="margin-top: 20px;">
                        <?=Html::activeHiddenInput($model, 'jenislayanan_id',[
                            'value' => $jenis_layanan,
                            'class' => 'jenislayanan-id'
                        ])?>
                        <?=Html::activeHiddenInput($model, 'layanan',[
                            'value' => '',
                            'class' => 'layanan'
                        ])?>
                        <?=Html::activeHiddenInput($model, 'is_change',[
                            'value' => 'false',
                            'class' => 'is-change'
                        ])?>
                        <?= 
                            $form->field($model, 'layanan_id', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-3',
                                    'wrapper' => 'col-md-8'
                                ]
                                ])->dropDownList(@$additional_data,[
                                'class' => 'form-control select2 col-md-8 layanan-id',
                                'prompt' => Yii::t('fe', 'Pilih'),
                            ])->label(@$label); 
                        ?>
                            
                        <?= $form->field($model, 'disc_persen', [
                            'labelOptions' => ['class' => 'text-left'],
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-3',
                                    'wrapper' => 'col-md-8'
                            ],
                            ])->textInput([
                                'placeholder' => Yii::t('fe', 'Diskon Persen'),
                                'class' => 'form-control input-sm doco-number disc-persen',
                                'autocomplete' => "off",
                                'id' => 'disc_persen',
                            ])->label(Yii::t('fe', 'Diskon % ')); 
                        ?>  

                        <?= $form->field($model, 'max_dijamin', [
                            'labelOptions' => ['class' => 'text-left'],
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-3',
                                    'wrapper' => 'col-md-8'
                            ],
                            ])->textInput([
                                'placeholder' => Yii::t('fe', 'Max Dijamin'),
                                'class' => 'form-control input-sm doco-number max-dijamin',
                                'autocomplete' => "off",
                                'id' => 'max_dijamin',
                            ])->label(Yii::t('fe', 'Max Dijamin')); 
                        ?>
                        <br>
                        <button type="button" class="btn btn-info btn-labeled btn-xs" style="float:left;" data-options="click" data-render="tipe-diskon" data-tab="tab-km" data-content="content-td" id="btn-simpan"><b><i class="fa fa-plus"></i></b>Tambah</button>

                    </div>
                    <div class="col-md-7">
                        <table id="table-result-<?=$jenis_layanan?>" class="table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody> 
                                <div id="cache-data">

                                </div>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-info btn-labeled btn-xs" style="float:right;" data-options="click" id="btn-simpan-all"><b><i class="fa fa-floppy-o"></i></b>Simpan</button>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
<?php

$this->registerJs('
    $(document).on("click", ".data-reload", function() {
        tableResult.draw();
    });
    
    var jenis_layanan = "'.$jenis_layanan.'";
    var label = "'.$label.'";
    var tableResult;
    $(document).ready(function() {
        resetForm();
        // Generate Table
        tableResult = $("#table-result-"+jenis_layanan).docoTabel({
            filter: true,
            // columnDefs: [ {
            //     orderable: false,
            //     className: "select-checkbox",
            //     targets:   0
            // }],
            select: {
                style:    "os",
                selector: "tr"
            },
            // sorting: [[2, "asc"]],
            ordering: false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/tipe-diskon/get-cache?cacheName='.$cacheName.'&&stat='.$action.'&&id="+tipediskon_id,
            columns: [
                {
                    title: label, 
                    data: "layanan", 
                    name: "layanan",
                    searchable: true,
                },
                {
                    title: "'.(\Yii::t("fe", "Diskon %")).'", 
                    data: "disc_persen", 
                    name: "disc_persen",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Maximum Dijamin")).'", 
                    data: "max_dijamin", 
                    name: "max_dijamin",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'", 
                    data: "aksi", 
                    name: "aksi",
                    searchable: false,
                },
            ],
            rowCallback: function( row, data ) {
                $("td:eq(2)", row).html(docoHelper.convertToRupiah(data.max_dijamin));
              }
        });

        $(".dataTables_filter").hide();
    });
', View::POS_END, 'e-index');

?>