<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use kartik\widgets\FileInput;

$this->title = \Yii::t('fe', 'Update Slider Rumah Sakit');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .datepicker>div{
        display:block;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
              <!-- breadcrumbs replace with this -->
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                      <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                DocoHelpers::generateToolbar([
                    'save' => [
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-check-square-o',
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-simpan',
                            // 'data-options' => 'link',
                            'id' => 'btn-save',
                            'onClick' => null,
                            // 'data-content' => 'content-perda',
                            // 'data-target' => '/laboratorium/inf-pasien-rujukan-lab/form-batal?id=',
                        ]
                    ],
                    'back' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-kembali',
                            // 'data-options' => 'link',
                            'id' => 'btn-kembali',
                            'onClick' => null,
                            // 'data-content' => 'content-perda',
                            // 'data-target' => '/laboratorium/inf-pasien-rujukan-lab/form-batal?id=',
                        ]
                    ],
                ], '#table-tindakan-ruangan');
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>

                <?php
                // $is_disabled = empty($id) ? false : true;
                $form = ActiveForm::begin([
                    'id' => 'form-slider',
                    'action' => '/master/info-slider-rumah-sakit/update?id='.$id_encrypt,
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_INLINE,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'role' => 'form',
                        'enctype' => 'multipart/form-data'
                    ]
                ]);
                ?>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'judul', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('judul'), 'class' => 'form-control input-sm']); ?>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label text-left control-label col-md-3" for="infosliderform-file_gambar"><?= Yii::t('fe', 'File Gambar');?></label>
                        <div class="col-md-9">

                             <?php
                            echo $form->field($model, 'file_gambar', ['horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-8',
                            ]])
                            ->widget(FileInput::classname(), [
                                'pluginOptions' => [
                                    'showUpload' => false,
                                    'allowedFileExtensions' => ['jpg', 'png'],
                                    'maxFileSize' => 2000,
                                    'maxImageWidth' => 1140,
                                    'maxImageHeight' => 400
                                ],
                                'options' => ['accept' => 'image/*', 'class' => 'file-input', 'id' => 'file'],
                            ])
                            ->label(Yii::t('fe', 'Gambar Slider <i>(1140x400)(Max 2MB)</i>'));
                            ?>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'tgl_mulai', ['horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-md-3',
                                    'wrapper' => 'col-md-9'
                                ]])->widget(DatePicker::classname(), [
                                    'name' => 'date_12',
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'value' => date('dd-M-yyyy'),
                                        // 'readonly' => true,
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd-M-yyyy',
                                            // 'endDate' => "0d",
                                    ]
                                ]);
                            ?>
                        </div>
                        <div class="col-md-12">
                            <?= $form->field($model, 'tgl_selesai', ['horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-md-3',
                                    'wrapper' => 'col-md-9'
                                ]])->widget(DatePicker::classname(), [
                                    'name' => 'date_12',
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'value' => date('dd-M-yyyy'),
                                        // 'readonly' => true,
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd-M-yyyy',
                                            // 'endDate' => "0d",
                                    ]
                                ]);
                            ?>
                        </div>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
// $this->registerCss($this->render('../assets/css/wizard.css'));
$this->registerJs($this->render('js/form.js'), View::POS_END);
?>
