<?php

/**
 @Author: Ardi Pratama
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\web\JsExpression;
?>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe', 'Reasesmen (SOAP)')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar clearfix">
        <?php 
        if($is_dokter){
	        echo DocoHelpers::generateToolbar([
	            'custom-save' => [
	                'type' => 'button',
	                'title' => Yii::t('fe', 'Ubah'),
	                'icon' => 'fa fa-pencil',
	                'attributes' => [
	                    'id' => 'ubah-soap',
	                    'data-options'=>'click'
	                ],
	            ],
	        ],'');
		}
        ?>

    </div>
    <div class="panel-body">
        <div class="row">
            <div class="panel panel-flat">
                <div class="panel-body">
                	<div class="row">
                		<div class="col-md-3">
                        	<label class="text-left control-label col-sm-5"><b><?=Yii::t('fe', 'Berat Badan')?></b></label>
                            <div class="col-sm-5">
                            	<p><b>:</b> <?=@$data_soap['beratbadan_kg']?> Kg</p>
                            </div>
                		</div>
                		<div class="col-md-3">
                        	<label class="text-left control-label col-sm-5"><b><?=Yii::t('fe', 'Tinggi Badan')?></b></label>
                            <div class="col-sm-5">
                            	<p><b>:</b> <?=@$data_soap['tinggibadan_cm']?> cm</p>
                            </div>
                		</div>
                		<div class="col-md-3">
                        	<label class="text-left control-label col-sm-5"><b><?=Yii::t('fe', 'Indeks Masa Tubuh')?></b></label>
                            <div class="col-sm-5">
                            	<p><b>:</b> <?=@$data_soap['imt']?> Kg/m2</p>
                            </div>
                		</div>
                		<div class="col-md-3">
                        	<label class="text-left control-label col-sm-5"><b><?=Yii::t('fe', 'Kategori Indeks Masa Tubuh')?></b></label>
                            <div class="col-sm-5">
                            	<p><b>:</b> <?=@$data_soap['imt']?></p>
                            </div>
                		</div>
                	</div>
                	<div class="row">
                		<div class="col-md-3">
                        	<label class="text-left control-label col-sm-5"><b><?=Yii::t('fe', 'Tekanan Darah')?></b></label>
                            <div class="col-sm-5">
                            	<p><b>:</b> <?=@$data_soap['td_diastolic']?> / <?=@$data_soap['td_systolic']?> mmHg</p>
                            </div>
                		</div>
                		<div class="col-md-3">
                        	<label class="text-left control-label col-sm-5"><b><?=Yii::t('fe', 'Pernafasan')?></b></label>
                            <div class="col-sm-5">
                            	<p><b>:</b> <?=@$data_soap['pernapasan']?> x/m</p>
                            </div>
                		</div>
                		<div class="col-md-3">
                        	<label class="text-left control-label col-sm-5"><b><?=Yii::t('fe', 'Detak Nadi')?></b></label>
                            <div class="col-sm-5">
                            	<p><b>:</b> <?=@$data_soap['detaknadi']?> x/m</p>
                            </div>
                		</div>
                		<div class="col-md-3">
                        	<label class="text-left control-label col-sm-5"><b><?=Yii::t('fe', 'Suhu Tubuh')?></b></label>
                            <div class="col-sm-5">
                            	<p><b>:</b> <?=@$data_soap['suhutubuh']?> c</p>
                            </div>
                		</div>
                	</div>
                    <div class="row">
                		<div class="col-md-3">
                        	<label class="text-left control-label col-sm-5"><b><?=Yii::t('fe', 'Nyeri')?></b></label>
                            <div class="col-sm-5">
                            	<p><b>:</b> <?=@$data_soap['keterangan_nyeri']?></p>
                            </div>
                		</div>
                		<div class="col-md-3">
                        	<label class="text-left control-label col-sm-5"><b><?=Yii::t('fe', 'Resiko Jatuh')?></b></label>
                            <div class="col-sm-5">
                            	<p><b>:</b> <?=@$data_soap['keterangan_resikojatuh']?></p>
                            </div>
                		</div>
                    </div>
                </div>
            </div>
        </div>
    	<div class="row">
            <div class="col-lg-6">

            <fieldset style="border:1px solid #ddd; padding:10px;">
                <legend style="font-weight:bold;"><?= Yii::t('app', 'SOAP') ?></legend>

                <div class="form-group">
                	<label class="control-label col-sm-4">
                		S <small class="text-muted">(subjektif)</small>
                	</label>
                	<div class="col-sm-8">
                        <p><b>:</b> <?=@$data_soap['subject']?></p>
                	</div>
                </div>
                <div class="form-group">
                	<label class="control-label col-sm-4">
                		O <small class="text-muted">(objektif)</small>
                	</label>
                	<div class="col-sm-8">
                        <p><b>:</b> <?=@$data_soap['object']?></p>
                    </div>
                </div>
                <div class="form-group">
                	<label class="control-label col-sm-3">
                		A  <small class="text-muted">(assesment)</small>
                	</label>
                	<div class="col-sm-9">
                		<div class="form-group">
                			<label class="control-label col-sm-4">Diagnosa Utama</label>
                			<div class="col-sm-8">
                        		<p><b>:</b> <?=@$data_soap['diagnosa_utama']?></p>
                			</div>
                		</div>
                		<div class="form-group">
                			<label class="control-label col-sm-4">Diagnosa Penyerta</label>
                			<div class="col-sm-8">
		            				<?php
		            				$html_diag_penyerta = '';
		            				if(isset($data_soap['list_diagnosa_penyerta']) && count($data_soap['list_diagnosa_penyerta'])>0){
		            					$html_diag_penyerta = '<ul>';
		            					foreach ($data_soap['list_diagnosa_penyerta'] as $k_dpenyerta => $v_dpenyerta) {
		            						// echo "$v_dpenyerta";
		            						$html_diag_penyerta .= '<li>'.@$v_dpenyerta.'</li>';
			            				}
			            				$html_diag_penyerta .= '</ul>';
			            			}
		                    		?>
                				<p><b>:</b><?=@$html_diag_penyerta?></p>
                			</div>
                		</div>
                    </div>
                </div>
                <div class="form-group">
                	<label class="control-label col-sm-4">
                		P <small class="text-muted">(plan)</small>
                	</label>
                	<div class="col-sm-8">
                        <p><b>:</b> <?=@$data_soap['planning']?></p>
                    </div>
                </div>
                <div class="form-group">
                	<label class="control-label col-sm-4">
                		Catatan Dokter
                	</label>
                	<div class="col-sm-8">
                        <p><b>:</b> <?=@$data_soap['catatan_dokter']?></p>
                    </div>
                </div>

            </fieldset>
            </div>
        </div>
    </div>
</div>


<?php
$this->registerJs('
	$("#ubah-soap").on("click",function(){

        $("#content-soap").docoLoad({
            url: "/rajal/pemeriksaan/ubah-soap?id='.$encryptedPendaftaranId.'",
            dataType: "html",
            success : function(data) {
        	}
    	});
	});
');
?>