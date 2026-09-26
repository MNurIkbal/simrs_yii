<?php
/**
 * @author Randy Vianda Putra
 * @todo View Master dokter
 * @copyright 21 November 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use kartik\widgets\FileInput;

$this->title = \Yii::t('fe', 'Edit Dokter');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

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
                    /*'save' => [
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-check-square-o',
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-simpan',
                            'id' => 'btn-save',
                            'onClick' => null,
                        ]
                    ],*/
                    'back' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-kembali',
                            'id' => 'btn-kembali',
                            'onClick' => null,
                        ]
                    ],
                ]);
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'form-dokter',
                        'action' => '/master/dokter/update?id='.$id_encrypt,
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
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
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="text-left control-label col-sm-3"><?= Yii::t('fe', 'Nama Dokter') ?></label>
                                <div class="col-sm-9">
                                    <label class="text-left text-bold control-label col-sm-12"><?= $model->nama_pegawai ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="text-left control-label col-sm-3"><?= Yii::t('fe', 'Nama Spesialis') ?></label>
                                <div class="col-sm-9">
                                    <label class="text-left text-bold control-label col-sm-12"><?= $model->spesialis_nama ?></label>
                                </div>
                            </div>
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
