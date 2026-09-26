$(document).ready(function() {
	Object.defineProperty(Array.prototype, 'chunk_inefficient', {
	  value: function(chunkSize, offset = chunkSize) {
	    var array = this;
			let stopMap = 0;
	    return [].concat.apply([],
	      array.map(function(elem, i) {
					if ((i + chunkSize) >= array.length && i % offset == 0) {
							stopMap++;
					}
	        return i % offset != 0 || stopMap >= 2 ? [] : [array.slice(i, i + chunkSize)];
	      })
	    );
	  }
	});

  var jumlahSplit = 10;
	var jumlahOffset = 5;

  var jumlahRacikan = _dataRacikan.length;
  var jumlahNonRacikan = _dataNonRacikan.length;

  var _dataChunkRacikan = _dataRacikan.chunk_inefficient(jumlahSplit, jumlahOffset);
  var _countDataChunkRacikan = _dataChunkRacikan.length;
  var _urutanRacikan = 1;

  var _dataChunkNonRacikan = _dataNonRacikan.chunk_inefficient(jumlahSplit, jumlahOffset);
  var _countDataChunkNonRacikan = _dataChunkNonRacikan.length;
  var _urutanNonRacikan = 1;

  var renderDataRacikan = inject(_dataChunkRacikan[0], 0);
  $(".data-racikan").html(renderDataRacikan);

  var renderDataNonRacikan = inject(_dataChunkNonRacikan[0], 0);
  $(".data-non-racikan").html(renderDataNonRacikan);

  function inject(data, urutan) {
    let html = "";
    let jumlahData = (data == undefined) ? 0 : data.length;
    let kurang = jumlahSplit - jumlahData;
    let latest = 0;

    if (data != undefined) {
	    data.forEach(function(v,i){
	      let no = i+1;
	      let number = (urutan*jumlahOffset) + no;
	      html += "<tr>"+
	                  "<td>"+
	                      "<div class='nomor-antrian'>"+number+"</div>"+
	                  "</td>"+
	                  "<td>"+
	                      "<div class='nomor-antrian'>"+v['no_antrian']+"</div>"+
	                  "</td>"+
	                  "<td>"+
	                      "<div class='nomor-antrian'>"+v['status_reseptur']+"</div>"+
	                  "</td>"+
	              "</tr>";
	      latest = no+1;
	    });
    } else {
    	latest = 1;
    }

    latest = latest + (urutan*jumlahOffset);
    for (var i = kurang; i > 0; i--) {
      html += "<tr>"+
                "<td>"+
                    "<div class='nomor-antrian'>"+latest+"</div>"+
                "</td>"+
                "<td>"+
                    "<div class='nomor-antrian'>-</div>"+
                "</td>"+
                "<td>"+
                    "<div class='nomor-antrian'>-</div>"+
                "</td>"+
            "</tr>";
      latest++;
    }

    return html;
  }

  // setInterval(function() {
  // 	let cc = 1;
  // 	refreshData();
  // 	console.log(cc);
  // 	cc++;
  // }, 5000);

  function refreshData() {
  	$.ajax({
        url: '/antrian/dashboard/get-data-antrian-farmasi?id='+_ruangan_response,
        type: 'get',
        success: function(res) {
        	_dataRacikan = res.response['list-antrian']['racikan'];
        	_dataNonRacikan = res.response['list-antrian']['non_racikan'];

			jumlahRacikan = _dataRacikan.length;
			jumlahNonRacikan = _dataNonRacikan.length;
			
			_dataChunkRacikan = _dataRacikan.chunk_inefficient(jumlahSplit, jumlahOffset);
			_countDataChunkRacikan = _dataChunkRacikan.length;
			// _urutanRacikan = 1;

			_dataChunkNonRacikan = _dataNonRacikan.chunk_inefficient(jumlahSplit, jumlahOffset);
			_countDataChunkNonRacikan = _dataChunkNonRacikan.length;
			// _urutanNonRacikan = 1;

			// renderDataRacikan = inject(_dataChunkRacikan[0], 0);
			// $(".data-racikan").html(renderDataRacikan);
			// 
			// renderDataNonRacikan = inject(_dataChunkNonRacikan[0], 0);
			// $(".data-non-racikan").html(renderDataNonRacikan);
        }
    })
  }

  setInterval(function() {
  	if(_urutanRacikan >= _countDataChunkRacikan){
  		_urutanRacikan = 0;
  	}

  	if(_urutanNonRacikan >= _countDataChunkNonRacikan){
  		_urutanNonRacikan = 0;
  	}
  	
    refreshData();

  	// proses
  	renderDataRacikan = inject(_dataChunkRacikan[_urutanRacikan], _urutanRacikan)
  	$(".data-racikan").html(renderDataRacikan);

  	renderDataNonRacikan = inject(_dataChunkNonRacikan[_urutanNonRacikan],_urutanNonRacikan)
  	$(".data-non-racikan").html(renderDataNonRacikan);


  	_urutanRacikan += 1;
  	_urutanNonRacikan += 1;
  }, 5000);


  // console.log(_dataChunkRacikan);


});
