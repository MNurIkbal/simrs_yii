$(document).ready(function () {
    async function LoadConnection() {
        let config = await $.getJSON("./../../../json/setup.json")
        if (config.origin == "true") {
            var socket = io.connect(window.location.origin);
        } else {
            var socket = io.connect(config.ip+':'+config.port);
        }
        const channel = `informasi-reservasi:${userLogin}`
        socket.on(channel, (message) => {
            table.draw()
        });        
    }
    
    LoadConnection()
});