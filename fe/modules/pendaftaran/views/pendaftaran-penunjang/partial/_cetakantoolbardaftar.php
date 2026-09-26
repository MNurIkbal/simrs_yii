<?php
    use app\components\DocoHelpers;
    use yii\helpers\Url;
?>

<div class="panel-toolbar clearfix">
    <?php
        $createsep = '';
        $tableID = 'table-daftar-terakhir';
    ?>
    <?=DocoHelpers::generateToolbar([
            // 'edit-pendaftaran'=>[
            //     'type'=>'button',
            //     'title' => \Yii::t('fe', 'Edit Pendaftaran'),
            //     'icon' => 'fa fa-pencil',
            //     'method' => 'not-exist',
            //     'attributes' => [
            //         'id'=>'btn-edit-pendaftaran',
            //         'class'=>'btn-print-pasien-terakhir',
            //         'data-options'=>'click',
            //         'data-target'=>Url::home().Yii::$app->controller->module->id.'/informasi-pasien/update?type='.$param.'&',
            //         'data-conditions' => 'pendaftaran_id'
            //     ]
            // ],
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
            'print-label-pasien-multiple' => [
                'type' => 'button',
                'title' => \Yii::t('fe', 'Print Label Pasien Multiple'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id' => 'btn-print-label-pasien',
                    'data-options' => 'modal',
                    'data-target' => '#modal_print_label',
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
            'print-label-penunjang'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Radiologi Besar'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-label-penunjang',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=> Url::home().Yii::$app->controller->module->id.'/daftar-penunjang/print-label-penunjang',
                    'disabled' => false
                ]
            ],
            'print-label-penunjang-kecil'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Radiologi Kecil'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-label-penunjang-kecil',
                    'class'=>'btn-print-pasien-terakhir ',
                    'data-options'=>'click',
                    'data-target'=> Url::home().Yii::$app->controller->module->id.'/daftar-penunjang/print-label-penunjang-kecil',
                    'disabled' => false
                ]
            ],
            'print-label-farmasi' => [
                'type' => 'button',
                'title' => \Yii::t('fe', 'Print Label Farmasi'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id' => 'btn-print-label-pasien',
                    'data-options' => 'modal',
                    'data-target' => '#modal_print_label',
                    'data-width' => '30%',
                    'data-url' => Url::home().Yii::$app->controller->module->id.'/informasi-pasien/pilih-jumlah-cetakan?jenis=rajal&primary=',
                    'data-conditions' => 'pendaftaran_id,pasien_id,no_pendaftaran'
                ]
            ],
        ], "#{$tableID}");?>
</div>