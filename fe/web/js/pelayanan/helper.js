let pelayananHelper = {
    transFormData : (serializeArray) => {
        return serializeArray.map((item) => {
                    if (item.name.match(/tgl|tanggal/)) {
                        item.value = item.value.split('/').join('-')
                    }
                    return item
                })
    }
}

$.fn.transFormData = function () {
  var _this = $(this);
  return pelayananHelper.transFormData(_this.serializeArray());
}