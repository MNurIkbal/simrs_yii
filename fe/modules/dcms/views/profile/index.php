<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;

$this->title = \Yii::t('fe', 'Profil User');
$this->params['breadcrumbs'][] = ['label' => 'Dcms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <!--<?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>-->
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
                        'enableAjaxValidation'=>false, 
                        'enableClientValidation'=>false,
                    ]); 
                ?>
                <div class="row">
                    <div class="col-md-12 centered text-center">
                        <i class="fa fa-user" style="font-size:120px"></i>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><?=Yii::t('fe', 'Nama pengguna') ?></label>
                            <div class="input-group"><span class="input-group-addon"><i class="fa fa-user"></i></span>
                                <input type="text" readonly class="form-control daterange-single" value="<?=Yii::$app->docoVars->user("nama");?>" placeholder="<?=(\Yii::t('fe', 'Nama pengguna'));?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><?=Yii::t('fe', 'Kata sandi') ?></label>
                            <div class="input-group"><span class="input-group-addon"><i class="fa fa-key"></i></span>
                                <?= $form->field($model, 'password_old', ['options' => ['tag' => false]])->passwordInput()->label(false) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><?=Yii::t('fe', 'Kata sandi baru') ?></label>
                            <div class="input-group"><span class="input-group-addon"><i class="fa fa-key"></i></span>
                                <?= $form->field($model, 'password_new', ['options' => ['tag' => false]])->passwordInput()->label(false) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><?=Yii::t('fe', 'Konfirmasi kata sandi') ?></label>
                            <div class="input-group"><span class="input-group-addon"><i class="fa fa-key"></i></span>
                                <?= $form->field($model, 'password_confirm', ['options' => ['tag' => false]])->passwordInput()->label(false) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?=Html::submitButton('<i class="fa fa-floppy-o"></i> '.\Yii::t('fe', 'Simpan').'', [
                    'class' => 'btn btn-info btn-xs',
                ]);?>
                <?=Html::resetButton('<i class="fa fa-repeat"></i> '.\Yii::t('fe', 'Ulang').'', [
                    'class' => 'btn btn-info btn-xs',
                ]);?>

                <?= Html::a('<i class="fa fa-file"></i> <span>' . \Yii::t('fe', 'Ekyc E-Sign') . '</span>', '#', [
                    'id' => 'esign-button-0',
                    'class' => 'btn btn-info btn-xs pull-right btn-ekyc hide',
                ]);?>
                <?= Html::a('<i class="fa fa-file"></i> <span>' . \Yii::t('fe', 'Ekyc E-Sign') . '</span>', '#', [
                    'id' => 'esign-button-1',
                    'class' => 'btn btn-info btn-xs pull-right btn-ekyc hide',
                ]);?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs('

    $("#ajax-form").submit(function(event){
        $(this).docoForm("submit",{
            success : function (data) {
            }
        });
        return true;
    });
', View::POS_END, 'b-index');

    $this->registerJs($this->render('js/esign.js'));

?>
