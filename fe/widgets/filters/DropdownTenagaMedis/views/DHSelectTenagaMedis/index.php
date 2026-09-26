<?php 
    use yii\web\View; 
?>

<div class="form-group">
    <select class="form-control" id="<?= $id ?>"></select>
</div>

<?php
    $this->registerJs("
        var configPegawaiTenagaMedis = {
            url: `/api/master/get-tenaga-medis`,
            additionalOption: {
                placeholder: `-- Pilih Dokter --`,
                allowClear: true
            },
            callbackProccess: (data) => {
                var results = []; 
                $.each(data.results.result, function (index, data) {
                    results.push({
                        id: data.pegawai_id,
                        text: data.nama_pegawai
                    });
                });
                return {
                    results: results,
                    pagination: {
                        more: data.results.pagination.more
                    },
                    incomplete_results: false,
                };    
            },
            callbackData: (params) => {
                return {
                    term: params.term,
                    page: params.page || 1,
                    limit: params.limit,
                }
            }
        }
        $(`#$id`).select2InfinityScroll(configPegawaiTenagaMedis);
    ", View::POS_READY);
?>