<?php
use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;

use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
?>

<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-12">
                	<div>

			            <div class="row">
		                    <div class="panel panel-default">
		                        <div class="panel-heading">
		                            <h5 class="panel-title"><?= Yii::t('fe', 'Status Lokalis') ?></h5>
		                        </div>
		                        <div class="panel-body">
		                            <div class="col-md-5">
		                                <div class="image-frame">
		                                <?php
											echo Html::img('@web/media/img/img-pemeriksaan/bagian_tubuh_medis.jpg', [
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
		                                                <?php
		                                                echo Html::dropDownList(null, null, ArrayHelper::map($data_bagiantubuh, 'bagiantubuh_id', 'namabagtubuh'),
		                                                               array(
		                                                                'class' => 'form-control bagian-tubuh',
		                                                                'style' => 'padding : 9px 12px !important;',
		                                                                'empty' => '-- Pilih --',
		                                                               ));
		                                                               ?>
		                                            </div>
		                                        </div>
		                                        <div class="form-group">
		                                         <label class="col-lg-3">Detail Bagian<sup style="color: red">*</sup></label>
		                                            <div class="col-lg-9">
		                                                <?php echo Html::dropDownList(null, null, [],
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
		                                <div class="col-lg-6">
		                                    <h6 class="panel-title"><?=Yii::t('fe', 'Terapi/Tindakan Yang Dilakukan Di IGD')?></h6>
		                                </div>
		                                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tabel-anggotatubuh doco-wrap-table-text">
		                                    <thead>
		                                        <tr class="bg-inverse">
		                                            <th>No</th>
		                                            <th><?=Yii::t('fe', 'Tanggal periksa')?></th>
		                                            <th><?=Yii::t('fe', 'Bagian tubuh')?></th>
		                                            <th><?=Yii::t('fe', 'Bagian tubuh detail')?></th>
		                                            <th><?=Yii::t('fe', 'Catatan')?></th>
		                                            <th><?=Yii::t('fe', 'Aksi')?></th>
		                                        </tr>
		                                    </thead>
		                                    <tbody>
		                                        <!-- table data -->
		                                    </tbody>
		                                </table>
		                            </div>
		                        </div>
		                    </div>
		                </div>

                	</div>
                </div>
            </div>
        </div>
    </div>
</div>
