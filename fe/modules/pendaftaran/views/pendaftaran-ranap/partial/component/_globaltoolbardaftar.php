<?php
    use app\components\DocoHelpers;
    use yii\helpers\Url;
    use yii\web\View;
?>

<div class="panel-toolbar clearfix">
    <?php
        $createsep = '';
        $tableID = 'table-daftar-terakhir';
    ?>
    <?=DocoHelpers::generateToolbar([
            'edit-pendaftaran'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Edit Pendaftaran'),
                'icon' => 'fa fa-pencil',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-edit-pendaftaran',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/informasi-pasien/update?type='.$param.'&',
                    'data-conditions' => 'pendaftaran_id'
                ]
            ],
            'print-label'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Label'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-label',
                    'class'=>'btn-print-pasien-terakhir ',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-label-pasien'
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
            'print-identitas-pasien'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print CM1'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-identitas-pasien',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-identitas-pasien'
                ]
            ],
            'print-surat-referal'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Surat Referal'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-surat-referal',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-surat-referal',
                ]
            ],
            'print-surat-keterangan'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Surat Keterangan'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-surat-keterangan',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-surat-keterangan',
                ]
            ]
        ], "#{$tableID}");?>
</div>