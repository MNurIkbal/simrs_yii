<div class="row">
    <div class="col-md-12 ml-3 mt-3">
        <div class="panel panel-default">
            <a data-toggle="collapse" href="#kebiasaanolahraga" role="button" aria-expanded="true" aria-controls="kebiasaanolahraga">
                <div class="panel-heading flex-container">
                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Tingkat Kebiasaan Olahraga'); ?></b></h6>
                    <div>
                        <ul class="icons-list">
                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                        </ul>
                    </div>
                </div>
            </a>
            <div class="panel-body collapse multi-collapse collapse in" id="kebiasaanolahraga">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($model, 'tingkat_kebiasaan_olahraga')->radioList([
                                0 => "Ringan (Intensitas riangan < 150 menit atau intensitas berat < 75 menit dalam seminggu)",
                                1 => "Direkomendasikan (Intensitas sedang < 150 - 300 menit atau intensitas berat  75 - 100 menit dalam seminggu)",
                                2 => "Sangat direkomendasikan (Intensitas sedang 150 - 300 menit atau intensitas berat 75 - 100 menit dalam seminggu ditambah olahraga penguatan otot 2x seminggu)"
                            ]); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($model, 'jenis_olahraga')->textInput(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>