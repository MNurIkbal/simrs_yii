let activeModule = null
$(".list-module").bind('click', ({ delegateTarget }) => {
    const { key, name } = $(delegateTarget).data()
    $("#header-page").text(`Log Error ${name}`)
    activeModule = key
    loadActivity()
})
$("#back-btn").bind('click', () => {
    $("#detail-section").hide()
    $("#module-section").show()
})
$("#refresh-btn").bind('click', () => {
    loadActivity(activeModule)
})

const loadActivity = () => {
    showLoader()
    $.ajax({
        url: '/dcms/error-log/log-activity',
        method: 'GET',
        data: {
            module: activeModule
        },
        success: ({ data }) => {
            hideLoader()
            $("#table-detail tbody").html('')
            if (data.logsArray.length > 0) {
                data.logsArray.map((detail, index) => {
                    $("#table-detail tbody").append(`
                        <tr class="row-table-header">
                            <td>${index + 1}</td>
                            <td>${convertDateByFormat(detail.date, 'd-m-Y h:i:s')}</td>
                            <td>${detail.url}</td>
                        </tr>
                        <tr style="cursor: default;">
                            <td colspan="3" style="padding:0px !important;">
                                <div class="detail-error" style="text-align: left; padding: 8px;">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn btn-info btn-labeled btn-xs pull-right btn-copy"><b><i class="fa fa-copy"></i></b>Salin Detail</button>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="control-label">Tanggal :</label>
                                        <p class="label-value-form detail-date">${convertDateByFormat(detail.date, 'd-m-Y h:i:s')}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="control-label">Nama File :</label>
                                        <p class="label-value-form detail-file">${detail.file}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="control-label">Error Message :</label>
                                        <p class="label-value-form detail-message">${detail.message}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="control-label">Baris Ke :</label>
                                        <p class="label-value-form detail-line">${detail.line}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="control-label">Endpoint :</label>
                                        <p class="label-value-form detail-url">${detail.url}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="control-label">Method :</label>
                                        <p class="label-value-form detail-method">${detail.method}</p>
                                    </div>
                                    <div class="col-sm-12" style="padding: 8px;">
                                        <label class="control-label">Payload :</label>
                                        <pre class="detail-payload">${JSON.stringify(detail.payload, undefined, 4)}</pre>
                                    </div>
                                <div>
                            </td>
                        </tr>
                    `)
                })
                $(".btn-copy").bind('click', ({ delegateTarget }) => {
                    copyToClipboard(delegateTarget)
                })
                $("#table-detail td[colspan=3]").find(".detail-error").hide()
                $("#table-detail tbody .row-table-header").click(function (event) {
                    event.stopPropagation()
                    var target = $(event.delegateTarget)
                    if (target.find("td").attr("colspan") > 1) {
                        target.slideUp()
                    } else {
                        target.next().find(".detail-error").slideToggle()
                        // target.closest("tr").next().toggleClass('hidden')
                    }
                })
            } else {
                $("#table-detail tbody").append('<tr><td class="text-center" colspan=3>Data log tidak tersedia</td></tr>')
            }
            $("#detail-section").show()
            $("#module-section").hide()
        }
    })
}

const copyToClipboard = (button) => {
    const rowDetail = $(button).closest('.detail-error')
    const el = document.createElement('textarea');
    el.value = `Tanggal : ${rowDetail.find('.detail-date').text()}\nNama File : ${rowDetail.find('.detail-file').text()}\nLine : ${rowDetail.find('.detail-line').text()}\nError MEssage : ${rowDetail.find('.detail-message').text()}\nEndpoint : ${rowDetail.find('.detail-url').text()}\nMethod : ${rowDetail.find('.detail-method').text()}\nPayload : ${rowDetail.find('.detail-payload').text()}`;
    el.setAttribute('readonly', '');
    el.style.position = 'absolute';
    el.style.left = '-9999px';
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);

    docoNotification('success', 'Detail berhasil disalin', '')
}
