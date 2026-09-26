<?php

/**
 * @author: arief
 * @description: ui untuk display antrian
**/

use app\components\DocoHelpers;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\web\View;
use yii\helpers\Url;

$this->title = $judulLayarAntrian;
$this->context->layout = 'antrian';
?>

<style type="text/css">
	.bgk-simrsdoco{
		background-color: #407e7a;
		border-color: #407e7a;
		color: #fff;
	}
	.panel-body{
		padding: 15px;
	}
	.img-rs{
		border-right: 1px solid #fff;
		padding-right: 30px;
	}

.btn-primary.raised {
    box-shadow: 0 3px 0 0 #00796B;
    background-color: #009688;
    border-color: #00796B;
}
.btn-primary.raised:active, .btn-primary.raised.active {
    background-color: #009688;
    box-shadow: none;
    margin-bottom: -3px;
    margin-top: 3px;
    border-color: #00796B;
}
.btn-huge{
    padding-top:30px;
    padding-bottom:30px;
}

.plan input{
	display: none;
}

.plan label{
	position: relative;
	color: #fff;
	background-color: #aaa;
	text-align: center;
	display: block;
	cursor: pointer;
}
.plan input:checked + .btn-primary.raised{
    box-shadow: 0 3px 0 0 #009688;
    background-color: #4DB6AC;
    border-color: #009688;
}
.plan input:checked + label:after{
	content: "\2713";
	width: 40px;
	height: 40px;
	line-height: 40px;
	border-radius: 100%;
	border: 1px solid #333;
	background-color: #00796B;
	/*border-color: black;*/
	z-index: 999;
	position: absolute;
	top: -10px;
	right: -10px;
}


label input[type="radio"] ~ i.fa.fa-circle-o{
    color: #c8c8c8;    display: inline;
}
label input[type="radio"] ~ i.fa.fa-dot-circle-o{
    display: none;
}
label input[type="radio"]:checked ~ i.fa.fa-circle-o{
    display: none;
}
label input[type="radio"]:checked ~ i.fa.fa-dot-circle-o{
    color: #407e7a;    display: inline;
}
label:hover input[type="radio"] ~ i.fa {
color: #407e7a;
}

div[data-toggle="buttons"] label {
display: inline-block;
padding: 6px 12px;
margin-bottom: 0;
font-size: 14px;
font-weight: normal;
line-height: 2em;
text-align: left;
white-space: nowrap;
vertical-align: top;
cursor: pointer;
background-color: none;
border: 0px solid 
#c8c8c8;
border-radius: 3px;
color: #c8c8c8;
-webkit-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
-o-user-select: none;
user-select: none;
}

div[data-toggle="buttons"] label:hover {
color: #407e7a;
}

div[data-toggle="buttons"] label:active, div[data-toggle="buttons"] label.active {
	color: #407e7a;
-webkit-box-shadow: none;
box-shadow: none;
}
</style>
<?php $imgRs = Url::to('@web/media/img/icon-app/icon-hospital.png');?>
<div class="container-fluid">
	<section class="panel" style="height: 100%;box-shadow: 0px 5px 5px rgba(0,0,0,0.3);">
		<header class="panel-heading bgk-simrsdoco">
			<div class="panel-body">
				<div class="col-md-2">
					<div class="text-center img-rs">
						<img src="<?=$imgRs?>">
					</div>
				</div>
				<div class="col-md-10">
					<h1>DOCO HEALTH</h1>
					<h2 class="no-margin title-antrian">
						<?php echo $judulLayarAntrian; ?>&nbsp;<small class="display-block"><?=$jenisLayarAntrian; ?></small>
					</h2>
				</div>
			</div>
		</header>
		<div class="panel-body">
			<div class="col-md-10 col-md-offset-1">
				<?php
				$form = ActiveForm::begin([
				    'id' => 'ambil-antrian-form', 
				    'type' => ActiveForm::TYPE_HORIZONTAL,
				    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
				]); 
				?>
					<h6>Pilih Poli</h6>
					<fieldset>
						<div class="row">
							<div class="col-lg-12">
								<?php foreach($list_poly_active as $poly_active){
									$primaryKey = 1;
									?>
								<div class="col-md-4 plan">
									<input type="radio" name="poly_pilih" id="<?='btn-pilih-poly-'.$poly_active['poly_id']?>" value="<?=$poly_active['poly_id']?>"><label for="<?='btn-pilih-poly-'.$poly_active['poly_id']?>" class="btn btn-primary btn-lg raised btn-huge"><?=$poly_active['poly_name']?><i class="badge bg-pink position-right">83</i></label>
								</div>

								<?php }?>
							</div>
						</div>
					</fieldset>
					<h6>Pilih Dokter</h6>
					<fieldset>
						<div class="row">
							<div class="col-lg-12">
								<?php foreach($list_poly_active as $poly_active){
									$primaryKey = 1;
									?>
								<div class="col-md-4 plan">
									<input type="radio" name="dokter_pilih" id="<?='btn-pilih-dokter-'.$poly_active['poly_id']?>" value="<?=$poly_active['poly_id']?>"><label for="<?='btn-pilih-dokter-'.$poly_active['poly_id']?>" class="btn btn-primary btn-lg raised btn-huge"><?=$poly_active['poly_name']?><i class="badge bg-pink position-right">83</i></label>
								</div>

								<?php }?>
							</div>
						</div>
					</fieldset>
					<h6>Keterangan Pasien</h6>
					<fieldset>
						<div class="row">
							<div class="col-md-4 plan">
								<input type="radio" name="status_pilih" id="btn-pilih-status-1" value="1">
								<label for="btn-pilih-status-1" class="btn btn-primary btn-lg raised btn-huge">Pasien Baru</label>
							</div>
							<div class="col-md-4 plan">
								<input type="radio" name="status_pilih" id="btn-pilih-status-2" value="2">
								<label for="btn-pilih-status-2" class="btn btn-primary btn-lg raised btn-huge">Pasien Lama</label>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 plan">
								<input type="radio" name="carabayar_pilih" id="btn-pilih-carabayar-1" value="1">
								<label for="btn-pilih-carabayar-1" class="btn btn-primary btn-lg raised btn-huge">Umum</label>
							</div>
							<div class="col-md-4 plan">
								<input type="radio" name="carabayar_pilih" id="btn-pilih-carabayar-2" value="1">
								<label for="btn-pilih-carabayar-2" class="btn btn-primary btn-lg raised btn-huge">BPJS</label>
							</div>
							<div class="col-md-4 plan">
								<input type="radio" name="carabayar_pilih" id="btn-pilih-carabayar-3" value="1">
								<label for="btn-pilih-carabayar-3" class="btn btn-primary btn-lg raised btn-huge">Jaminan/Asuransi Lain</label>
							</div>
						</div>
						<div class="row">
							<div class="btn-group btn-group-vertical" data-toggle="buttons">
						        <label class="btn active">
						          <input type="radio" name='gender1' checked><i class="fa fa-circle-o fa-2x"></i><i class="fa fa-dot-circle-o fa-2x"></i> <span>  Umum</span>
						        </label>
						        <label class="btn">
						          <input type="radio" name='gender1'><i class="fa fa-circle-o fa-2x"></i><i class="fa fa-dot-circle-o fa-2x"></i><span> Polri</span>
						        </label>
					      	</div>
						</div>
					</fieldset>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
		<div class="panel-footer">
			<div class="text-center">
				<div id="clock1" class="label label-rounded label-default"></div>
			</div>
		</div>
	</section>
<div>

<div class="modal fade modal-step-antrian" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			...
		</div>
	</div>
</div>

<?php
$this->registerJs("
	$(document).on('click','[data-toggle=\"modal\"]', function(event) {
	    event.preventDefault();
	    _this = $(this);
	    // _this.button('loading');
	    modal = $(_this.data('target'));
	    width = _this.data('width');
	    var modal_content = modal.find('div.modal-dialog');
	    url = _this.attr('href');

	    if (typeof width != 'undefined') {
	        modal_content.css('width',width);
	    }

	    if (typeof url == 'undefined') {
	        url = _this.attr('action');
	    }

	    $('.modal-content', modal).empty();
	    var _html = '<div class=\"text-center\">';
	        _html += '<h3><i class=\"icon-spinner4 spinner position-center\"></i>&nbsp;&nbsp;<b>Memuat . . . </b></h3>';
	    _html +=    '</div>';
	    modal.find('.modal-content').html(_html).load(url, function() {
	        _this.button('reset');
	    });
	});
	
	$('#ambil-antrian-form').addClass('steps-basic');
    $(\".steps-basic\").steps({
        headerTag: \"h6\",
        bodyTag: \"fieldset\",
        transitionEffect: \"fade\",
        titleTemplate: '<span class=\"number\">#index#</span> #title#',
        labels: {
            finish: 'Cetak Antrian'
        },
        onFinished: function (event, currentIndex) {
            alert(\"Form submitted.\");
        }
    });
	$('.img-check').click(function(e) {
        $('.img-check').not(this).removeClass('check')
    		.find('input').prop('checked',false);
    	$(this).addClass('check')
            .find('input').prop('checked',true);
    });
    $('#clock1').clock({
    	'dateFormat':'l, d F Y',
    	'timeFormat':'H:i:s',
    	'langSet':'id'
    	});
	",
    View::POS_READY,
    'layarantrian'
);
?>