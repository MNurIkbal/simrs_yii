<div class="row">
    <div class="col-sm-4">
        <?= $form->field($model, 'nafas', [
            'addon' => ['append' => ['content' => 'x/Menit']]
        ])
        ->textInput([
            'class' => $classFormAngka,
            'data-param' => 'frekuensi_nafas'
        ])
        ->label('Frekuensi Nafas &nbsp;<span id="score-frekuensi_nafas" class="score-label" style="font-size:12px;">[Skor: ]</span>'); ?>
    </div>
    <div class="col-sm-4">
        <?= $form->field($model, 'sistolik', [
            'addon' => ['append' => ['content' => 'mmHg']]
        ])
        ->textInput([
            'class' => $classFormAngka,
            'data-param' => 'sistolik'
        ])
        ->label('Sistolik &nbsp;<span id="score-sistolik" class="score-label" style="font-size:12px;">[Skor: ]</span>'); ?>
    </div>
    <div class="col-sm-4">
        <?= $form->field($model, 'spo2', ['addon' => ['append' => ['content' => '%']]])
        ->textInput(['class' => $classFormAngka, 'data-param' => 'spo2'])
        ->label('SPO2 &nbsp;<span id="score-spo2" class="score-label" style="font-size:12px;">[Skor: ]</span>'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <?= $form
            ->field($model, 'penggunaan_oksigen')
            ->dropdownList(
                ['' => '-- Pilih --', 1 => 'Tanpa Oksigen', 2 => 'Dengan Oksigen'],
                ['class' => 'form-control input-sm select2 ews-input', 'data-param' => 'penggunaan_oksigen']
            )->label('Penggunaan O2 &nbsp;<span id="score-penggunaan_oksigen" class="score-label" style="font-size:12px;">[Skor: ]</span>');
        ?>
    </div>
    <div class="col-sm-4">
        <?= $form->field($model, 'nadi', ['addon' => ['append' => ['content' => 'x/Menit']]])
        ->textInput(['class' => $classFormAngka, 'data-param' => 'denyut_nadi'])
        ->label('Denyut Nadi &nbsp;<span id="score-denyut_nadi" class="score-label" style="font-size:12px;">[Skor: ]</span>'); ?>
    </div>
    <div class="col-sm-4">
        <?= $form->field($model, 'suhu', ['addon' => ['append' => ['content' => '°C']]])
        ->textInput(['class' => 'form-control input-sm ews-input suhu', 'data-param' => 'suhu'])
        ->label('Suhu &nbsp;<span id="score-suhu" class="score-label" style="font-size:12px;">[Skor: ]</span>'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <?= $form
            ->field($model, 'kesadaran')
            ->dropdownList(
                ['' => '-- Pilih --', 1 => 'Sadar Penuh', 2 => 'Rangsangan Suara', 3 => 'Nyeri', 4 => 'Tidak Ada Respon'],
                ['class' => 'form-control input-sm select2 ews-input', 'data-param' => 'kesadaran']
            )->label('Kesadaran &nbsp;<span id="score-kesadaran" class="score-label" style="font-size:12px;">[Skor: ]</span>');
        ?>
    </div>
</div>
