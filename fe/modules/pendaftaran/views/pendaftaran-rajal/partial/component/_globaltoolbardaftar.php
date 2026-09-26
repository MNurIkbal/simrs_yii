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
            'edit-pendaftaran'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Edit Pendaftaran'),
                'icon' => 'fa fa-pencil',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-edit-pendaftaran',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/informasi-pasien/update',
                    'data-conditions' => 'pendaftaran_id'
                ]
            ],
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
            'print-asesmen'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Form Rajal'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-asesmen',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-asesmen'
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
            ],
            'print-triase'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Form IGD'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-triase',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-triase',
                    'disabled' => ($param != 'igd') ? true : false
                ]
            ],
            'print-label-penunjang'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Print Label Penunjang'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-label-penunjang',
                    'class'=>'btn-print-pasien-terakhir',
                    'data-options'=>'click',
                    'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-label-penunjang',
                    'disabled' => ($param != 'penunjang') ? true : false
                ]
            ],
        ], "#{$tableID}");?>
</div>