<?php 

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
            </div>
            <div class="panel-body">
                <?php 
                $form = ActiveForm::begin([
                        'id'=>'pencarian-form',
                        'type' => ActiveForm::TYPE_VERTICAL,
                        'enableClientValidation' => false,
                        'enableAjaxValidation' => false,
                    ]);
                ?>
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'jenis_transaksi', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-8'
                            ]
                        ])->dropDownList([], [
                            'prompt' => '-- Pilih --', 
                            'class' => 'selectTransaksi'
                        ]);
                        ?>
                    </div>
                </div>
                <?php ActiveForm::end() ?>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="dashboard-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs("
    $(document).ready(function(){
        $('.selectTransaksi').select2({
            placeholder: 'Pilih Jenis Transaksi',
            allowClear: false,
            data: [
                {id: 1, text: 'Registrasi'},
                {id: 2, text: 'Edit Registrasi'},
                {id: 3, text: 'Batal Registrasi'},
            ]
        });
        $('.selectTransaksi').on('change', function(){
            $.ajax({
                method: 'POST',
                data: $('#pencarian-form').serializeArray(),
                url: '/master/dashboard-integration/index',
                beforeSend: function(){
                    $('.dashboard-content').html('<center><h3> <i class=\'fa fa-gear fa-spin\'></i> Harap Tunggu....</h3></center>');
                },
                success: function(response){
                    $('.dashboard-content').html(response);
                },
                error: function(xhr, status, error) {
                    var err = eval('(' + xhr.responseText + ')');
                    docoNotification('error', 'Terjadi Kesalahan', err.message);
                    $('.dashboard-content').html('');
                }
            });
        });
    });
    ", View::POS_END, 'js-pencarian');
?>