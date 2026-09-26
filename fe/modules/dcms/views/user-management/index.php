<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Dcms', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <?php
                $form = ActiveForm::begin([
                    'id' => 'ajax-form',
                    'enableAjaxValidation'=>false,
                    'enableClientValidation'=>false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]);
            ?>
            <div class="panel-toolbar clearfix">
                <div class="col-md-12 btn-group pull-left">
                    <?= $form->field($model, 'nama_pengguna', [
                                'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-3',
                                'wrapper' => 'col-md-3'
                            ]])
                            ->dropDownList($options_data_user, [
                                'placeholder' => $model->getAttributeLabel('peranpenggunanamalain'),
                                'class' => 'select2 pemakai',
                                'prompt' => Yii::t('fe', '-- Pilih nama pengguna --')

                            ]);
                    ?>

                    <?= $form->field($model, 'peranpenggunanamalain', [
                                'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-3',
                                'wrapper' => 'col-md-3'
                            ]])
                            ->dropDownList([], [
                                'placeholder' => $model->getAttributeLabel('peranpenggunanamalain'),
                                'class' => 'select2'
                            ]);
                    ?>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <?= Html::submitButton('<i class="fa fa-floppy-o"></i>&nbsp;' . Yii::t('fe','Simpan'),
                                    ['class' => 'btn bg-teal']) ?>
                            <?= Html::a('<i class="fa fa-arrow-left"></i>&nbsp;'  . Yii::t('fe','Kembali'),
                                    '/dcms/peran-pengguna',
                                    ['class' => 'btn bg-slate']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            <div class="panel-body">
                <div id="tab-role-menu">
                    <b><h2  class="text-center"><?= Yii::t('fe','Tidak ada yang di tampilkan') ?></h2></b>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    $(".pemakai").change(function(e) {
        var id = $(this).val()
        console.log(id)

    })

', View::POS_END, 'b-index');
?>
