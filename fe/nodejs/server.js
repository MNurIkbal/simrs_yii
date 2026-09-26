var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var redis = require('redis');
var cron = require('node-cron');

var config = require('./../web/json/setup.json');
var queue = [];
var stat = 0;
server.listen(config.port_server);

io.on('connection', function (socket) {
    var redisClient = redis.createClient(config.port_redis, config.host_redis);
    redisClient.subscribe('display-antrian-' + config.name);
    redisClient.subscribe('display-notif-' + config.name);
    redisClient.subscribe('panggil-antrian-' + config.name);
    redisClient.subscribe('order-farmasi-' + config.name);
    redisClient.subscribe('order-ambulan-' + config.name);
    redisClient.subscribe('update-laboratorium-' + config.name);
    redisClient.subscribe('update-radiologi-' + config.name);
    redisClient.subscribe('new-notification-' + config.name);
    redisClient.subscribe('update-notification-' + config.name);
    redisClient.subscribe('export-excel-' + config.name);
    redisClient.psubscribe('sensus-harian-rajal:*');
    redisClient.psubscribe('export-excel:*');
    redisClient.psubscribe('export-pdf:*');
    redisClient.psubscribe('sync-eklaim:*');
    redisClient.psubscribe('informasi-reservasi:*');
    redisClient.subscribe('display-dashboard-kamar-' + config.name);
    redisClient.psubscribe('corporate:' + config.name + ':*');
    redisClient.psubscribe('create-invoice-corporate:' + config.name + ':*');
    redisClient.psubscribe('cetak-invoice-corporate:' + config.name + ':*');
    redisClient.psubscribe('invoice:*');
    redisClient.subscribe('ketersediaan-bed-' + config.name);
    redisClient.subscribe('permintaan-makan-notification-' + config.name);
    redisClient.subscribe('pembantaran-notification-' + config.name);
    redisClient.psubscribe('get-laporan:*');
    redisClient.subscribe('laporan-morbiditas-ranap');
    redisClient.psubscribe('syncDataSatusehat:*');
    redisClient.psubscribe('kunjungan-rawat-inap:*');
    redisClient.psubscribe('laporan-lead-time-resep-datatable:*');
    redisClient.psubscribe('integrasi-kasir:*');
    redisClient.psubscribe('bulk-register:*');
    redisClient.on("message", function (channel, message) {
        var dataMessage = JSON.parse(message);
        // if (Object.keys(dataMessage.data) == 'panggil_antrian') {
        //     queue.push(message);

        //     if (queue.length <= 1) {
        //         socket.emit(channel, message);
        //         stat = 1;
        //         removeQueue();
        //     }
        // } else {
        socket.emit(channel, message)
    });

    redisClient.on("pmessage", function (pattern, channel, message) {
        var dataMessage = JSON.parse(message);
        socket.emit(channel, message);
    })


    socket.on('disconnect', function () {
        redisClient.quit();
    });

});
