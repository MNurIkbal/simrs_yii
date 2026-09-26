$.fn.select2noAjuan = function () {
    let configAjuan = {
        url: "/penjamin-asuransi/transaksi-penerimaan-pembayaran/get-ajuan",
        additionalOption: {
            placeholder: "-- Pilih No Ajuan --"
        },
        callbackProccess: (data) => {
            var results = [];
            $.each(data.results.result, function (index, pengajuan) {
                results.push({
                    id: pengajuan.pengajuanklaim_id,
                    text: pengajuan.no_pengajuanklaim
                });
            });

            _noajuan = data.results.rawData;

            return {
                "results": results,
                "pagination": {
                    more: data.results.pagination.more
                },
                "incomplete_results": false,
            };
        },
        callbackData: (params) => {
            penjamin_id = $('#penjamin_id').val();
            return {
                term: params.term,
                page: params.page || 1,
                limit: params.limit,
                id: penjamin_id
            }
        }
    }
    $(this).select2InfinityScroll(configAjuan);
};


