var docoHelper = {
  is_pembulatankeatas: false,
  satuanpembulatan: 500,
  decimal_places: 2,
  listen: false,
  options: {},
  onClick: function (object) {
    var _obj = $(object);
  },
  convertToAngka: function (rupiah) {
    rupiah = rupiah.toString();
    var rupiah2 = rupiah.split(',');
    var result = parseFloat(rupiah2[0].replace(/[^0-9\-]/g, ''));
    if (typeof rupiah2[1] != 'undefined') {
      result = result + '.' + rupiah2[1];
    }
    return result;
  },
  convertToDecimal: function (index, val, replaceTo, limit = false) {
    var valNumber = index.value;
    if (valNumber != undefined) {
      var valNumber = index.value;
      valNumber = checkLimitless(valNumber, limit);
    } else {
      valNumber = checkLimitless(index, limit);
    }
    var num = parseFloat(valNumber.replace(',', '.'));
    num = isNaN(num) ? 0 : num;
    num = num > 100 ? 100 : num;
    num = num < 0 ? 0 : num;
    var rounded = num.toFixed(2); // Round Number
    index.value = rounded.toString().replace(val, replaceTo); // Output Result
    if (index.value != undefined) {
      return index.value;
    } else {
      index = rounded.toString().replace(val, replaceTo); // Output Result
      return index;
    }
  },
  convertToAngkaWComma: function (rupiah) {
    rupiah = rupiah.toString();
    var rupiah2 = rupiah.split(',');
    var result = parseInt(rupiah2[0].replace(/[^0-9][.,]/g, ''));

    return result;
  },
  valToDecimal: function (val, roundTo = 2) {
    val = isNaN(val) ? 0 : val;
    val = val == null ? 0 : val;
    var _val = parseFloat(val).toFixed(roundTo); // Round Number
    _val = _val.toString();
    _val = _val.replace('.', ',');

    return _val;
  },
  convertToRupiah: function (angka) {
    angka = Math.round(angka * 100) / 100;
    var number_string = angka.toString().toString().replace(/\./g, ','),
      split = number_string.split(','),
      absvalue = split[0];
    var _split = split[0].replace(/\-/g, '');
    var sisa = _split.length % 3,
      rupiah = _split.substr(0, sisa),
      ribuan = _split.substr(sisa).match(/\d{1,3}/gi);
    var simbol = absvalue.match(/\-/gm);
    simbol = simbol == null ? '' : simbol;

    if (ribuan) {
      separator = sisa ? '.' : '';
      rupiah += separator + ribuan.join('.');
    }

    rupiah =
      simbol + (split[1] != undefined ? rupiah + ',' + split[1] : rupiah);

    return rupiah;
  },
  numberFormat: function (number, decimals, dec_point, thousand_sep) {
    dec_point = typeof dec_point !== 'undefined' ? dec_point : ',';
    thousands_sep = typeof thousands_sep !== 'undefined' ? thousands_sep : '.';

    var parts = number.toFixed(decimals).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

    return parts.join(dec_point);
  },
  // This function turn 1.000.000,69 to database number format 1000000.69
  // Instead using replaceAll() this function use split() and join().
  // compatible issue with older js engine.
  removeNumberFormat: function (value) {
    var number = parseFloat(value.replace(/\./g, "").replace(',', '.'));

    return number;
  },
  showInstalasi: [330, 329, 319],
  showCaraBayar: [326, 315, 314],
  groupUmum: 417,
  groupBPJS: 418,
  groupJaminan: 419,
  //last edited by: rizqi fitrianto
  //date: 16-01-2018
  //desc: function delete, avoid redundant code
  fungsidelete: function (options, _this, btn) {
    $.ajax({
      url: options.url,
      type: 'POST',
      dataType: 'json',
      async: options.async,
      data: { _csrf: $('meta[name="csrf-token"]').attr('content') },
      beforeSend: function () {
        options.before();
        showLoader();
      },
      success: function (data) {
        options.success(data);

        var title = 'Proses Berhasil !';
        var text = 'Data berhasil dihapus';

        try {
          if(data.response.message != undefined) {
            text = data.response.message
          } else {
            title = data.response.title;
            text = data.response.text;
          }

          if (typeof data.response.return != 'undefined') {
            eval('( ' + data.response.return + ' )');
          }
        } catch (e) { }

        new PNotify({
          title: title,
          text: text,
          addclass: 'alert alert-success alert-arrow-right alert-styled-right',
          type: 'success',
        });
        btn.button('reset');
      },
      error: function (data) {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
        options.error(data);
        hideQuestionDialog();
        $('body').find('.confirm-dialog-overlay').remove();

        // jika error server dan ini ajax toggle maka akan kembali ke value semula
        var bootstrapSwitch = _this.attr('bootstrapSwitch')
        if(bootstrapSwitch != undefined){
          _this.bootstrapSwitch('state', !_this.is(':checked'), "false");
        }

        var data = data.responseJSON;
        // var title = 'Proses Gagal !';
        // var text = 'Terjadi kesalahan pada sistem';
        var title;
        var text;
        if (
          typeof data.responseJSON != 'undefined' &&
          data.responseJSON != null
        ) {
          title = data.responseJSON.title;
          text = data.responseJSON.text;
        } else {
          title = 'Proses Gagal !';
          text = 'Terjadi kesalahan pada sistem';
        }

        try {
          title = data.response.title;
          text = data.response.text;
        } catch (e) { }
        // Kondisi skip notif error
        if(options.skipErrorNotif != true) {
            new PNotify({
              title: title,
              text: text,
              addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
              type: 'error',
            });
          }
        btn.button('reset');
      },
    }).done(function () {
      hideQuestionDialog();
      $('body').find('.confirm-dialog-overlay').remove();
      btn.button('reset');
    });
  },
  detail: function (object) {
    var _tr = $(object).closest('tr');
    var _a = $(object).find('i');
    var _lenght = _tr.children('td').length;
    var _url = $(object).data('source');
    var tr = $('<tr/>');
    var td = $('<td/>');

    if (_a.hasClass('fa-minus-square-o')) {
      _a.removeClass('fa-minus-square-o');
      _tr.next().remove();
      _a.addClass('fa-plus-square-o');
    } else {
      _a.removeClass('fa-plus-square-o');
      td.attr('colspan', _lenght);
      tr.insertAfter(_tr);
      tr.append(td);
      $.ajax({
        url: _url,
        type: 'GET',
        dataType: 'html',
        beforeSend: function (data) {
          showLoader();
          td.html(
            '<h1 class="text-center text-primary"><i class="fa fa-gear fa-spin fa-3x fa-fw"></i> Loading . . .</h1>'
          );
        },
        success: function (data) {
          td.html(data);
        },
        error: function (data) {
          td.html(
            '<div class="text-center"><h1><b> ERROR ' +
            data.status +
            ' ( ' +
            data.statusText +
            ' ) !!!</b></h1></div>'
          );
        },
      });
      _a.addClass('fa-minus-square-o');
    }
  },
  terbilang: function (angka) {
    if (angka >= 0) {
      var _string = [
        '',
        'Satu',
        'Dua',
        'Tiga',
        'Empat',
        'Lima',
        'Enam',
        'Tujuh',
        'Delapan',
        'Sembilan',
        'Sepuluh',
        'Sebelas',
      ];
      if (angka < 12) {
        return ' ' + _string[Math.floor(angka)];
      } else if (angka < 20) {
        return this.terbilang(angka - 10) + ' Belas';
      } else if (angka < 100) {
        return (
          this.terbilang(angka / 10) + ' Puluh' + this.terbilang(angka % 10)
        );
      } else if (angka < 200) {
        return ' Seratus' + this.terbilang(angka - 100);
      } else if (angka < 1000) {
        return (
          this.terbilang(angka / 100) + ' Ratus' + this.terbilang(angka % 100)
        );
      } else if (angka < 2000) {
        return ' Seribu' + this.terbilang(angka - 1000);
      } else if (angka < 1000000) {
        return (
          this.terbilang(angka / 1000) + ' Ribu' + this.terbilang(angka % 1000)
        );
      } else if (angka < 1000000000) {
        return (
          this.terbilang(angka / 1000000) +
          ' Juta' +
          this.terbilang(angka % 1000000)
        );
      }
    } else {
      return 'Null';
    }
  },
  ajax: function (options, _this, btn) {
    btn.button('loading');
    $('#confirm-dialog-overlay').remove();
    var header = options.confirmTitle ? options.confirmTitle : 'Perhatian !';
    var message = options.confirmMessage
      ? options.confirmMessage
      : 'Apakah anda yakin untuk menyimpan data ini ?';
    var label = {
      buttons: {
        Yes: 'button-yes',
        No: 'button-no',
      },
    };
    var _ajax = function (options, _this, btn) {
      var data
      if(options.isDataString) {
        data = options.data
      } else {
        data = Object.keys(options.data).length
          ? options.data
          : _this.serializeArray();
        if (options.isUpload) {
          data = options.data;
        }
        $.each(data, function (key, val) {
          try {
            var _input = $("input[name='" + val.name + "']");
            if (_input.hasClass('doco-number')) {
              if (_input.val() != '') {
                data[key].value = docoHelper.convertToAngka(_input.val());
              }
            }
          } catch (error) { }
        });
      }

      $.ajax({
        url: options.url,
        type: options.method,
        dataType: options.dataType,
        async: options.async,
        // cache: false,
        contentType: options.contentType,
        processData: options.processData,
        data: data,
        beforeSend: function (xhr) {
          options.before();
          showLoader();
          docoHelper.listen = true;
        },
        success: function (data) {
          var data = data;
          var succTitle = 'Proses Berhasil !';
          var succMsg = 'Data berhasil di simpan.';
          options.success(data);

          try {
            // Setting Sukses Title
            if (data.response.title) {
              succTitle = data.response.title;
            }
            // Setting Sukses Message
            if (data.response.text) {
              succMsg = data.response.text;
            }

            if (data.response.return) {
              eval('( ' + data.response.return + ' )');
            }
          } catch (e) { }

          // Cek notif
          if (options.skipSuccessNotif != true) {
            docoNotification('success', succTitle, succMsg);
            btn.button('reset');
            $('[data-popup="tooltip"]').tooltip();
          }
        },
        error: function (data, status, error) {
          docoHelper.listen = false;
          if (
            typeof options.skipScrollUp == 'undefined' ||
            !options.skipScrollUp
          ) {
            $('html, body').animate({ scrollTop: 0 }, 'slow');
          }

          var errMsg = 'Terjadi kesalahan, silahkan cek inputan.';
          var errTitle = 'Proses Gagal !';
          // extend message from backend : Ali
          $('.help-block.error').remove();
          try {
            var error = data.responseJSON.response;
            var sttsErr = data.status;
            var extMessage = error.message ? ' , ' + error.message : '';
            if (sttsErr == '422') {
              var logo =
                '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp';
              if (error.message) {
                errMsg = error.message;
              }
              if (error.text) {
                errMsg = error.text;
              }
              // Setting Error title
              if (error.title) {
                errTitle = error.title;
              }
              // Parsing Error;
              $.each(error.data, function (key, val) {
                var _field = $('[name="' + key + '"]');
                var _div = $('.error_' + key);
                var _getId = _field.attr('id');
                var _group = _field.closest('div.input-group');
                var _selectize = _field
                  .closest('.form-group')
                  .find('div.selectize-control');
                var _select2 = _field.closest('div').find('.select2-container');
                // Menambahkan class Error pada form-group
                _field.parent('div').addClass('has-error');
                _field.parent('.required').addClass('has-error');
                $('.field-' + _getId).addClass('has-error');
                // Cara kedua menempelkan manual error pada form
                var replaceKey = key.replace(/[\[\]\'\!]/g, '');
                var manualErr = $('#error_' + replaceKey);
                if (_field.parent('div').find('.help-block').length > 0) {
                  _field
                    .parent('div')
                    .find('.help-block')
                    .html(`${logo}${val[0]}`);
                } else {
                  if (manualErr.length) {
                    manualErr.html(
                      '<span class="help-block error">' +
                      logo +
                      val[0] +
                      '</span>'
                    );
                  } else {
                    if (_group.length) {
                      _group.after(
                        '<span class="help-block error">' +
                        logo +
                        val[0] +
                        '</span>'
                      );
                    } else if (_selectize.length) {
                      _selectize.after(
                        '<span class="help-block error">' +
                        logo +
                        val[0] +
                        '</span>'
                      );
                    } else if (_select2.length) {
                      _select2.after(
                        '<span class="help-block error">' +
                        logo +
                        val[0] +
                        '</span>'
                      );
                    } else if (_div.length) {
                      _div.after(
                        '<span class="help-block error">' +
                        logo +
                        val[0] +
                        '</span>'
                      );
                    } else {
                      _field.after(
                        '<span class="help-block error">' +
                        logo +
                        val[0] +
                        '</span>'
                      );
                    }
                  }
                }
              });
            } else {
              errMsg = 'Terjadi kesalahan pada sistem ' + extMessage;
              errTitle = errTitle ? errTitle : 'Error ' + sttsErr + ' !';
            }
          } catch ($e) {
            // var errTitle = 'Proses error ' + data.status + ' !';
            // var errMsg = error;
          }
          let resultErr = options.error(data);
          hideQuestionDialog();
          if (typeof resultErr === 'undefined') {
            $('body').find('.confirm-dialog-overlay').remove();
            if (options.skipErrorNotif != true) {
              docoNotification('error', errTitle, errMsg);
            }
          }
          btn.button('reset');
          $('[data-popup="tooltip"]').tooltip();
        },
      }).done(function () {
        hideQuestionDialog();
        $('body').find('.confirm-dialog-overlay').remove();
        $('[data-popup="tooltip"]').tooltip();
        btn.button('reset');
        docoHelper.listen = false;
      });
    };

    if (options.skipConfirm) {
      _ajax(options, _this, btn);
    } else {
      $.showQuestionDialog(header, message, label, function (reaction) {
        if (reaction == 'Yes') {
          docoHelper.listen = false;
          hideQuestionDialog();
          _ajax(options, _this, btn);
        } else {
          docoHelper.confirmRejectAction(options, _this, btn);
        }
      });

      if(options.isScrollableConfirm) {
        $('.confirm-message-text').css({
          display: "block",
          overflow: "auto"
        })
      }
    }
  },
  delete: function (options, _this, btn) {
    btn.button('loading');
    $('#confirm-dialog-overlay').remove();
    var header = options.confirmTitle ? options.confirmTitle : 'Perhatian !';
    var add = '';
    if (options.additional) {
      add = $('#confirm-form').clone().removeClass('hidden');
      add.find('.input-pemakai').attr('id', 'pemakai-validasi');
      add.find('.input-sandi').attr('id', 'sandi-validasi');
      add = add.html();
    }
    var message = options.confirmMessage
      ? options.confirmMessage + add
      : 'Apakah anda yakin untuk menghapus data ini ?' + add;
    var label = {
      buttons: {
        Yes: 'button-yes',
        No: 'button-no',
      },
    };
    if (options.additional) {
      var label = {
        buttons: {
          Yes: 'button-yes',
          No: 'button-no',
        },
        hidden: true,
      };
    }
    if (options.skipConfirm) {
      hideQuestionDialog();
      docoHelper.fungsidelete(options, _this, btn);
    } else {
      // coba cek ini
      $.showQuestionDialog(header, message, label, function (reaction) {
        if (reaction == 'Yes') {
          //hideQuestionDialog();
          if (options.additional) {
            //render ajax validasi user
            var user = $('#pemakai-validasi').val();
            var pass = $('#sandi-validasi').val();
            $.ajax({
              url: baseUrl + 'site/cek-user',
              type: 'POST',
              data: 'nama_pemakai=' + user + '&katakunci_pemakai=' + pass,
              success: function (response) {
                if (response.trim() == 'sukses') {
                  hideQuestionDialog();
                  docoHelper.fungsidelete(options, _this, btn);
                } else {
                  new PNotify({
                    title: 'Terjadi Kesalahan',
                    text: response,
                    addclass:
                      'alert alert-warning alert-arrow-right alert-styled-right',
                    type: 'error',
                  });
                }
              },
            });
          } else {
            hideQuestionDialog();
            docoHelper.fungsidelete(options, _this, btn);
          }
        } else {
            docoHelper.confirmRejectAction(options, _this, btn);
        }
      });
    }
  },
  /**
   * author: Rizqi Febian
   * Fungsi pembulatan
   * num = total yg ingin dibulatkan
   * _targetClass = text buat kolom selisih pembulatan
   * _totalClass = text buat kolom total setelah ditambah pembulatan
   * penggunaan ada di Frontend TransaksiPembayaranUangMukaController action key
   */
  pembulatan: function (num, _targetClass, _totalClass) {
    var isNaik = docoHelper.is_pembulatankeatas;
    var satuan = docoHelper.satuanpembulatan;
    var tmp = num % satuan;
    var add = 0;
    var result = 0;
    if (tmp > 0) {
      if (isNaik == true) {
        add = Math.abs(satuan - tmp);
        result = parseFloat(num) + add;
      } else {
        add = tmp;
        result = num - tmp;
      }
    } else {
      result = num;
    }
    _targetClass.val(add);
    _totalClass.val(docoHelper.convertToRupiah(result));
  },
    calculateRounding: function(num){
        var isNaik = docoHelper.is_pembulatankeatas
        var satuan = docoHelper.satuanpembulatan
        if (isNaik){
            num_round = satuan != 0 ? Math.ceil(num/satuan)*satuan : num
            num_selisih = satuan != 0 ? num_round - num : 0
        } else {
            num_round = satuan != 0 ? Math.floor(num/satuan)*satuan : num
            num_selisih = satuan != 0 ? num - num_round : 0
        }
        return {
            nominal : num,
            nominal_round : num_round,
            nominal_selisih : num_selisih,
            round_up : isNaik,
            satuan_round : satuan,
        }
    },
  adjustmentMasukNama: 'adjusmen_masuk',
  adjustmentKeluarNama: 'adjusmen_keluar',
  getDataFromAction: function (link, callback) {
    $.ajax({
      method: 'GET',
      url: link,
    })
      .done(function (data, textStatus, jqXHR) {
        var result = {
          data: data,
          textStatus: textStatus,
          jqXHR: jqXHR,
        };
        callback(result);
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        var result = {
          data: null,
          textStatus: textStatus,
          jqXHR: jqXHR,
          errorThrown: errorThrown,
        };
        callback(result);
      });
  },
  repeatPlayAudio: function (audio, times, ended) {
    if (times <= 0) {
      return;
    }
    var played = 0;
    audio.addEventListener('ended', function () {
      played++;
      if (played < times) {
        audio.play();
      } else if (ended) {
        ended();
      }
    });
    audio.play();
  },
  htmlEscape: ( _html ) => {
    if ( _html == null || _html == '') {
      return _html
    }

    return _html.replace(/<br\s*[\/]?>/gi, "").replace(/&/gi, '%').replace(/%amp;/gi, '&').replace(/%gt;/g, '%').replace(/%/gi, '>')
  },
  confirmRejectAction: (options, _this, btn) => {
    // jika error server dan ini ajax toggle maka akan kembali ke value semula
    docoHelper.listen = false;
    if(typeof _this.attr('bootstrapSwitch') !== 'undefined'){
        _this.bootstrapSwitch('state', !_this.is(':checked'), "false");
    }
    hideQuestionDialog();
    btn.button('reset');
    $('[data-popup="tooltip"]').tooltip();
  },
  isObjectNotEmptyAndNotNull: function (obj) {
    return obj !== null && typeof obj !== 'undefined' && Object.keys(obj).length > 0;
  }
};

var _setUrl = function () {
  var urlBrowser = window.location.pathname;
  var _lastUrl = localStorage.getItem('last-url');
  var _indexUrl = localStorage.setItem('index-url', 1);
  var result = false;

  if (_lastUrl != urlBrowser) {
    localStorage.setItem('last-url', urlBrowser);
    localStorage.setItem('index-url', 0);
    result = true;
  }
  return true;
};

/**
 * Form Submit
 */

$.fn.docoForm = function (triger, content) {
  var _this = $(this);
  /** Ini Untuk Extensi Keperluan before = BeforeSend Semua Sama Kaya Ajax Jquery */
  $('[data-popup="tooltip"]').tooltip('destroy');
  var _default = {
    confirmMessage:
      typeof _this.attr('data-confirm-message') !== 'undefined'
        ? _this.attr('data-confirm-message')
        : null,
    confirmTitle:
      typeof _this.attr('data-confirm-title') !== 'undefined'
        ? _this.attr('data-confirm-title')
        : null,
    url:
      typeof _this.attr('action') !== 'undefined' ? _this.attr('action') : null,
    method:
      typeof _this.attr('method') !== 'undefined'
        ? _this.attr('method')
        : 'POST',
    target:
      typeof _this.attr('target-event') !== 'undefined'
        ? _this.attr('target-event')
        : null,
    model:
      typeof _this.attr('target-model') !== 'undefined'
        ? _this.attr('target-model')
        : null,
    skipConfirm:
      typeof _this.attr('skip-confirm') !== 'undefined' ? true : false,
    skipSuccessNotif:
      typeof _this.attr('skip-success-notif') !== 'undefined' ? true : false,
    skipErrorNotif:
      typeof _this.attr('skip-error-notif') !== 'undefined' ? true : false,
    additional: typeof _this.attr('additional') !== 'undefined' ? true : false,
    data: {},
    async: true,
    formInput: _this,
    success: function (event, data) { },
    error: function (event, data) { },
    before: function (event) { },
    dataType: 'json',
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    processData: true,
    isUpload: false,
    isToggleStatus: false,
    isScrollableConfirm: false,
  };

  var options = $.extend({}, _default, content);

  switch (triger) {
    case 'click':
      docoHelper.ajax(options, _this, _this);
      break;
    case 'delete':
      docoHelper.delete(options, _this, _this);
      break;
    default:
      docoHelper.options = options;
      $(this).submit(function (event) {
        if (docoHelper.listen) {
          return false;
        } else {
          event.preventDefault();
          docoHelper.listen = true;
          docoHelper.ajax(
            docoHelper.options,
            _this,
            _this.find('button[type=submit]')
          );
        }
      });
      break;
  }
};

$.fn.preventDoubleSubmit = function (options, _this) {
  $(this).submit(function (event) {
    if (docoHelper.listen) {
      return false;
    } else {
      event.preventDefault();
      docoHelper.listen = true;
      docoHelper.ajax(options, _this, _this.find('button[type=submit]'));
    }
  });
};

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 *
 * Fungsi Dropdown Select2 with Pagination - showing 10 data per scroll
 * mencegah load data keseluruhan pada dropdown
 *
 * Request data diambil dari parameter config = {}
 * config = {} : customize data variable / memodifikasi ajax response
 *
 * Infinity Scroll Select2
 * InfinityScrollSelect2
 */

// select2 penjamin
$.fn.select2Penjamin = function () {
  let configPenjamin = {
      url: "/api/master/get-penjamin",
      additionalOption: {
        placeholder: "-- Penjamin --"
      },
      callbackProccess : (data) => {
          var results = [];
          $.each(data.results.result, function (index, penjamin) {
              results.push({
                  id: penjamin.penjamin_id,
                  text: penjamin.penjamin_nama
              });
          });

          return {
              "results": results,
              "pagination": {
                  more: data.results.pagination.more
              },
              "incomplete_results": false,
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
  $(this).select2InfinityScroll(configPenjamin);
}

// select2 cara bayar
$.fn.select2CaraBayar = function () {
  let configCaraBayar = {
    url: "/api/master/get-cara-bayar",
    additionalOption: {
      placeholder: "-- Cara Bayar --"
    },
    callbackProccess: (data) => {
      var results = [];
      $.each(data.results.result, function (index, caraBayar) {
        results.push({
          id: caraBayar.carabayar_id,
          text: caraBayar.carabayar_nama
        });
      });
      return {
        "results": results,
        "pagination": {
          more: data.results.pagination.more
        },
        "incomplete_results": false
      };
    },
    callbackData: (params) => {
      return {
        term: params.term,
        page: params.page || 1,
        limit: params.limit
      }
    }
  }
  $(this).select2InfinityScroll(configCaraBayar);
}

// Select2 Pegawai Berdasarkan Ruangan
/*
* ruanganId: used for filter employee based on ruangan_id
* additionalPayload is object based, all params is customable based on what u need, but there is list params u can used:
* instalasi_id: used for filter employee based on instalasi_id
* kelompokpegawai_id: used for filter what kind of employee u want to return, this params refer to kelompokpegawai_id, if u not set it, this function will return all employee
* column: used for select employee record based on what column u need, make sure u set comma as separator and dont add any space, ex: pegawai_id,nama_pegawai
*/
$.fn.select2Pegawai = function (ruanganId = null, additionalPayload = {}) {
  let configPegawai = {
      url: "/api/master/get-list-data-pegawai",
      additionalOption: {
        placeholder: "-- Pilih Pegawai --"
      },
      callbackProccess : (data) => {
          var results = [];
          $.each(data.results.result, function (index, pegawai) {
              results.push({
                  id: pegawai.pegawai_id,
                  text: pegawai.nama_pegawai
              });
          });

          return {
              "results": results,
              "pagination": {
                  more: data.results.pagination.more
              },
              "incomplete_results": false,
          };
      },
      callbackData: (params) => {
          if ( additionalPayload.column != undefined ) {
            additionalPayload.column = additionalPayload.column.split(',')
          }
          return {
              term: params.term,
              page: params.page || 1,
              limit: params.limit,
              ruangan_id: ruanganId,
              ...additionalPayload
          }
      }
  }
  $(this).select2InfinityScroll(configPegawai);
}
$.fn.select2Ruangan = function (instalasiId = null, additionalPayload = {}) {
  let configRuangan = {
      url: "/gudang/transaksi-formulir-barang/get-ruangan2",
      additionalOption: {
        placeholder: "-- Pilih Ruangan --"
      },
      callbackProccess : (data) => {
        console.log(data);
          var results = [];
          $.each(data.results.result, function (index, ruangan) {
              results.push({
                  id: ruangan.ruangan_id,
                  text: ruangan.ruangan_nama
              });
          });

          return {
              "results": results,
              "pagination": {
                  more: data.results.pagination.more
              },
              "incomplete_results": false,
          };
      },
      callbackData: (params) => {
        console.log(params);
          return {
              term: params.term,
              page: params.page || 1,
              limit: params.limit,
              instalasi_id: instalasiId,
              ...additionalPayload
          }
      }
  }
  console.log(configRuangan);
  $(this).select2InfinityScroll(configRuangan);
}

$.fn.docoPaginationSelec2 = function (config = {}) {
  let additionalOption = {};
  var _url = config._api; 
  var parentConfig = config?.parent
  
  if(parentConfig !== undefined) {
    additionalOption.dropdownParent = $(`${parentConfig}`);
  } else {
      let parentData = $(this).parent();
      if(parentData !== undefined) {
        additionalOption.dropdownParent = parentData;    
      }
  }

  var _default = {
    ...additionalOption,
    placeholder: '-- Select Option --',
    minimumInputLength: 0,
    ajax: {
      url: _url,
      delay: 500, // Number of milliseconds before triggering the request
      dataType: 'json',
      data: function (params) {
        return {
          q: params.term,
          page: params.page || 1,
        };
      },
      processResults: function (data, params) {
        params.page = params.page || 1;
        return {
          results: data.result,
          pagination: {
            more: data.pagination.more,
          },
        };
      },
      dropdownCssClass: 'bigdrop',
      escapeMarkup: function (markup) {
        return markup;
      },
      templateResult: function (object) {
        return object.text;
      },
      templateSelection: function (subject) {
        return subject.text;
      },
    },
  };
  var options = $.extend(true, {}, _default, config);
  return $(this).select2(options);
};

/**
 * Data tabel Docotel
 **/
$.fn.docoTabel = function (option) {
  var _this = $(this);
  if(_this.length == 0) {
    return;
  }
  var _url =
    typeof _this.data('source') !== 'undefined' ? _this.data('source') : null;
  var stateSave = _setUrl();
  var _indexUrl = localStorage.getItem('index-url');
  var tableId = _this.attr('id');
  if (_indexUrl == 0) {
    var urlBrowser = window.location.pathname;
    localStorage.removeItem(`DataTables_${tableId}_/${urlBrowser}`);
  }
  var _default = {
    stateSave: true,
    cacheFilter: true,
    filter: true,
    displayLength: 10,
    processing: true,
    serverSide: true,
    lengthMenu: [
      [10, 25, 50],
      [10, 25, 50],
    ],
    ajax: _url,
  };

  if (typeof option.formFilters != 'undefined') {
    _this
      .parent()
      .prepend(
        `<div class="advanced-filter select2-md" id="advanced-filter-${tableId}"></div>`
      );
    var filterForm = $(`#advanced-filter-${tableId}`).filterForm({
      tableElement: _this,
      arrayForm: option.formFilters,
      ...(typeof option.filterRendered != 'undefined'
        ? { filterRendered: option.filterRendered }
        : {}),
    });
    if (typeof option.ajax == 'string') {
      option.ajax.url = option.ajax;
      option.ajax.data = {};
    }
    if (typeof option.ajax.data == 'undefined') {
      option.ajax.data = {};
    }
    // edited by ilhamsyah 14-07-2021, worklist filter default
    if (option.cacheFilter == true) {
      for (let key in option.formFilters) {
        let formFilter = option.formFilters[key]
        if(typeof formFilter === 'object') {
          keyName = formFilter.fieldName
        } else {
          keyName = formFilter
        }
        for (var i = 0; i < localStorage.length; i++) {
          if (localStorage.key(i).includes(tableId)) {
            if (localStorage.key(i).includes(keyName)) {
              let isi
              try {
                isi = JSON.parse(localStorage.getItem(localStorage.key(i)))
              } catch (e) {
                isi = localStorage.getItem(localStorage.key(i))
              }
              if(typeof isi == 'object'){
                if(Array.isArray(isi)) {
                  let arrVal = [];
                  for(let temp in isi) {
                    temp = isi[temp]
                    arrVal.push(temp.value);
                    if($(`#${tableId}-${keyName}--form [value='${temp.value}']`).length == 0) {
                      let option = new Option(temp.text, temp.value, false, false);
                      $(`#${tableId}-${keyName}--form`).append(option);
                    }
                    $(`#${tableId}-${keyName}--form`).val(arrVal).trigger('change', { 'elemfrom': _this });
                  }
                } else {
                  if($(`#${tableId}-${keyName}--form [value='${isi.value}']`).length == 0) {
                      let option = new Option(isi.text, isi.value, false, false);
                      $(`#${tableId}-${keyName}--form`).append(option);
                  }
                  $(`#${tableId}-${keyName}--form`).val(isi.value).trigger('change', { 'elemfrom': _this });
                }
              } else {
                if(typeof formFilter.type !== 'undefined' && typeof formFilter.type.name !== 'undefined' && formFilter.type.name == 'rangeDate') {
                  explode = isi.split(" - ")
                  $(`#${keyName}-startDate`).val(explode[0]).trigger('change')
                  $(`#${keyName}-endDate`).val(explode[1]).trigger('change')
                  $(`#${tableId}-${keyName}--form`).val(isi).trigger('change')
                } else {
                  $(`#${tableId}-${keyName}--form`).val(isi).trigger('change', { 'elemfrom': _this })
                }
              }
            }
          }
        }
        option.ajax.data.advancedFilter = serializeArrayToJson($(`#form-filter__${tableId}`));
      }
    }
    option.preDrawCallback = function(settings) {
      // ilhamsyah, set session filter
      const formWrapper = $(`#form-filter__${tableId}`);
      var dataArray = formWrapper.serializeArray()
      var checkmultiple = {}
      var arrListFilter = []
      for (var i = 0; i < localStorage.length; i++) {
          if (localStorage.key(i).startsWith("FilterTable/" + tableId)) {
              arrListFilter.push(localStorage.key(i))
          }
      }
      for(let filter in arrListFilter) {
          localStorage.removeItem(arrListFilter[filter])
      }
      dataArray.forEach(function (data) {
        var name = data.name
        var type = $(`#form-filter__${tableId} [name='${data.name}']`)[0].nodeName
        if (type == "INPUT") {
          isi = data.value;
          if(isi == null || isi == '') {
            return;
          }
          localStorage.setItem("FilterTable/" + tableId + "/" + name, isi)
        }
        else if (type == "SELECT") {
          let val = data.value
          if(val == null || val == '') {
            return;
          }
          try {
            let text = $(`[name='${data.name}'] [value='${val}']`)[0].innerHTML
            isi = {value:val,text:text}
            if(name.indexOf('[]') >= 0) {
              if(typeof checkmultiple[name] == 'undefined') {
                checkmultiple[name] = [];
              }
              checkmultiple[name].push(isi);
              localStorage.setItem("FilterTable/" + tableId + "/" + name, JSON.stringify(checkmultiple[name]))
            } else {
              localStorage.setItem("FilterTable/" + tableId + "/" + name, JSON.stringify(isi))
            }
          } catch (e) {
          }
        }
      })
    }
  }

  var options = $.extend(true, _default, option);
  return _this.DataTable(options);
};

const loaderShow = () => {
  if (
    typeof $('.loader-section') === 'undefined' ||
    $('.loader-section').length === 0
  ) {
    $('body').append(`
            <div class="loader-section">
                <div class="loader-overlay"></div>
                <div class="loader-wrapper">
                    <div class="loader-content-wrapper">
                        <div class="loader-content">
                            <i class="fa fa-gear fa-spin fa-3x fa-fw"></i>
                            <span>Sedang memproses....</span>
                        </div>
                    </div>
                </div>
            </div>
        `);
  }
};

const showLoader = (message = 'Memproses Data . . .') => {
  message = '. . .'  
  if (
    typeof $('.loader-section') === 'undefined' ||
    $('.loader-section').length === 0
  ) {
    $('body').append(`
            <div class="loader-section">
                <div class="loader-overlay"></div>
                <div class="indicator">
                    <svg width="24px" height="18px">
                        <polyline id="back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline>
                        <polyline id="front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline>
                    </svg>
                    <span>${message}</span>
                </div>
            </div>
        `);
  }
  $('html').css('overflow-y', 'hidden');
};
const hideLoader = () => {
  $('.loader-section').remove();
  $('html').css('overflow-y', 'scroll');
};

var getMenuItem = function (itemData) {
  if (itemData.level > 0) {
    classSub = itemData.sub ? 'dropdown-submenu' : '';
    // carretSub = (itemData.sub)? '&nbsp;<span class="caret"></span>':'';
    toggleSub = itemData.sub
      ? "class='dropdown-toggle' data-toggle='dropdown'"
      : '';

    var item = $("<li class='" + classSub + "'>").append(
      $('<a>', {
        href: baseUrl + itemData.url,
        html:
          '<i class="' +
          itemData.icon +
          ' position-left"></i> ' +
          itemData.name,
        class: itemData.sub ? 'dropdown-toggle' : '',
        'data-toggle': itemData.sub ? 'dropdown' : '',
      })
    );

    if (itemData.sub) {
      var subList = $('<ul>');
      subList.addClass('dropdown-menu width-200');

      $.each(itemData.sub, function () {
        subList.append(getMenuItem(this));
      });

      item.append(subList);
    }
  } else {
    classSub = itemData.sub ? 'dropdown' : '';
    carretSub = itemData.sub ? '&nbsp;<span class="caret"></span>' : '';
    toggleSub = itemData.sub
      ? "class='dropdown-toggle' data-toggle='dropdown'"
      : '';

    var item = $("<li class='" + classSub + "'>").append(
      $('<a>', {
        href: baseUrl + itemData.url,
        html:
          '<i class="' +
          itemData.icon +
          ' position-left"></i> ' +
          itemData.name +
          carretSub,
        class: itemData.sub ? 'dropdown-toggle' : '',
        'data-toggle': itemData.sub ? 'dropdown' : '',
      })
    );

    if (itemData.sub) {
      var subList = $('<ul>');
      subList.addClass('dropdown-menu width-250');

      $.each(itemData.sub, function () {
        subList.append(getMenuItem(this));
      });

      item.append(subList);
    }
  }

  return item;
};

var getModulesItem = function (itemData) {
  var item =
    '<div class="col-md-1 list-module">' +
    '<div class="panel panel-body border-top-success text-center isi-module">' +
    '<i class="fa fa fa-dashboard"></i>' +
    '<h6 class="no-margin text-semibold">Rawat</h6>' +
    '<p>Jalan</p>' +
    '<a href="#" class="velocity-animation" data-animation="tada"></a>' +
    '</div>' +
    '</div>';
  return item;
};

$(function () {
  $('.select2').select2({
    width: '100%',
  });
  // Tagging support
  $('.select-multiple-tags').select2({});
});

function handleCheckboxEvent(e) {
  e.preventDefault();

  if (e.keyCode === 32) {
    // If spacebar fired the event
    this.checked = !this.checked;
  }
}
$(document).ready(function () {
  // var inputs = document.getElementsByTagName('input');

  // for(var i = 0; i < inputs.length; i++) {
  //     if(inputs[i].type.toLowerCase() == 'checkbox') {
  //         inputs[i].addEventListener("click", handleCheckboxEvent, true);
  //         inputs[i].addEventListener("keyup", handleCheckboxEvent, true);
  //     }
  // }
  $('.list-module').mouseenter(function () {
    $(this).find('.velocity-animation').click();
  });

  $('table.DTFC_Cloned').remove();

  // kebutuhan hide header di antrian : ali.padilah@docotel.com
  var xhideHeader = localStorage.getItem('hideHeader');
  if (xhideHeader == 1) {
    setTimeout(function () {
      hideHeader();
    }, 10);
  }

  $('#scrollmenu').scroll(function () {
    $('#scrollmenu').css('overflow', 'auto');
  });

  $(function () {
    //        $('ul>li>ul>li>ul.dropdown-menu').addClass('subadvanced');

    // whenever we hover over a menu item that has a submenu
    $('li.parent').on('mouseover', function () {
      $('.wrapper').css('overflow', 'visible');
      $('.wrapper').css('max-height', 'none');
    });

    $('li.parent').on('mouseout', function () {
      $('.wrapper').css('max-height', '400px');
      $('.wrapper').css('overflow', 'auto');
    });
  });

  $(function () {
    // whenever we hover over a menu item that has a submenu
    $('.wrapper-header li.parent').on('mouseover', function (e) {
      showSubMenu($(this));
    });

    $('.wrapper-header li.parent>a.dropdown-toggle').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
    });
  });

  function showSubMenu(element) {
    let $menuItem = element,
      $submenuWrapper = $('> .wrapper-header', $menuItem);

    // grab the menu item's position relative to its positioned parent
    let menuItemPos = $menuItem.position();

    let topPosition = (menuItemPos.top - ($submenuWrapper.height() / 2)) >= 0 ? menuItemPos.top - ($submenuWrapper.height() / 2) : 0;

    // place the submenu in the correct position relevant to the menu item
    $submenuWrapper.css({
      top: $submenuWrapper.height() == $menuItem.height() ? menuItemPos.top : topPosition,
      left: menuItemPos.left + Math.round($menuItem.outerWidth() * 0.75)
    });
  }

  // end
});

$(document).on('click', '.reset', function (event) {
  event.preventDefault();
  var _form = $(this).closest('form');

  _form[0].reset();
});

$(document).on(
  'click',
  '[data-toggle="modal"],[data-btntrigger="modal"]',
  function (event) {
    event.preventDefault();
    _this = $(this);
    _this.button('loading');
    modal = $(_this.data('target'));
    width = _this.data('width');
    style = _this.data('style');
    var modal_content = modal.find('div.modal-dialog');
    var url = _this.attr('href');

    if (typeof width != 'undefined') {
      modal_content.css('width', width);
    }

    if (typeof style != 'undefined') {
      modal_content.attr('style', '');
      let _style = modal_content.attr('style');
      _style += style;
      modal_content.attr('style', _style);
    }

    if (typeof url == 'undefined') {
      url = _this.attr('action');
    }

    $('.modal-content', modal).empty();
    var _html = '<div class="text-center">';
    _html +=
      '<h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' +
      i18next.t('memuat') +
      ' . . . </b></h3>';
    _html += '</div>';
    $('[data-popup="tooltip"]').tooltip('destroy');
    modal
      .find('.modal-content')
      .html(_html)
      .load(url, function (responseText, statusText, xhr) {
          if (statusText == 'success') {
              modal.find('.select2').select2();
              // Tagging support
              $('.select-multiple-tags').select2({
                tags: true,
              });
              $('[data-popup="tooltip"]').tooltip();
              _this.button('reset');
              if ($(event.currentTarget).data('btntrigger') != 'undefined') {
                modal.modal('show');
              }
          } else {
              // masih bisa dikembangkan *Prof Yafi
              let response = JSON.parse(responseText);
              docoNotification('error', 'Proses Gagal!', response?.metadata?.message);
              // force remove modal-backdrop if stuck
              // index modal = x, index modalbackdrop = x - 1
              let zIndexModal = modal[0].style.zIndex; //get z index from inline style (not auto inherit)
              $('.modal-backdrop').filter(function(){
                  return $(this).css('z-index') == (zIndexModal - 1)
              }).remove();
              
              modal.modal('hide');
              _this.button('reset');
          }
      });
  }
);

// Author Arief -- Change Status
function changeStatus(element) {
  elem = $(element);

  elemID = elem.data('id');
  elemModule = elem.data('module');
  elemValue = elem.is(':checked') ? 1 : 0;

  $.ajax({
    url: baseUrl + elemModule,
    type: 'POST',
    data: {
      _csrf: $('meta[name="csrf-token"]').attr('content'),
      id: elemID,
      is_active: elemValue,
    },
    success: function (data, status, xhr) {
      elem.bootstrapSwitch({
        setState: elem.is(':checked'),
      });
      new PNotify({
        title: 'Berhasil',
        text: 'Status berhasil diganti',
        addclass: 'alert alert-success alert-arrow-right alert-styled-right',
        type: 'success',
      });
    },
  });
}

/*function convertToDecimal(index, val, replaceTo) {
    var valNumber = index.value;
    var num = parseFloat(valNumber.replace(',' , '.'));
     num = isNaN(num) ? 0 : num;
    var rounded = num.toFixed(2); // Round Number
    index.value = rounded.toString().replace(val, replaceTo); // Output Result
}*/

// Author Ramdhan
// Jquery Plugin (Jquery Datatable Filter with Only Bootstrap Theme)
// Edited ali.padilah@docotel.com
(function ($) {
  $.fn.datatableBootstrapFilter = function (
    ffTable,
    custom = false,
    position = false,
    clear = false,
    options = false
  ) {
    var formFilter = $(
      '<form class="advancedFilter" onsubmit="return false;"></form>'
    );
    var formFilterBody = formFilter.append(
      '<div class="row" id="ffBody"></div>'
    );
    var formFilterFoot = formFilter.append(
      '<div class="row" id="ffFoot"></div>'
    );
    var wrapper = $(this);
    var columns = ffTable.settings()[0].aoColumns;
    var i = 0;
    var columnCount = 1;
    var inputs = [];
    var state = ffTable.state.loaded();
    var tableId = ffTable.tables().nodes().to$().attr('id');
    var urlBrowser = window.location.pathname;
    var idDtabels = `DataTables_${tableId}_${urlBrowser}`;
    var datas = JSON.parse(localStorage.getItem(idDtabels));

    $.each(columns, function (i, data) {
      var title = data.title;
      var index = data.idx;
      var id = 'filter_' + data.data;
      var _val = '';
      var _label = '';
      var _startDate = (_endDate = '');
      var today = new Date();
      var _year = today.getFullYear();
      var _month = today.getMonth();
      var _day = today.getDate();
      var date = new Date(_year, _month, _day);
      var _bulan = date.toLocaleString('default', { month: 'short' });
      var filter_tooltip = ''

      if (state != null) {
        if (state.columns[index].search.search) {
          _val = state.columns[index].search.search;
        }
        if (state.columns[index].search.label) {
          _label = state.columns[index].search.label;
        }
      } else {
        ffTable.state.save();
      }

      if (position == false) {
        if (data.bSearchable) {
          var inputField =
            '<input type="text" class="form-control" id="' +
            id +
            '" value="' +
            _val +
            '" -placeholder="' +
            title +
            '" col-index="' +
            index +
            '">';
          if (custom)
            for (var x = 0; x < custom.length; x++) {
              field = custom[x];
              if (index == field[0]) {
                var jInput = $(field[1]);
                if(field[2] != undefined){
                  title = field[2]
                }
                if(field[3] != undefined){
                  filter_tooltip = field[3]
                }
                if (jInput.find('select').length) {
                  jInput.find('select').attr('col-index', index);
                  var dataOption = {
                    id: _val,
                    text: _label,
                  };

                  if (
                    jInput
                      .find("[col-index='" + index + "']")
                      .find("option[value='" + dataOption.id + "']").length
                  ) {
                    jInput
                      .find("option[value='" + dataOption.id + "']")
                      .attr('selected', true);
                    jInput.trigger('change');
                  } else {
                    var newOption = new Option(
                      dataOption.text,
                      dataOption.id,
                      true,
                      true
                    );
                    jInput
                      .find("[col-index='" + index + "']")
                      .append(newOption)
                      .trigger('change');
                  }
                } else if (jInput.find('input').length) {
                  jInput.find('input').attr('col-index', index);
                  jInput
                    .find("[col-index='" + index + "']")
                    .attr('value', _val);
                  if (jInput.find('.startDate') || jInput.find('.endDate')) {
                    if (_val == '') {
                      _startDate = _endDate = _day + '-' + _bulan + '-' + _year;
                    } else {
                      var dateRange = _val.split(' - ');
                      if (typeof dateRange[0] != '') {
                        _startDate = dateRange[0];
                        _endDate = dateRange[1];
                      }
                    }

                    jInput.find('.startDate').attr('value', _startDate);
                    jInput.find('.endDate').attr('value', _endDate);
                  }
                } else {
                  if (jInput.is('select')) {
                    jInput.attr('col-index', index);
                    var dataOption = {
                      id: _val,
                      text: _label,
                    };
                    if (jInput.find("option[value='" + dataOption.id + "']")) {
                      jInput
                        .find("option[value='" + dataOption.id + "']")
                        .attr('selected', true);
                      jInput.trigger('change');
                    } else {
                      var newOption = new Option(
                        dataOption.text,
                        dataOption.id,
                        true,
                        true
                      );
                      jInput.append(newOption).trigger('change');
                    }
                  }
                  jInput.attr('col-index', index);
                }
                var inputField = jInput.prop('outerHTML');
              }
            }

          var el =
            '<div class="col-md-3"><label>' +
            title +
            ' :      '+filter_tooltip+'</label>' +
            inputField +
            '</div>';
          formFilterBody.find('#ffBody').append(el);
        }
        i++;
      } else {
        var inputField =
          '<input type="text" class="form-control" id="' +
          id +
          '" value="' +
          _val +
          '" placeholder="' +
          title +
          '" col-index="' +
          index +
          '">';
        if (custom)
          for (var x = 0; x < custom.length; x++) {
            field = custom[x];
            if (index == field[0]) {
              var jInput = $(field[1]);
              if (jInput.attr('type') === 'text') continue;
              if (jInput.find('select').length) {
                jInput.find('select').attr('col-index', index);
                var dataOption = {
                  id: _val,
                  text: _label,
                };

                if (
                  jInput
                    .find("[col-index='" + index + "']")
                    .find("option[value='" + dataOption.id + "']").length
                ) {
                  jInput
                    .find("option[value='" + dataOption.id + "']")
                    .attr('selected', true);
                  jInput.trigger('change');
                } else {
                  var newOption = new Option(
                    dataOption.text,
                    dataOption.id,
                    true,
                    true
                  );
                  jInput
                    .find("[col-index='" + index + "']")
                    .append(newOption)
                    .trigger('change');
                }
              } else if (jInput.find('input').length) {
                jInput.find('input').attr('col-index', index);
                jInput.find("[col-index='" + index + "']").attr('value', _val);
                if (
                  jInput.find('.startDate, .startDatePR, .startDateVerif') ||
                  jInput.find('.endDate, .endDatePR, .endDateVerif')
                ) {
                  if (_val == '') {
                    if (
                      jInput
                        .find('.startDate, .startDatePR, .startDateVerif')
                        .data('value') != undefined
                    ) {
                      _startDate = jInput
                        .find('.startDate, .startDatePR, .startDateVerif')
                        .data('value');
                    } else {
                      _startDate = _day + '-' + _bulan + '-' + _year;
                    }

                    if (
                      jInput
                        .find('.endDate, .endDatePR, .endDateVerif')
                        .data('value') != undefined
                    ) {
                      _endDate = jInput
                        .find('.endDate, .endDatePR, .endDateVerif')
                        .data('value');
                    } else {
                      _endDate = _day + '-' + _bulan + '-' + _year;
                    }
                  } else {
                    var dateRange = _val.split(' - ');
                    if (typeof dateRange[0] != '') {
                      _startDate = dateRange[0];
                      _endDate = dateRange[1];
                    }
                  }

                  jInput
                    .find('.startDate, .startDatePR, .startDateVerif')
                    .attr('value', _startDate);
                  jInput
                    .find('.endDate, .endDatePR, .endDateVerif')
                    .attr('value', _endDate);
                }
              } else {
                if (jInput.is('select')) {
                  jInput.attr('col-index', index);
                  if (jInput.hasClass('select-multiple-tags')) {
                    _val =_val.split(',');
                    _label = _label.split(',');
                    var dataOption = {
                      id: _val,
                      text: _label,
                    };
                    $.each(_val, function(i,e){
                      jInput.find("option[value='" + e + "']").attr("selected", true);
                    });
                    jInput.trigger('change');
                  } else {
                    var dataOption = {
                      id: _val,
                      text: _label,
                    };
                    if (jInput.find("option[value='" + dataOption.id + "']")) {
                      jInput
                        .find("option[value='" + dataOption.id + "']")
                        .attr('selected', true);
                      jInput.trigger('change');
                    } else {
                      var newOption = new Option(
                        dataOption.text,
                        dataOption.id,
                        true,
                        true
                      );
                      jInput.append(newOption).trigger('change');
                    }
                  }
                }
                jInput.attr('col-index', index);
              }
              var inputField = jInput.prop('outerHTML');
            }
          }

        var el = clear ? '' : '<div class="form-group col-md-3"></div>';

        if (data.bSearchable) {
          el =
            '<div class="form-group new-filter col-md-3 col-xs-6 ' +
            columnCount +
            '" id="' +
            id +
            '"><label>' +
            title +
            ' :</label><br>' +
            inputField +
            '</div>';
          columnCount++;
        }
        inputs.push(el);
        i++;
      }
    });

    if (position) {
      for (var pos in position) {
        var idx_a = pos;
        var idx_z = position[idx_a];
        var tmp = inputs[idx_a];
        inputs[idx_a] = inputs[idx_z];
        inputs[idx_z] = tmp;
      }
      var _html = '';
      var no = 1;
      for (var acx = 0; acx < inputs.length; acx++) {
        if (acx <= 3) {
        } else {
          let htmlNew = $(inputs[acx]);
          htmlNew.addClass('collapse-filter-click');
          if (htmlNew.html() !== undefined) {
            inputs[
              acx
            ] = `<div class="form-group col-md-3 col-xs-6 collapse-filter-click">${htmlNew.html()}</div>`;
          }
        }
        if (inputs[acx] != '') {
          if (no % 4) {
            _html += inputs[acx];
          } else {
            _html += inputs[acx];
            _group = $('<div class="row" id="ffBody"></div>').append(_html);
            formFilterBody.append(_group);
            _html = '';
          }
          no++;
        }

        // formFilterBody.find("#ffBody").append(inputs[acx]);
      }

      if (_html != '') {
        _group = $('<div class="row" id="ffBody"></div>').append(_html);
        formFilterBody.append(_group);
      }
    }

    formFilterFoot
      .find('#ffFoot')
      .append(
        '<div class="col-md-12" style="display: none"><center><button type="button" class="btn btn-sm btn-primary btn-xs advancedFilterDo"><i class="fa fa-search"></i> Cari</button>&nbsp;<button type="reset" class="btn btn-sm btn-aqua btn-xs -advancedFilterHide"><i class="fa fa-repeat"></i> Ulang</button></center></div>'
      );
    formFilter.append(formFilterBody).append(formFilterFoot);

    wrapper
      .html($(formFilter))
      .append(
        '<button type="button" class="btn btn-sm btn-primary btn-xs advancedFilterShow" style="display:none;"><i class="fa fa-search"></i> Pencarian Lengkap</button>'
      )
      .append('<div class="clearfix"></div>');

    $(wrapper).on('click', '.advancedFilterDo', function (event) {
      event.preventDefault();
      $(wrapper).find('.advancedFilterDo').button('loading');
      wrapper.find('.advancedFilter input').each(function () {
        var input = $(this);
        var index = input.attr('col-index');
        ffTable.column(index).search(input.val());
        if (datas != null) {
          if (typeof datas.columns[index] !== 'undefined') {
            datas.columns[index].search.search = input.val();
          }
        }
      });
      wrapper.find('.advancedFilter select').each(function () {
        var input = $(this);
        var index = input.attr('col-index');
        var data_id = input.val();
        var data_text = input.find('option:selected').text();
        ffTable.column(index).search(data_id ? data_id : '');
        if (datas != null) {
          if (typeof datas.columns[index] !== 'undefined') {
            datas.columns[index].search.search = data_id;
            datas.columns[index].search.label = data_text;
          }
        }
      });
      wrapper.find('.advancedFilter select[multiple]').each(function () {
        var input = $(this);
        var index = input.attr('col-index');
        var values = new Array();
        let labels = new Array();

        $.each(input.find('option:selected'), function (i, item) {
          values.push($(item).val());
          labels.push($(item).text());
        });
        if (datas != null) {
          if (typeof datas.columns[index] !== 'undefined') {
            datas.columns[index].search.search = values.join();
            datas.columns[index].search.label = labels.join();
          }
        }
        ffTable.column(index).search(values.join());
      });
      ffTable.draw();
    });

    // $(wrapper).on("-keyup", ".advancedFilter input", function (e){
    // if(e.which == 13) {
    // $(wrapper).find(".advancedFilterDo").button('loading');
    // wrapper.find(".advancedFilter input").each( function () {
    // var input = $(this);
    // var index = input.attr("col-index");
    // ffTable.column(index).search(input.val());
    // });
    // wrapper.find(".advancedFilter select").each(function () {
    // var input = $(this);
    // var index = input.attr("col-index");
    // ffTable.column(index).search(input.find("option:selected").val());
    // });
    // wrapper.find(".advancedFilter select[multiple]").each(function () {
    // var input = $(this);
    // var index = input.attr("col-index");
    // var values = new Array();

    // $.each(input.find("option:selected"), function(i, item) {
    // values.push($(item).val());
    // });
    // ffTable.column(index).search(values.join());
    // });
    // ffTable.draw();
    // }
    // });

    // $(wrapper).on("-change", ".advancedFilter select", function (e){
    // if(e.which == 13) {
    // $(wrapper).find(".advancedFilterDo").button('loading');
    // wrapper.find(".advancedFilter input").each( function () {
    // var input = $(this);
    // var index = input.attr("col-index");
    // ffTable.column(index).search(input.val());
    // });
    // wrapper.find(".advancedFilter select").each(function () {
    // var input = $(this);
    // var index = input.attr("col-index");
    // ffTable.column(index).search(input.find("option:selected").val());
    // });
    // wrapper.find(".advancedFilter select[multiple]").each(function () {
    // var input = $(this);
    // var index = input.attr("col-index");
    // var values = new Array();

    // $.each(input.find("option:selected"), function(i, item) {
    // values.push($(item).val());
    // });
    // ffTable.column(index).search(values.join());
    // });
    // ffTable.draw();
    // }
    // });

    $(wrapper).on('click', '.advancedFilterShow', function () {
      wrapper.find('.advancedFilter').show();
      wrapper.find('.advancedFilterShow').hide();
    });

    $(wrapper).on('click', '.advancedFilterHide', function () {
      wrapper.find('.advancedFilter').hide();
      wrapper.find('.advancedFilterShow').show();
    });

    ffTable.on('draw', function () {
      $(wrapper).find('.advancedFilterDo').button('reset');
      if (datas != null) {
        localStorage.setItem(idDtabels, JSON.stringify(datas));
      }
    });

    $(wrapper).find('.select2').select2();
  };
})(jQuery);

/**
 *
 * @author afil
 * @desc untuk load page/html/view untuk renderAjax/ renderPartial
 * @var options object, yang dibutuhin banget urlnya aja
 *
 */
$.fn.docoLoad = function (options) {
  var _this = $(this);
  var _url = options.url ? options.url : null;
  var _dataType = options.dataType ? options.dataType : 'html';
  var _data = options.data ? options.data : null;
  var _type = options.type ? options.type : 'GET';
  var _contentType = options.contentType
    ? options.contentType
    : 'application/html; charset=utf-8';
  var defaultError = function (res) {
    var _response = JSON.parse(res.responseText);
    var _html = '<div class="text-center">';
    _html += '<h3><b>' + _response.response.text + '</b></h3>';
    _html += '</div>';
    _this.html(_html);
  }
  var success = options.success ? options.success : function (event, data) { };
  var error = options.error ? options.error : defaultError;
  var before = options.before ? options.before : function (event) { };

  var loading_text = i18next.t('memuat');
  var error_text = i18next.t('gagal_muat_halaman');

  if (typeof loading_text == 'undefined') {
    loading_text = 'loading';
  }

  // generate loading text html
  var _html = '<div class="text-center">';
  _html +=
    '<h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' +
    loading_text +
    ' . . . </b></h3>';
  _html += '</div>';

  // generate error text html
  var _html = '<div class="text-center">';
  _html +=
    '<h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' +
    loading_text +
    ' . . . </b></h3>';
  _html += '<br />';
  _html += '</div>';

  $.ajax({
    type: _type,
    url: _url,
    data: _data,
    dataType: _dataType,
    contentType: _contentType,
    beforeSend: function () {
      var _html = '<div class="text-center">';
      _html +=
        '<h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' +
        loading_text +
        ' . . . </b></h3>';
      _html += '</div>';
      _this.html(_html);
    },
    success: function (res) {
      _this.html(res);
      options.success(res);
    },
    error:error,
  });
};

/*!
 Enkripsi js
 Â©author ali.padilah@docotel.com
*/
function js_encrypt(data, array) {
  var secret_key = 'Doco bandung';
  if (array) {
    return CryptoJS.AES.encrypt(JSON.stringify(data), secret_key);
  } else {
    return CryptoJS.AES.encrypt(data, secret_key);
  }
}

function js_decrypt(data, array) {
  var secret_key = 'Doco bandung';
  var bytes = CryptoJS.AES.decrypt(data.toString(), secret_key);

  return JSON.parse(bytes.toString(CryptoJS.enc.Utf8));
}

/*!
 Translator js
 Â©author ali.padilah@docotel.com
*/
window.onload = function () {
  var defaultLang = document.documentElement.lang;
  i18next
    .use(i18nextXHRBackend)
    .use(i18nextBrowserLanguageDetector)
    .init(
      {
        fallbackLng: 'en',
        debug: false,
        ns: ['lang'],
        defaultNS: 'lang',
        lng: defaultLang,
        backend: {
          loadPath: baseUrl + 'json/lang/{{lng}}/{{ns}}.json',
          crossDomain: true,
        },
      },
      function (err, t) {
        // init set content
        updateContent();
      }
    );

  function updateContent() {
    list_lang = [
      {
        id: 'ID',
        name: 'Indonesia',
      },
      {
        id: 'EN',
        name: 'English',
      },
    ];

    var top_lang = '';
    var main_lang = '';
    $.each(list_lang, function (i, v) {
      if (v.id == defaultLang) {
        top_lang += '<a class="dropdown-toggle" data-toggle="dropdown">';
        top_lang += v.name;
        top_lang += '<span class="caret"></span>';
        top_lang += '</a>';
      } else {
        main_lang +=
          '<li><a class="' +
          v.name +
          '" onclick="changeLng(\'' +
          v.id +
          '\');" >' +
          v.name +
          '</a></li>';
      }
    });

    var nav_lang = top_lang;
    nav_lang += '<ul class="dropdown-menu">';
    nav_lang += main_lang;
    nav_lang += '</ul>';
    $('#nav_lang').html(nav_lang);
  }

  window.changeLng = function (lng) {
    i18next.changeLanguage(lng);
    sessionStorage.setItem('LNG', lng);

    $.ajax({
      url: baseUrl + 'site/set-language',
      type: 'POST',
      data: {
        _csrf: $('meta[name="csrf-token"]').attr('content'),
        lang: lng,
      },
      success: function (data, status, xhr) {
        location.reload();
      },
    });
  };
};

/*!
 Unset Method and workspace js
 Â©author ali.padilah@docotel.com
*/

function changeWorkspace(element) {
  var room_id = $(element).data('id');
  var room_index = $(element).data('index');
  var instalasi_index = $(element).data('instalasi-index');
  var home = $(element).data('home');
  var tombol_back = $(".data-back");

  if (home) {
    RemoveFilterSession()
  }
  $.ajax({
    url: baseUrl + 'site/set-method',
    type: 'POST',
    data: {
      _csrf: $('meta[name="csrf-token"]').attr('content'),
      unset: home,
      roomID: room_id,
      roomIndex: room_index,
      instalasiIndex: instalasi_index,
    },
    success: function (data, status, xhr) {
      if(tombol_back != null && tombol_back.attr("href") != null) {
        location.replace(tombol_back.attr("href"));
      } else {
        location.reload();
      }
      // $.ajax({
      //     url: baseUrl + "master/loket/reset-loket",
      //     type: "POST",
      //     success: function (data, status, xhr) {
      //     },
      //     complete: () => {
      //         hideLoader()
      //     }
      // });
    },
  });
}
//edited by Rizqi Fitrianto
//add click filter cari and reset for new design
//13-02-2018
$(document).on('click', '.filter-cari', function () {
  var parent = $(this).data('parent');
  if (typeof parent !== 'undefined') {
    $(parent).find('.advancedFilterDo').click();
  } else {
    $('.advancedFilterDo').click();
  }
});
$(document).on('click', '.filter-reset', function () {
  var parent = $(this).data('parent');
  if (typeof parent !== 'undefined') {
    $(parent).find('[type=reset]').click();
    $(parent).find('.advancedFilterDo').click();
  } else {
    $('.advancedFilter [type=reset]').click();
    $('.advancedFilterDo').click();
  }
});

//by Ramdhan
//add click filter cari and reset for new design
//13-02-2018
$(document).on('click', '.data-filter', function (event) {
  event.preventDefault();
  var parent = $(this).data('parent');
  if (typeof parent !== 'undefined') {
    $(parent + ' .advancedFilterDo').click();
  } else {
    $('.advancedFilterDo').click();
  }
});

$(document).on('click', '.data-reset', function (event) {
  event.preventDefault();
  //last edit by Rizqi febian
  //add clear select2
  //23-02-2018
  var parent = $(this).data('parent');
  $('.form-group').removeClass('has-error');
  $('span.help-block.error').remove();
  $('div.help-block.error').remove();
  _parentValue = null;
  if (typeof parent !== 'undefined') {
    let resetSelect2 = true;
    $(parent + ' [type=reset]').click();
    if ($(this).hasClass('reset-on-modal-form')) {
      // prevent reset all select2 on form when use modal on form
      resetSelect2 = false;
    }
    if (resetSelect2) {
      $('.select2').val('').trigger('change');
    }
    $(parent + ' .advancedFilterDo').click();
  } else {
    localStorage.clear();
    $('.advancedFilter [type=reset]').click();
    $('.select2').val('').trigger('change');
    $('.advancedFilterDo').click();
  }
});

$(document).ready(function () {
  $('[data-toggle="tooltip"]').tooltip();
  $(document)
    .bind('ajaxSend', function (xhr, note, request) {
      if (
        request.type.toLowerCase() !== 'get' &&
        (typeof request.withoutLoading == 'undefined' ||
          (typeof request.withoutLoading != 'undefined' &&
            !request.withoutLoading))
      ) {
        showLoader();
      }
    })
    .bind('ajaxComplete', function (event, xhr, optionApi) {
      $('[data-toggle="tooltip"]').tooltip();
      hideLoader();
      if (
        typeof xhr.responseJSON !== 'undefined' &&
        typeof xhr.responseJSON.meta !== 'undefined' &&
        typeof xhr.responseJSON.meta.message !== 'undefined'
      ) {
        $('body').find('.confirm-dialog-overlay').remove();
        if (xhr.status === 422) {
          if (
            typeof optionApi.withoutScroll == 'undefined' ||
            (typeof optionApi.withoutScroll == 'undefined' &&
              !optionApi.withoutScroll)
          ) {
            $('html, body').animate({ scrollTop: 20 }, 'slow');
          }
          const responseJson =
            typeof xhr.responseJSON.data.errors !== 'undefined'
              ? xhr.responseJSON.data.errors
              : xhr.responseJSON.data;
          docoNotification(
            'warning',
            typeof xhr.responseJSON.meta.title !== 'undefined' ? xhr.responseJSON.meta.title : 'Terjadi Kesalahan!',
            xhr.responseJSON.meta.message
          );
          const formSelectedId =
            typeof xhr.responseJSON.data.formId !== 'undefined'
              ? xhr.responseJSON.data.formId
              : null;
          Object.keys(responseJson).map((itemElementError) => {
            let elementError =
              formSelectedId !== null
                ? $(`#${formSelectedId} [name='${itemElementError}']`)
                : $(`[name='${itemElementError}']`);
            const parentSection = $(elementError.parents('.form-group')[0]);
            parentSection.addClass('has-error');
            const errorMessage = `<i class="fa fa-exclamation-circle"></i>${responseJson[itemElementError][0]}`;
            const formElementData = elementError.closest('form').data();
            if (
              typeof foemElementData != 'undefined' &&
              typeof formElementData.horizontalForm !== 'undefined' &&
              parentSection.find('div.help-block').length > 0
            ) {
              $(parentSection.find('div.help-block')[0]).html(errorMessage);
            } else if (
              parentSection.find('span.help-block.error').length == 0
            ) {
              parentSection.append(`
                            <span class="help-block error">${errorMessage}</span>
                        `);
            } else {
              $(parentSection.find('span.help-block.error')[0]).html(
                errorMessage
              );
            }
          });
        } else if (xhr.status >= 400 && xhr.status <= 499) {
          if (
            typeof optionApi.withoutScroll == 'undefined' ||
            (typeof optionApi.withoutScroll == 'undefined' &&
              !optionApi.withoutScroll)
          ) {
            $('html, body').animate({ scrollTop: 20 }, 'slow');
          }
          docoNotification(
            'error',
            typeof xhr.responseJSON.meta.title !== 'undefined' &&
              xhr.responseJSON.meta.title !== null
              ? xhr.responseJSON.meta.title
              : 'Terjadi kesalahan pada input.',
            xhr.responseJSON.meta.message
          );
        } else if (xhr.status >= 500) {
          if (
            typeof optionApi.withoutScroll == 'undefined' ||
            (typeof optionApi.withoutScroll == 'undefined' &&
              !optionApi.withoutScroll)
          ) {
            $('html, body').animate({ scrollTop: 20 }, 'slow');
          }
          docoNotification('error', 'Terjadi kesalahan pada server', '');
        }
      } else if (xhr.status >= 500) {
        if (
          typeof optionApi.withoutScroll == 'undefined' ||
          (typeof optionApi.withoutScroll == 'undefined' &&
            !optionApi.withoutScroll)
        ) {
          $('html, body').animate({ scrollTop: 20 }, 'slow');
        }
        docoNotification('error', 'Terjadi kesalahan pada server', '');
      } else if (
        typeof xhr.responseJSON != 'undefined' &&
        typeof xhr.responseJSON.redirectUrl != 'undefined'
      ) {
        location.replace(xhr.responseJSON.redirectUrl);
      }
    });
});

/**
 *
 * author : iqbal@docotel.com
 * fungsi
 *
 */
function downloadSubmit(element) {
  elem = $(element);
  event.preventDefault();
  var url = elem.data('target');
  window, open(url, '_blank');
}

/**
 *
 * author : rizfardi@docotel.com
 * fungsi untuk trigger submit form saat click button yang diatas atas itu
 * masukannya id form di tag data-target
 *
 */
function triggerSubmit(element) {
  elem = $(element);

  elemID = elem.data('target');
  if (elemID.length > 1) {
    elemID = '#' + elemID;
    $(elemID).submit();
  }
}

//add by Rizqi Fitrianto
//date range picker helper
//27-02-2018
var dateRangeHelper = function (
  startClass,
  endClass,
  targetClass,
  limit = true,
  endDateFirst = false,
  isInModal = false,
) {
  //declare variable
  var start = $(startClass);
  var end = $(endClass);
  var target = $(targetClass);

  start.attr('readonly', true);
  end.attr('readonly', true);

  // Options
  var oneDay = 24 * 60 * 60 * 1000;
  var rangeDemoFormat = '%e-%b-%Y';
  var rangeDemoConv = new AnyTime.Converter({
    format: rangeDemoFormat,
    moment: moment(),
  });

  $('#rangeDemoToday').click(function (e) {
    start.val(rangeDemoConv.format(new Date())).change();
  });

  // Clear dates
  $('#rangeDemoClear').click(function (e) {
    start.val('').change();
  });
  // Start date
  start.AnyTime_noPicker().AnyTime_picker({
    format: rangeDemoFormat,
  });

  if(isInModal){
      $('#AnyTime--'+startClass.replace('#', '')).appendTo('div#modal_backdrop');
  }

  // End date
  if(endDateFirst){
    end.AnyTime_noPicker().AnyTime_picker({
      format: rangeDemoFormat,
    });

    if(isInModal){
        $('#AnyTime--'+endClass.replace('#', '')).appendTo('div#modal_backdrop');
    }
  }

  try {
    let fromDay = rangeDemoConv.parse(start.val()).getTime();
    var dateValue;
    let dayLater = new Date(fromDay + oneDay);

    let setDaysLater = new Date(fromDay + 30 * oneDay); //Default 30 days
    if (limit) {
      setDaysLater = new Date(fromDay + 50 * 12 * 30 * oneDay); //5 tahun kedepan
    }
    dayLater.setHours(0, 0, 0, 0);

    let valEnd = rangeDemoConv.format(dayLater);
    if (end.val()) {
      valEnd = rangeDemoConv.format(new Date(end.val()));
    }
    end
      .AnyTime_noPicker()
      .removeAttr('disabled')
      .val(valEnd)
      .AnyTime_picker({
        earliest: dayLater - oneDay,
        format: rangeDemoFormat,
        latest: setDaysLater,
      });

      if(isInModal){
          $('#AnyTime--'+endClass.replace('#', '')).appendTo('div#modal_backdrop');
      }

  } catch (error) { }

  // On value change
  start.change(function (e) {
    try {
      fromDay = rangeDemoConv.parse(start.val()).getTime();

      dayLater = new Date(fromDay + oneDay);
      dayLater.setHours(0, 0, 0, 0);
      if (limit) {
        setDaysLater = new Date(fromDay + 50 * 12 * 30 * oneDay); //5 tahun kedepan
      } else {
        setDaysLater = new Date(fromDay + 30 * oneDay); //Default 30 days
      }

      setDaysLater.setHours(23, 59, 59, 999);

      if(limit) {
        // Change end date format to global if invalid
        if (isNaN(new Date(end.val())) || end.val() == '') {
          end.val(rangeDemoConv.format(new Date())).change();
        }  
      } else {
        let currentEnd = new Date(end.val());
        if (isNaN(currentEnd) || end.val() === '' || currentEnd > setDaysLater || currentEnd < dayLater) {
          end.val(rangeDemoConv.format(setDaysLater)).change();
        }
      }
      

      // End date
      end
        .AnyTime_noPicker()
        .removeAttr('disabled')
        .AnyTime_picker({
          earliest: dayLater - oneDay,
          format: rangeDemoFormat,
          latest: setDaysLater,
        });

      if(isInModal){
          $('#AnyTime--'+endClass.replace('#', '')).appendTo('div#modal_backdrop');
      }

      if (new Date(start.val()) > new Date(end.val())) {
        if(endDateFirst){
          end.val(rangeDemoConv.format(dayLater)).change();
        }else{
          end.AnyTime_noPicker().val(rangeDemoConv.format(dayLater));
        }
      }
      dateValue = start.val() + ' - ' + end.val();
      target.val(dateValue);
    } catch (e) {
      // Disable End date field
      end.val('').attr('disabled', 'disabled');
    }
  });

  end.change(function (e) {
    dateValue = start.val() + ' - ' + end.val();
    target.val(dateValue);
  });
};

//add by Rizqi Fitrianto
//btn menu click helper
//14-03-2018
$(document).on('click', '.btn-toolbar', function (e) {
  e.preventDefault();
  var tableId = $(this).attr('data-table');
  var table = $(tableId).DataTable();
  var tableData = table.row('.selected').data();
  var options = $(this).attr('data-options');
  var type_target = $(this).attr('target');
  type_target = typeof type_target != 'undefined' ? type_target : '_self';
  var conditions = $(this).attr('data-conditions')
    ? $(this).attr('data-conditions').split(',')
    : '';
  var dataCustom = $(this).attr('data-custom');
  var dataMultiParams = $(this).attr('data-multiple_params')
  ? $(this).attr('data-multiple_params').split(',')
  : '';
  var dataCallback = $(this).attr('data-callback')

  var multiSelect = table.rows('.selected').data();
  var primaryData = [];
  var secondaryData = [];

  for (var i = 0; i < multiSelect.length; i++) {
    if('primary' in multiSelect[i]) {
      let dataParams = $(this).attr('data-params') ? multiSelect[i][$(this).attr('data-params')] : multiSelect[i].primary;
      primaryData.push(dataParams);
    }
  }

  for (var i = 0; i < multiSelect.length; i++) {
    if('secondary' in multiSelect[i]) {
      secondaryData.push(multiSelect[i].secondary);
    }
  }

  /*
   * add by Anggoro
   * disabled all action if disabled attribute exist
   * 18 - 02 - 2020
   */
  if ($(this).attr('disabled') == 'disabled') {
    return false;
  }

  if (typeof $(this).attr('data-toggle') === 'undefined') {
    if (options == 'excel' || options == 'pdf' || options == 'print') {
      var col = table.data().count();
      if (col === 0) {
        docoNotification(
          'warning',
          'Terjadi Kesalahan',
          'Data Tidak Tersedia!'
        );
      } else {
        var url = window.location.origin;
        var target = $(this).attr('data-target');
        window.open(url + target + $.param(table.ajax.params()));
      }
    } else if (options == 'aksi') {
      $(this).docoForm('click', {
        title: 'sukses',
        method: 'POST',
        type: 'json',
        success: function (response) { },
      });
    } else if (options == 'click') {
      return true;
    } else if (options == 'link') {
      var target = $(this).attr('data-target');
      window.open(target, type_target);
    } else if (options == 'custom-print') {
      var url = window.location.origin;
      var target = $(this).attr('data-target');
      window.open(url + target + $.param(table.ajax.params()));
    } else {
      if (typeof tableData !== 'undefined') {
        if (primaryData.length > 0) {
          /*
          var primary = $(this).attr('data-params')
            ? tableData[$(this).attr('data-params')]
            : tableData.primary;
          */
          var secondary = '';
          if (secondaryData.length > 0) {
            var secondary = secondaryData.join();
          }
          var primary = primaryData.join();
          var ext = '';
          if (conditions.length > 0) {
            $.each(conditions, function (index, value) {
              let condParams = tableData[value]
              if ( $.inArray(value, dataMultiParams) >= 0) {
                multiParamsVal = []
                $.each(table.rows('.selected').data(), (_k, _v) => {
                  if (_v[value] != '' || _v[value] != null) {
                    multiParamsVal.push(_v[value])
                  }
                })

                condParams = multiParamsVal.join(',')
              }
              ext += '&' + value + '=' + condParams;
            });
          }

          var target = $(this).attr('data-target');
          if ($(this).is('button')) {
            if (options == 'delete') {
              var additional = $(this).attr('data-additional');
              var messageText = undefined;
              if ($(this).attr('data-message')) {
                messageText = $(this).attr('data-message');
              }
              if (additional) {
                $(this).attr('action', target + primary + ext);
                $(this).docoForm('delete', {
                  additional: 'data-rm',
                  confirmMessage: messageText,
                  success: function (data) {
                    table.draw();
                  },
                });
              } else {
                $(this).attr('action', target + primary + ext);
                $(this).docoForm('delete', {
                  success: function (data) {
                    table.draw();
                  },
                });
              }
            } else if (options == 'modal') {
              var url = $(this).attr('data-url')
                ? $(this).attr('data-url')
                : $(this).attr('data-href')
                  ? $(this).attr('data-href')
                  : null;

              $(this).attr('action', url + primary + ext);
              $(this).attr('data-toggle', 'modal');

              $(this).trigger('click');

              $(this).removeAttr('action');
              $(this).removeAttr('data-toggle');
            } else if (options == 'excel-serconn') {
                let column = table.data().count();
                let url = $(this).attr('data-url') ? $(this).attr('data-url') : $(this).attr('data-href') ? $(this).attr('data-href') : null;
                let data_custom = $(this).attr('data-custom') ? $(this).attr('data-custom') : null;
                if(column === 0) {
                    docoNotification('warning', 'Terjadi Kesalahan', 'Data Tidak Tersedia!');
                } else {
                  var params = ''
                  if(conditions.length > 0) {
                    $.each(conditions, function (index, value) {
                      params += value + '=' + tableData[value] + '&';
                    });
                  }

                  params = `${params == '' ? params : encodeURI(params)}${$.param(table.ajax.params())}`

                  if (data_custom == true || data_custom == 'true'){
                    $(this).attr('action', url + secondary);
                  } else {
                    $(this).attr('action', url + params);
                  }

                  $(this).attr('data-toggle', 'modal');

                  $(this).trigger('click');

                  $(this).removeAttr('action');
                  $(this).removeAttr('data-toggle');
                }
            } else {
              var _pages = $(this).attr('data-pages');
              _pages = typeof _pages != 'undefined' ? _pages : '_self';
              let tableDataMultiple = table.rows('.selected').data();
              if(tableDataMultiple.length > 1){
                  primary = tableDataMultiple.map(function(val){
                          return val.primary;
                      });
                  primary = primary.join(','); // jadiin query string, function mirip explode
              }
              $(this).attr('action', target + primary);
              window.open(target + primary + ext, _pages);
            }
          } else {
            $(this).attr('href', target + primary);
            window.open(target + primary + ext, '_self');
          }
        } else {
          docoNotification(
            'warning',
            'Terjadi Kesalahan',
            'Primary tidak didefinisikan!'
          );
        }
      } else {
        if (options == 'excel-serconn') {
          let column = table.data().count();
          let url = $(this).attr('data-url') ? $(this).attr('data-url') : $(this).attr('data-href') ? $(this).attr('data-href') : null;
          var tablePrams = table.ajax.params();
          var dataAttr = $(this).data();
          if (typeof dataAttr.tableParamExclude !== 'undefined') {
            // avoid HTTP 414 "Request URI too long" error for large table
            if (dataAttr.tableParamExclude.includes('columns')) {
              tablePrams.columns.forEach((v, i, arr) => {
                var {searchable, orderable, search, ...other} = v
                arr[i] = other
              })
            }
          }
          if (dataCustom == true || dataCustom == 'true'){
            column = 1;
          }
          if (column === 0) {
            docoNotification('warning', 'Terjadi Kesalahan', 'Data Tidak Tersedia!');
          } else {
            if (dataCustom == true || dataCustom == 'true'){
              $(this).attr('action', url);
            } else {
              $(this).attr('action', url + $.param(tablePrams));
            }
            $(this).attr('data-toggle', 'modal');

            $(this).trigger('click');

            $(this).removeAttr('action');
            $(this).removeAttr('data-toggle');
          }
        } else {
          docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
        }
      }
      if ( typeof dataCallback != 'undefined') {
        try {
          eval($('#btn-print-status-pasien').attr('data-callback'))()
        } catch (e) {

        }
      }
    }
  }
});
//add by Rizqi Fitrianto
//handle notification event
//19-03-2018
// edit by aweutist
// edit by rizal 2018-05-04 14:12:40
// edit by fajar 2021-04-26 12:16:30
function docoNotification(type, title = null, msg, hide = true) {
  var _class = '';
  var _msg = msg;
  var check_warning = $('.alert-warning').length;
  var check_danger = $('.alert-danger').length;
  var check_success = $('.alert-success').length;
  var generate_notif = true;

  if (type === 'warning') {
    _class = "alert alert-warning alert-arrow-right alert-styled-right";
    var _title = title ? title : i18next.t("Warning");
    if (check_warning >= 1) {
      generate_notif = false;
    }
  } else if (type === 'error') {
    var _title = title ? title : i18next.t("Error");
    _class = "alert alert-danger alert-arrow-right alert-styled-right";
    if (check_danger >= 1) {
      generate_notif = false;
    }
  } else if (type === 'success') {
    var _title = title ? title : i18next.t("Success");
    _class = "alert alert-success alert-arrow-right alert-styled-right";
    if (check_success >= 1) {
      generate_notif = false;
    }
  }

  if (generate_notif) {
    return new PNotify({
      title: _title,
      text: _msg,
      addclass: _class,
      type: type,
      hide: hide,
    });
  }
}

//add by ARIEF SAPUTRA
//handle number leading zero
//05-04-2018
function pad(str, max) {
  str = str.toString();
  return str.length < max ? pad('0' + str, max) : str;
}

$(document).on('change keyup', '.doco-number', function (e) {
  var _angka = docoHelper.convertToAngka($(this).val());
  if ($(this).hasClass('nullable')) {
    _value = isNaN(_angka) ? null : _angka;
  } else {
    if ($(this).hasClass('unsigned')) {
      _angka = isNaN(_angka) ? 0 : Math.abs(_angka);
    } else {
      _angka = isNaN(_angka) ? 0 : _angka;
    }
    var _value = docoHelper.convertToRupiah(_angka);
    if (_value == 'NaN') {
      _value = 0;
    }
  }

  $(this).val(_value);
});

$(document).on('change keyup', '.doco-number-wcomma', function (e) {
  var _angka = docoHelper.convertToAngkaWComma($(this).val());
  _angka = isNaN(_angka) ? 0 : _angka;
  $(e.currentTarget).val(_angka);

  return _angka;
});

/*
  doco minute only untuk kebutuhan input hanya menit saja, max 60 min 0, dikombinasikan dengan class doco number
*/
$(document).on('keyup keypress blur change', '.doco-minute-only', function (e) {
    if (parseInt(this.value) > 60) {
        this.value = 60;
    } else if (parseInt(this.value) <= 0) {
        this.value = 0;
    }
});

/**
 *
 * @author: rizfardi@docotel.co.id
 * fungsi global number only
 *
 */
$(document).on('keydown', '.docoNumberOnly', function (e) {
  // Allow: backspace, delete, tab, escape, enter and .
  if (
    $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
    // Allow: Ctrl/cmd+A
    (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
    // Allow: Ctrl/cmd+C
    (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
    // Allow: Ctrl/cmd+X
    (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
    // Allow: home, end, left, right
    (e.keyCode >= 35 && e.keyCode <= 39)
  ) {
    // let it happen, don't do anything
    return;
  }
  // Ensure that it is a number and stop the keypress
  if (
    (e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
    (e.keyCode < 96 || e.keyCode > 105)
  ) {
    e.preventDefault();
  }
});

function convertTanggalView(tanggal) {
  var date = new Date(tanggal);
  var year = date.getFullYear();

  var month = (1 + date.getMonth()).toString();
  month = month.length > 1 ? month : '0' + month;

  var day = date.getDate().toString();
  day = day.length > 1 ? day : '0' + day;

  return day + '-' + month + '-' + year;
}

function convertTanggalYmd(tanggal) {
  var date = new Date(tanggal);
  var year = date.getFullYear();

  var month = (1 + date.getMonth()).toString();
  month = month.length > 1 ? month : '0' + month;

  var day = date.getDate().toString();
  day = day.length > 1 ? day : '0' + day;

  return year + '-' + month + '-' + day;
}

function convertTanggaldMY(tanggal) {
  let date = new Date(tanggal); // Or your specific date object

  let day = String(date.getDate()).padStart(2, '0');
  let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
  let month = monthNames[date.getMonth()];
  let year = date.getFullYear();

  const formattedDate = `${day}-${month}-${year}`;

  return formattedDate;
}

function getUmur(from, to) {
  const tglLahir = new Date(from);
  const today = new Date(to);

  var year = 0;
  var month = 0;
  var day = 0;
  var subtraction = 0;
  subtraction = new Date(
    new Date(today.getFullYear(), today.getMonth()) - 1
  ).getDate();
  // return subtraction;
  year = today.getFullYear() - tglLahir.getFullYear();
  month = today.getMonth() - tglLahir.getMonth();
  if (month < 0) {
    year--;
    month += 12;
  }
  day = today.getDate() - tglLahir.getDate();
  if (day < 0) {
    month--;
    if (month < 0) {
      year--;
      month += 12;
    }
    day += subtraction;
  }

  const date_merge = [];
  date_merge.push(year + ' Tahun' + ' ');
  date_merge.push(month + ' Bulan' + '');
  date_merge.push(day + ' Hari');
  if (date_merge.length > 1) date_merge.splice(date_merge.length - 1, 0, ' ');

  return date_merge.join('');
}

/**
 *
 * @author : metafiliana
 * @desc : reset form base on id or name
 * @param : $form = form id / name
 * // to call, use:
 * resetForm($('#myform')); // by id, recommended
 * resetForm($('form[name=myName]')); // by name
 *
 */
function docoResetForm($form) {
  $form
    .find('input:text, input:password, input:file, select, textarea')
    .val('');
  $form
    .find('input:radio, input:checkbox')
    .removeAttr('checked')
    .removeAttr('selected');
  $('.select2', $form).val('').trigger('change');
  $form.find('span.checked').removeClass('checked');
}

/**
 *
 * @author : rizal
 * @desc : double dependent
 *
 */
$(document).on('change', '.dep-to-child', function () {
  var _childId = $(this).data('depend_id');
  var _valueId = $('#' + _childId).val();
  var _prompt = $(this).data('depend_prompt');
  var _url = $(this).data('url');
  var _storage = $(this).data('storage');
  var _key = $(this).data('key');
  var _value = $(this).data('value') ? $(this).data('value') : null;
  // addtional dynamic option if set key id
  var _val = $(this).data('val');
  if ($(this).val()) {
    $.ajax({
      url: _url,
      type: 'POST',
      dataType: 'json',
      data: {
        depdrop_parents: [$(this).val()],
      },
      beforeSend: function () { },
      success: function (res) {
        var dataOut = res.output;
        var selected = false;
        $('#' + _childId).empty();
        var promptOpt = new Option(_prompt, '', false, false);
        $('#' + _childId).append(promptOpt);
        $.each(dataOut, function (i, item) {
          selected = dataOut[i].id == _valueId ? true : false;
          var newOption = new Option(
            dataOut[i].name,
            dataOut[i].id,
            false,
            selected
          );
          $('#' + _childId).append(newOption);
        });
        $('#' + _childId).trigger('change');
      },
    });
  } else {
    var listAllChild = JSON.parse(localStorage.getItem(_storage));
    $('#' + _childId).empty();
    var promptOpt = new Option(_prompt, '', false, false);
    $('#' + _childId).append(promptOpt);
    $.each(listAllChild, function (i, item) {
      // for condition _key option is _id
      const prefix = _key.match(/_id/g);
      if (prefix == '_id') {
        let value = _value ? item[_value] : item;
        selected = listAllChild[i][_key] == _parentValue ? true : false;
        var newOption = new Option(
          value,
          typeof listAllChild[i][_key] == 'undefined'
            ? i
            : listAllChild[i][_key],
          false,
          selected
        );
      } else {
        // for condition filter
        var newOption = new Option(
          listAllChild[i][_key],
          listAllChild[i][_key],
          false,
          false
        );
      }
      $('#' + _childId).append(newOption);
    });
    $('#' + _childId)
      .val('')
      .trigger('change');
  }
});
var _parentValue;
$(document).on('change', '.dep-to-parent', function () {
  var _parentId = $(this).data('depend_id');
  var _prompt = $(this).data('depend_prompt');
  var _url = $(this).data('url');
  if ($(this).val()) {
    $.ajax({
      url: _url,
      type: 'POST',
      dataType: 'json',
      data: {
        depdrop_parents: [$(this).val()],
      },
      beforeSend: function () { },
      success: function (res) {
        var dataOut = res.output;
        if (dataOut.length) {
          if ($('#' + _parentId).val() != dataOut[0].id) {
            _parentValue = dataOut[0].id;
            $('#' + _parentId)
              .val(dataOut[0].id)
              .trigger('change');
          }
        }
      },
    });
  } else {
    // $("#"+parentId).val(dataOut[0].id).trigger("change");
  }
});

$(document).on('click keyup change', 'input ,select, textarea', function () {
  var value = $(this).val();
  if (value != '') {
    var obj = $(this).closest('.form-group');
    $($(this).closest('div')).removeClass('has-error');
    obj.removeClass('has-error');
    obj.find('span.help-block.error').html('');
    obj.find('p.help-block.error').html('');
    obj.find('div.help-block').html('');
  }
});

/**
 *
 * @author : Randy Vianda Putra
 * @desc : remove keypress space for kode unique
 *
 */

$(document).on('keydown', '.kode_unique', function (e) {
  if (e.keyCode == 32) {
    return false;
  }
});

$(document).on('keydown', '.remove_space', function (e) {
  let firstChar = $(this).val();
  if (e.keyCode == 32 && firstChar == '') {
    return false;
  }
});

// kebutuhan hide header di antrian : ali.padilah@docotel.com

function hideHeader() {
  $('#navbar-second').hide(1000);
  $('.navbar-right').hide(1000);
  $('.back-antrian').hide(1000);
  setTimeout(function () {
    $('.page-container').css('cssText', 'margin-top: 40px !important;');
    $('.hd-up').css('display', 'none');
    $('.hd-down').css('display', 'block');
  }, 600);
  localStorage.setItem('hideHeader', 1);
}

function showHeader() {
  $('#navbar-second').show(1000);
  $('.navbar-right').show(1000);
  $('.back-antrian').show(1000);
  setTimeout(function () {
    $('.page-container').css('cssText', 'margin-top: 80px !important;');
    $('.hd-down').css('display', 'none');
    $('.hd-up').css('display', 'block');
  }, 600);
  localStorage.setItem('hideHeader', 0);
}

// end
/**
 *
 * @author : Randy Vianda Putra
 * @desc : generate table
 *
 */
function generateTable(tableId, dataJson) {
  let html = htmlTable(dataJson);
  if (html) {
    $(tableId).empty();
    $(tableId).append(html);
  } else {
    $(tableId).empty();
    $(tableId).append(html);
  }
}

function htmlTable(dataJson) {
  let html = '';
  if (typeof dataJson !== undefined) {
    if (typeof dataJson.header !== undefined) {
      let header = dataJson.header;
      if (header) {
        html += '<thead>';
        html += "<tr class='bg-inverse'>";
        for (let i = 0; i < header.length; i++) {
          html += '<th>' + `${header[i]}` + '</th>';
        }
        html += '</tr>';
        html += '</thead>';
      }
    }
    if (typeof dataJson.detail !== undefined) {
      let detail = dataJson.detail;
      if (detail) {
        detail.forEach((value, key) => {
          html += '<tbody>';
          html += '<tr>';
          if (dataJson.field) {
            let field = dataJson.field;
            for (let i = 0; i < field.length; i++) {
              html += '<td>' + `${value[field[i]]}` + '</td>';
            }
          }
          html += '</tr>';
          html += '</tbody>';
        });
      } else {
        let count_row = dataJson.header.length;
        html += '<tr>';
        html +=
          '<td colspan=' +
          count_row +
          ' class="text-center">' +
          i18next.t('Data Tidak Ditemukan') +
          '</td>';
        html += '</tr>';
      }
    }
  }
  return html;
}

$(document).on('keydown', '#sandi-validasi', function (event) {
  if (event.keyCode == 18) {
    $('#sandi-validasi').blur();
  }
});

$(document).on('keydown', null, 'alt+y', function (event) {
  var confirmDialogue = $('#confirm-dialog');

  if (typeof confirmDialogue != 'undefined' && confirmDialogue !== null) {
    $('.button-yes').click();
  }
});

$(document).on('keydown', null, 'alt+n', function (event) {
  var confirmDialogue = $('#confirm-dialog');

  if (typeof confirmDialogue != 'undefined' && confirmDialogue !== null) {
    $('.button-no').click();
  }
});

$(document).on('keydown', null, function (event) {
  if (event.key == 'Enter') {
    $('.button-yes').click();
  }
});

/**
 * @todo Validasi tanggal dengan format d-m-y H:i yang menggunakan masking
 * @author Tsani Nashrullah <tsani@docotel.com>
 */
$(document).on('blur', "*[data-mask='99-99-9999 99:99']", function (event) {
  var date = $(this).val();
  var regex =
    /^(((0[1-9]|[12]\d|3[01])\-(0[13578]|1[02])\-((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\-(0[13456789]|1[012])\-((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\-02\-((19|[2-9]\d)\d{2}))|(29\-02\-((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))) ([0-1]?[0-9]|2[0-4]):([0-5][0-9])$/g;
  var resultRegex = regex.test(date);

  if (date != '' && date != '__-__-____ __:__' && !resultRegex) {
    docoNotification('warning', 'Perhatian!', 'Format Tanggal dan Jam Salah!');

    $(this).focus();
    $(this).val('');
  }
});
/**
 * @todo Validasi tanggal yang menggunakan masking
 * @author Sigit Arif Munandar <sigit@docotel.com>
 */
$(document).on('blur', "*[data-mask='99-99-9999']", function (event) {
  var date = $(this).val();
  var regex =
    /^(((0[1-9]|[12]\d|3[01])\-(0[13578]|1[02])\-((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\-(0[13456789]|1[012])\-((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\-02\-((19|[2-9]\d)\d{2}))|(29\-02\-((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/g;
  var resultRegex = regex.test(date);

  if (date != '' && date != '__-__-____' && !resultRegex) {
    docoNotification('warning', 'Perhatian!', 'Format Tanggal Salah!');

    $(this).focus();
    $(this).val('');
  }
});

/**
 * @todo Generate umur
 * @author Sigit Arif Munandar <sigit@docotel.com>
 */
function generateUmur(dateString) {
  var now = new Date();
  var today = new Date(now.getYear(), now.getMonth(), now.getDate());

  var yearNow = now.getYear();
  var monthNow = now.getMonth();
  var dateNow = now.getDate();

  var dob = new Date(
    dateString.substring(6, 10),
    dateString.substring(3, 5) - 1,
    dateString.substring(0, 2)
  );

  var yearDob = dob.getYear();
  var monthDob = dob.getMonth();
  var dateDob = dob.getDate();
  var age = {};
  var ageString = '';
  var yearString = '';
  var monthString = '';
  var dayString = '';

  yearAge = yearNow - yearDob;

  if (monthNow >= monthDob) var monthAge = monthNow - monthDob;
  else {
    yearAge--;
    var monthAge = 12 + monthNow - monthDob;
  }

  if (dateNow >= dateDob) {
    var dateAge = dateNow - dateDob;
  } else {
    monthAge--;
    var dateAge = 31 + dateNow - dateDob;

    if (monthAge < 0) {
      monthAge = 11;
      yearAge--;
    }
  }

  age = {
    years: yearAge,
    months: monthAge,
    days: dateAge,
  };

  yearString = ' tahun';
  monthString = ' bulan';
  dayString = ' hari';
  ageString =
    age.years +
    yearString +
    ' ' +
    age.months +
    monthString +
    ' ' +
    age.days +
    dayString;

  return ageString;
}

// $(document).on('click', '.open>ul>li>a', function (e){
//     e.preventDefault();
//     $(this).parent().parent().parent().toggleClass('open');
//     $('.open>ul>li>ul').removeClass('show');
// })

//Advanced filter
//collapsing filter
//Vega
$(document).ready(function () {
  generateFilter();
});

function checkLimitless(val, limit) {
  if (limit == true) {
    if (val > 100) {
      val = '100';
      val.toString();
    }
  }
  return val;
}

function generateFilter(tab = null, filtername = 'filter-form') {
  var dataLabel = $('.advanced-filter').data('label');
  dataLabel =
    typeof dataLabel != 'undefined' && dataLabel != null && dataLabel != ''
      ? `<span>${dataLabel}</span>`
      : null;
  const filter = `
        <div class="flex-container ${dataLabel != null ? 'filter-label-btn' : ''
    }">

            <div class="flex-filter">
                <div class="col-md-12 ${filtername}" id="filterform"></div>
            </div>
            <div class="flex-1">
                <a id="collapseClick">
                    <div class=" more-filter">
                        <p class="no-margin"> ${dataLabel != null
      ? `<span>${dataLabel}</span>`
      : '<i class="fa fa-search"></i>'
    } <i id="morefilter" class="fa fa-chevron-down"></i></p>
                    </div>
                </a>
            </div>
        </div>
    `;
  if (tab) {
    $('.' + tab).append(filter);
  } else {
    $('.advanced-filter').append(filter);
  }
}

$(document).ready(function () {
  $(document).on('click', 'a#collapseClick', function () {
    $('.collapse-filter-click').slideToggle(300);
    $('#morefilter').toggleClass('fa-chevron-down');
    $('#morefilter').toggleClass('fa-chevron-up');
  });
});
$(document).ready(function () {
  $('a').click(function () {
    $(this).find('li').children('#chevron').toggleClass('fa-chevron-down');
    $(this).find('li').children('#chevron').toggleClass('fa-chevron-up');
  });
});

// mencegah karakter lain selain angka desimal
$(document).ready(function () {
  $(document).on('input', '.doco-decimal', function () {
    match = /(\d{0,9})[^.]*((?:\.\d{0,2})?)/g.exec(
      this.value.replace(/[^\d.]/g, '')
    );
    this.value = match[1] + match[2];
  });
});

// mencegah karakter lain selain angka desimal dengan menggunakan koma
$(document).ready(function () {
  $(document).on('input', '.doco-decimal-wcomma', function () {
    match = /(\d{0,9})[^,]*((?:\,\d{0,2})?)/g.exec(
      this.value.replace(/[^\d,]/g, '')
    );
    this.value = match[1] + match[2];
  });
});

// mencegah karakter lain selain angka desimal dengan menggunakan koma, dengan max 3 angka di belakang koma
$(document).ready(function () {
  $(document).on('input', '.doco-decimal-wcomma-3', function () {
    match = /(\d{0,9})[^,]*((?:\,\d{0,3})?)/g.exec(
      this.value.replace(/[^\d,]/g, '')
    );
    this.value = match[1] + match[2];
  });
});

$(document).on('keydown', '.docoNumberFloat', function (e) {
  if (
    $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
    (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
    (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
    (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
    (e.keyCode >= 35 && e.keyCode <= 39)
  ) {
    return;
  }
  if (
    (e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
    (e.keyCode < 96 || e.keyCode > 105) &&
    (e.keyCode < 188 || e.keyCode > 188)
  ) {
    e.preventDefault();
    return;
  }
});

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * mencegah karakter lain selain angka desimal dengan validasi tidak boleh 0 dan maksimal 100
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */
$(document).ready(function () {
  $(document).on('input', '.doco-decimal-100', function () {
    match = /(\d{0,3})[^.]*((?:\.\d{0,2})?)/g.exec(
      this.value.replace(/[^\d.]/g, '')
    );
    this.value = match[1] + match[2];
  });

  $('#adm_persen').change(function () {
    if ($(this).val() < 0 || $(this).val() == 0) {
      docoNotification('error', 'Proses Gagal !', 'Tidak Boleh 0');
      $(this).val('1');
    }
  });

  $('#adm_persen').keyup(function () {
    if ($(this).val() > 100) {
      docoNotification('error', 'Proses Gagal !', 'Maksimal 100');
      $(this).val('100');
    }
  });
});
/**
 * @todo global hot key list anda save
 * @author <iqbal@docotel.com>
 */
$(document).ready(function () {
  $(document).on('keydown', null, function (e) {
    if (e.key == 'Enter') {
      $('.filter-form, .data-filter').click();
    }

    if (e.key == 'F7') {
      $('.filter-form, .data-reset').click();
    }
  });
});

$(document).on('keydown', null, 'alt+s', function (event) {
  if ($('#modal_backdrop').hasClass('in')) {
    $('#modal_backdrop, #btn-submit').click();
  } else {
    $('.form-horizontal, #btn-submit').click();
  }
});

$(document).on('keydown', null, 'esc', function (event) {
  /*
    var content = $("#modal_backdrop").find("div.modal-content").html();
    var regex_html = /<[^>]*>/g;

    //@todo check content sebelum di toggle

    if (content.search(regex_html) >= 1) {
        $('#modal_backdrop').modal('hide');
    }
    */

  if ($('#modal_backdrop').hasClass('in')) {
    $('#modal_backdrop').modal('toggle');
  }
});

/**
 * @todo global tab order select2 stay focus
 * @author <iqbal@docotel.com>
 */
$('select').on('select2:close', function () {
  $(this).focus();
});

/**
 * @todo auto focus to element with tabindex = 0
 * @autho <tri.anggoro@docotel.com>
 */

$('[tabindex=0]').focus();

function bindingRegion(urlApi, objectElement) {
  // object element must be 4 element -> province,city,district,village
  const { province, city, district, village } = objectElement;
  const defaultValue = [
    {
      id: '',
      text: '--Pilih--',
    },
  ];
  existVillage = typeof village !== 'undefined';
  province.bind('change', () => {
    if (province.val() !== '' && province.val() !== null) {
      $.ajax({
        url: urlApi,
        method: 'GET',
        data: {
          type: 'city',
          foreignId: province.val(),
        },
        success: (res) => {
          const { data } = res;
          refreshOptionSelect2(city, data);
          // city.val('').trigger('change')
          // district.val('').trigger('change')
          city.prop('disabled', false);
          resetDropdownRegion(district);
          resetDropdownRegion(village);
        },
      });
    } else {
      city.val('').trigger('change');
      city.prop('disabled', true);
    }
  });
  city.bind('change', () => {
    if (city.val() !== '' && city.val() !== null) {
      $.ajax({
        url: urlApi,
        method: 'GET',
        data: {
          type: 'district',
          foreignId: city.val(),
        },
        success: (res) => {
          const { data } = res;
          refreshOptionSelect2(district, data);
          // district.val('').trigger('change')
          district.prop('disabled', false);
          resetDropdownRegion(village);
        },
      });
    } else {
      district.val('').trigger('change');
      district.prop('disabled', true);
    }
  });
  if (existVillage) {
    district.bind('change', () => {
      if (district.val() !== '' && district.val() !== null) {
        $.ajax({
          url: urlApi,
          method: 'GET',
          data: {
            type: 'village',
            foreignId: district.val(),
          },
          success: (res) => {
            const { data } = res;
            refreshOptionSelect2(village, data);
            // village.val('').trigger('change')
            village.prop('disabled', false);
          },
        });
      } else {
        village.val('').trigger('change');
        village.prop('disabled', true);
      }
    });
    village.bind('change', () => {
      if (village.val() !== '' && village.val() !== null) {
        village.prop('disabled', false);
      }
    });
  }
  if (province.val() == null || province.val() == '') {
    city.prop('disabled', true);
  }
  if (city.val() == null || city.val() == '') {
    district.prop('disabled', true);
  }
  if ((district.val() == null || district.val() == '') && existVillage) {
    village.prop('disabled', true);
  }
}

function fetchRegionApi(urlApi, type, foreignId = null) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: urlApi,
      method: 'GET',
      data: {
        type,
        foreignId: foreignId !== null ? foreignId : '',
      },
      success: (res) => {
        resolve(res.data);
      },
      error: (res) => {
        resolve([]);
      },
    });
  });
}

function setRegionData(urlApi, object) {
  const placeholder = '— PILIH —';
  showLoader();
  const isExistVillage = typeof object.village !== 'undefined';
  return new Promise((resolve, reject) => {
    const { province, city, district, village } = object;
    province.element.unbind('change');
    city.element.unbind('change');
    district.element.unbind('change');
    if (isExistVillage) {
      village.element.unbind('change');
    }
    if (province.value !== null && province.value !== '') {
      destroySelect2IfExist(province.element);
      province.element.select2();
      province.element.val(province.value).trigger('change');
      const dataCity = fetchRegionApi(urlApi, 'city', province.value);
      refreshOptionSelect2(city.element, dataCity);
      if (province.value !== null && province.value !== '') {
        city.element.prop('disabled', false);
      }
      if (city.value !== null && city.value !== '') {
        city.element.val(city.value).trigger('change');
        const dataDistrict = fetchRegionApi(urlApi, 'district', city.value);
        refreshOptionSelect2(
          district.element,
          [{ id: null, text: placeholder }].concat(dataDistrict)
        );
        district.element.prop('disabled', false);
      }

      if (district.value !== null && district.value !== '') {
        district.element.val(district.value).trigger('change');
        if (isExistVillage) {
          village.element.prop('disabled', false);
          const dataVillage = fetchRegionApi(urlApi, 'village', district.value);
          refreshOptionSelect2(
            village.element,
            [{ id: null, text: placeholder }].concat(dataVillage)
          );
          if (village.value !== null && village.value !== '') {
            village.element.val(village.value).trigger('change');
          } else {
            village.element.val('').trigger('change');
          }
        }
      }
      var attr = {
        province: province.element,
        city: city.element,
        district: district.element,
      };
      var existVillage = isExistVillage ? { village: village.element } : {};
      var mergeObj = $.extend({}, attr, existVillage);
      bindingRegion(urlApi, mergeObj);
    }
    hideLoader();
    resolve(true);
  });
}

function resetDropdownRegion(element) {
  // if (element.hasClass('select2-hidden-accessible')) {
  //     element.select2('destroy')
  // }
  element.prop('disabled', true);
}

function destroySelect2IfExist(element, additionalCallback = () => { }) {
  if (element.hasClass('select2-hidden-accessible')) {
    element.select2('destroy');
    additionalCallback();
  }
}
function refreshOptionSelect2(
  element,
  newPayload,
  payloadKey = {},
  options = {}
) {
  element.empty();
  const { withoutPlaceholder } = options;
  let data = [];
  let dataKeyLoop = {
    id: 'id',
    text: 'text',
  };
  if (
    typeof payloadKey.id !== 'undefined' &&
    typeof payloadKey.text !== 'undefined'
  ) {
    dataKeyLoop = payloadKey;
  }
  if (element.hasClass('select2-hidden-accessible')) {
    element.select2('destroy');
    let dataKode = element.data('code');
    let value = null;
    if (newPayload.constructor === Array) {
      if (
        typeof withoutPlaceholder == 'undefined' ||
        (typeof withoutPlaceholder != 'undefined' && !withoutPlaceholder)
      ) {
        element.append(new Option('-- Pilih --', '', false, false));
      }
      newPayload.map((item) => {
        if (item.kode == dataKode) {
          value = item.id;
        }
        element.append(
          new Option(item[dataKeyLoop.text], item[dataKeyLoop.id], false, false)
        );
      });
    } else {
      Object.keys(newPayload).map((keyObject) => {
        element.append(
          new Option(newPayload[keyObject], keyObject, false, false)
        );
      });
    }
    element.select2();

    if (value) {
      element.val(value).trigger('change');
    }
  } else {
    if (newPayload.constructor === Array) {
      data = newPayload;
    } else {
      Object.keys(newPayload).map((keyObject) => {
        data.push({
          [keyObject]: newPayload[keyObject],
        });
      });
    }
    element.select2({
      data,
    });
  }
}

function confirmationDialog(
  message = 'Apakah anda yakin untuk menyimpan data ini ?',
  callback = () => { }
) {
  $.showQuestionDialog(
    'Perhatian !',
    message,
    {
      buttons: {
        Yes: 'button-yes',
        No: 'button-no',
      },
    },
    function (reaction) {
      callback(reaction == 'Yes');
    }
  );
}

function renderPickadate(element, option = {}) {
  let optionPickadate = {
    editable: true,
    format: 'dd-mm-yyyy',
    formatSubmit: 'dd-mm-yyyy',
    selectMonths: true,
    selectYears: true,
    min: [1900, 01, 01],
    max: true,
    onClose: function () {
      element.focus();
    },
  };
  if (typeof option.moreThanToday !== 'undefined' && option.moreThanToday) {
    optionPickadate.min = new Date();
    delete optionPickadate.max;
  }
  element.pickadate(optionPickadate);
  const picker = element.pickadate('picker');
  if (typeof option.dependElementPicker !== 'undefined') {
    if(typeof option.readOnly === 'undefined') {
      option.dependElementPicker.on('click', function (event) {
        if (picker.get('open')) {
          picker.close();
        } else {
          picker.open();
        }
        event.stopPropagation();
      });
    }
  }
  if (typeof option.lowerThanToday !== 'undefined' && option.lowerThanToday) {
    element.bind('change', ({ delegateTarget }) => {
      if ($(delegateTarget).val() != '') {
        const splitDate = $(delegateTarget).val().split('-');
        const date = `${splitDate[2]}-${splitDate[1]}-${splitDate[0]}`;

        var myDate = new Date(date);
        var today = new Date();

        if (myDate > today) {
          $(delegateTarget).pickadate('picker').set('select', new Date());
        }
      }
    });
  }
  if (typeof option.defaultValue !== 'undefined') {
    // picker.set('select', new Date(option.defaultValue))
    setTimeout(() => {
      picker.set('select', new Date(option.defaultValue));
    }, 1000);
  }
}

const convertDateByFormat = (stringDate, formatDate = 'd-m-y H:i') => {
  if (typeof stringDate !== 'undefined' && stringDate != '') {
    let result = '';
    const usedMonthName = {
      0: 'Januari',
      1: 'Februari',
      2: 'Maret',
      3: 'April',
      4: 'Mei',
      5: 'Juni',
      6: 'Juli',
      7: 'Agustus',
      8: 'September',
      9: 'Oktober',
      10: 'November',
      11: 'Desember',
    };
    const date = new Date(stringDate);
    for (let index = 0; index < formatDate.length; index++) {
      switch (formatDate.substr(index, 1)) {
        case 'd':
          result =
            result +
            (parseInt(date.getDate()) <= 9
              ? '0' + date.getDate()
              : date.getDate());
          break;
        case 'm':
          result = result + usedMonthName[date.getMonth()];
          break;
        case 'M':
          result =
            result +
            (parseInt(date.getMonth() + 1) <= 9
              ? `0${date.getMonth() + 1}`
              : date.getMonth() + 1);
          break;
        case 'Y':
        case 'y':
          result = result + date.getFullYear();
          break;
        case 'h':
          result =
            result +
            (parseInt(date.getHours()) <= 9
              ? '0' + date.getHours()
              : date.getHours());
          break;
        case 'i':
          result =
            result +
            (parseInt(date.getMinutes()) <= 9
              ? '0' + date.getMinutes()
              : date.getMinutes());
          break;
        case 's':
          result =
            result +
            (parseInt(date.getSeconds()) <= 9
              ? '0' + date.getSeconds()
              : date.getSeconds());
          break;
        default:
          result = result + formatDate.toLowerCase().substr(index, 1);
          break;
      }
    }
    return result;
  } else {
    return '';
  }
};

function upperCaseFirst(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

$.fn.select2InfinityScroll = function (option) {
  let additionalOption = {};
  const { callbackData, callbackProccess, url } = option;
  if (typeof option.additionalOption != 'undefined') {
    additionalOption = option.additionalOption;
  }
  
  const parent = $(this).parent();    
  if( typeof parent != 'undefined') {
    if(! parent.is('td')) {
      console.log(parent)
      additionalOption.dropdownParent = parent;
    }
  }
  

  if (typeof url != 'undefined' && url != '') {
    $(this).select2({
      ...additionalOption,
      width: '100%',
      language: 'id',
      ajax: {
        url,
        data: function (params) {
          params.limit = 10;
          if (typeof option.limit != 'undefined') {
            params.limit = option.limit;
          }
          if (callbackData !== null && typeof callbackData === 'function') {
            return callbackData(params);
          } else {
            return {
              payload: {
                term: params.term,
                page: params.page || 1,
                limit: params.limit,
              },
            };
          }
        },
        delay: 1000,
        processResults: function (res, params) {
          params.page = params.page || 1;
          const { data } = res;
          const lengthOriginRes = data.length;
          if (data.length > params.limit) {
            data.splice(params.limit, 1);
          }
          const responseProcess = {
            results: data,
            pagination: {
              more: lengthOriginRes > params.limit,
            },
          };
          if (
            callbackProccess !== null &&
            typeof callbackProccess === 'function'
          ) {
            return callbackProccess(responseProcess);
          } else {
            return responseProcess;
          }
        },
      },
    });
  }
};

function infinityScrollSelect2(
  element,
  url,
  callbackData = null,
  callbackProccess = null
) {
  element.select2({
    width: '100%',
    language: 'id',
    ajax: {
      url,
      data: function (params) {
        if (callbackData !== null && typeof callbackData === 'function') {
          return callbackData(params);
        } else {
          return {
            payload: {
              term: params.term,
              page: params.page || 1,
            },
          };
        }
      },
      delay: 1000,
      processResults: function (res, params) {
        if (
          callbackProccess !== null &&
          typeof callbackProccess === 'function'
        ) {
          return callbackProccess(res, params);
        } else {
          params.page = params.page || 1;
          const { data } = res;
          const lengthOriginRes = data.length;
          if (data.length > 10) {
            data.splice(10, 1);
          }
          return {
            results: data,
            pagination: {
              more: lengthOriginRes > 10,
            },
          };
        }
      },
    },
  });
}

function validateForm(formElement, modelForm, option) {
  let isValidated = true;
  let rulesAndElement = {};
  if (typeof option.rules != 'undefined') {
    rulesAndElement = option.rules;
  } else {
    rulesAndElement = option;
  }
  Object.keys(rulesAndElement).map((keyValue) => {
    const elementChecking = $(
      formElement.find(
        `[name="${modelForm !== null ? `${modelForm}[${keyValue}]` : keyValue
        }"]`
      )
    );
    // Checking by rules
    const arrayRules =
      rulesAndElement[keyValue].constructor === Array
        ? rulesAndElement[keyValue]
        : rulesAndElement[keyValue].split('|');
    arrayRules.map((itemRule) => {
      let resultValidation = {};
      const rule = itemRule.split(':')[0];
      let paramRule = itemRule.split(':');
      paramRule.splice(0, 1);
      paramRule = paramRule.join(':').split(',');
      switch (rule) {
        case 'required':
          const conditionRadioButton =
            typeof elementChecking !== 'undefined' &&
            elementChecking.length > 1 &&
            elementChecking[1].type == 'radio' &&
            (typeof elementChecking.parent().find('input:checked').val() ==
              'undefined' ||
              elementChecking.parent().find('input:checked').val() == '');
          if (
            conditionRadioButton ||
            (elementChecking.length === 1 &&
              (typeof elementChecking === 'undefined' ||
                (typeof elementChecking !== 'undefined' &&
                  (elementChecking.val() === '' ||
                    elementChecking.val() === null))))
          ) {
            resultValidation = {
              message: '{element} Tidak boleh kosong',
            };
          }
          break;
        case 'integer':
          if (
            !Number.isInteger(
              parseInt(elementChecking.val().split('.').join(''))
            )
          ) {
            resultValidation = {
              message: '{element} harus berformat angka',
            };
          }
          break;
        case 'greaterThanEqual':
          if (elementChecking.val() < parseInt(paramRule[0])) {
            resultValidation = {
              message: `Nilai {element} harus lebih dari atau sama dengan ${paramRule[0]}`,
            };
          }
          break;
        case 'greaterThan':
          if (elementChecking.val() <= parseInt(paramRule[0])) {
            resultValidation = {
              message: `Nilai {element} harus lebih dari ${paramRule[0]}`,
            };
          }
          break;
        case 'lowerThanEqual':
          if (elementChecking.val() > parseInt(paramRule[0])) {
            resultValidation = {
              message: `Nilai {element} harus kurang dari atau sama dengan ${paramRule[0]}`,
            };
          }
          break;
        case 'lowerThan':
          if (elementChecking.val() >= parseInt(paramRule[0])) {
            resultValidation = {
              message: `Nilai {element} harus kurang dari ${paramRule[0]}`,
            };
          }
          break;
        case 'between':
          if (
            elementChecking.val() < parseInt(paramRule[0]) ||
            elementChecking.val() > parseInt(paramRule[1])
          ) {
            resultValidation = {
              message: `Nilai {element} harus diantara ${paramRule[0]} dan ${paramRule[1]}`,
            };
          }
          break;
        default:
          break;
      }
      if (typeof resultValidation.message !== 'undefined') {
        isValidated = false;
        const parentSection = $(elementChecking.parents('.form-group')[0]);
        const label =
          typeof elementChecking.data('label') !== 'undefined' &&
            elementChecking.data('label') !== ''
            ? elementChecking.data('label')
            : $(parentSection.find('label.control-label')).html();
        parentSection.addClass('has-error');
        const errorMessage = `<i class="fa fa-exclamation-circle"></i>${resultValidation.message.replace(
          new RegExp('{element}', 'gmi'),
          label
        )}`;
        if (parentSection.find('span.help-block.error').length == 0) {
          parentSection.append(`
                        <span class="help-block error">${errorMessage}</span>
                    `);
        } else {
          $(parentSection.find('span.help-block.error')[0]).html(errorMessage);
        }
      }
    });
  });
  if (typeof option.successCallback !== 'undefined' && isValidated) {
    option.successCallback(isValidated);
  } else if (typeof option.failedCallback !== 'undefined' && !isValidated) {
    option.failedCallback(isValidated);
  } else {
    return isValidated;
  }
}

function isNumberKey(event, element = null, type = 'without-commas') {
  if (type == 'with-commas') {
    // VALIDASI ANGKA 0
    if (
      ($(element).val().toString().length == 0 && event.charCode == 46) ||
      ($(element).val().toString().indexOf('.') > 0 && event.charCode == 46)
    ) {
      return false;
    }
    const dataDecimal =
      $(element).val().split('.').length > 1
        ? $(element).val().split('.')[1]
        : null;
    if (dataDecimal !== null && dataDecimal.length >= 2) {
      return false;
    }

    // VALIDASI ANGKA APABILA NILAI PERTAMA BERNILAI 0 MAKA INPUT SELANJUTNYA HARUS BERNILAI .
    if (
      $(element).val().toString().length == 1 &&
      event.charCode != 46 &&
      $(element).val() == 0
    ) {
      $(element).val(event.keyValue);
    }
    return (
      (event.charCode >= 48 && event.charCode <= 57) ||
      event.charCode == 0 ||
      event.charCode == 46
    );
  } else {
    if (type != 'code') {
      // VALIDASI ANGKA APABILA NILAI PERTAMA BERNILAI 0 MAKA TIDAK BISA MENGINPUT ANGKA .
      if ($(element).val().toString().length == 1 && $(element).val() == 0) {
        $(element).val(
          (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0
            ? event.key
            : 0
        );
        return false;
      }
    }
    return (
      (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0
    );
  }
}

window.onscroll = function () {
  checkBtnToolbarPosition();
};

function checkBtnToolbarPosition() {
  var panelHeader = $('.panel-toolbar');
  var panelHeaders = $('.panel-toolbars');
  panelHeaders.removeClass('sticky');
  panelHeader.removeClass('sticky');
  // component toolbar
  if (panelHeader.length) {
    var header = panelHeader[0];
    if (panelHeader.length > 1) {
      var _tabPanel = $('.panel-toolbar').closest('div.tab-pane.active');
      if (
        _tabPanel.length &&
        _tabPanel.find('.panel-toolbar').not('.panel-toolbar-hidden').length > 0
      ) {
        header = _tabPanel
          .find('.panel-toolbar')
          .not('.panel-toolbar-hidden')[0];
      }
    }
    /** check header tab-pane */
    var sticky = header.offsetTop;
    if (window.pageYOffset > sticky) {
      header.classList.add('sticky');
    } else {
      header.classList.remove('sticky');
    }
  }

  if (panelHeaders.length) {
    var headers = panelHeaders[0];
    if (panelHeaders.length > 1) {
      var _tabPanel = $('.panel-toolbars').closest('div.tab-pane.active');
      if (
        _tabPanel.length &&
        _tabPanel.find('.panel-toolbars').not('.panel-toolbar-hidden').length >
        0
      ) {
        headers = _tabPanel
          .find('.panel-toolbars')
          .not('.panel-toolbar-hidden')[0];
      }
    }
    /** check header tab-pane */
    var stickys = headers.offsetTop;
    if (window.pageYOffset > stickys) {
      headers.classList.add('sticky');
    } else {
      headers.classList.remove('sticky');
    }
  }
}

// ---------------- ToggleSwitchBootstrap ------------------------------------- //
// Created By Prof. Dr. Ir. H. yafi.maulana@sirs.co.id
// *Note - Harus didefine didalam document ready function
//       - Dianjurkan menggunakan Class ketimbang ID
//       - untuk datatable, usahakan dideclare di initComplete
$.fn.docoToggleSwitch = function (options = {}) {
  const _this = $(this);

  // Next Pengembangan yang dicomment dibawah
  // $.fn.bootstrapSwitch.defaults.size = 'mini';
  // $.fn.bootstrapSwitch.defaults.onColor = 'success';
  // $.fn.bootstrapSwitch.defaults.offColor = 'danger';
  // _this.bootstrapSwitch();

  $(_this).on('switchChange.bootstrapSwitch', ({currentTarget}, state) => {
    let _default = {
      data : {
        id : typeof $(currentTarget).attr("data-id") !== 'undefined' ? $(currentTarget).attr("data-id") : null,
        status: typeof $(currentTarget).is(":checked") !== 'undefined' ? ($(currentTarget).is(":checked") ? 1 : 0 ) : null,
      },
      error: () => {
        // jika error server dan ini ajax toggle maka akan kembali ke value semula
        if(typeof _this.attr('bootstrapSwitch') !== 'undefined'){
          _this.bootstrapSwitch('state', !_this.is(':checked'), "false");
        }
      }
    };
    let _options = $.extend({}, _default, options);
    $(currentTarget).docoForm("click", _options);
  });
}
// ----------------- End of ToggleSwitchBootstrap ------------------------

$.fn.toggleSwitchStatus = function (option = {}) {
  const element = $(this);
  element.bootstrapSwitch();
  let payload;
  let dataElement = [];
  element.map((index) => {
    dataElement[index] = $(element[index]).data();
    if (typeof dataElement[index].url != 'undefined') {
      $(element[index]).on('switchChange.bootstrapSwitch', () => {
        try {
          payload =
            typeof dataElement[index].payload != 'undefined'
              ? JSON.parse(dataElement[index].payload)
              : {};
        } catch (error) {
          payload = {};
        }
        $.ajax({
          url: dataElement[index].url,
          method:
            typeof dataElement[index].method != 'undefined'
              ? dataElement[index].method
              : 'POST',
          data: payload,
          success: (res) => {
            if (typeof option.callbackSuccess != 'undefined') {
              option.callbackSuccess(res);
            }
          },
        });
      });
    }
  });
};

$(() => {
  moment.locale('id');
  if (moduleName == 'ambulan') {
    $('#ambulance-notification-list .notification-date').each(
      (index, element) => {
        const dataElement = $(element).data();
        $(element).html(
          `<i class="fa fa-clock-o"></i>${moment(dataElement.date)
            .locale('id')
            .fromNow()}`
        );
      }
    );
  } else if (moduleName == 'farmasi') {
    $('#farmasi-notification-list .notification-date').each(
      (index, element) => {
        const dataElement = $(element).data();
        $(element).html(
          `<i class="fa fa-clock-o"></i>${moment(dataElement.date)
            .locale('id')
            .fromNow()}`
        );
      }
    );
  }
});

const checkIdAndClass = (obj) => {
  var result = typeof obj.id != 'undefined' && obj.id != '' ? `#${obj.id}` : '';
  if (typeof obj.class != 'undefined' && obj.class != '') {
    result = `${result != '' ? `${result},` : ''}.${obj.class}`;
  }
  return result;
};

$.fn.dependentMultipleHandler = function (options) {
  const { formHandler } = options;
  let typeElement = false;
  let elementNameDependent = '';
  let rulesByKey = {};
  formHandler.map((arrayForm) => {
    rulesByKey[arrayForm.value] = checkIdAndClass(arrayForm);
    elementNameDependent = `${elementNameDependent != '' && typeof arrayForm.id != 'undefined'
      ? `${elementNameDependent},`
      : ''
      }${typeof arrayForm.id != 'undefined' && arrayForm.id != ''
        ? `#${arrayForm.id}`
        : ''
      }`;
    if (typeof arrayForm.class != 'undefined' && arrayForm.class != '') {
      elementNameDependent = `${elementNameDependent != '' ? `${elementNameDependent},` : ''
        }.${arrayForm.class}`;
    }
  });
  if ($(elementNameDependent).is(':radio')) {
    typeElement = 'radio';
  } else if ($(elementNameDependent).is(':checkbox')) {
    typeElement = 'checkbox';
  }
  $(this).bind('change', ({ currentTarget }) => {
    if ($(currentTarget).is(':radio')) {
      $(elementNameDependent).prop('checked', false);
      $(elementNameDependent).prop('disabled', true);
      $(elementNameDependent).val('');
      for (let indexRules = 0; indexRules < formHandler.length;) {
        var eachElementDependent = checkIdAndClass(formHandler[indexRules]);
        if ($(currentTarget).val() == formHandler[indexRules].value) {
          $(eachElementDependent).prop('disabled', false);
        }
        indexRules += 1;
      }
    } else if (
      $(currentTarget).is(':checkbox') &&
      typeof rulesByKey[$(currentTarget).val()] != 'undefined'
    ) {
      $(rulesByKey[$(currentTarget).val()]).prop(
        'disabled',
        !$(currentTarget).is(':checked')
      );
      if (!$(currentTarget).is(':checked')) {
        $(rulesByKey[$(currentTarget).val()]).prop('checked', false);
        $(rulesByKey[$(currentTarget).val()]).val('');
      }
    }
    if (typeElement == 'radio' || typeElement == 'checkbox') {
      $.uniform.update();
    }
  });
};

const serializeArrayToJson = (formElement) => {
  const data = $(formElement).serializeArray();
  let result = {};
  data.map((eachForm) => {
    // multiple select handling
    if (!eachForm.name.includes('[]')) {
      result[eachForm.name] = eachForm.value;
    } else {
      result[eachForm.name.replace(/\[\]/, '')] = $(
        `[name="${eachForm.name}"]`
      ).val();
    }
  });
  return result;
};

function invertColor(hex, bw) {
  if (hex == '') {
    return '#ffffff';
  }

  if (hex.indexOf('#') === 0) {
    hex = hex.slice(1);
  }
  // convert 3-digit hex to 6-digits.
  if (hex.length === 3) {
    hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
  }
  if (hex.length !== 6) {
    throw new Error('Invalid HEX color.');
  }
  var r = parseInt(hex.slice(0, 2), 16),
    g = parseInt(hex.slice(2, 4), 16),
    b = parseInt(hex.slice(4, 6), 16);
  if (bw) {
    return r * 0.299 + g * 0.587 + b * 0.114 > 186 ? '#000000' : '#FFFFFF';
  }
  // invert color components
  r = (255 - r).toString(16);
  g = (255 - g).toString(16);
  b = (255 - b).toString(16);
  // pad each with zeros and return
  return '#' + padZero(r) + padZero(g) + padZero(b);
}

$(document).on('click', '[data-type="api-trigger"]', ({ currentTarget }) => {
  const element = $(currentTarget);
  const data = element.data();
  if (typeof data.url != 'undefined') {
    element.docoForm('click', {
      url: data.url,
      method: 'GET',
      type: 'JSON',
      confirmMessage:
        typeof data.msg != 'undefined' ? data.msg : 'Apakah Anda yakin?',
      success: function (res) {
        if (typeof data.redirect != 'undefined') {
          location.replace(data.redirect);
        } else if (typeof data.tableid != 'undefined') {
          $(`#${data.tableid}`).DataTable().draw();
        }
      },
    });
  }
});

// edited by ilhamsyah 22-07-2021, Remove Filter Session
function RemoveFilterSession(tableId = null) {
  let _sessionKey = "FilterTable/"
  if (tableId) {
    _sessionKey = _sessionKey + tableId
  }
  for (var i = sessionStorage.length; i--;) {
    if (sessionStorage.key(i).includes(_sessionKey)) {
      sessionStorage.removeItem(sessionStorage.key(i));
    }
  }
}

$(document).on('click', '.btn-reset--datatable', ({ currentTarget }) => {
  const tableId = $(currentTarget).data('table-id');
  RemoveFilterSession(tableId)
  const element = $(`#filter-section__${tableId}`);
  const formWrapper = $(`#form-filter__${tableId}`);
  element.find('input').val('');
  element.find('select').val(null).trigger('change', {'elemfrom':currentTarget});
  const tableElement = $(`#${tableId}`).DataTable();
  showLoader();
  tableElement.context[0].ajax.data.advancedFilter =
    serializeArrayToJson(formWrapper);
  tableElement.ajax.reload();
});
$(document).on('click', '.btn-search--datatable', ({ currentTarget }) => {
  const tableId = $(currentTarget).data('table-id');
  const formWrapper = $(`#form-filter__${tableId}`);
  const tableElement = $(`#${tableId}`).DataTable();
  showLoader();
  tableElement.context[0].ajax.data.advancedFilter =
    serializeArrayToJson(formWrapper);
  tableElement.ajax.reload();
});

const ucWords = function (string) {
  return string.replace(
    /(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
    function (s) {
      return s.toUpperCase();
    }
  );
};

$.fn.filterForm = function ({ tableElement, arrayForm, filterRendered }) {
  const wrapperFilter = $(this);
  var dataLabel = $('.advanced-filter').data('label');
  var tableId = tableElement.prop('id');
  dataLabel =
    typeof dataLabel != 'undefined' && dataLabel != null && dataLabel != ''
      ? `<span>${dataLabel}</span>`
      : null;
  wrapperFilter.append(`
        <div class="flex-container filter-label-btn">
            <div class="flex-filter">
                <div class="col-md-12" id="filter-section__${tableId}"></div>
            </div>
            <div class="flex-1">
                <div class="col-md-6">
                    <button id="btn-search__${tableId}" type="button" data-placement="bottom" data-toggle="tooltip" title="Cari" data-table-id="${tableId}" class="btn btn-xs btn-only btn-primary-color btn-search--datatable"><i class="fa fa-search"></i></button>
                    <button id="btn-reset__${tableId}" type="button" data-placement="bottom" data-toggle="tooltip" title="Reset Filter" data-table-id="${tableId}" class="btn btn-xs btn-only btn-primary-color btn-reset--datatable"><i class="fa fa-undo"></i></button>
                </div>
                <div class="col-md-6 text-right">
                    <a id="collapse__${tableId}">
                        <div class="more-filter">
                            <p class="no-margin"> Filter <i id="morefilter__${tableId}" class="fa fa-chevron-down"></i></p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    `);
  $('[data-toggle="tooltip"]').tooltip({
    trigger: 'hover'
  })
  const element = $(`#filter-section__${tableId}`);
  let elementAppend = '';
  let isRangeDate = false;
  element.append(`<div class="row"></div>`);
  arrayForm.map((form, indexOption) => {
    var itemOption = form;
    var type =
      typeof itemOption.type != 'undefined'
        ? typeof itemOption.type.name != 'undefined'
          ? itemOption.type.name
          : itemOption.type
        : 'text';
    if (typeof form != 'object') {
      itemOption = {
        fieldName: form,
      };
    }
    var label = `${typeof itemOption.label != 'undefined'
      ? itemOption.label
      : ucWords(itemOption.fieldName.replace(/_/g, ' '))
      } :`;
    isRangeDate = false;
    elementAppend = `
            <div class="col-sm-3" id="${itemOption.fieldName}--filter">
                <div class="form-group">
                    <label for="${itemOption.fieldName}">${label}</label>`;
    switch (type) {
      case 'text':
        elementAppend += `<input id="${tableId}-${itemOption.fieldName}--form" name="${itemOption.fieldName}" class="form-control input-xs">`;
        break;
      case 'rangeDate':
        isRangeDate = true;
        var optionElement =
          typeof itemOption.type.payload != 'undefined'
            ? itemOption.type.payload
            : [];
        let startDateValue = moment().locale('en').format('DD-MMM-YYYY');
        endDateValue = moment().locale('en').format('DD-MMM-YYYY');
        if (
          typeof optionElement.startDate != 'undefined' &&
          optionElement.startDate != ''
        ) {
          startDateValue = optionElement.startDate;
        }
        if (
          typeof optionElement.endDate != 'undefined' &&
          optionElement.endDate != ''
        ) {
          endDateValue = optionElement.endDate;
        }
        targetValue = startDateValue + ' - ' + endDateValue;

        if (
          typeof optionElement.allDate != 'undefined' &&
          optionElement.allDate
        ) {
          startDateValue = '';
          endDateValue = '';
          targetValue = '';
        }

        elementAppend += `<div class='input-group'>
                    <input id="${itemOption.fieldName}-startDate" value='${startDateValue}' type='text' class='form-control input-xs startDate' />
                    <span class='input-group-addon'>-</span>
                    <input id="${itemOption.fieldName}-endDate" value='${endDateValue}' type='text' class='form-control input-xs endDate' />
                    <input type='text' style='display:none' id="${tableId}-${itemOption.fieldName}--form" name="${itemOption.fieldName}" class='targetDate' readonly='true' value='${targetValue}'>
                </div>`;
        break;
      case 'select':
        var optionElement = `<option value="">- Semua -</option>`;
        var payloadOption =
          typeof itemOption.type.payload != 'undefined'
            ? itemOption.type.payload
            : [];
        payloadOption.map((payloadOption) => {
          optionElement += `<option value="${payloadOption.id}">${payloadOption.text}</option>`;
        });
        elementAppend += `
                    <select id="${tableId}-${itemOption.fieldName}--form" name="${itemOption.fieldName}" class="form-control input-xs">
                        ${optionElement}
                    </select>
                `;
        break;
      case 'selectMultiple':
        var optionElement = `<option value="">- Semua -</option>`;
        var payloadOption =
          typeof itemOption.type.payload != 'undefined'
            ? itemOption.type.payload
            : [];
        payloadOption.map((payloadOption) => {
          optionElement += `<option value="${payloadOption.id}">${payloadOption.text}</option>`;
        });
        elementAppend += `
                    <select id="${tableId}-${itemOption.fieldName}--form" name="${itemOption.fieldName}[]" class="form-control input-xs" multiple="multiple">
                        ${optionElement}
                    </select>
                `;
        break;
      case 'dropdownScroll':
        elementAppend += `<select id="${tableId}-${itemOption.fieldName}--form" name="${itemOption.fieldName}" class="form-control input-xs"></select>`;
        break;
      default:
        break;
    }
    elementAppend += `
            </div>
        </div>`;
    element.find('.row').last().append(elementAppend);
    if (
      (indexOption + 1) % 4 == 0 &&
      arrayForm.length > 4 &&
      indexOption + 1 <= arrayForm.length
    ) {
      element.append(`<div class="row row__hidden"></div>`);
    }
    if (isRangeDate) {
      dateRangeHelper(
        `#${itemOption.fieldName}-startDate`,
        `#${itemOption.fieldName}-endDate`,
        `#${tableId}-${itemOption.fieldName}--form`,
        true,
        false,
        typeof optionElement.isInModal != 'undefined' && optionElement.isInModal ? optionElement.isInModal : false,
      );
    }
    if (type == 'dropdownScroll') {
      // bind infinity scroll
      element.find(`[name="${itemOption.fieldName}"]`).select2InfinityScroll({
        url: itemOption.type.url,
        callbackData: (param) => {
          return {
            ...param,
            ...(typeof itemOption.type.additionalPayload != 'undefined'
              ? itemOption.type.additionalPayload
              : {}),
          };
        },
      });
    }
    if (type == 'selectMultiple') {
      element
        .find(`[name="${itemOption.fieldName}[]"]`)
        .on('change', function () {
          if ($(this).children(':selected').val() === '') {
            $(this).children(':selected').prop('selected', false);
          }
        });
    }
  });
  element.wrap(`<form id="form-filter__${tableId}" action="#"></form>`);
  element.find('select').not('.select2-hidden-accessible').select2();
  element
    .find('selectMultiple')
    .not('.select2-hidden-accessible')
    .select2({ width: 'element' });
  $(`#collapse__${tableId}`).bind('click', ({ currentTarget }) => {
    element.find('.row__hidden').slideToggle(300);
    $(currentTarget).find('i.fa').toggleClass('fa-chevron-down');
    $(currentTarget).find('i.fa').toggleClass('fa-chevron-up');
  });
  if (typeof filterRendered != 'undefined') {
    filterRendered(element);
  }
  return $(`#form-filter__${tableId}`);
};

$(document).on('click', '.new-window-btn', ({ currentTarget }) => {
  const url = $(currentTarget).data('url');
  if (typeof url != 'undefined') {
    window.open(url, '__blank', 'heigh=100%&width=100%');
  }
});
$(document).on('show.bs.modal', '.modal', function () {
  let baseZIndex = 1030;
  
  $('.modal:visible').each(function(){
      if ($(this).css('z-index') > baseZIndex) {
          baseZIndex = $(this).css('z-index');
      }
  }); 
  
  let zIndex = parseInt(baseZIndex) + 10;
  $(this).css('z-index', zIndex);
  setTimeout(function () {
    $('.modal-backdrop')
      .not('.modal-stack')
      .css('z-index', zIndex - 1)
      .addClass('modal-stack');
  }, 0);
});

$(document).on('click', 'button[data-dismiss-confirmation="modal"]', function(e){
    let userModalConfirm = localStorage.getItem('user-modal-confirm');
    let _thisModal = $(this).parents('.modal.fade');
    let _thisId = window.location.pathname+'#'+_thisModal.attr('id');
    let _clickedByUser = (e.isTrigger == 'undefined' || e.isTrigger == false || e.isTrigger == undefined) ? true : false;
    let confirmStatus;
    let dialogOption = {
        header : 'Perhatian !',
        message : 'Apakah anda yakin menutup form ini ?',
        options : {
            confirmation: true,
            buttons: {
                Yes: 'button-yes',
                No: 'button-no',
            },
            warningConfirmation: false,
        }
    };

    let prevData = userModalConfirm ? JSON.parse(userModalConfirm) : {};

    if((!prevData[_thisId] == true) && dialogOption.options.confirmation == true && _clickedByUser){
        $.showQuestionDialog(dialogOption.header, dialogOption.message, dialogOption.options, function (reaction) {
            hideQuestionDialog();
            $('.warning-confirm-message').remove();
            if (reaction.confirmation == 'Yes') {
                _thisModal.modal('hide');
            }
            prevData[_thisId] = reaction.isDisabled;
            localStorage.setItem('user-modal-confirm', JSON.stringify(prevData));
        });
    }else{
        _thisModal.modal('hide');
    }
});

const thousandFormat = (x, withPrefix = true) => {
  return (
    (withPrefix ? 'Rp. ' : '') +
    parseFloat(x)
      .toString()
      .replace(/\B([?<!\.\d]*)(?=(\d{3})+(?!\d))/g, ',')
      .replace(/\./g, '|')
      .replace(/\,/g, '.')
      .replace(/\|/g, ',')
  );
};

$(document).on('click', '.nav-tabs .nav-item', ({ currentTarget, option }) => {
  const parentNav = $(currentTarget).parents('.nav-tabs');
  parentNav.find('.nav-item').removeClass('active');
  $(currentTarget).addClass('active');
});


var dateRangeHelper2 = function(startClass, endClass, targetClass, limit = true){
    var current_datetime = new Date()
    var formatted_date = current_datetime.getDate() + "-" + current_datetime.getMonth() + "-" + current_datetime.getFullYear();
    var start = $(startClass);
    var end = $(endClass);
    var target = $(targetClass);
    // Unbind old event
    $(document).off('change', startClass);
    $(document).off('change', endClass);
    $(document).off('keydown', startClass);
    $(document).off('keydown', endClass);
    // Bind event
    $(document).on(`change`, start ,function(e) {
        const localStart = $(startClass);
        const localEnd = $(endClass);
        const localTarget = $(targetClass);
        if (!localStart.val()) {
            localStart.val(localEnd.val());
        }
        var startDate = new Date(localStart.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
        var endDate = new Date(localEnd.attr('value').replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
        if (startDate > endDate) {
            docoNotification("warning", "Perhatian!", "Tanggal Mulai Harus Lebih Kecil Dari Tanggal Akhir!");
            localStart.val(localEnd.val());
        }
        dateValue = localStart.val()+' - '+localEnd.val();
        const isEmpty = !localStart.val() || !localEnd.val();
        if(isEmpty) {
            localTarget.attr('value', '');
            return false;
        }
        localTarget.attr('value', dateValue);
    });

    $(document).on(`change`, end, function(e) {
        const localStart = $(startClass);
        const localEnd = $(endClass);
        const localTarget = $(targetClass);
        if (!localEnd.val()) {
            localEnd.val(localStart.val());
        }
        var startDate = new Date(localStart.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
        var endDate = new Date(localEnd.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
        if (startDate > endDate) {
            docoNotification("warning", "Perhatian!", "Tanggal Akhir Harus Lebih Besar Dari Tanggal Mulai!");
            localEnd.val(localStart.val());
        }
        dateValue = localStart.val()+' - '+localEnd.val();
        const isEmpty = !localStart.val() || !localEnd.val();
        if(isEmpty) {
            localTarget.attr('value', '');
            return false;
        }
        localTarget.attr('value', dateValue);
    });

    $(document).on(`keydown`, start, function(e) {
        const localStart = $(startClass);
        const localEnd = $(endClass);
        const localTarget = $(targetClass);
        if (e.which == 13) {
            if (!localEnd.val()) {
                localEnd.val(localStart.val());
            }
            var startDate = new Date(localStart.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
            var endDate = new Date(localEnd.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
            if (startDate > endDate) {
                docoNotification("warning", "Perhatian!", "Tanggal Akhir Harus Lebih Besar Dari Tanggal Mulai!");
                localEnd.val(localStart.val());
            }
            dateValue = localStart.val()+' - '+localEnd.val();
            const isEmpty = !localStart.val() || !localEnd.val();
            if(isEmpty) {
                localTarget.attr('value', '');
                return false;
            }
            localTarget.attr('value', dateValue);
        }
    });

    $(document).on(`keydown`, end ,function(e) {
        const localStart = $(startClass);
        const localEnd = $(endClass);
        const localTarget = $(targetClass);
        if (e.which == 13) {
            if (!localEnd.val()) {
                localEnd.val(localStart.val());
            }
            var startDate = new Date(localStart.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
            var endDate = new Date(localEnd.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
            if (startDate > endDate) {
                docoNotification("warning", "Perhatian!", "Tanggal Akhir Harus Lebih Besar Dari Tanggal Mulai!");
                localEnd.val(localStart.val());
            }
            dateValue = localStart.val()+' - '+localEnd.val();
            const isEmpty = !localStart.val() || !localEnd.val();
            if(isEmpty) {
                localTarget.attr('value', '');
                return false;
            }
            localTarget.attr('value', dateValue);
        }
    });
}
function filterByKey({
    lists, key, value
}){
    let tempArray = [];
    for (var i=0; i < lists.length; i++) {
        if (lists[i][key] == value) {
            tempArray.push(lists[i]);
        }
    }
    return tempArray;
}

function getCurrentTimeInt() {
    const d = new Date();
    const currentTime = d.getTime();
    return currentTime;
}


function dataURItoBlobPdf(dataURI) {
    const byteString = window.atob(dataURI);
    const arrayBuffer = new ArrayBuffer(byteString.length);
    const int8Array = new Uint8Array(arrayBuffer);
    for (let i = 0; i < byteString.length; i++) {
        int8Array[i] = byteString.charCodeAt(i);
    }
    const blob = new Blob([int8Array], { type: "application/pdf" });
    return blob;
}

/* Update Session Storage per value/attr */
/* ------------EXAMPLE -------------------------------*/
/* @params1   string    'suggestsoaprjform'*/
/* @params2   object    {soapRjForm["cppt"] : 'perubahan terbaru'}*/

function updateSessionStorage(sessionStorageName, objectValue, options = {}){
    let prevData = JSON.parse(sessionStorage.getItem(sessionStorageName));

    if(prevData != null){
        if(options.key == 'undefined'){
            return;
        }

        // jika session storage dalam bentuk array of obj
        if(options.isArraySession != 'undefined' && options.isArraySession){
            objectValue.forEach(function(val, key){
                let index = prevData.findIndex(item => item[options.key] == val[options.key]);
                if(index >= 0){
                    prevData[index] = val;
                }
            })
        }else{
            objectValue.keys(objectValue).forEach(function(val, key){
              prevData[val] = objectValue[val];
            })
        }
        sessionStorage.setItem(sessionStorageName, JSON.stringify(prevData));
    }

}

function calculateDate(start_date, end_date) {
    var start_date = new Date(start_date);
    var end_date = new Date(end_date);

    let difference = end_date.getTime() - start_date.getTime();
    let count_days = Math.ceil(difference / (1000 * 3600 * 24));

    return count_days;
}


/*
* check array object identik
* value = array1, other array 2 / array komparasi
*/
function isArrayEqual(value, other) {
  // Get the value type
  	var type = Object.prototype.toString.call(value);

  	// If the two objects are not the same type, return false
  	if (type !== Object.prototype.toString.call(other)) return false;

  	// If items are not an object or array, return false
  	if (['[object Array]', '[object Object]'].indexOf(type) < 0) return false;

  	// Compare the length of the length of the two items
  	var valueLen = type === '[object Array]' ? value.length : Object.keys(value).length;
  	var otherLen = type === '[object Array]' ? other.length : Object.keys(other).length;
  	if (valueLen !== otherLen) return false;

  	// Compare two items
  	var compare = function (item1, item2) {

  		// Get the object type
  		var itemType = Object.prototype.toString.call(item1);

  		// If an object or array, compare recursively
  		if (['[object Array]', '[object Object]'].indexOf(itemType) >= 0) {
  			if (!isArrayEqual(item1, item2)) return false;
  		}

  		// Otherwise, do a simple comparison
  		else {
  			// If the two items are not the same type, return false
  			if (itemType !== Object.prototype.toString.call(item2)) return false;

  			// Else if it's a function, convert to a string and compare
  			// Otherwise, just compare
  			if (itemType === '[object Function]') {
  				if (item1.toString() !== item2.toString()) return false;
  			} else {
  				if (item1 !== item2) return false;
  			}

  		}
  	};

  	// Compare properties
  	if (type === '[object Array]') {
  		for (var i = 0; i < valueLen; i++) {
  			if (compare(value[i], other[i]) === false) return false;
  		}
  	} else {
  		for (var key in value) {
  			if (value.hasOwnProperty(key)) {
  				if (compare(value[key], other[key]) === false) return false;
  			}
  		}
  	}

  	// If nothing failed, return true
  	return true;
};


/**
 *
 * @author: yafi.maulana@sirs.co.id
 * Fungsi Global Signa Only Select2
 * trigger search field signa only jiga parent selectnya memiliki class docoSelect2SignaFormatOnly
 * ex : <select class="select2 docoSelect2SignaFormatOnly"></select>
 *
 */
 
$(document).on('keydown', '.select2-search__field', function(e) {
    let parent = $(".select2-container--open").siblings('select.docoSelect2SignaFormatOnly')
    if (parent.length > 0) {
        if (
            // Allow: backspace, delete, tab, escape, enter and .
            $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
            // Allow: Ctrl/cmd+A
            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: Ctrl/cmd+C
            (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: Ctrl/cmd+X
            (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39) ||
            // Allow: alphabet
            (e.keyCode >= 65 && e.keyCode <= 90) ||
            // Allow: numeric only
            (e.keyCode >= 48 && e.keyCode <= 57 && e.shiftKey == false) ||
            // Allow: %, *, (, ), @, spasi, komma, titik
            ([53, 56, 48, 57, 50].includes(e.keyCode) && e.shiftKey == true) ||
            // Allow: - = _ + spasi
            ([187, 189, 32].includes(e.keyCode)) ||
            // Allow: , . / [ ]
            ([219, 221, 191, 188, 190].includes(e.keyCode) && e.shiftKey == false)
        ) {
            // let it happen, don't do anything
            return;
        } else {
            e.preventDefault();
        }
    }
    
});

// mencegah karakter lain selain format signa
$(document).on('input', '.select2-search__field', function (e) {
    let regex = /[^a-z0-9 ,.%*()=&@_/\[\]+-]/giu;
    let parent = $(".select2-container--open").siblings('select.docoSelect2SignaFormatOnly') 
    if (parent.length > 0 && !e.isTrigger) {
        let text = $(this).val().replace(regex, '')
        $(this).val(text);
        $(this).trigger('input');
    }
});

/*
  End Of Fungsi Global Signa Format Only
*/
