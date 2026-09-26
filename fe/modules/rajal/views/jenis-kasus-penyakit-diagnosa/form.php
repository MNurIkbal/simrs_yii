<?php

/**
 * @Author: afil
 * @Date:   2018-01-10 10:20:25
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-12 09:31:24
 * @Description: 
 */
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii', 'Rawat jalan'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii', 'Kasus penyakit diagnosa'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$title;?></b> - <?=$sub_title;?></h3>
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

            <div class="panel-body">
                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'ajax-form', 
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                        'options' => [
                            // 'data-confirm-show' => 'true',
                            'data-notification-show' => 'true',
                        ],
                    ]); 
                ?>
                <?=$form
                    ->field($modelKasuspenyakitdiagnosa, 'jeniskasuspenyakit_id', ['labelOptions' => ['class' => 'text-right']])
                    ->dropDownList(ArrayHelper::map($data_kasus, 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama'), [
                        'class' => 'form-control input-sm select2',
                        'id' => 'kasus_id',
                        'prompt' => Yii::t('fe', '-- Pilih --')
                    ]);
                ?>
                <?=$form
                    ->field($modelKasuspenyakitdiagnosa, 'diagnosa_id', ['labelOptions' => ['class' => 'text-right']])
                    ->dropDownList(ArrayHelper::map($data_diagnosa, 'diagnosa_id', 'diagnosa_nama'), [
                        'class' => 'form-control input-sm select2',
                        'id' => 'diagnosa_id',
                        'prompt' => Yii::t('fe', '-- Pilih --')
                    ]);
                ?>

                <div class="text-right">
                    <?=Html::submitButton('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), [
                        'class' => 'btn btn-success btn-sm', 
                        'id' => 'buttonAddKasus',
                    ]); ?>
                </div>
                <?php ActiveForm::end(); ?>

                <div class="form-group">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <?php if ($action == 'create'){ ?>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" id="dataKasuspenyakitdiagnosaCreate" 
                    data-source="<?=Url::home();?>rajal/jenis-kasus-penyakit-diagnosa/get-data-session"
                    data-filter=".form-filter"
                    data-test="true"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'Jenis kasus penyakit')?></th>
                            <th><?=Yii::t('fe', 'Kode - nama diagnosa')?></th>
                            <th><?=Yii::t('fe', 'Aksi')?></th>
                        </tr>
                    </thead>
                    <tbody> 
                    </tbody>
                </table>
                <?php }else{ ?>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" id="dataKasuspenyakitdiagnosa" 
                    data-source="<?=Url::home();?>rajal/jenis-kasus-penyakit-diagnosa/get-data-all?action=<?=$action;?>"
                    data-filter=".form-filter"
                    data-test="true"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'Jenis kasus penyakit')?></th>
                            <th><?=Yii::t('fe', 'Kode - nama diagnosa')?></th>
                            <th><?=Yii::t('fe', 'Aksi')?></th>
                        </tr>
                    </thead>
                    <tbody> 
                    </tbody>
                </table>
                <?php } ?>
                <div class="form-group">
                    <?=Html::button('<i class="fa fa-floppy-o"></i> '.Yii::t('fe', 'Simpan'), 
                        [
                            'class' => 'btn btn-bg-teal btn-sm',
                            'id' => 'buttonSave',
                            'action' => '/rajal/jenis-kasus-penyakit-diagnosa/save-data-kasus',
                            'method' => 'json',
                        ]);
                    ?>
                    <?=Html::button('<i class="fa fa-refresh"></i> '.Yii::t('fe', 'Segarkan'), [
                        'class' => 'btn btn-lime-green btn-sm data-reload',
                        'id' => 'buttonRefresh',
                    ]);?>
                    <?=Html::a('<i class="fa fa-arrow-left"></i> '.Yii::t('fe', 'Kembali'), 
                        Url::home().'rajal/jenis-kasus-penyakit-diagnosa/index', 
                        [
                            'class' => 'btn bg-slate btn-sm',
                        ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?=
    $this->registerJs($this->render('js/_jeniskasuspenyakitdiagnosa.js'));
?>