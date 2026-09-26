function processExpandDetail(target, data)
{
    let row = target.closest('tr');
    let table = row.closest('table');

    if (table.find('#expand-detail').length > 0) {
        table.find('#expand-detail').remove();
    }

    table.find('.show-detail').each(function({currentTarget}){
        if($(this).attr('data-expanded')){
            $(this).html('<i class=\"fa fa-plus-square-o\"></i>');
            $(this).removeAttr('data-expanded');
        }else if($(this).is(target)){
            let index = row.index();
            expand_data = data.data[index];
            $(this).attr('data-expanded', true);
            $(this).html('<i class=\"fa fa-minus-square-o\"></i>');
            table_html = drawTable(expand_data, data.columns);
            $(this).closest('tr').after(table_html);
        }
    });
}
function drawHeaderTable(columns)
{
    let html = '<thead><tr class=\"bg-inverse\">';
    for (let i=0; i < columns.length ; i++) {
        html += `<th>${upperCaseFirst(columns[i])}</th>`;
    }
    html += '</tr></thead>';
    return html;
}

function drawValueTable(data_row, no)
{
    let html = `<td>${no}</td>`;
    for(const property_name in data_row){
        html += `<td>${data_row[property_name] != null ? data_row[property_name] : '-'}</td>`;
    }
    return html;
}

function drawBodyTable(data)
{
    let html = '<tbody>';
    for (let i=0; i < data.length; i++) {
        html += `<tr>${drawValueTable(data[i], i+1)}</tr>`;
    }
    html += '</tbody>';
    return html;
}

function drawTable(data, columns)
{
    let html = `<tr id="expand-detail"><td colspan="100%">
        <div class=\"panel panel-default\">
            <div class=\"panel-heading\">
                <h6 class=\"panel-title\">Detail</h6>
            </div>
            <div class=\"panel-body\">
                <div class=\"row\">
                    <div class=\"col-sm-12\">
                        <table class=\"table table-striped table-condensed table-hover\" style=\"width:100%\">
                            ${drawHeaderTable(columns)}
                            ${drawBodyTable(data)}
                        </table>
                    </div>
                </div>
            </div>
        </div></td></tr>`;
    return html;
}

function resetFormFilter()
{
    $('#terraTanggalStart').val('').trigger('change');
    $('#terraTanggalEnd').val('').trigger('change');
    $('#targetDate').val('').trigger('change');
    $('#filter_dokter').val('').trigger('change');
}

function loadDataTable(selectorId, reload = false)
{
    let tableActiveId = $(selectorId).find('table').attr("id");
    let datatable = $('#'+tableActiveId).DataTable();
    let formWrapper = $(`.body-history-terra .filter-forms`);

    //Jika filter beda, maka reload datatable
    if(JSON.stringify(datatable.context[0].ajax.data.advancedFilter) !=
       JSON.stringify(serializeArrayToJson(formWrapper)) ||
       reload
       ){

        datatable.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper);
        datatable.ajax.reload();
    }

}

$(document).ready(function(){

    $('#filter_dokter').select2InfinityScroll({
        url: url +'/all-dokter-list',
        additionalOption: {
            dropdownParent: $('#modal_backdrop')
        },
        callbackData: (params) => {
            return {
                payload: {
                    term: params.term,
                    page: params.page || 1,
                    limit: params.limit,
                    is_active: true,
                }
            }
        }
    })

    $('#terraTanggalStart').pickadate({
        format: 'dd/mm/yyyy',
        selectMonths: true,
        selectYears: true,
        onOpen: function() {
        },
        onClose: function () {
            $('#terraTanggalEnd').focus();
        },
    });
    $('#terraTanggalEnd').pickadate({
        format: 'dd/mm/yyyy',
        selectMonths: true,
        selectYears: true,
    });

    if($('#terraTanggalEnd').val() === null || $('#terraTanggalEnd').val() === ''){
        $('#terraTanggalEnd').attr('disabled', 'disabled');
    };

    $('#terraTanggalStart').on('change', function(){
        if(($('#terraTanggalEnd').val() === null || $('#terraTanggalEnd').val() === '') && $('#terraTanggalStart').val() != null){
            $('#terraTanggalEnd').pickadate('picker').set('select', $('#terraTanggalStart').val(), {format: 'dd/mm/yyyy'});
            $(this).pickadate('picker').set('max', $('#terraTanggalEnd').val());
            $('#terraTanggalEnd').removeAttr('disabled');
        }
        $('#terraTanggalEnd').pickadate('picker').set('min', $(this).val());

        $('.targetDate').val($(this).val()+'-'+$('#terraTanggalEnd').val()).change();
    })

    $('#terraTanggalEnd').on('change', function(){
        if($('#terraTanggalEnd').val() === null || $('#terraTanggalEnd').val() === ''){
            $('#terraTanggalEnd').attr('disabled', 'disabled');
        };
        $('#terraTanggalStart').pickadate('picker').set('max', $(this).val());
        $('.targetDate').val($('#terraTanggalStart').val()+'-'+$(this).val()).change();
    })

    $('.data-reset').on('click', function(){
        resetFormFilter();
        let selectorId = $('.body-history-terra .nav-tabs a.active').attr('href');
        loadDataTable(selectorId, true);
    });

    $('.body-history-terra .data-filter').on('click', function(e){
        // Code copy dari advancedFilterDo on click  docoHealth dan dicustom
        let selectorId = $('.body-history-terra .nav-tabs a.active').attr('href');
        loadDataTable(selectorId, true);
    })

    $('.body-history-terra .nav-tabs a').on('click', function(e){
        loadDataTable($(this).attr('href'));
    })

})
