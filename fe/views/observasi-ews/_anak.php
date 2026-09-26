<div class="row">
    <div class="col-sm-4">
        <?= $form
            ->field($model, 'perilaku')
            ->dropdownList(
                [
                    '' => '-- Pilih --',
                    1 => 'Sesuai',
                    2 => 'Diam/Tidur',
                    3 => 'Sensitif',
                    4 => 'Letargie/bingung/penurunan respon terhadap nyeri'
                ],
                ['class' => 'form-control input-sm select2 ews-input', 'data-param' => 'perilaku']
            )->label('Perilaku &nbsp;<span id="score-perilaku" class="score-label" style="font-size:12px;">[Skor: ]</span>');
        ?>
    </div>
    <div class="col-sm-4">
        <?= $form
            ->field($model, 'sistem_kardiovaskuler')
            ->dropdownList(
                [
                    '' => '-- Pilih --',
                    1 => 'Pink/CRT 1-2 detik',
                    2 => 'Pucat/CRT 3 detik',
                    3 => 'Sianosis/CRT 4 detik, takikardia 20x/menit diatas normal',
                    4 => 'Sianosis/mottled atau CRT ≥ 5 atau takikardia, nadi lebih tinggi/ rendah 30x/menit'
                ],
                ['class' => 'form-control input-sm select2 ews-input', 'data-param' => 'sistem_kardiovaskuler']
            )->label('Sistem Kardiovaskuler &nbsp;<span id="score-sistem_kardiovaskuler" class="score-label" style="font-size:12px;">[Skor: ]</span>');
        ?>
    </div>
    <div class="col-sm-4">
        <?= $form
            ->field($model, 'sistem_respirasi')
            ->dropdownList(
                [
                    '' => '-- Pilih --',
                    1 => 'Normal tidak ada retraksi',
                    2 => 'RR > 10 diatas normal, menggunakan otot-otot aksesoris pernapasan',
                    3 => 'RR > 20 diatas normal, terdapat retraksi dada',
                    4 => 'Dibawah normal dengan retraksi dana tau grunting (mendengkur)'
                ],
                ['class' => 'form-control input-sm select2 ews-input', 'data-param' => 'sistem_respirasi']
            )->label('Sistem Respirasi &nbsp;<span id="score-sistem_respirasi" class="score-label" style="font-size:12px;">[Skor: ]</span>');
        ?>
    </div>
</div>