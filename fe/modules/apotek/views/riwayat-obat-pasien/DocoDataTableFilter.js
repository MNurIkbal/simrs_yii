// Author Anggoro
// New Datatabe Boostrap Filter [On Testing],
// Make to compatible from previous function (read: datatableBootstrapFilter)

(function ($) {
    $.fn.DocoDatatableFilter = function (table, custom_search = false, custom_position = false, clear = false, options = false) {
        var form_filter = $(`<form class="advancedFilterNew" onsubmit="return false;"></form>`);
        var filter_body = form_filter.append(`<div class="row" id="newFilterBody"></div>`);
        var filter_footer = form_filter.append(`<div class="row" id="newFilterFooter"></div>`);
        var table_wrapper = $(this);
        var columns = table.settings()[0].aoColumns;
        var inputs_order = [];
        var inputs_order_custom = [];
        var list_custom_search = [];

        var table_id = table.tables().nodes().to$().attr('id');
        var window_path = window.location.pathname;
        var localStorageKey = `DataTables_${table_id}_${window_path}`

        var search_state = jQuery.parseJSON(localStorage.getItem(localStorageKey));
        var stateSaveSetting = table.settings()[0].oInit.stateSave;

        if (custom_search != false) {
            $.each(custom_search, function(i, e){
                var col_index = e[0];
                var col_context = e[1];
                list_custom_search[col_index] = col_context;
            });
        }

        $.each(columns, function (index, column){
            if (column.bSearchable) {
                var input_field = '';
                if (list_custom_search[index] != undefined) {
                   var field_dom = $(list_custom_search[index]);
                   input_field = field_dom.prop('outerHTML');
                } else {
                   input_field = `<input type="text" class="form-control" id="filter-`+ column.data +`" placeholder="`+ column.title +`" col-index="`+ index +`" >`;
                }

                var filter_wrapper = $(`<div class="col-md-3" ><label>`+ column.title +` :</label>`+ input_field +`</div>`);

                var search_state_col = null;
                if (search_state != null) {
                    search_state_col = search_state.columns[index].search.search;
                    label_state_col = search_state.columns[index].search.label;
                }

                if (search_state_col != null) {

                    // Tanggal starDate dan endDate
                    if (filter_wrapper.find('.startDate').length > 0 || filter_wrapper.find('.endDate').length > 0) {
                        var dateRange = search_state_col.split(" - ");
                        if (dateRange != '') {
                            var input_start_date = $(filter_wrapper.find('.startDate')[0]);
                            var input_end_date = $(filter_wrapper.find('.endDate')[0]);
                            var input_target_date = $(filter_wrapper.find('.targetDate')[0]);

                            input_start_date.val(dateRange[0]);
                            input_end_date.val(dateRange[1]);
                            input_target_date.val(dateRange[0]+ ' - ' +dateRange[1]);
                        }
                    }

                    if (filter_wrapper.find('select').length > 0) {
                        var select = $(filter_wrapper.find(`[col-index="`+ index +`"]`));
                        var selected_option = new Option(label_state_col, search_state_col, true, true);
                        select.append(selected_option).trigger('change');
                    }

                    if (filter_wrapper.find('input').length > 0) {
                        var input = $(filter_wrapper.find(`[col-index="`+ index +`"]`));
                        input.val(search_state_col);
                    }
                }

                // Mengurutkan sesuai custom_position
                if (Object.values(custom_position).indexOf(index) >= 0) {
                    inputs_order_custom.push(filter_wrapper);
                } else {
                    inputs_order.push(filter_wrapper);
                }
            }
        });

        $.each(inputs_order_custom.concat(inputs_order), function(i, input){
            filter_body.find("#newFilterBody").append(input);
        });

        form_filter
            .append(filter_body)
            .append(filter_footer);

        table_wrapper.html(form_filter)
            .append(`<button class="btn btn-xs btn-sm btn-primary newAdvancedFilterShow" type="button" style="display:none"><i class="fa fa-search"></i> Pencarian Lengkap</button>`)
            .append(`<div class="clearfix"></div>`);

        filter_footer.find("#newFilterFooter")
            .append(`<div class="col-md-12" style="display:none"><button class="advancedFilterDo" type="button"></button></div>`);

        if (search_state == null && stateSaveSetting) {
            table.state.save();
            search_state = table.state();
        }

        $(form_filter).on('click', '.advancedFilterDo', function (e){
            e.preventDefault();

            $(form_filter).find(`.advancedFilterNew`).button('loading');

            form_filter.find(`input`).each(function() {
                var input = $(this);
                var col_index = input.attr('col-index');

                if (col_index != undefined && input.val() != undefined && input.val() != '') {
                    table.column(col_index).search(input.val());
                    if (stateSaveSetting) {
                        search_state.columns[col_index].search.search = input.val();
                    }
                }
            });

            form_filter.find('select').each(function() {
                var input = $(this);
                var col_index = input.attr('col-index');
                var selected_option = input.find('option:selected');
                var selected_data = {
                    id: selected_option.val(),
                    text: selected_option.text()
                }

                if (col_index != undefined && selected_data.id != '') {
                    table.column(col_index).search(input.val());
                    if (stateSaveSetting) {
                        search_state.columns[col_index].search.search = selected_data.id;
                        search_state.columns[col_index].search.label = selected_data.text;
                    }
                }
            });

            form_filter.find('select[multiple]').each(function() {
                var input = $(this);
                var col_index = input.attr('col-index');
                var selected_options = [];

                $.each(input.find(`option:selected`), function (i, item){
                    selected_options.push($(item).val());
                })

                if (col_index != undefined && selected_options.length > 0) {
                    table.column(col_index).search(selected_options.join());
                }
            });

            table.draw();
        });

        table.on('draw', function(){
            $(table_wrapper).find(".advancedFilterDo").button("reset");
            if (stateSaveSetting) {
                localStorage.setItem(localStorageKey, JSON.stringify(search_state));
            }
        });

        $(table_wrapper).find('.select2').select2();
    };
}(jQuery));