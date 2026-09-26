<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;

$this->title = 'Pengiriman Dokumen Rekam Medik';
$this->params['breadcrumbs'][] = ['label' => 'Rm', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-0">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body login-form">
                <div class="row">
                    <div class="col-md-12">
                        <?php 
                            $form = ActiveForm::begin([
                                'id' => 'ajax-form',
                                'enableAjaxValidation'=>false, 
                                'enableClientValidation'=>false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_LARGE]
                            ]); 
                        ?>
                        <?=$form->field($model, 'tglpeminjamanrm', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control pickadate']); ?>
                        <?=$form
                            ->field($model, 'idx_pasien', ['labelOptions' => ['class' => 'text-right']])
                            ->dropDownList(ArrayHelper::map($pasien, 'idx_pasien', 'pasien_m.no_rekam_medik'), [
                                'class' => 'form-control input-sm select2',
                                'prompt' => 'Pilih Pasien',
                            ]);
                        ?>
                        <?= $form->field($model, 'idx_instalasi', ['labelOptions' => ['class' => 'text-right']])->dropDownList(ArrayHelper::map($instalasi, 'idx_instalasi', 'instalasi_nama'), ['id'=>'idx_instalasi','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($model, 'idx_ruangan', ['labelOptions' => ['class' => 'text-right']])->widget(DepDrop::classname(), [
                            'options'=>['id'=>'idx_ruangan'],
                            'pluginOptions'=>[
                                'depends'=>['idx_instalasi'],
                                'placeholder'=>'-- PILIH --',
                                'url' => Url::to(['/master/ruangan/list-ruangan'])
                            ]
                        ]); ?>
                        <?=$form
                            ->field($model, 'idx_pegawai', ['labelOptions' => ['class' => 'text-right']])
                            ->dropDownList(ArrayHelper::map($pegawai, 'idx_pegawai', 'nama_pegawai'), [
                                'class' => 'form-control input-sm select2',
                                'prompt' => 'Pilih Peminjam',
                            ]);
                        ?>
                        <?=$form->field($model, 'keterangan_peminjaman', ['labelOptions' => ['class' => 'text-right']])->textArea(['class' => 'form-control']); ?>
                        <div class="text-right">
                            <?=Html::submitButton('Simpan', ['class' => 'btn btn-success btn-sm']); ?>
                            <?=Html::a('Kembali',Url::home().'rm/pengiriman-dok-rekam-medik/informasi',['class' => 'btn btn-default btn-sm']); ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs(
    "
    $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',
        });

    $('#ajax-form').docoForm('submit',{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            // $('#modal_backdrop').modal('toggle');
            // table.draw();
        }
    });

    ",
    View::POS_READY,
    'pengiriman-dok-rekam-medik-js'
);
?>
