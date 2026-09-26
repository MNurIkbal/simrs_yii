<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;
?>
<?= DocoHelpers::generateToolbar([
    'search',
    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
    // 'pdf' => [
    //     'attributes' => [
    //         'data-target' => Url::home() . 'pendaftaran/informasi-pasien/export-pdf?jenis=' . $jenis . '&'
    //     ],
    // ],
    // 'excel' => [
    //     'attributes' => [
    //         'data-target' => Url::home() . 'pendaftaran/informasi-pasien/export-excel?jenis=' . $jenis . '&'
    //     ]
    // ],
    'edit' => [
        'title' => \Yii::t('fe', 'Edit Pendaftaran'),
        'attributes' => [
            'id' => 'data-edit',
            'disabled' => true,
            'data-options' => false,
            'data-target' => '/pendaftaran/informasi-pasien/update?id=',
            'class' => 'btn btn-info btn-labeled btn-xs data-edit btn-toolbar'
        ]
    ],
    'edit-pasien' => [
        'title' => \Yii::t('fe', 'Edit Data Pasien'),
        'icon' => 'fa fa-edit',
        'attributes' => [
            'id' => 'btn-edit-pasien',
            'data-options' => 'link',
            'data-target' => '',
            'target' => '_self'
        ]
    ],
    'update-status' => [
        'type' => 'button',
        'title' => \Yii::t('fe', 'Update Status'),
        'icon' => 'fa fa-pencil',
        'attributes' => [
            'id' => 'data-update-status',
            'data-options' => 'modal',
            'data-target' => '#modal_backdrop',
            'data-url' => Url::home() . 'pendaftaran/informasi-pasien/confirm-update-status?jenis=' . $jenis . '&no_pendaftaran=',
            'data-params' => 'no_pendaftaran',
            'disabled' => false
        ]
    ],
    'create-sep-manual' => [
        'type' => 'button',
        'title' => \Yii::t('fe', 'Input SEP'),
        'icon' => 'fa fa-pencil',
        'method' => 'not-exist',
        'attributes' => [
            'id' => 'create-sep-manual',
            'data-options' => 'modal',
            'data-target' => '#modal_backdrop',
            'data-width' => '90%',
            'data-url' => Url::home() . 'pendaftaran/daftar-rajal/get-form-bpjs-manual?params=' . $jenis . '&tipe=informasi',
            'disabled' => true
        ]
    ],
    'cetak-sep' => [
        'type' => 'button',
        'title' => \Yii::t('fe', 'Print sep'),
        'icon' => 'fa fa-print',
        'method' => 'not-exist',
        'attributes' => [
            'id' => 'btn-print-sep',
            'data-target' => Url::home() . 'pendaftaran/end-point/print-sep?pendaftaran_id=',
            'data-pages' => '_blank',
            'id' => 'btn-print-sep',
            'disabled' => true
        ],
    ],
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
            'data-url' => '/pendaftaran/informasi-pasien/pilih-jumlah-cetakan?jenis=' . $jenis . '&pendaftaran_id=',
            'data-conditions' => 'pasien_id,no_pendaftaran'
        ]
    ],
    'print-kartu-pasien' => [
        'type' => 'button',
        'title' => \Yii::t('fe', 'Print Kartu Pasien'),
        'icon' => 'fa fa-print',
        'method' => 'not-exist',
        'attributes' => [
            'id' => 'btn-print-kartu-pasien',
            'class' => 'btn-print-pasien-terakhir',
            'data-pages' => '_blank',
            'data-target' => Url::home() . 'pendaftaran/daftar-' . (($jenis != 'mcu') ? $jenis : 'rajal') . '/print-kartu-pasien?&pendaftaran_id=',
            'data-conditions' => 'pasien_id',
        ]
    ],
    'print-triase'=>[
        'type'=>'button',
        'title' => \Yii::t('fe', 'Print Form IGD'),
        'icon' => 'fa fa-print',
        'method' => 'not-exist',
        'attributes' => [
            'id'=>'btn-print-triase',
            'class'=>'btn-print-pasien-terakhir ',
            'data-pages'=>'_blank',
            'data-target'=>Url::home().'pendaftaran/daftar-rajal/print-triase?pendaftaran_id=',
            'data-conditions' => 'pasien_id',
            'disabled' => ($jenis != 'igd') ? true : false
        ]
    ],
    'batal' => [
        'type' => 'button',
        'title' => \Yii::t('fe', 'Batal'),
        'icon' => 'fa fa-ban',
        'attributes' => [
            'id' => 'data-batal',
            'data-options' => 'modal',
            'data-target' => '#modal_backdrop',
            'data-url' => $urlbatal,
            'data-params' => 'no_pendaftaran',
            'disabled' => false
        ]
    ],
    'pengajuan-sep'=>[
        'type'=>'button',
        'title' => \Yii::t('fe', 'Pengajuan SEP'),
        'icon' => 'fa fa-pencil',
        'attributes'=>[
            'id'=>'pengajuan-sep',
            'data-options'=>'modal',
            'data-target'=>'#modal_backdrop',
            'data-url'=>Url::home().'pendaftaran/informasi-pasien/pengajuan-sep?id=',
            'disabled'=>true
        ]
    ],
], '#laporan'); ?>