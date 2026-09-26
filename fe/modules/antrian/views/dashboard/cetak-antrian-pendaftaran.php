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

    .antrian-header{
        display: flex;
        margin: 0px 70px 0px 70px;
        font-size: 18px;
        font-weight: 600;
        color: #000000;
    }

    .nama-rs{
        margin-top: 15px;
    }

    .detail-header{
        display: flex;
        justify-content: center;
        text-align: center;
        margin: 0px 20px 0px 20px;
    }

    .info-header{
        display: flex;
        justify-content: space-between;
        margin: 10px 20px 20px 20px;
        border-top: 1px solid;
        border-bottom: 1px solid;
    }

    .no-antrian{
        font-size: 28px;
        color: #000000;
    }

    .label-bottom{
        border-top: 1px solid;
        margin: 20px;
    }

    .label-bottom-text{
        margin-top: 10px;
    }

    .label-bottom-text p{
        margin:0;
        font-size: 12px;
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

<?php for ($i = 0; $i < $jumlah_cetak; $i++) { ?>
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
<?php } ?>
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