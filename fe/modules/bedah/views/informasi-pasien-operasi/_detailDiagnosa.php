<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
			<div class="panel-toolbar clearfix">
				<div class="panel-body">
                <?php if(!empty($diagnosaUtama)) : ?>
                    <p><strong>Diagnosa Utama : </strong></p>   
                    <p><strong><ol> <?= !empty($diagnosaUtama['text']) ? $diagnosaUtama['text'] : '-' ?> </ol></strong></p>
                    <?php else : ?>
                    <p><strong><center>Data tidak ditemukan.</center></strong></p>
                <?php endif; ?>


                <?php if(!empty($diagnosaPenyerta) && is_array($diagnosaPenyerta)) : ?>
                    <p><strong>Diagnosa Penyerta : </strong></p>
                    <?php $no = 1; foreach ($diagnosaPenyerta as $k => $v) : ?>
                    <p><strong><ol><?= $no++ ?>. <?= isset($v['text']) ? $v['text'] : '-'?></ol></strong></p>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <p><strong><center>Data tidak ditemukan.</center></strong></p>
                <?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
