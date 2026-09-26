<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use app\components\DocoConstants;
    use app\components\DocoHelpers;
use kartik\widgets\DatePicker;
?>

<style type="text/css">
    .modal-dialog { width: 75%; }
    .datepicker > div{
        display: block;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $content['judul_surat']; ?></h5>
</div>
<div class="modal-body">
    <?php    
    $tglHariIni = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
    $tahun_terbilang = DocoHelpers::Terbilang($tglHariIni['tahun']);

    $form = ActiveForm::begin([
        'id' => 'edit-surat-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
    ]);
    ?>
    
    <div class="col-md-12 title">
        <p style="font-size: 12pt; font-weight: bold; text-align: center;">
            <?= $content['judul_surat'] ?><br>
            <?php if($content['enable_no_surat']): ?>
                NO : <?= $model->no_surat; ?>
            <?php endif; ?>
        </p>
    </div>

    <div class="col-md-12 section-1">
        <?php 
            $section_1 = $content['section_1'];
            $section_1 = str_replace('#nama_dokter#', $model->nama_pegawai, $section_1); 
            $section_1 = str_replace('#hari#', $tglHariIni['hari'], $section_1);
            $section_1 = str_replace('#tanggal#', $tglHariIni['tanggal'], $section_1);
            $section_1 = str_replace('#bulan#', $tglHariIni['bulan'], $section_1);
            $section_1 = str_replace('#tahun_terbilang#', $tahun_terbilang, $section_1);
        ?>
        
        <?php if($content['enable_form_pegawai']): ?>
            <div style="margin-bottom: 2%">
            <p style="text-align: justify;">Yang bertanda tangan dibawah ini :</p>
            <?= $form->field($model, 'nama_pegawai', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-2',
                        'wrapper' => 'col-md-5'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm'
                ]); 
            ?>

            <?= $form->field($model, 'nip_pegawai', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-2 has-star',
                        'wrapper' => 'col-md-5'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm'
                ]); 
            ?>
            <?= $form->field($model, 'jabatan_pegawai', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-2 has-star',
                        'wrapper' => 'col-md-5'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm'
                ]); 
            ?>
            </div>
        <?php else: ?>
            <?= Html::activeHiddenInput($model, 'nama_pegawai') ?>
            <?= Html::activeHiddenInput($model, 'nip_pegawai') ?>
            <?= Html::activeHiddenInput($model, 'jabatan_pegawai') ?>
        <?php endif; ?>
        
        <?= $section_1; ?>
    </div>
    
    <div class="col-md-12 section-data-pasien">
        <?= Html::activeHiddenInput($model, 'no_surat') ?>
        <?= Html::activeHiddenInput($model, 'surat_keterangan_pasien_id') ?>
        <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
        <?= Html::activeHiddenInput($model, 'surat_keterangan_id') ?>
        <?= Html::activeHiddenInput($model, 'pegawai_id') ?>  
        <?= Html::activeHiddenInput($model, 'is_eklaim') ?>    
        
        <?php if($content['enable_no_rm']): ?>
            <?= $form->field($model, 'no_rekam_medik', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-2',
                        'wrapper' => 'col-md-5'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm'
                ]); 
            ?>
        <?php else: ?> 
            <?= Html::activeHiddenInput($model, 'no_rekam_medik') ?>
        <?php endif; ?>  

        <?= $form->field($model, 'nama_pasien', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-2',
                    'wrapper' => 'col-md-5'
                ]
            ])->textInput([
                'class' => 'form-control input-sm'
            ]); 
        ?>
        
        <?= $form->field($model, 'tempat_lahir', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-2',
                    'wrapper' => 'col-md-5'
                ]
            ])->textInput([
                'class' => 'form-control input-sm'
            ]); 
        ?>

        <?= $form->field($model, 'tgl_lahir', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-2',
                        'wrapper' => 'col-md-5'
                    ]
                ])->widget(DatePicker::classname(), [
                'name' => 'date_12',
                'value' => $model->tgl_lahir,
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'endDate' => "0d"
                ]
            ]); 
        ?>
        
        <?php 
            if($content['show_umur']) :
                $model->umur = str_replace("umur", "", DocoHelpers::getUmur($model->tgl_lahir));
                echo $form->field($model, 'umur', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-2',
                        'wrapper' => 'col-md-5'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm', 
                    'readonly' => true
                ]);
        ?>
        <?php else: ?> 
            <?= Html::activeHiddenInput($model, 'umur') ?>
        <?php endif; ?>  
        
        <?= $form->field($model, 'jenis_kelamin', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-2',
                    'wrapper' => 'col-md-5'
                ]
            ])->radioList(
                ['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan'],
                [
                    'inline' => true,
                    'class' => 'bs-radio'
                ]
            );
        ?>
        
        <?= $form->field($model, 'pekerjaan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-2',
                    'wrapper' => 'col-md-5'
                ]
            ])->textInput([
                'class' => 'form-control input-sm'
            ]); 
        ?>

        <?= $form->field($model, 'alamat', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-2',
                    'wrapper' => 'col-md-5'
                ]
            ])->textarea([
                'class' => 'form-control input-sm',
                'placeholder' => Yii::t('fe', $model->getAttributeLabel('alamat'))
            ]); 
        ?>
        
        <?php if($content['enable_atas_permintaan']): ?>
            <?= $form->field($model, 'atas_permintaan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-2',
                        'wrapper' => 'col-md-5'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm'
                ]); 
            ?>
        <?php endif; ?>
    </div>

    <?php if($content['enable_pemberian_informasi']): ?>
    <div class="col-md-12 pemberian-informasi">
        <h3><b>Pemberian Informasi :</b></h3>
        <?= $form->field($model, 'dokter_pelaksana', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-2',
                    'wrapper' => 'col-md-5'
                ]
        ])->dropDownList([],[
                'class' => 'select2 form-control input-sm',
                'id' => 'dokter_list',
                'name' => 'SuratKeteranganPasienForm[additional_data][dokter_pelaksana]',
                'prompt' => '— Pilih —'
        ]);
        ?>

        <?= $form->field($model, 'pemberi_informasi',[
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-2',
                'wrapper' => 'col-md-5',
            ]
        ])->dropDownList([],[
                'class' => 'select2',
                'id' => 'pegawai_list',
                'name' => 'SuratKeteranganPasienForm[additional_data][pemberi_informasi]',
                'prompt' => '— Pilih —'
        ]);
        ?>
    </div>
    <?php endif; ?>

    <div class="col-md-12 section-2">
        <?= $content['section_2']; ?>
    </div>

    <div class="col-md-12 section-3">
        <?= $content['section_3']; ?>
    </div>

    <div class="col-md-12 section-hasil">
        <?= $content['section_hasil']; ?>
    </div>

    <div class="modal-footer">
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
        <?php if(!empty($model->surat_keterangan_pasien_id)) : ?>
        <?= Html::button(\Yii::t('fe', '<i class="fa fa-print"></i> Cetak'),[
            'class' => 'btn bg-teal btn-sm btn-print-surat',
            'data-target' => '/reports/viewer/'.$content['code_report'].'?pendaftaran_id=#pendaftaran_id#&surat_keterangan_id='.$model->surat_keterangan_id
        ]); ?>
        <?php endif; ?>
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save', 'id' => 'btn-save-surat']); ?>
    </div>
<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    var tgl_lahir = '<?= $model->tgl_lahir ?>';
    var dokterPelaksana = '<?= $model->dokter_pelaksana ?>';
    var pegawai_informasi = '<?= $model->pemberi_informasi ?>';
    var umur = '<?= $model->umur ?>';
    var str = <?= json_encode($model->additional_data, JSON_HEX_APOS) ?>.replace(/\\'/g, "\\'").replace(/\\"/g, '\\"');
    var additionalData = $.parseJSON(str);
    var pendaftaran = '<?= $model->pendaftaran_id ?>';
    
    $(document).ready(function() {
        $("input[name='SuratKeteranganPasienForm[tgl_lahir]']").val(tgl_lahir);
        $.each(additionalData, function(key, value) {
            let inputType = $("input[name='SuratKeteranganPasienForm[additional_data]["+ key +"]']").attr('type');
            let textareaType = $("textarea[name='SuratKeteranganPasienForm[additional_data]["+ key +"]']").attr('type');
            let selectType = $("select[name='SuratKeteranganPasienForm[additional_data]["+ key +"]']").attr('type');
            if(inputType == 'radio' || inputType == 'checkbox') {
                $("input[name='SuratKeteranganPasienForm[additional_data]["+ key +"]'][value='"+ value +"']").prop("checked", true);
            } else {
                $("input[name='SuratKeteranganPasienForm[additional_data]["+ key +"]']").val(value);
            }

            if(selectType == 'select') {
                $(`select[name='SuratKeteranganPasienForm[additional_data][${key}]'] option[value='${value}']`).attr("selected","selected");
            }

            $("textarea[name='SuratKeteranganPasienForm[additional_data]["+ key +"]']").val(value);

            if(key == "dokter_pelaksana"){
                var _options = new Option(value, value, false, false);
                $('#dokter_list').append(_options).val(value).trigger('change')
            }

            if(key == "pemberi_informasi"){
                var _optionsPegawai = new Option(value, value, false, false);
                $('#pegawai_list').append(_optionsPegawai).val(value).trigger('change')
            }
        })

        $(".field-suratketeranganpasienform-nip_pegawai").addClass("required");
        $(".field-suratketeranganpasienform-jabatan_pegawai").addClass("required");

        $("input[name='SuratKeteranganPasienForm[additional_data][umur]']").val(umur);

        $('#dokter_list').select2InfinityScroll({
            url: baseUrl + modul + url + '/all-pegawai-list?kelompok=1',
        })

        $('#pegawai_list').select2InfinityScroll({
            url: baseUrl + modul + url + '/all-pegawai-list?kelompok=null',
        })
    })

    $("#btn-save-surat").click(function (e) { 
        e.preventDefault();
        var formData = $("#edit-surat-form").serializeArray();
        $().docoForm('click', {
            method: "POST",
            url: baseUrl + modul + url + '/save-edit-surat-keterangan',
            data: formData,
            success: function (data) {
                $("#modal-surat-keterangan").modal("hide");
                tbl_surat.draw();
            }
        });
    });

    $('.pickadate').pickadate({
        format: 'yyyy-mm-dd',
        selectMonths: true,
        selectYears: 60,
        showButtonPanel: false,
        max: true
    });
    
    $(".btn-print-surat").unbind('click');
    $(".btn-print-surat").on('click', function({ currentTarget }) {
        let url = $(this).attr('data-target').replace('#pendaftaran_id#', pendaftaran);
        window.open(url, '_blank');
    })
</script>