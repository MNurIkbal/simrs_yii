<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
// use app\components\DocoHelpers;
// use app\components\DocoController;

$this->title = Yii::t('fe', 'Jadwal dokter');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
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
                            <?php
                                echo Html::dropDownList('ruangan_id',1,$listRuangan,[
                                    'class' => 'select2',
                                    'id'=>'ruangan_id',
                                    'prompt'=>Yii::t('fe', '--Pilih--'),
                                    ]);
                                    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <?php
                                echo Html::dropDownList('dokter_id',1,$listDokter,[
                                    'class' => 'select2',
                                    'id'=>'dokter_id',
                                    'prompt'=>Yii::t('fe', '--Pilih--'),
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <?php
                            echo Html::button('<i class="fa fa-plus"></i>&nbsp;' . Yii::t('fe', 'Tambah'),[
                                'class' => 'btn btn-default renderJadwal',
                                'style' => 'margin-right:5px;'
                            ]);
                            ?>
                        </div>
                    </div>
                    <?php
                        echo Html::endForm();
                    ?>
                    </div>

                    <br>
                    <div class='content-jadwaldokter'>
                        <!-- from render partial -->
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs("

$('.renderJadwal').click(function() {
        $('.content-jadwaldokter').docoLoad({
            url: '/master/jadwal-dokter/render-jadwal-dokter?ruangan_id=' + $('#ruangan_id').val() + '&pegawai_id=' + $('#dokter_id').val(),
        });
    });

",View::POS_END,'create-jadwaldokter');
?>