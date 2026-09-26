<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<style media="screen">
    .image-frame-luka-bakar, .image-frame{
        position: relative;
    }
</style>
<div class="row status-lokalis-dewasa">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title"><?=Yii::t('fe', 'Status lokalis')?></h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-5">
                        <h3><?= Yii::t('fe', 'Anatomi Tubuh'); ?></h3>
                        <div class="image-frame">
                        <?php
                            echo Html::img( '@web/media/img/img-pemeriksaan/bagian_tubuh_medis.jpg', [
                                'width'=> 500,
                                'height'=> 520,
                            ]);
                        ?>
                        </div>
                        <!-- modal anatomi start -->
                        <div class="tag" style="display: none" data-show="1">
                            <span style="box-sizing: border-box;position: absolute;border: 6px solid #b93d3d;border-color: transparent transparent #ff0000 #ff0000;transform-origin: 0 0;transform: rotate(135deg);box-shadow: -3px 3px 3px -3px rgba(0,0,0,0.3);margin-left: 18px;"></span>
                            <div class="well well-sm" style="min-height:130px;">
                                <div class="form-group">
                                <label class="col-lg-3">Bagian<sup style="color: red">*</sup></label>
                                    <div class="col-lg-9">
                                        <?php echo Html::dropDownList('bagain_tubuh', null, ArrayHelper::map($data_bagiantubuh, 'bagiantubuh_id', 'namabagtubuh'),
                                                    array(
                                                        'class' => 'form-control bagian-tubuh',
                                                        'style' => 'padding : 9px 12px !important;',
                                                        'empty' => '-- Pilih --',
                                                    )); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                <label class="col-lg-3">Detail Bagian<sup style="color: red">*</sup></label>
                                    <div class="col-lg-9">
                                        <?php echo Html::dropDownList('bagain_tubuh', null, [],
                                                    array(
                                                        'class' => 'form-control bagian-tubuh-detail',
                                                        'style' => 'padding : 9px 12px !important;',
                                                        'empty' => '-- Pilih --',
                                                    )); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                <label class="col-lg-3">Catatan<sup style="color: red">*</sup></label>
                                    <div class="col-lg-9">
                                        <input type="text"
                                            placeholder="catatan.."
                                            class="form-control add-caption">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <p class="helper-text ">(tekan <b>enter</b> untuk selesai)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- modal anatomi end -->
                    </div>
                    <div class="col-md-7">
                        <h3><?= Yii::t('fe', 'Tabel Pemeriksaan Anatomi Tubuh'); ?></h3>
                        <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tabel-anggotatubuh">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No</th>
                                    <th><?=Yii::t('fe', 'Tanggal periksa')?></th>
                                    <th><?=Yii::t('fe', 'Bagian tubuh')?></th>
                                    <th><?=Yii::t('fe', 'Bagian tubuh detail')?></th>
                                    <th><?=Yii::t('fe', 'Keterangan')?></th>
                                    <th><?=Yii::t('fe', 'Aksi')?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- table data -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <h3><?= Yii::t('fe', 'Luka Bakar'); ?></h3>
                        <div class="image-frame-luka-bakar">
                        <?php
                            echo Html::img( '@web/media/img/img-pemeriksaan/bagian_luka_bakar.png', [
                                'width'=> 350,
                                'height'=> 550,
                            ]);
                        ?>
                        </div>
                        <!-- modal anatomi start -->
                        <div class="tag-luka-bakar" style="display: none" data-show="1">
                            <span style="box-sizing: border-box;position: absolute;border: 6px solid #b93d3d;border-color: transparent transparent #ff0000 #ff0000;transform-origin: 0 0;transform: rotate(135deg);box-shadow: -3px 3px 3px -3px rgba(0,0,0,0.3);margin-left: 18px;"></span>
                            <div class="well well-sm" style="min-height:130px;">
                                <div class="form-group">
                                <label class="col-lg-3">Berat Luka Bakar</label>
                                    <div class="col-lg-9">
                                    <?=$form->field($model, 'berat_luka_bakar')->radioList($data_luka_bakar, ['inline' => true])->label(false)?>
                                    </div>
                                </div>
                                <div class="form-group">
                                <label class="col-lg-3">Bagian<sup style="color: red">*</sup></label>
                                    <div class="col-lg-9">
                                        <?php echo Html::dropDownList('bagain_tubuh', null, ArrayHelper::map($data_bagiantubuh, 'bagiantubuh_id', 'namabagtubuh'),
                                                    array(
                                                        'class' => 'form-control bagian-tubuh-luka-bakar',
                                                        'style' => 'padding : 9px 12px !important;',
                                                        'empty' => '-- Pilih --',
                                                    )); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                <label class="col-lg-3">Detail Bagian<sup style="color: red">*</sup></label>
                                    <div class="col-lg-9">
                                        <?php echo Html::dropDownList('bagain_tubuh', null, [],
                                                    array(
                                                        'class' => 'form-control bagian-tubuh-detail-luka-bakar',
                                                        'style' => 'padding : 9px 12px !important;',
                                                        'empty' => '-- Pilih --',
                                                    )); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                <label class="col-lg-3">Catatan<sup style="color: red">*</sup></label>
                                    <div class="col-lg-9">
                                        <input type="text"
                                            placeholder="catatan.."
                                            class="form-control add-caption-luka-bakar">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <p class="helper-text ">(tekan <b>enter</b> untuk selesai)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- modal anatomi end -->
                    </div>
                    <div class="col-md-7">
                        <h3><?= Yii::t('fe', 'Tabel Pemeriksaan Luka Bakar'); ?></h3>
                        <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tabel-luka-bakar">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No</th>
                                    <th><?=Yii::t('fe', 'Tanggal Periksa')?></th>
                                    <th><?=Yii::t('fe', 'Bagian Tubuh')?></th>
                                    <th><?=Yii::t('fe', 'Bagian Tubuh Detail')?></th>
                                    <th><?=Yii::t('fe', 'Keterangan')?></th>
                                    <th><?=Yii::t('fe', 'Berat Luka Bakar')?></th>
                                    <th><?=Yii::t('fe', 'Aksi')?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <br>
                        <br>
                        <br>
                        <br>
                        <div class="form-group">
                            <label class="control-label col-sm-2"><?=$model->attributeLabels()['luka_bakar']?></label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <?=Html::activeTextInput($model, 'luka_bakar', [
                                        'class'=>'form-control luka_bakar doco-decimal-100',
                                        'id' => 'adm_persen',
                                        ])?>
                                    <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', '%')?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
