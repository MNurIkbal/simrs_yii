<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
// use app\components\DocoHelpers;
// use app\components\DocoController;

$this->title = Yii::t('fe', 'Penjadwalan dokter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
            <div class="panel-heading">
              <!-- breadcrumbs replace with this -->
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    // 'excel',
                    'excel2' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Unduh excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not exist',
                        'attributes' => [
                            'class' => 'data-excel2',
                            'data-options' => 'click',
                            // 'data-toggle' => 'modal',
                            // 'data-target' => '#modal_backdrop',
                        ]
                    ]
                    // 'detail',
                ]);?>
            </div>


            <div class="panel-body">
                <div class="row">
                    <?php
                        echo Html::beginForm(null,'POST',[
                                'class' => 'form-filter',
                            ]);
                    ?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label><?= Yii::t('fe', 'Instalasi'); ?> :</label>
                                <?php
                                echo Html::dropDownList('instalasi_id','',$listInstalasi,[
                                    'class' => 'select2',
                                    'id'=>'instalasi_id',
                                    'prompt'=>Yii::t('fe', '--Pilih--'),
                                ]);
                                ?>
                            </div>
                            <div class="col-md-3">
                                <label><?= Yii::t('fe', 'Ruangan'); ?> :</label>
                                <?php
                                echo DepDrop::widget(
                                    [
                                        'name'=>'ruangan_id',
                                        'options'=>[
                                            'id'=>'ruangan_id',
                                            'class'=>'select2',
                                        ],
                                        'pluginOptions'=>[
                                            'depends'=>['instalasi_id'],
                                            'placeholder'=>\Yii::t('fe', '--Pilih--'),
                                            'url'=>Url::to(['/pendaftaran/penjadwalan-dokter/dep-drop-list-ruangan'])
                                        ]
                                    ]
                                );
                                ?>
                            </div>
                            <div class="col-md-3">
                                <label><?= Yii::t('fe', 'Dokter'); ?> :</label>
                                <?php
                                    echo Html::dropDownList('dokter_id','',$listDokter,[
                                        'class' => 'select2',
                                        'id'=>'dokter_id',
                                        'prompt'=>Yii::t('fe', '--Pilih--'),
                                    ]);
                                ?>
                            </div>
                            <div class="col-md-3">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label><?= Yii::t('fe', 'Hari'); ?> :</label>
                                <?php
                                    echo Html::dropDownList('hari','',$listHari,[
                                        'class' => 'select2',
                                        'id'=>'hari',
                                        'prompt'=>Yii::t('fe', '--Pilih--'),
                                    ]);
                                ?>
                            </div>
                            <div class="col-md-3">
                                <?= Html::label(Yii::t('fe', 'Jam Mulai:')) ?>
                                <?php
                                    echo Html::textInput('jam_mulai','',[
                                        'class' => 'form-control pickatime',
                                        'id' => 'jam_mulai',
                                        'placeholder' => 'Jam Mulai'
                                    ]);
                                ?>
                            </div>
                            <div class="col-md-3">
                                <?= Html::label(Yii::t('fe', 'Jam Selesai:')) ?>
                                <?php
                                    echo Html::textInput('jam_selesai','',[
                                        'class' => 'form-control pickatime',
                                        'id' => 'jam_selesai',
                                        'placeholder' => 'Jam Selesai'
                                    ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    echo Html::endForm();
                    ?>
                    </div>

                    <br>

                    <!-- table content -->
                    <div class='col-md-offset-9 col-md-3'>
                        <table>
                            <tr>
                                <td>Legend : &nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td class='bg-info'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td>&nbsp;&nbsp; Aktif &nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td class='bg-light'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td>&nbsp;&nbsp; Tidak aktif &nbsp;&nbsp;&nbsp;&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                    <div class="table-responsive pre-scrollable" style="padding-left:4px; padding-right:4px; max-height:70vh; overflow:auto;">
                        <table
                                class="table table-xs table-bordered"
                                spam="datatable-basic table-striped table-hover dataTable no-footer"
                                id="data-jadwaldokter"
                                style="width: 100%;"
                                >
                            <thead>
                                <tr>
                                    <th>Poliklinik</th>
                                    <th>Dokter</th>
                                    <th>Hari</th>
                                    <?php foreach ($listJam as $jam): ?>
                                        <th style='padding-left:3px; padding-right:3px;'>
                                            <?= $jam; ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class='list-jadwal-dokter'>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td colspan=29><?= Yii::t('fe', 'Data tidak ditemukan'); ?></td>
                                </tr>
                            </tbody>
                        </div>
                    </div>
                </div>
                <br>


            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $('.pickatime').pickatime({
        format: 'HH:i'
    });
    ", View::POS_READY, 'time-handler');
$this->registerJs($this->render('jadwal_dokter.js'));
?>
