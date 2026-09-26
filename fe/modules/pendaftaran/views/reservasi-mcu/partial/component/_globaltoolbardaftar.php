<?php
    use app\components\DocoHelpers;
    use yii\helpers\Url;
    use yii\web\View;
?>

<div class="panel-toolbar clearfix">
    <?php
        if ($param == 'igd') {
            $createsep = 'hidden';
        }else {
            $createsep = '';
        }

        $tableID = 'table-daftar-terakhir';
        // if ($param == 'ranap') {
        //     $tableID = 'table-daftar-terakhir-ranap';
        // }else if ($param == 'rajal') {
        //     $tableID = 'table-daftar-terakhir';
        // }else if ($param == 'igd') {
        //     $tableID = 'table-daftar-terakhir-igd';
        // }
    ?>
    <?=DocoHelpers::generateToolbar([
            'print-karcis'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Karcis'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-karcis',
                    'class'=>'btn-print-pasien-terakhir ',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-karcis'
                ]
            ],
            'print-status-pasien'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Tracer'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-status-pasien',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-status-pasien'
                ]
            ],
            'print-kartu-pasien'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Kartu Pasien'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-kartu-pasien',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-kartu-pasien'
                ]
            ],
            'print-gelang-pasien-dewasa'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Gelang Dewasa'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-gelang-pasien-dewasa',
                    'class'=>'btn-print-pasien-terakhir ',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-gelang-pasien'
                ]
            ],
            'print-gelang-pasien-anak'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Gelang Anak'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-gelang-pasien-anak',
                    'class'=>'btn-print-pasien-terakhir ',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-gelang-pasien-anak'
                ]
            ],
            'print-label-pasien'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Gelang Pasien'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-label-pasien',
                    'class'=>'btn-print-pasien-terakhir ',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-label-pasien'
                ]
            ],
            
            // 'print-label'=>[
            //     'type'=>'button',
            //     'title' => \Yii::t('fe', 'Print Label'),
            //     'icon' => 'fa fa-print',
            //     'method' => 'not-exist',
            //     'attributes' => [
            //         'id'=>'btn-print-label',
            //         'class'=>'btn-print-pasien-terakhir ',
            //         'data-options'=>'click',
            //         'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-label-pasien'
            //     ]
            // ],
            'print-label-pasien-multiple' => [
                'type' => 'button',
                'title' => \Yii::t('fe', 'Print Label Pasien Multiple'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id' => 'btn-print-label-pasien',
                    'data-options' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'data-width' => '30%',
                    'data-url' => Url::home().Yii::$app->controller->module->id.'/informasi-pasien/pilih-jumlah-cetakan?jenis='.$param.'&primary=',
                    'data-conditions' => 'pendaftaran_id,pasien_id,no_pendaftaran'
                ]
            ],
            'print-sep'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print SEP'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-sep',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=> Url::home().Yii::$app->controller->module->id.'/end-point/print-sep',
                    'disabled'=>true
                ]
            ],
            'create-sep'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Create SEP'),
                'icon' => 'fa fa-pencil',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-create-sep',
                    'class'=> $createsep,
                    'data-options'=>'modal',
                    'data-target' => '#modal_backdrop',
                    'data-width' => '90%',
                    'data-url' => $module.'/get-form-bpjs?params='.$param.'-',
                    // 'data-url' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/get-form-bpjs?params='.$param.'-',
                    'disabled'=>true
                ]
            ],
            'create-sep-manual'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Input SEP'),
                'icon' => 'fa fa-pencil',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-create-sep-manual',
                    'data-options'=>'modal',
                    'data-target' => '#modal_backdrop',
                    'data-width' => '90%',
                    'data-url' => $module.'/get-form-bpjs-manual?params='.$param,
                    // 'data-url' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/get-form-bpjs-manual?params='.$param,
                    'disabled'=>true
                ]
            ],
            'print-r2k'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print R2K'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-r2k',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-r2k',
                    'disabled' => ($param != 'rajal') ? true : false
                ]
            ],
            'print-r2mk'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print R2MK'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-r2mk',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-r2mk',
                    'disabled' => ($param != 'ranap') ? true : false
                ]
            ],
            'print-label-bed'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Label Bed'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-label-bed',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-label-bed',
                    'disabled' => ($param != 'ranap') ? true : false
                ]
            ],
            'print-r2k'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print R2K'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-r2k',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-r2k',
                    'disabled' => ($param != 'rajal') ? true : false
                ]
            ],
            'print-r2mk'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print R2MK'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-r2mk',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-r2mk',
                    'disabled' => ($param != 'ranap') ? true : false
                ]
            ]
        ], "#{$tableID}");?>
</div>