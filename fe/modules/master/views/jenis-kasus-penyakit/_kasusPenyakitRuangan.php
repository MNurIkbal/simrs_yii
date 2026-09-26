<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
// use app\components\DocoController;

$this->title = Yii::t('fe', 'Jenis Kasus Penyakit Ruangan');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/#/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/#/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?php
                        echo Html::beginForm(null,'POST',[
                                'class' => 'form-filter',
                            ]);
                    ?>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label><?= Yii::t('fe', 'Jenis kasus penyakit'); ?> :</label>
                            <?php
                                echo Html::textInput('jeniskasuspenyakit_nama',null,[
                                    'class' => 'form-control',
                                    'placeholder' => Yii::t('fe', 'Nama'),
                                ]);
                            ?>
                        </div>
                        <div class="col-md-4">
                            <label><?= Yii::t('fe', 'Nama ruangan'); ?> :</label>
                            <?php
                                echo Html::textInput('ruangan_nama',null,[
                                    'class' => 'form-control',
                                    'placeholder' => Yii::t('fe', 'Nama ruangan')
                                ]);
                            ?>
                        </div>
                        <div class="col-md-4">
                            <label><?= Yii::t('fe', 'Status'); ?> :</label>
                            <?php
                                echo Html::dropDownList('status',1,$status,[
                                    'class' => 'select2',
                                ]);
                            ?>
                        </div>
                    </div>

                    <?php
                        echo Html::endForm();
                    ?>
                    <div class="form-group">
                        <div class="col-md-12">
                            <hr>
                        </div>
                    </div>
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer"
                            id="data-kasuspenyakitruangan" data-filter=".form-filter" style="width: 100%;" >
                        <thead>
                            <tr class="bg-inverse">
                                <th><?= Yii::t('fe', 'No'); ?></th>
                                <th><?= Yii::t('fe', 'Jenis kasus penyakit'); ?></th>
                                <th><?= Yii::t('fe', 'Nama ruangan'); ?></th>
                                <th><?= Yii::t('fe', 'Status'); ?></th>
                                <th><?= Yii::t('fe', 'Aksi'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <div class="form-group">
                        <?php
                        echo Html::button(
                            '<i class="fa fa-plus" aria-hidden="true"></i>
                                    &nbsp;' . Yii::t('fe', 'Tambah') . ' ' . $this->title,
                            [
                                'action' => Url::home().'master/jenis-kasus-penyakit/create-ruangan',
                                'class' => 'btn btn-success btn-xs',
                                'style' => 'margin-right:5px',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop'
                            ]
                        );
                        echo Html::button('Excel',[
                                    'class' => 'btn bg-teal-400 btn-xs reset',
                                    'style' => 'margin-right:5px'
                                ]);
                        echo Html::button('Print',[
                                    'class' => 'btn bg-teal-400 btn-xs',
                                    'style' => 'margin-right:5px'
                                ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
