<?php
use app\components\DocoHelpers;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = $title;
$this->context->layout = 'display-kamar';

$tanggalIndonesia = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
$tanggal = $tanggalIndonesia['hari'].', '.date('d-m-Y');
$time = date('H:i');
?>
<?php $this->registerCss($this->render('assets/style.css'));?>
<div class="container-fluid" id="dashboard-kamar">
	<?php 
		$total_card = count($kamar);
	 	$total_slide = 1;
        $items = [];
        $hide = 'hide';
        if ($total_card > 0) {
            if ($total_card > 12) {
                $hide = '';
            }

            $pembagi = $total_card / 12;
            $total_slide = ceil($pembagi);
            $items = array_chunk($kamar, 12);
           
        }
	?>
	<div id="slide-kamar" class="carousel slide" data-ride="carousel" data-interval="10000">
		  <!-- Indicators -->
		  <ol class="carousel-indicators">
		  	<?php for ($i=0; $i < $total_slide; $i++) {
		  		$class = '';
		  		if ($i == 0) {
		  			$class = 'active';
		  		}
		  	?>
		    	<li data-target="#slide-kamar" data-slide-to="<?= $i;?>" class="<?= $class;?>"></li>
		    <?php }?>
		  </ol>

		  <!-- Wrapper for slides -->
		  <div class="carousel-inner">
		  	<?php for ($i=0; $i < $total_slide; $i++) {
		  		$class = '';
		  		if ($i == 0) {
		  			$class = 'active';
		  		}
		  	?>
			    <div class="item <?= $class;?>">
			    	<div class="row">
						<?php foreach ($items[$i] as $key => $value) { 
							$class = ($value['jumlah_kosong'] == 0) ? 'empty' : '';
						?>
							<div class="col-sm-3 card-wrapper">
								<div class="card">
									<div class="card-header">
										<?= $value['kamar'];?>
									</div>
									<div class="card-body">
										<h2 class="number <?= $class ?>" id="kamar-<?= $value['kamarruangan_id']?>"><?= $value['jumlah_kosong']?></h2>
										<h4 class="text">Kosong</h4>
									</div>
								</div>
							</div>
						<?php }?>
					</div>		    		
			    </div>
			<?php }?>
		  </div>
		</div>
</div>
<div class="navbar-fixed-bottom">
    <img src="/media/img/layar-antrian/footer.png" alt="Footer" class="img-responsive">
    <a class="navbar-brand">
        <div id="time" class="footer-time"><?= $time ?></div>
        <div class="footer-date"><?= $tanggal ?></div>
    </a>
    <marquee class="marquee" direction="" onmouseover="this.stop();" onmouseout="this.start();">
       <?= $konfig['dash_kamarfooter'] ? $konfig['dash_kamarfooter'] : '';?>
    </marquee>
</div>
<?php
    $this->registerJs($this->render('assets/listener.js'));
?>