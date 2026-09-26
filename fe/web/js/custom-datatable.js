
let pageDatatable = 1;
function generateDatatable(element, options = {}, callbackSuccess = () => { }, callbackFailed = () => { }) {
    showLoader()

    const { url, columns } = options
    const data = {
        columns
    }
    if (typeof options.withHeader === 'undefined' || (typeof options.withHeader !== 'undefined' && options.withHeader)) {
        if ($(element).find('.defaultFilter').length === 0) {
            $(`
                <div class="row advancedFilter defaultFilter">
                </div>
            `).insertBefore($(element).find('.datatable-table-wrapper'))
        }
        if (typeof options.withLimit === 'undefined' || (typeof options.withLimit !== 'undefined' && options.withLimit)) {
            //** Init limit perpage */
            if ($(element).find('#limitDatatableSection').length === 0) {
                $($(element).find('.defaultFilter')[0]).append(`
                    <div class="col-md-2 form-inline" id="limitDatatableSection">
                        <label>Tampilkan</label>
                        <select name="limitPerPage" class="form-control">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label>Data</label>
                    </div>
                `)
                $(element).find("select[name='limitPerPage']").bind('change', () => {
                    Object.assign(options, { initDatatable: false, notRefreshPagination: false })
                    generateDatatable(element, options, callbackSuccess, callbackFailed)
                })
            }
        } else {
            if ($(element).find('#limitDatatableSection').length === 0) {
                $($(element).find('#defaultFilter')[0]).append(`
                    <div class="col-md-2>&nbsp;</div>
                `)
            }
        }

        if (typeof options.withSearchKey === 'undefined' || (typeof options.withSearchKey !== 'undefined' && options.withSearchKey)) {
            //** Init limit perpage */
            if ($(element).find('.searchKeySection').length === 0) {
                $($(element).find('.defaultFilter')[0]).append(`
                    <div class="col-md-10 searchKeySection">
                        <form action="#" class="form-inline pull-right form">
                            <div class="form-group">
                                <label for="searchKey">Cari</label>
                                <input type="text" class="form-control input-sm" name="searchKey">
                            </div>
                        </form>
                    </div>
                `)
            }
        } else {
            if ($(element).find('.searchKeySection').length === 0) {
                $($(element).find('#defaultFilter')[0]).append(`
                    <div class="col-md-2>&nbsp;</div>
                `)
            }
        }
    }
    const tableElement = $($(element).find('table')[0]);
    if (!tableElement.hasClass('datatable')) {
        tableElement.addClass('datatable')
    }
    let orderedParam = {
        key: '',
        type: ''
    }
    if ($(element).find('.fa-angle-double-down').length > 0) {
        orderedParam = {
            key: columns[$(`#${$(element).prop('id')} .datatable-header`).index($(element).find('.fa-angle-double-down').parent())].data,
            type: '1'
        }
    } else if ($(element).find('.fa-angle-double-up').length > 0) {
        orderedParam = {
            key: columns[$(`#${$(element).prop('id')} .datatable-header`).index($($(`#${$(element).prop('id')}`).find('.fa-angle-double-up')[0]).parent())].data,
            type: '2'
        }
    }
    const headerTable = $($(element).find('table')[0]).find('th')
    for (let indexHeader = 0; indexHeader < columns.length; indexHeader++) {
        if (typeof columns[indexHeader].className !== 'undefined' && columns[indexHeader].className !== '') {
            $(headerTable[indexHeader]).addClass(columns[indexHeader].className)
        }
        $(headerTable[indexHeader]).addClass('datatable-header')
        if (typeof columns[indexHeader].orderable === 'undefined' || (typeof columns[indexHeader].orderable !== 'undefined' && columns[indexHeader].orderable === 1 && !$(headerTable[indexHeader]).hasClass('clickable'))) {
            $(headerTable[indexHeader]).addClass('clickable')
            $(headerTable[indexHeader]).addClass(`datatable-header-${columns[indexHeader].data.split('.').join('-')}`)
            $(headerTable[indexHeader]).unbind()
            $(headerTable[indexHeader]).bind('click', ({ delegateTarget }) => {
                $(delegateTarget).find('.fa-angle-double-up')
                let orderType = 0
                if ($(delegateTarget).find('.fa-angle-double-up').length > 0) {
                    $(delegateTarget).find('.fa-angle-double-up').remove()
                } else if ($(delegateTarget).find('.fa-angle-double-down').length > 0) {
                    $(delegateTarget).find('.fa-angle-double-down').remove()
                    orderType = 2
                } else {
                    orderType = 1
                }
                $($(element).find('table')[0]).find('.fa-angle-double-down').remove()
                $($(element).find('table')[0]).find('.fa-angle-double-up').remove()
                if (orderType === 1) {
                    $(delegateTarget).append('<i class="fa fa-angle-double-down"></i>')
                    orderedParam
                } else if (orderType === 2) {
                    $(delegateTarget).append('<i class="fa fa-angle-double-up"></i>')
                }
                Object.assign(options, { initDatatable: false, notRefreshPagination: true })
                generateDatatable(element, options, callbackSuccess, callbackFailed)
            })
        }
    }
    Object.assign(data, {
        searchKey: $(element).find('input[name="searchKey"]').val(),
        orderedParam,
        page: pageDatatable,
        limit: $(element).find("select[name='limitPerPage']").length !== 0 ? $(element).find("select[name='limitPerPage']").val() : 10
    })
    $(element).find('form button[type="reset"]').unbind()
    $(element).find('form button[type="reset"]').bind('click', (e) => {
        e.preventDefault()
        $($(element).find('form')[0]).trigger('reset')
        // Object.assign(options.payload, getNewestAdditionalFilter(element))
        Object.assign(options, { initDatatable: false, notRefreshPagination: false })
        generateDatatable(element, options, callbackSuccess, callbackFailed)
    })
    $(element).find('form button[type="submit"]').unbind()
    $(element).find('form button[type="submit"]').bind('click', (e) => {
        e.preventDefault()
        // Object.assign(options.payload, getNewestAdditionalFilter(element))
        Object.assign(options, { initDatatable: false, notRefreshPagination: false })
        generateDatatable(element, options, callbackSuccess, callbackFailed)
    })
    const elementPayload = options.payload
    if (typeof options.payload !== 'undefined') {
        Object.assign(data, mapPayload(options.payload))
    }
    if (typeof options.initDatatable !== 'undefined' && !options.initDatatable) {
        const pushStateData = $.extend({}, data)
        delete pushStateData.columns
        window.history.pushState(pushStateData, document.title, window.location.origin + window.location.pathname + mappingObjectForPushState(pushStateData))
    } else if (typeof options.initDatatable !== 'undefined' && options.initDatatable && window.location.search !== '') {
        // breakdown query url
        let queryParam = window.location.search.split('?')
        queryParam.splice(0, 1)
        queryParam = decodeURIComponent(queryParam.join('?')).split('&')
        queryParam.map((valueArray) => {
            // result split query url index 0 is key, index 1 is value
            let splitQueryUrl = valueArray.split('=')
            try {
                const json = JSON.parse(decodeURIComponent(splitQueryUrl[1]))
                Object.assign(data, {
                    [splitQueryUrl[0]]: json
                })
                switch (splitQueryUrl[0]) {
                    case 'orderedParam':
                        if (json.type === '1') {
                            $($(element).find(`.datatable-header-${json.key.split('.').join('-')}`)[0]).append('<i class="fa fa-angle-double-down"></i>')
                            orderedParam
                        } else if (json.type === '2') {
                            $($(element).find(`.datatable-header-${json.key.split('.').join('-')}`)[0]).append('<i class="fa fa-angle-double-up"></i>')
                        }
                        break;
                    case 'limit':
                        $(element).find('select[name="limitPerPage"]').val(json)
                        break;
                    case 'searchKey':
                        $(element).find('input[name="searchKey"]').val(json)
                        break;
                    default:
                        $(elementPayload[splitQueryUrl[0]]).val(json)
                        break;
                }
            } catch (e) {
                const valueInput = decodeURIComponent(splitQueryUrl[1])
                Object.assign(data, {
                    [splitQueryUrl[0]]: valueInput
                })
                switch (splitQueryUrl[0]) {
                    case 'searchKey':
                        $(element).find('input[name="searchKey"]').val(valueInput)
                        break;
                    default:
                        if (typeof elementPayload !== 'undefined') {
                            $(elementPayload[splitQueryUrl[0]]).val(valueInput)
                        }
                        break;
                }
            }
        })
    }
    if (typeof options.notRefreshPagination === 'undefined' || (typeof options.notRefreshPagination !== 'undefined' && !options.notRefreshPagination)) {
        Object.assign(data, {
            page: 1
        })
        pageDatatable = 1
    }
    $.ajax({
        url,
        method: 'GET',
        data: data,
        success: (res) => {
            const tbodySection = $($(element).find('tbody')[0])
            tbodySection.html('')
            if (res.data.records.length > 0) {
                const records = res.data.records
                for (let indexRecord = 0; indexRecord < records.length; indexRecord++) {
                    let trHtml = '<tr>'
                    let valueOfColumn = ''
                    for (let indexColumn = 0; indexColumn < columns.length; indexColumn++) {
                        if (columns[indexColumn].isSerialNumber) {
                            valueOfColumn = (pageDatatable - 1) * data.limit + (indexRecord + 1)
                        } else {
                            const splitObject = columns[indexColumn].data.split('.')
                            if (splitObject.length > 1) {
                                valueOfColumn = getValueColumnDatatable(records[indexRecord], splitObject)
                            } else {
                                valueOfColumn = records[indexRecord][columns[indexColumn].data]
                            }
                        }
                        trHtml += `<td ${typeof columns[indexColumn].className !== 'undefined' ? `class="${columns[indexColumn].className}"` : ''}>${escapeHtml(valueOfColumn)}</td>`
                    }
                    tbodySection.append(`${trHtml}</tr>`)
                    tbodySection.find('td').last()
                    if (typeof options.bindElement !== 'undefined') {
                        options.bindElement(records[indexRecord], tbodySection.find('tr').last())
                    }
                }
            } else {
                const totalColumn = $(`#${$(element).prop('id')} .datatable-header`).length
                tbodySection.append(`
                    <tr>
                        <td class="text-center" colspan="${totalColumn}">Data tidak ditemukan</td>
                    </tr>
                `)
            }
            const endShowRecords = ((pageDatatable - 1) * data.limit) + parseInt(data.limit)
            if ($(element).find(".datatable-footer").length === 0) {
                $(element).append(`
                    <div class="datatable-footer">
                        <div class="col-sm-4 datatable-footer-infoTotal text-muted">
                            <p>Menampilkan ${(pageDatatable - 1) * data.limit + 1}-${endShowRecords > res.data.totalRecords ? res.data.totalRecords : endShowRecords} dari ${res.data.totalRecords}</p>
                        </div>
                        <div class="col-sm-8 datatable-footer-pagination text-right">
                            ${paginationSection(pageDatatable, data.limit, res.data.totalRecords)}
                        </div>
                    </div>
                `)
            } else {
                $(element).find('.datatable-footer-infoTotal').html(`<p>Menampilkan ${(pageDatatable - 1) * data.limit + 1}-${endShowRecords > res.data.totalRecords ? res.data.totalRecords : endShowRecords} dari ${res.data.totalRecords}</p>`)
                $(element).find('.datatable-footer-pagination').html(paginationSection(pageDatatable, data.limit, res.data.totalRecords))
            }
            if (res.data.totalRecords === 0) {
                $(element).find('.datatable-footer-infoTotal').html('')
                $(element).find('.datatable-footer-pagination').html('')
            }
            $(element).find('.datatable-footer-pagination li').bind('click', ({ delegateTarget }) => {
                if (!$(delegateTarget).hasClass('disabled') && !$(delegateTarget).hasClass('active')) {
                    if (isNaN(parseInt($(delegateTarget).text()))) {
                        pageDatatable = $(delegateTarget).data('page') === 'next' ? pageDatatable + 1 : pageDatatable - 1
                    } else {
                        pageDatatable = parseInt($(delegateTarget).text())
                    }
                    Object.assign(options, { initDatatable: false, notRefreshPagination: true })
                    generateDatatable(element, options, callbackSuccess, callbackFailed)
                }
            })
            callbackSuccess(res)
            hideLoader()
        },
        failed: (xhr) => {
            hideLoader()
            callbackFailed(xhr)
        },
        error: (xhr) => {
            hideLoader()
            callbackFailed(xhr)
        }
    })
}

function paginationSection(existingPage, limitPerPage, totalRecords) {
    let elementPagination = '<ul class="pagination">'
    const totalPage = Math.ceil(totalRecords / limitPerPage)
    let startPagination = 0
    let endPagination = 5

    elementPagination += `<li data-page="before" class="${(existingPage === 1) ? 'disabled' : 'clickable'}"><span>«</span></li>`
    if ((existingPage - 4) > 0) {
        elementPagination += `<li class="clickable hidden-xs"><span>1</span></li>`
    }
    if (totalPage > 5 && existingPage >= 5) {
        startPagination = existingPage - 4
        endPagination = ((existingPage + 4) >= totalPage) ? totalPage : existingPage + 4
        elementPagination += '<li data-page="next" class="disabled hidden-xs"><span>...</span></li>'
    } else if (totalPage < 5) {
        endPagination = totalPage
    }
    for (let index = startPagination; index < endPagination; index++) {
        elementPagination += `<li class="clickable hidden-xs${existingPage === (index + 1) ? ' active' : ''}"><span>${index + 1}</span></li>`
    }
    if ((existingPage + 4) < totalPage) {
        elementPagination += `<li class="disabled hidden-xs"><span>...</span></li><li class="clickable hidden-xs"><span>${totalPage}</span></li>`
    }
    elementPagination += `<li data-page="next" class="${(existingPage === totalPage ? 'disabled' : 'clickable')}"><span>»</span></li></ul>`
    return elementPagination
}

function getNewestAdditionalFilter(wrapper) {
    const result = {}
    const arrayFilter = $(wrapper).find('form').serializeArray()
    for (let indexArrayFilter = 0; indexArrayFilter < arrayFilter.length; indexArrayFilter++) {
        if (arrayFilter[indexArrayFilter].name !== "searchKey") {
            Object.assign(result, {
                [arrayFilter[indexArrayFilter].name]: arrayFilter[indexArrayFilter].value
            })
        }
    }
    return result
}

function mapPayload(payload) {
    const keyPayload = Object.keys(payload)
    const result = {}
    for (let indexPayload = 0; indexPayload < keyPayload.length; indexPayload++) {
        Object.assign(result, {
            [keyPayload[indexPayload]]: typeof payload[keyPayload[indexPayload]] === 'object' ? $(payload[keyPayload[indexPayload]]).val() : payload[keyPayload[indexPayload]]
        })
    }
    return result
}

function getValueColumnDatatable(originObject, arrayValueToObject) {
    let objectKeyRecord = Object.keys(originObject)
    let tempObject = originObject
    let result = ''
    for (let indexSplitObj = 0; indexSplitObj < arrayValueToObject.length; indexSplitObj++) {
        if (objectKeyRecord.indexOf(arrayValueToObject[indexSplitObj]) >= 0 && tempObject[arrayValueToObject[indexSplitObj]] !== null) {
            if ((indexSplitObj + 1) >= arrayValueToObject.length) {
                result = tempObject[arrayValueToObject[indexSplitObj]]
            } else {
                tempObject = tempObject[arrayValueToObject[indexSplitObj]]
                objectKeyRecord = Object.keys(tempObject)
            }
        } else {
            result = ''
            indexSplitObj = arrayValueToObject.length
        }
    }
    return result
}

function mappingObjectForPushState(dataPayload) {
    const keyObject = Object.keys(dataPayload)
    let result = ''
    for (let index = 0; index < keyObject.length; index++) {
        let key = keyObject[index]
        let value = dataPayload[key]
        result += `${index === 0 ? '?' : '&'}${key}=${encodeURIComponent((typeof value === 'object' ? JSON.stringify(value) : value))}`
    }
    return result
}

function escapeHtml(text) {
    if (typeof text !== 'undefined' && text !== null && /[<]\bscript\b[>]|[</]\bscript\b[>]/gi.test(text.toString())) {
        text = text.toString().split(/[<]\bscript\b[>]/gi)
        let result = ''
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        text.map((itemText) => {
            if (/[</]\bscript\b[>]/gi.test(itemText)) {
                result += '<script>'.replace(/[&<>"']/g, function (m) { return map[m]; }) + itemText.replace(/[&<>"']/g, function (m) { return map[m]; })
            } else {
                result += itemText
            }
        })
        return result
    } else {
        return (text === null || typeof text === 'undefined') ? '' : text
    }
}
