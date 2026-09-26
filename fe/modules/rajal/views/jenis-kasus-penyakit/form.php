<?php

/**
 * @Author: afil
 * @Date:   2018-01-03 14:05:59
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-11 16:14:13
 */
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat jalan'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Kasus penyakit ruangan'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b> - <?=$sub_title;?></h3>
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
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]); 
                ?>
                <?=$form->field($modelRuangan, 'instalasi_nama', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm', 'disabled' => 'disabled', 'value' => 'Nama instalasi']); ?>
                <?=$form->field($modelRuangan, 'ruangan_nama', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm', 'disabled' => 'disabled', 'value' => 'Nama ruangan']); ?>
                <?= Html::hiddenInput('KasusPenyakitRuanganForm[ruangan_id]', $id_ruangan, ['class' => 'id_ruangan', 'id' => 'ruangan_id']); ?>
                <?=$form
                    ->field($modelKasuspenyakitruangan, 'jeniskasuspenyakit_id', ['labelOptions' => ['class' => 'text-right']])
                    ->dropDownList(ArrayHelper::map($data_kasus, 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama'), [
                        'class' => 'form-control input-sm select2',
                        'id' => 'kasus_id',
                        'prompt' => '— Pilih —'
                    ]);
                ?>

                <div class="text-right">
                    <?=Html::submitButton('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), ['class' => 'btn btn-success btn-sm', 'id' => 'buttonAddKasus']); ?>
                </div>
                <?php ActiveForm::end(); ?>

                <div class="form-group">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <?php if ($action == 'create'){ ?>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" id="dataKasuspenyakitruanganCreate" 
                    data-source="<?=Url::home();?>rajal/jenis-kasus-penyakit/get-data-session"
                    data-filter=".form-filter"
                    data-test="true"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'Instalasi')?></th>
                            <th><?=Yii::t('fe', 'Nama ruangan')?></th>
                            <th><?=Yii::t('fe', 'Nama kasus penyakit')?></th>
                            <th><?=Yii::t('fe', 'Nama lain kasus penyakit')?></th>
                            <th><?=Yii::t('fe', 'Aksi')?></th>
                        </tr>
                    </thead>
                    <tbody> 
                    </tbody>
                </table>
                <?php }else{ ?>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" id="dataKasuspenyakitruangan" 
                    data-source="<?=Url::home();?>rajal/jenis-kasus-penyakit/get-data-all?action=<?=$action;?>"
                    data-filter=".form-filter"
                    data-test="true"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'Instalasi')?></th>
                            <th><?=Yii::t('fe', 'Nama ruangan')?></th>
                            <th><?=Yii::t('fe', 'Nama kasus penyakit')?></th>
                            <th><?=Yii::t('fe', 'Nama lain kasus penyakit')?></th>
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
                            'action' => '/rajal/jenis-kasus-penyakit/save-data-kasus',
                            'method' => 'json',
                        ]);
                    ?>
                    <?=Html::button('<i class="fa fa-refresh"></i> '.Yii::t('fe', 'Segarkan'), [
                        'class' => 'btn btn-lime-green btn-sm data-reload',
                        'id' => 'buttonRefresh',
                    ]);?>
                    <?=Html::a('<i class="fa fa-arrow-left"></i> '.Yii::t('fe', 'Kembali'),  
                        Url::home().'rajal/jenis-kasus-penyakit/index', 
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
    $this->registerJs($this->render('js/_jeniskasuspenyakit.js'));
?>