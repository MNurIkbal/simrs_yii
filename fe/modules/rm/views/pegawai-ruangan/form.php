<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rm'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs($this->render('js/_script.js'));

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?= Breadcrumbs::widget([
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

            <div class="panel-body">
                <?php 
                $form = ActiveForm::begin([
                    'id' => 'ajax-form', 
                    'options' => ['class' => 'form-horizontal'],
                ]); 
                echo $form->field($model, 'ruangan_id')->hiddenInput(['value'=> $workspace['ruangan_id']])->label(false);
                echo $form->field($model, 'nama_pegawai')->hiddenInput(['id'=> 'nama_pegawai'])->label(false);
                echo $form->field($model, 'kelompok_pegawai')->hiddenInput(['id'=> 'kelompok_pegawai'])->label(false);
                echo $form->field($model, 'is_edit')->hiddenInput(['id'=> 'is_edit', 'value' => $edit])->label(false);
                ?>

                <div class="form-group">
                    <label class="control-label col-lg-2"><?= Yii::t('fe', 'Instalasi') ?></label>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'nama_instalasi', ['labelOptions' => ['class' => 'text-left']])
                        ->textInput([
                            'class' => 'form-control input-sm', 
                            'value' => !empty($workspace['instalasi_nama']) ? $workspace['instalasi_nama'] : '', 
                            'readonly' => true
                        ])->label(false); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-2"><?= Yii::t('fe', 'Ruangan') ?></label>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'nama_ruangan', ['labelOptions' => ['class' => 'text-left']])
                        ->textInput([
                            'class' => 'form-control input-sm', 
                            'value' => !empty($workspace['ruangan_nama']) ? $workspace['ruangan_nama'] : '', 
                            'readonly' => true
                        ])->label(false); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-2"><?= Yii::t('fe', 'Nama pegawai') ?></label>
                    <div class="col-lg-6">
                        <div class="input-group">
                            <?= Html::activeDropDownList($model, 'pegawai_id',
                                ArrayHelper::map([], 'pegawai_id', 'nama_pegawai'), [
                                    'class' => 'select2 autoPegawai',
                                    'prompt' => Yii::t('fe', '-- Pilih --')
                                ]) 
                            ?>
                            <?= Html::hiddenInput('RuanganPegawaiForm[pegawai_id]', '', ['class' => 'pegawai_id']); ?>
                            <span class="input-group-addon">
                                <?php
                                    echo Html::a('<i class="fa fa-list-ul"></i>
                                        <i class="fa fa-search"></i>',
                                        Url::to([$url_search_popup]), [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal_backdrop'
                                    ]);
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 pull-right">
                        <?=Html::submitButton(Yii::t('fe', ' Tambah '), ['class' => 'btn btn-success fa fa-plus add']); ?>
                    </div>
                </div>
                
                <?php if($edit == 0) : ?>
                <div class="form-group">
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="example-all" 
                        data-source="<?=Url::home();?>rm/pegawai-ruangan/get-data-session"
                        data-filter=".form-filter"
                        data-test="true"
                        >
                        <thead>
                            <tr>
                                <!-- <th><?//=Yii::t('fe', 'Rownum')?></th> -->
                                <th><?=Yii::t('fe', 'Ruangan')?></th>
                                <th><?=Yii::t('fe', 'Nama pegawai')?></th>
                                <th><?=Yii::t('fe', 'Kelompok pegawai')?></th>
                                <th><?=Yii::t('fe', 'Aksi')?></th>
                            </tr>
                        </thead>
                        <tbody> 
                        </tbody>
                    </table>
                </div>
                <?php else : ?>
                <div class="form-group">
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="example-all" 
                        data-source="<?=Url::home();?>rm/pegawai-ruangan/get-data-all"
                        data-filter=".form-filter"
                        data-test="true"
                        >
                        <thead>
                            <tr>
                                <!-- <th><?//=Yii::t('fe', 'Rownum')?></th> -->
                                <th><?=Yii::t('fe', 'Ruangan')?></th>
                                <th><?=Yii::t('fe', 'Nama pegawai')?></th>
                                <th><?=Yii::t('fe', 'Kelompok pegawai')?></th>
                                <th><?=Yii::t('fe', 'Aksi')?></th>
                            </tr>
                        </thead>
                        <tbody> 
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <?= Html::button(Yii::t('fe', ' Simpan'), 
                        [
                            'class' => 'btn bg-teal fa fa-floppy-o',
                            'id' => 'buttonSave',
                            'action' => '/rm/pegawai-ruangan/save',
                            'method' => 'json',
                        ]);
                    ?>
                    
                    <?= Html::button(Yii::t('fe', ' Refresh'), [
                        'class' => 'btn btn-lime-green fa fa-refresh data-reload',
                        'style' => 'background-color:#32CD32;color:white;'
                    ]);?>
                    <?= Html::a(Yii::t('fe', 'Kembali'), 
                        Url::home().'rm/pegawai-ruangan/index', 
                        [
                            'class' => 'btn bg-slate',
                        ]);
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div id="modal" class="modal fade " style='z-index:1065;' data-backdrop="static">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
        </div>
    </div>
</div> -->

<?php ActiveForm::end(); ?>