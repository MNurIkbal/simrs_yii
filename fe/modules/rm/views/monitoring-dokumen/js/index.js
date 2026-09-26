let _panelHtml = ''
let _defaultFilters = []
let page = 1
let limit = 20
let generalPage = []
$(document).ready( function() {
    setPagination()
    _panelHtml = `
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-white" style="padding: 3px">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-1" style="padding: 4px 8px">
                                <input type="checkbox" class="checkbox-item" id="checkbox-item-#dokrmid#" onClick="checkboxClicked(this)" data-id="#dokrmid#" data-instalasi="#instalasinama#">
                            </div>
                            <div class="col-md-8" style="padding: 4px 8px">
                                <b style="font-size: 15px; margin-left: 10px">#no_rekam_medik#</b>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-transparent btn-xs btn-only" data-toggle="collapse" role="button" href="#collapsible-content-#dokrmid#">
                                    <i class="fa fa-chevron-down" style="color: black"></i>
                                </button>
                                <button type="button" class="btn btn-transparent btn-xs btn-only dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" style="color: black"></i>
                                </button>
                                  <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a href="#" onClick="sendTo([#dokrmid#], this, 'request')"><i class="fa fa-file-text"/>Request</a></li>
                                    <li><a href="#" onClick="sendTo([#dokrmid#], this, 'delivery')"><i class="fa fa-cube"/>Delivery</a></li>
                                    <li><a href="#" onClick="sendTo([#dokrmid#], this, 'issues')"><i class="fa fa-warning"/>Issues</a></li>
                                    ` + (isRM ? `<li><a href="#" onClick="sendTo([#dokrmid#], this, 'receive')"><i class="fa fa-envelope"/>Receive</a></li>` : ``) + `
                                    ` + (isRM ? `<li><a href="#" onClick="sendReturn([#dokrmid#], this, event)"><i class="fa fa-rotate-left"/>Return</a></li>` : ``) + `
                                    #remindPos#
                                  </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="ow" style="font-size: 13px; margin-bottom: 0;">#nama_pasien#</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <p style="font-size: 13px; margin-bottom: 0">#tgl_pendaftaran#</p>
                            </div>
                        </div>
                        <div class="collapse" id="collapsible-content-#dokrmid#">
                            <div class="row">
                                <div class="col-md-12">
                                    <p style="font-size: 13px">
                                        #no_pendaftaran# <br>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <p style="font-size: 13px">
                                        <b>#ruangan#</b>
                                    </p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <span class="label label-primary">#status_dokumen#</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
    loadData()
    dateRangeHelper(".startDate",".endDate",".targetDate", true);
})

var loadData = (elem = null, reset = false) => {
    _defaultFilters = [
        {
            name: 'tgl_pendaftaran',
            value: $('.targetDate').val(),
        },
        {
            name: 'nama_rm_pendaftaran',
            value: $('#nama-rm-pendaftaran').val(),
        },
        {
            name: 'limit',
            value: limit
        },
        {
            name: 'page',
            value: page
        }
    ]
    if (elem != null) {
        let _requestFilters = [];
            _filters = _defaultFilters;
        $.each( elem.data() , (key, item) => {
            _requestFilters.push({
                    name: key,
                    value: item
                })
        })
        _defaultFilters.push({
            name:'page',
            value: generalPage.find( item => item.name == elem.attr('id')).value
        })
        fetchData( elem, $.merge(_requestFilters, _defaultFilters) , reset)
    } else {
        $.each( $('.monitoring-panel') , (k,v) => {
            let _requestFilters = [];
                _filters = _defaultFilters;
            $.each( $(v).data() , (key, item) => {
                _requestFilters.push({
                        name: key,
                        value: item
                    })
            })
            fetchData( $(`#${ $(v).attr('id') }`), $.merge(_requestFilters, _defaultFilters) , true)
        })
    }
}

var fetchData = ( elem, payload , reset) => {
    let _params = paramConverter(payload)
        _data = []
    $.ajax({
        url: '/rm/monitoring-dokumen/get-document-data?' + _params,
        method: 'get',
        beforeSend: () => {
            const _loadingContent = `
                    <div class="row">
                        <div class="col-md-12 text-center no-data-sign">
                            <h6 style="color: gray; margin-top: 15px"><i class="fa fa-spinner fa-pulse"></i> Sedang Mengambil Data...</h6>
                        </div>
                    </div>
                `
            if (reset) {
                elem.find(`.panel-data`).html('')
                elem.find(`.panel-data`).append(_loadingContent)
            } else {
                elem.find(`.panel-data`).find('.show-more-sign').find('button').html(`<i class="fa fa-spinner fa-pulse"></i> Sedang Mengambil Data...`)
            }
        },
        error: function(response) {            
            if(elem.find(".checkbox-item:checked").length > 0) {
                elem.find(".div-action-title").slideDown();
                elem.find(".div-title").slideUp();
            } else {
                elem.find(".div-action-title").slideUp();
                elem.find(".div-title").slideDown();
            }
        },
        success: function(response) {
            const {data} = response;
            if( !data.length ) {
                elem.find(`.panel-data`).find('.no-data-sign').find('h6').text('Belum Ada Data Tersedia')
            } else {
                /* params need to replace */
                /* #no_rekam_medik#
                /* #nama_pasien#
                /* #tgl_pendaftaran#
                /* #no_pendaftaran#
                /* #ruangan#
                /* #status_dokumen#
                /* #dokrmid#
                /* -----------------------*/
                $.each( data, (key,item) => {
                    if (key == limit && data.length > limit) {
                        return false
                    }
                    _content = _panelHtml;
                    if ((!isRM && (item.status_rekam_medik == CONST_MONITORING_RM_REQUEST || item.status_rekam_medik == CONST_MONITORING_RM_DELIVERY)) || (isRM && !(item.status_rekam_medik == CONST_MONITORING_RM_REQUEST || item.status_rekam_medik == CONST_MONITORING_RM_DELIVERY))) {
                        _content = _content.replaceAll('#remindPos#', `<li><a href="#" onClick="sendTo([#dokrmid#], this, 'remind')"><i class="fa fa-calendar"/>Remind</a></li>`)
                    } else {
                        _content = _content.replaceAll('#remindPos#', '')
                    }
                    _content = _content.replaceAll('#no_rekam_medik#', item.no_rekam_medik).replaceAll('#nama_pasien#', item.nama_pasien).replaceAll('#tgl_pendaftaran#', moment(item.tgl_permintaan).format( 'DD/MM/YYYY' ) ).replaceAll('#no_pendaftaran#', item.no_pendaftaran).replaceAll('#ruangan#', item.status_ruangan).replaceAll('#status_dokumen#', item.status_rekam_medik_nama).replaceAll('#dokrmid#', item.permintaandokrekammedik_id).replaceAll('#instalasinama#', item.instalasi_nama.trim().replaceAll(' ', '_').toLowerCase())
                    if (!reset) {
                        elem.find(`.panel-data`).find('.show-more-sign').before(_content)
                    } else {
                        elem.find(`.panel-data`).append(_content)
                    }
                })
                elem.find('input[type=checkbox]').uniform()
                if (data.length > limit && !$(`#${elem.attr('id')}`).find('.show-more-sign').length ) {
                    elem.find(`.panel-data`).append(`
                        <div class="row show-more-sign">
                            <div class="col-xs-13 text-center" style="margin-bottom: 10px">
                                <button class="btn btn-info btn-sm" id="btn-load-more-${elem.attr('id')}"  data-panel_id="${elem.attr('id')}">
                                    Muat Lebih Banyak
                                </button>
                            </div>
                        </div>
                    `)
                } else if ( data.length > limit && $(`#${elem.attr('id')}`).find('.show-more-sign').length ) {
                    elem.find(`.panel-data`).find('.show-more-sign').find('button').html(`Muat Lebih Banyak`)
                } else {
                    elem.find('.show-more-sign').addClass('hidden')
                }
                $(`#btn-load-more-${elem.attr('id')}`).unbind()
                $(`#btn-load-more-${elem.attr('id')}`).bind('click', ({delegateTarget}) => {
                    generalPage.find(item => item.name == elem.attr('id')).value += 1
                    loadData( $(`#${$(delegateTarget).data('panel_id')}`) )
                })
                elem.find(`.no-data-sign`).remove()
            }
            if(elem.find(".checkbox-item:checked").length > 0) {
                elem.find(".div-action-title").slideDown();
                elem.find(".div-title").slideUp();
            } else {
                elem.find(".div-action-title").slideUp();
                elem.find(".div-title").slideDown();
            }
        }
    })
}

var paramConverter = ( object ) => {
    let parameters = []
    $.each( object, (k,v) => {
        if(v.value != '') {
            parameters.push( encodeURI(v.name + '=' + v.value) )
        }
    })

    return parameters.join('&');
}

var setPagination = () => {
    generalPage = []
    $.each( $('.monitoring-panel') , (k,v) => {
        generalPage.push({
            name: $(v).attr('id'),
            value: 1
        })
    })
}

$('#filter-monitoring').bind('submit', (e) => {
    e.preventDefault()
    page = 1
    setPagination()
    loadData()
})

function checkboxClicked(self) {
    var panel = $(self).closest(".monitoring-panel");
    if(panel.find(".checkbox-item:checked").length > 0) {
        panel.find(".div-action-title").slideDown();
        panel.find(".div-title").slideUp();
    } else {
        panel.find(".div-action-title").slideUp();
        panel.find(".div-title").slideDown();
    }
}

function checklistAll(self) {
    var panel = $(self).closest(".monitoring-panel");
    if(panel.find(".checkbox-item").length == panel.find(".checkbox-item:checked").length) {
        panel.find(".checkbox-item").trigger('click');
    } else {
        panel.find(".checkbox-item:not(:checked)").trigger('click');
    }
}

function sendReturnAll(self, event) {
    var panel = $(self).closest(".monitoring-panel");
    var listId = [];
    var listChecked = panel.find(".checkbox-item:checked");

    for(var i = 0; i < listChecked.length; i++) {
        listId.push($(listChecked[i]).data()['id']);
    }

    sendReturn(listId, self, event);
}

function sendReturn(listId, elemClicked, event) {
    event.preventDefault();
    $('#rakModal').modal('show');
    $('#dropdown_rak').prop('selectedIndex',0);
    $('#dropdown_rak').trigger('change');

    $('#rakModal button.btn-simpan').click(function() {
        $('#rakModal').modal('hide');
        var rak = {
            lokasirak_id:$("#dropdown_rak").val(),
            subrak_id:$("#dropdown_subrak").val()
        };
        sendTo(listId, elemClicked, 'return', rak);
    });
    
}

function sendToAll(self, status) {
    var panel = $(self).closest(".monitoring-panel");
    var listId = [];
    var listChecked = panel.find(".checkbox-item:checked");

    for(var i = 0; i < listChecked.length; i++) {
        listId.push($(listChecked[i]).data()['id']);
    }

    sendTo(listId, self, status);
}

function sendTo(listId, elemClicked, statusTo, rak = null) {
    elemFrom = $(elemClicked).closest(".monitoring-panel");

    elemTo = [];
    if (statusTo == 'issues') {
        temp = [];
        for (var i = 0; i < listId.length; i++) {
            temp.push($('#checkbox-item-' + listId[i]).data()['instalasi']);
        }
        temp.filter(function(value, index, self) {
            return self.indexOf(value) === index;
        });

        for (var i = 0; i < temp.length; i++) {
            elemTemp = $('#monitoring-panel-' + temp[i]);
            elemTo.push(elemTemp);
            data = elemTemp.data();
        }
    } else if (statusTo === 'remind') {
        data = {status_rekam_medik: remindId};
    } else {
        elemTemp = $('#monitoring-panel-'+statusTo);
        elemTo.push(elemTemp);
        data = elemTemp.data();
    }
    requestData = {
        _csrf: $('meta[name="csrf-token"]').attr('content'),
        permintaandokrm_id: listId,
        status: (typeof data === 'undefined' || data == null) ? null : data['status_rekam_medik']
    };
    if(rak != null) {
        requestData = {...requestData, ...rak};
    }
    $.ajax({
        url: '/rm/monitoring-dokumen/update-document-status',
        type: "POST",
        data: requestData,
        success: function (data, status, xhr) {
            if(statusTo !== 'remind') {
                temp = elemTo;
                temp.push(elemFrom);

                var unique = [];
                for(var i = 0; i < temp.length; i++) {
                    if(!unique.includes(elemTo[i].attr('id'))){
                        unique.push(elemTo[i].attr('id'));
                        loadData(elemTo[i], true);
                    }
                }                
            }
        }
    });    
}


$(document).on('change', '#dropdown_rak', function () {
    var dataOut = subrak[$(this).val()];
    var selected = false;

    var child = $("#dropdown_subrak");
    var _prompt = $(this).data("depend_prompt");
    var _valueId = child.val();


    child.empty();
    var promptOpt = new Option(_prompt, "", false, false);
    child.append(promptOpt);
    $.each(dataOut, function (i, item) {
        selected = dataOut[i].subrak_id == _valueId ? true : false;
        var newOption = new Option(dataOut[i].subrak_nama, dataOut[i].subrak_id, false, selected);
        child.append(newOption);
    })
    child.trigger("change");
});