<?php 

/**
 * @Author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Dashboard Odoo', 'url' => ['index']];
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
                {id:20,text:'Daily Cutoff'},
                {id: 1, text: 'Sale Order Line'},
                {id: 2, text: 'Deposit'},
                {id: 3, text: 'Scroll Cashier'},
                {id:4,text:'Stock Out'},
                {id:5,text:'Stock Return'},
                {id:6,text:'Store Consumption'},
                {id:7,text:'GRN'},
                {id:8,text:'Product Template'},
                {id:9, text: 'Patient'},
                {id:10, text: 'Sale Order'},
                {id:11, text: 'Partner (exc. Patient)'},
                {id:12, text: 'Sale Order Bill'},
                // {id:13, text: 'Sale Order Cob'},
                {id:14, text: 'Master Ruangan'},
                {id:15, text: 'UOM'},
                {id:16, text: 'Patient Debt'}
            ]
        });
        $('.selectTransaksi').on('change', function(){
            $.ajax({
                method: 'POST',
                data: $('#pencarian-form').serializeArray(),
                url: '/master/dashboard-odoo/index',
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
$this->registerJs($this->render('dateondemand.js'),View::POS_END);
?>