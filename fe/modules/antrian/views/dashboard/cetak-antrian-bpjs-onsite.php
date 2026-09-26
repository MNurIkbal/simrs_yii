<?php
    $this->context->layout = 'antrian';
?>
<style>
    body {
        padding: 10px;
        margin: 0px auto;
    }
    .cetak {
        border: 1px solid black;
        height: 500px;
        margin-top: 150px;     
    }

    .big-icon {
        font-size: 1cm;
    }

    .home-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .button-home {
        border-radius: 50%;
        background-color: #58de9e;
        padding: 12px;
        max-width: 70px;
        width: 20vw;
        text-align: center;
        transition: opacity 0.3s ease;
    }

    .button-home:hover {
        opacity: 0.9;
    }

    .no-antrian-poli{
        font-size: 32px;
    }

    .text-antrian{
        margin-top: 10px;
    }

    .text-antrian p{
        margin: 0px;
    }

    .label-bottom{
        margin-top: 10px;
    }

    .label-bottom p{
        margin: 0px;
        font-size: 12px;
    }

    .info-rs{
        margin-top: 20px;
    }

    .nama-rs{
        font-size: 20px;
    }

    .info-header{
        display: flex;
        justify-content: space-between;
        margin: 10px 20px 0px 20px;
    }

    @media print {
        .cetak {
            width: 300px;
            margin-top: 0;
            border:none;
        }
        .home-icon, embed {
            display:none;
        }
        body {
            background-color: none;
        }

    }
</style>
<body onload="window.print()">
    <div class="col-md-3 col-md-offset-4">
        <div class="cetak">
            <?=$data_header;?>
            <?=$data_body;?>
            <?=$data_footer;?>
        </div>
    </div>
    <div class="col-md-1 home-icon">
        <a type="button" onclick="goBack()" class="text-default">
            <div class="button-home">
                <i class="fa fa-home
 big-icon"></i>
            </div>
        </a>
    </div>
</body>
<script>
var urlBack = "<?=$url_kembali?>";

window.addEventListener('afterprint', function () {
    setTimeout(function() {
        window.location.href = urlBack;
    }, 5000);
});

function goBack() {
    window.location.replace(urlBack);
}
</script>