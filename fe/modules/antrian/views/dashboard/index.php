<?php

/* @var $this yii\web\View */

/**
 * @author: unknow
 * @modify : ali.padilah@docotel.com 1/8/2018
 * @description: ui untuk display antrian
**/

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::$app->docoVars->identity("nama_rumahsakit");
?>
<style type="text/css">
	.btn-kembali{
		display: none;
	}
</style>
<div class="col-sm-12 text-right" style="font-size: 20px;">
	<a class="fa fa-chevron-up mr-2 hd-up" onclick="hideHeader()" ></a>
	<a class="fa fa-chevron-down mr-2 hd-down" onclick="showHeader()" style="display:none"></a>
</div>


<div class="site-index">
    <div class="data-module" id="pengambilan">
		<!-- List styles -->
		<!--
        <h2 class="content-group text-semibold">
            <?= strtoupper(Yii::t('fe','Pilih Jenis Antrian'))?>
        </h2>
		-->
        <div class="row pd-20">
        	<div class="col-sm-4 text-left arrow">
				<a href="#antrianCaraousel" role="button" data-slide="prev" style="display: block;"> 
				    <span class="glyphicon glyphicon-chevron-left"></span>
				    <?= strtoupper(Yii::t('fe','Daftar Sebelumnya'))?>
				</a>
        	</div>
        	<div class="col-sm-4">
		        &nbsp;
        	</div>
        	<div class="col-sm-4 text-right arrow">
        		<a href="#antrianCaraousel" role="button" data-slide="next" style="display: block;"> 
				    <?= strtoupper(Yii::t('fe','Daftar Selanjutnya'))?>
				    <span class="glyphicon glyphicon-chevron-right"></span>
				</a>
        	</div>
        </div>

        <div class="row pd-20">
        	<div id="antrianCaraousel" class="carousel slide" data-interval="false" style="display: block;">
        		<div id="loading-content"></div>
			   	<div class="carousel-inner carousel-parent">
			   		<?php foreach ($list_jenis_antrian as $k => $v): ?>
			   			<div class="item <?= $k== 0 ? 'active' : '' ?>">
							<center>
								<div class="carousel-item ">
									<?php foreach ($v as $key => $value): ?>
										<div class="col-sm-4 list-antrian">
									        <div class="panel text-center" style="padding: 40px 4px 40px 4px;">
									        	<img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>" style="width:50%; border-radius: 50%;border: 2px solid #54be8b;">
									            <h6 class="no-margin text-semibold jenis-antrian">
									            	<?= strtoupper($value['name']); ?>
									            </h6>

									            <a href="<?php echo $value['url']; ?>" class="btn btn-info btn-more <?php echo $value['class'];?>" data-id="<?php echo $value['id'];?>">
													<?= Yii::t('fe','Ambil Antrian')?>
												</a>
									        </div>
										</div>
									<?php endforeach ?>
								</div>
							</center>
						</div>
					<?php endforeach ?>
			   </div>
			</div>
        </div>
        <!-- /list styles -->
    </div>

    <div class="data-module" id="layar" style="display: none;">
		<!-- List styles -->
		<!--
        <h2 class="content-group text-semibold">
            <?= strtoupper(Yii::t('fe','Pilih Jenis Layar Antrian'))?>
        </h2>
		-->
        <div class="row pd-20">
        	<div class="col-sm-4 text-left">
				<a href="#layarCaraousel" role="button" data-slide="prev" style="display: block;"> 
				    <span class="glyphicon glyphicon-chevron-left"></span>
				    <?= strtoupper(Yii::t('fe','Daftar Sebelumnya'))?>
				</a>
        	</div>
        	<div class="col-sm-4">
		        &nbsp;
        	</div>
        	<div class="col-sm-4 text-right">
        		<a href="#layarCaraousel" role="button" data-slide="next" style="display: block;"> 
				    <?= strtoupper(Yii::t('fe','Daftar Selanjutnya'))?>
				    <span class="glyphicon glyphicon-chevron-right"></span>
				</a>
        	</div>
        </div>

        <div class="row pd-20">
        	<div id="layarCaraousel" class="carousel slide" data-interval="false" style="display: block;">
			   	<div class="carousel-inner">
			   		<?php foreach ($displayMenu as $k => $v): ?>
			   			<div class="item <?= $k== 0 ? 'active' : '' ?>">
							<center>
								<div class="carousel-item ">
									<?php foreach ($v as $key => $value): ?>
										<div class="col-sm-4 list-antrian">
									        <div class="panel text-center" style="padding: 40px 4px 40px 4px;">
									        	<img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>" style="width:50%; border-radius: 50%;border: 2px solid #54be8b;">
									            <h6 class="no-margin text-semibold jenis-antrian">
									            	<?= strtoupper($value['name']); ?>
									            </h6>

									            <a href="<?php echo $value['url']; ?>" class="btn btn-info btn-more">
													<?= Yii::t('fe','Layar Antrian')?>
												</a>
									        </div>
										</div>
									<?php endforeach ?>
								</div>
							</center>
						</div>
					<?php endforeach ?>
			   </div>
			</div>
        </div>
        <!-- /list styles -->
    </div>

    <div class="row">
        <center>
            <button class="btn btn-info btn-layar" onclick="showmore()"><?= Yii::t('fe','Pindah ke layar antrian')?></button>
			<button class="btn btn-info btn-kembali" ><?= Yii::t('fe','Kembali')?></button>
            <button class="btn btn-default btn-antrian" onclick="showless()" style="display:none;"><?= Yii::t('fe','Pindah ke pengambilan antrian')?></button>
        </center>
    </div>
</div>

<!--begin carousel-->

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        
       
    }, false);
    var icon = '<?= Yii::$app->docoVars->workspace("modul_icon") == "-" ? "/media/img/newsimrs_image/logo-menu/12 MODUL ANTRIAN.svg": Yii::$app->docoVars->workspace("modul_icon"); ?>';
    function showmore () {
        $("#pengambilan").css('display','none');
        $("#layar").css('display','block');
        $(".btn-layar").css('display','none');
        $(".btn-antrian").css('display','block');
    }

    function showless () {
        $("#pengambilan").css('display','block');
        $("#layar").css('display','none');
        $(".btn-layar").css('display','block');
        $(".btn-antrian").css('display','none');
    }
</script>

<?php 
    $this->registerJs($this->render('../assets/js/index.js'));
?>
