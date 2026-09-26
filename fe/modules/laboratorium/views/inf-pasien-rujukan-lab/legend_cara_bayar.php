<div class="col-md-6">
    <div class='legend-index'>
    <div class='legend-header'>Keterangan Cara Bayar</div>
        <div class="legend-wrapper">
            <?php foreach($legendCaraBayar as $key=>$value): ?>
                <div class="legend-information">
                    <div class="legend-information__color" style="background-color: <?= $value['carabayar_kode_warna']; ?>"></div>
                    <div class="legend-information__text"><?= $value['carabayar_nama']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>