$.ajaxSetup({
  cache:false
});
$.getJSON("./../../json/setup.json", function (config) {
    console.log(config);
    var socket = io.connect('http://'+config.ip+':'+config.port);
    // var socket = io.connect('http://localhost:'+config.port);
    socket.on('display-antrian-' + config.name, function (data) {
        var return_data = JSON.parse(data);
        $.each(return_data.data, function(key,value){
            if (key == 'proses_antrian_farmasi') {
                console.log("return_data");
                generateDataFarmasi(value.data,value.ruangan);
            }
        });
    });
});