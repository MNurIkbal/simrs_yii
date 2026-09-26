
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\typeahead\Typeahead;
?>

<?php echo Html::hiddenInput('no_asuransi_hidden', '', ['id' => 'no_asuransi_hidden']); ?>
<?php echo Html::hiddenInput('nama_pemilik_asuransi', '', ['id' => 'nama_pemilik_asuransi', 'style' => 'text-transform:uppercase']); ?>
<?= Html::activeHiddenInput($modelAsuransi, 'asuransipasien_id', ['id' => 'asuransipasien_id']); ?>

<?= $form->field($modelAsuransi, 'namapemilikasuransi')->textInput([
    'style' => 'text-transform:uppercase'
])->label('Peserta'); ?>
<?= $form->field($modelAsuransi, 'nokartuasuransi', [
    'options' => [
        'class' => 'required'
    ],
    'addon' => ['append' => [
            'content' => '<i class="fa fa-refresh"></i>'
        ]
    ]])->widget(Typeahead::classname(),[
        'pluginOptions' => [
            'highlight' => true,
            'minLength' => 3,
            'limit' => 10
        ],
        'dataset' => [
            [
                'limit' => 10,
                'display' => 'value',
                'remote' => [
                    'url' => Url::to(['/pendaftaran/daftar-rajal/get-asuransi']) . '?q=',
                    'wildcard' => '%QUERY',
                    'replace' => new JsExpression('function (url, uriEncodedQuery) {
                        var _penjamin = $("#penjamin_id").val();
                        if(_penjamin == "") {
                            docoNotification("warning", "Peringatan", "Penjamin harus dipilih terlebih dahulu!");
                                                            
                            return false;
                        }
                        return url + uriEncodedQuery +"&penjamin_id=" + encodeURIComponent(_penjamin);
                    }')
                ]
            ]
        ],
        'pluginEvents' => [
            "typeahead:selected" => "function(obj, item) {
                console.log(item);
                var _no_asuransi = item.value.split(' - ');
                var no_asuransi = '';
                var nama = '';
                var tgl_lahir = '';
                var _masaberlakukartu = '';
                                                
                if(item.masaberlakukartu != null) {
                    var masaberlakukartu = item.masaberlakukartu.split(/[- :]/);

                    var tgl = masaberlakukartu[2];
                    var bln = masaberlakukartu[1];
                    var thn = masaberlakukartu[0];
                    _masaberlakukartu = tgl + '-' + bln + '-' + thn;
                }
                                                
                if(_no_asuransi.length != 0) {
                    no_asuransi = _no_asuransi[0];
                    nama = _no_asuransi[1];
                    tgl_lahir = _no_asuransi[2];
                }

                $('#no_asuransi_hidden').val(no_asuransi);
                $('#nama_pemilik_asuransi').val(nama);
                $('#asuransiform-namapemilikasuransi').val(item.namapemilikasuransi);
                $('#asuransiform-nomorpokokperusahaan').val(item.nomorpokokperusahaan);
                $('#asuransiform-namaperusahaan').val(item.namaperusahaan);
                $('#masaberlakukartu').val(_masaberlakukartu);
                $('#asuransipasien_id').val(item.asuransipasien_id);
                $('#asuransiform-nokartuasuransi').prop('readonly', true);
            }",
        ]
    ])->textInput([
        'placeholder' => $modelAsuransi->getAttributeLabel('nokartuasuransi'),
        'class' => 'form-control input-sm typeahead',
        'readonly' => !empty($modelAsuransi->nokartuasuransi) ? true : false
    ])->hint('<span id="note-asuransi" style="color:green;"></span>')->label('No Kartu');
?>
<?= $form->field($modelAsuransi, 'nomorpokokperusahaan')->textInput()->label('No Jaminan'); ?>
<?= $form->field($modelAsuransi, 'namaperusahaan')->textInput([
    'style' => 'text-transform:uppercase'
])->label('Nama Badan Usaha'); ?>
<?= $form->field($modelAsuransi, 'masaberlakukartu', [
        'addon' => [
            'append' => [
                ['content' => '<i id="btn_addon_tgllahir" class="fa fa-calendar "></i>'],
            ],
        ]
    ])->textInput([
        'class'=>'pickadate-w-month',
        'data-mask' => '99-99-9999',
        'id' => 'masaberlakukartu'
    ])->label('Tanggal Berlaku'); ?>
        	