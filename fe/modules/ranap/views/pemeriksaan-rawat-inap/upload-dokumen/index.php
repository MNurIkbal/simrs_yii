<?= \app\components\widgets\InputFile::widget([
		'model' => $model,
		'data'	=> $data,
		'dokumen' => $dokumen
	])?>
		
<?php

// /**
//  * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
//  * A product of PT. Citraraya Nusatama
//  * Powered by Sirs
//  */

// use yii\web\View;
// use yii\helpers\Html;
// use yii\helpers\Url;
// use yii\widgets\Breadcrumbs;
// use kartik\widgets\ActiveForm;
// use yii\helpers\ArrayHelper;
// use kartik\widgets\FileInput;
// use app\components\DocoHelpers;
?>
<!-- <div class="panel">
    <div class="panel-white">
        <div class="panel-body">
        	<div class="row">
	        	<div class="col-md-12">
	                	<?php
	                    // $form = ActiveForm::begin([
	                    //     'id' => 'upload-dokumen-pasien-form',
	                    //     'enableAjaxValidation' => false,
	                    //     'enableClientValidation' => false,
	                    //     'type' => ActiveForm::TYPE_HORIZONTAL,
	                    //     'formConfig' => [
	                    //         'labelSpan' => 3,
	                    //         'deviceSize' => ActiveForm::SIZE_SMALL
	                    //     ],
	                    //     'options' => [
	                    //         'role' => 'form',
	                    //         'enctype' => 'multipart/form-data'
	                    //     ]
	                    // ]);
	                    ?>
	                	<div class="row">
	                        <div class="col-md-6">
	                            <?php
									// $form->field($model, 'attachment', [
	                                //     'horizontalCssClasses' => [
	                                //     'label' => 'text-left control-label col-sm-4',
	                                //     'wrapper' => 'col-md-8',
	                                //     'id' => 'file-logo-header',
	                                //     'class' => 'custom-file-upload'
	                                // ]])->widget(FileInput::classname(),[
	                                // 	'pluginOptions'=>[]
	                                // ]);
	                            ?>
	                        </div>
	                    </div>
	                </div>
	                <?php //ActiveForm::end(); ?>
	        </div>
	    </div>
	    <div class="row">
	        <div class="col-md-12">
	        	<table class="table-striped table-condensed table-hover no-footer">
	        		<thead>
	        			<tr class="bg-inverse">
	        				<th>a</th>
	        			</tr>
	        		</thead>
	        		<tbody>
	        			<tr>
	        				<td>1</td>
	        			</tr>
	        		</tbody>
	        	</table>
	        </div>
	    </div>
    </div>
</div> -->

<?php
// $this->registerJs($this->render("upload.js"));