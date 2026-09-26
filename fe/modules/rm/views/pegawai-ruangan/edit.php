<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
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
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]); 
                echo $form->field($model, 'ruangan_id')->hiddenInput(['value'=> $data['ruangan_id']])->label(false);
                echo $form->field($model, 'nama_pegawai')->hiddenInput(['id'=> 'nama_pegawai'])->label(false);
                ?>

                <div class="form-group">
                    <label class="control-label col-lg-2"><?= Yii::t('fe', 'Instalasi') ?></label>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'nama_instalasi', ['labelOptions' => ['class' => 'text-left']])
                        ->textInput([
                            'class' => 'form-control input-sm', 
                            'value' => !empty($data['instalasi_nama']) ? $data['instalasi_nama'] : '', 
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
                            'value' => !empty($data['ruangan_nama']) ? $data['ruangan_nama'] : '', 
                            'readonly' => true
                        ])->label(false); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-2"><?= Yii::t('fe', 'Nama pegawai') ?></label>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'pegawai_id', [
                            'addon' => [
                                'prepend' => [
                                    'content' => Html::button('', [
                                            'action' => Url::to([$url_search_popup]),
                                            'data-toggle' => 'modal',
                                            'class'=>'btn btn-primary fa fa-search btn-sm searchmodal',
                                            'data-target' => '#modal_backdrop'
                                        ]),

                                    'asButton' => true,
                                ]
                        ]])
                        ->widget(Select2::classname(), [
                            'initValueText' => '', 
                            'options' => ['placeholder' => Yii::t('fe', 'Pilih')],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                ],
                                'ajax' => [
                                    'url' => Url::to([$url_search]),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function(pegawai) { return pegawai.text; }'),
                                'templateSelection' => new JsExpression('function (pegawai) { return pegawai.text; }'),
                            ],
                            'pluginEvents' => [
                                'select2:select' => new JsExpression('function (pegawai) { 
                                    $("#nama_pegawai").val(pegawai.params.data.text);
                                }'),

                            ]
                        ])->label(false) ?>
                    </div>
                    <div class="col-lg-4 pull-right">
                        <?=Html::submitButton(Yii::t('fe', ' Tambah '), ['class' => 'btn btn-success fa fa-plus add']); ?>
                    </div>
                </div>

                <div class="form-group">
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="example-edit" 
                        data-source="<?=Url::home();?>rm/pegawai-ruangan/get-data?pegawai_id=<?= $identity->pegawai_id ?>&edit=true"
                        data-filter=".form-filter"
                        data-test="true"
                        >
                        <thead>
                            <tr>
                                <th><?=Yii::t('fe', 'Rownum')?></th>
                                <th><?=Yii::t('fe', 'Ruangan')?></th>
                                <th><?=Yii::t('fe', 'Nama pegawai')?></th>
                                <th><?=Yii::t('fe', 'Aksi')?></th>
                            </tr>
                        </thead>
                        <tbody> 
                        </tbody>
                    </table>
                </div>

                <div class="form-group">
                    <?= Html::button(Yii::t('fe', ' Simpan'), 
                        [
                            'class' => 'btn bg-teal fa fa-floppy-o',
                            'id' => 'buttonSave',
                            'action' => '/rm/pegawai-ruangan/save',
                            'method' => 'json',
                        ]);
                    ?>
                    
                    <?= Html::button(Yii::t('fe', 'Segarkan'), [
                        'class' => 'btn btn-info btn-sm data-reload',
                    ]);?>
                    <?= Html::a(Yii::t('fe', 'Kembali'), 
                        Url::home().'rm/pegawai-ruangan/index', 
                        [
                            'class' => 'btn btn-default btn-sm',
                        ]);
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>


<div id="modal_backdrop" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>