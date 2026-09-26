<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
textarea {
    resize: none;
}
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-12">
                    <?=DocoHelpers::generateToolbar([
                        'save'=>[
                            'attributes'=>[
                                'data-target'=>'penerimaan-pengeluaran-form',
                                
                            ]
                        ]
                    ]);?>
                </div>
                
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id'=>'penerimaan-pengeluaran-form',
                    'options' => [
                        'class' => 'horizontal-form ajax-form',
                        'role'=>'form',
                        ],
                    'enableClientValidation'=>false
                    ]); ?>
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h6 class="panel-title"><b><?=Yii::t('fe', 'Form Penerimaan/Pengeluaran') ?></b></h6>
                                    </div>

                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="col-md-12" style="margin-bottom: 15px">
                                                    <div class="form-group">
                                                        <?=
                                                            $form->field($model_form, 'jenis_transaksi')->radioList($jenisTrans, ['inline'=>true],['id' => 'jenis_transaksi', 'tabindex' => 1])
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-bottom: 15px">
                                                    <div class="form-group">
                                                        <?php $model_form->tanggal_transaksi = date('d-M-Y'); ?>
                                                        <?= $form->field($model_form, 'tanggal_transaksi', [
                                                        'horizontalCssClasses' => [
                                                                'label' => 'text-left control-label col-sm-4 text-bold',
                                                                'wrapper' => 'col-md-6'
                                                            ]
                                                        ])->widget(DatePicker::classname(), [
                                                            'name' => 'date_12',
                                                            'value' => date('Y-m-d'),
                                                            'readonly' => true,
                                                            'language' => 'en',
                                                            'pluginOptions' => [
                                                                'autoclose' => true,
                                                                'format' => 'dd-M-yyyy',
                                                                'endDate' => '0d',
                                                                // 'startDate' => $model_form->tanggal_transaksi,
                                                            ]
                                                        ]); ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-bottom: 15px">
                                                    <div class="form-group">
                                                        <?= $form->field($model_form, 'tipe',[
                                                        'horizontalCssClasses' => [
                                                                'label' => 'text-left control-label col-sm-4',
                                                                'wrapper' => 'col-md-8'
                                                            ],
                                                        ])->dropDownList($tipeTrans,[
                                                            'class' => 'form-control select2',
                                                            'id' => 'tipe',
                                                            'prompt' => '— Pilih —', 
                                                            'tabindex' => 3
                                                        ])->label($model_form->attributeLabels()['tipe']); ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-bottom: 15px">
                                                    <div class="form-group">
                                                        <?= $form->field($model_form, 'dari_kepada',[
                                                        'horizontalCssClasses' => [
                                                                'label' => 'text-left control-label col-sm-4',
                                                                'wrapper' => 'col-md-8'
                                                            ],
                                                        ])->dropDownList([],[
                                                            'class' => 'form-control select2',
                                                            'id' => 'dari_kepada',
                                                            'tabindex' => 4
                                                        ])->label($model_form->attributeLabels()['dari_kepada']); ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pendaftaran hidden" style="margin-bottom: 15px">
                                                    <div class="form-group">
                                                        <?= $form->field($model_form, 'id_pendaftaran', [
                                                                'horizontalCssClasses' => [
                                                                    'label' => 'text-left control-label col-sm-2',
                                                                    'wrapper' => 'col-md-5'
                                                                ]
                                                        ])->dropDownList([],[
                                                                'class' => 'select2 form-control input-sm',
                                                                'id' => 'no_pendaftaran',
                                                                'prompt' => '— Pilih —'
                                                        ]);
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-bottom: 15px">
                                                    <div class="form-group">
                                                        <?= 
                                                            $form->field($model_form, 'metode')->radioList($metodeBayar, ['inline'=>true],['id' => 'metode', 'tabindex' => 5])
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-bottom: 15px">
                                                    <div class="form-group">
                                                        <?= $form->field($model_form, 'jenisnontunai_id',[
                                                        'horizontalCssClasses' => [
                                                                'label' => 'text-left control-label col-sm-4',
                                                                'wrapper' => 'col-md-8'
                                                            ],
                                                        ])->dropDownList([],[
                                                            'class' => 'form-control select2',
                                                            'id' => 'jenisnontunai_id',
                                                            'disabled' => true,
                                                            'tabindex' => 4
                                                        ])->label($model_form->attributeLabels()['jenisnontunai_id']); ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-bottom: 15px">
                                                    <div class="form-group">
                                                        <?=$form->field($model_form, 'jumlah')->textInput([
                                                            'class' => 'form-control jumlah doco-number ',
                                                            'tabindex' => 6
                                                        ])->label($model_form->attributeLabels()['jumlah']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="col-md-12" style="margin-bottom: 15px">
                                                    <div class="form-group">
                                                        <?= $form->field($model_form, 'kategori',[
                                                        'horizontalCssClasses' => [
                                                                'label' => 'text-left control-label col-sm-4',
                                                                'wrapper' => 'col-md-8'
                                                            ],
                                                        ])->dropDownList([],[
                                                            'class' => 'form-control select2',
                                                            'id' => 'kategori',
                                                            'tabindex' => 7
                                                        ])->label($model_form->attributeLabels()['kategori']); ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-bottom: 15px">
                                                    <div class="form-group">
                                                        <?= $form->field($model_form, "deskripsi", [
                                                            'horizontalCssClasses' => [
                                                                'label' => 'control-label col-md-4',
                                                                'wrapper' => "col-md-8"
                                                            ]
                                                        ])->textarea([
                                                            'class' => 'form-control tb-ecollection',
                                                            'id' => 'deskripsi',
                                                            'rows' => '6',
                                                            'tabindex' => 8
                                                        ])->label($model_form->attributeLabels()['deskripsi']); ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-bottom: 15px">
                                                    <div class="form-group">
                                                        <?= $form->field($model_form, "referensi", [
                                                            'horizontalCssClasses' => [
                                                                'label' => 'control-label col-md-4',
                                                                'wrapper' => "col-md-8"
                                                            ]
                                                        ])->textarea([
                                                            'class' => 'form-control tb-ecollection',
                                                            'id' => 'referensi',
                                                            'rows' => '4',
                                                            'tabindex' => 9
                                                        ])->label($model_form->attributeLabels()['referensi']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/index.js'), View::POS_END); 
?>
<?php
$this->registerJs('
    var _vendor = '. $vendor .';
    var _karyawan = '. $karyawan .';
    var _pasien = '. $pasien .';
', View::POS_END);