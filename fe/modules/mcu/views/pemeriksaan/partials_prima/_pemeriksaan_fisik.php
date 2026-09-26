<div class="row">
    <div class="col-md-12">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#pemeriksaan-fisik" role="button" aria-expanded="false" aria-controls="pemeriksaan-fisik" onKeyPress="">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Pemeriksaan Fisik'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse" id="pemeriksaan-fisik">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($modelFisikNew, 'berat_badan', [
                                    'addon' => ['append' => ['content' => 'Kg']],
                                    ])->textInput([
                                        'class' => 'form-control input-sm doco-decimal-wcomma',
                                        'tabindex' => '1'
                                    ]);
                                ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($modelFisikNew, 'tinggi_badan', [
                                'addon' => ['append' => ['content' => 'cm']],
                                ])->textInput([
                                    'class' => 'form-control input-sm doco-decimal-wcomma',
                                    'tabindex' => '2'
                                ]);
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($modelFisikNew, 'imt', [])
                                    ->label(Yii::t('fe', 'Indeks Massa Tubuh (IMT)'))
                                    ->textInput([
                                        'class' => 'form-control input-sm doco-decimal-wcomma',
                                        'tabindex' => '3'
                                    ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
