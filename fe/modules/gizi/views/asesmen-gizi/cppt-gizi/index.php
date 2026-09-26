<?php 

use yii\web\View;
use app\components\DocoHelpers;

?>

<style type="text/css">
    .vertical-top {
        vertical-align: top !important;
    }

    .dataTables_scroll{
        min-height: 102px;
        height: 550px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered" id="table-cppt" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th width="150" id="sort-tgl-cppt-gizi" sort-attr="<?= $get_sort; ?>" style="cursor: pointer;">
                        <?=Yii::t('fe', 'Tanggal/Jam')?>
                    </th>
                    <th width="250"><?=Yii::t('fe', 'Profesional Pemberi Asuhan')?></th>
                    <th><?=Yii::t('fe', 'Hasil Asesmen Pasien')?></th>
                    <th><?=Yii::t('fe', 'Instruksi PPA')?></th>
                    <th width="200"><?=Yii::t('fe', 'Verifikasi')?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<?php 
    $this->registerJs("
        var table;
        $(document).ready(function() {
            table = $('#table-cppt').dataTable({
                filter: false,
                lengthChange: false,
                sorting: [[1, 'asc']], 
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                scrollY: 750,
                deferRender: true,
                scrollCollapse: true,
                scroller: true,
                scroller: {
                    loadingIndicator: true
                },
                dom: 'lfrti',
                ajax: baseUrl + 'gizi/asesmen-gizi/get-data-asesment-cppt?id={$pendaftaran_id}&sort={$get_sort}',
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false, 
                        className: 'vertical-top',
                        sortable: false
                    },
                    {title: '".(\Yii::t('fe', 'Tanggal/Jam'))."', data: 'tgl_cppt', orderable: true, className: 'vertical-top'},
                    {title: '".(\Yii::t('fe', 'Profesional Pemberi Asuhan'))."', data: 'pegawai_nama', orderable: false, className: 'vertical-top'},
                    {title: '".(\Yii::t('fe', 'Hasil Asesmen Pasien'))."', data: 'hasil_asesmen', orderable: false, className: 'vertical-top'},
                    {title: '".(\Yii::t('fe', 'Instruksi PPA'))."', data: 'instruksi_ppa', orderable: false, className: 'vertical-top'},
                    {title: '".(\Yii::t('fe', 'Verifikasi'))."', data: 'verifikasi', orderable: false, className: 'vertical-top'},
                ],
                createdRow: function(row,data,index) {
                    if(data.form_asesmen != null){
                        var td_hasil = $(row).find('td:eq(3)').find('.hasil_asesmen_gizi');
                        $('<div class=\'form_adime hidden\'></div>').insertAfter(td_hasil);
                    }
                    $(row).find('.edit-adime').bind('click', () => {
                        $(this).parent().find('.simpan-adime').removeClass('hidden')
                        $(this).parent().find('.batal-adime').removeClass('hidden')    
                        $(row).find('.edit-adime').addClass('hidden')
                        $(row).find('td:eq(3)').find('.hasil_asesmen_gizi').addClass('hidden');
                        $(row).find('td:eq(3)').find('.form_adime').removeClass('hidden');
                        if($(row).find('td:eq(3)').find('.form_adime')){
                            $(row).find('td:eq(3)').find('.form_adime').docoLoad({
                                type:'POST',
                                url: '/gizi/asesmen-gizi/cppt-gizi-form-adime?id='+pendaftaran_id,
                                dataType: 'html',
                                data: JSON.stringify({data:data.form_asesmen}),
                                contentType: 'application/json',
                                success : function(data) {
                                    // $(' .select2 ').select2();
                                }
                            });
                        }
                    });
                    $(row).find('.batal-adime').bind('click', () => {
                        $(this).parent().find('.simpan-adime').addClass('hidden')
                        $(this).parent().find('.batal-adime').addClass('hidden')  
                        $(row).find('.edit-adime').removeClass('hidden')
                        $(row).find('td:eq(3)').find('.hasil_asesmen_gizi').removeClass('hidden');
                        $(row).find('td:eq(3)').find('.form_adime').html('');
                        $(row).find('td:eq(3)').find('.form_adime').addClass('hidden')
                    });
                    $(row).find('.simpan-adime').bind('click', () => {
                        let form = $(row).find('td:eq(3)').find('.form_adime').find('#form-adime-' + data.form_asesmen.pagt_id);
                        showLoader('Memproses Data ...')
                        $.ajax({
                            url: form.prop('action') + '?id=' + pendaftaran_id,
                            method: 'POST',
                            data: form.serializeArray(),
                            success: (response) => {
                                docoNotification('success', 'Proses berhasil', 'ADIME berhasil disimpan')
                                hideLoader()
                                $('#tab-cppt-gizi').click();
                            },
                            error: (response) => {
                                docoNotification('error', 'Proses gagal', 'ADIME gagal disimpan')
                                hideLoader()
                            }
                        });
                    });
                }
            });


            $('.dataTables_scrollBody > div').remove();

        });

        $('#sort-tgl-cppt-gizi').on('click', function(e) {
            var sort_status = $(this).attr('sort-attr');
            if (sort_status == 'sort_asc') {
                $('#sort-tgl-cppt-gizi').attr('sort-attr', 'sort_desc');
                sort_status = 'sort_desc';
            } else {
                $('#sort-tgl-cppt-gizi').attr('sort-attr', 'sort_asc');
                sort_status = 'sort_asc';
            }
            
            $('#content-cppt-gizi').docoLoad({
                url: '/gizi/asesmen-gizi/cppt-gizi?id='+pendaftaran_id+'&sort='+sort_status,
                dataType: 'html',
                success : function(data) {
                    // $(' .select2 ').select2();
                }
            });    
        });
    ");
    $this->registerJs($this->render('_form_adime.js'), View::POS_END);
?>