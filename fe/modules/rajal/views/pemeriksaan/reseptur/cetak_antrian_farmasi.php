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
        height: 340px;
        margin-top: 150px;
    }

    .top-date {
        font-size: 15px;
        margin-top: 5px;
        margin-left: 10px;
    }

    .text-no-antrian {
        font-size: 65px;
    }
    
    .text-info-antrian {
        font-size: 16px;
    }

    .big-icon {
        font-size: 1cm;
    }

    .info-rs {
        margin-top: 35px;
    }

    .home-icon {
        margin-top: 390px;
    }

    .button-home {
        border-radius: 50px;
        background-color: #29979b;
        padding: 10px;
        width: 60%;
    }

    .button-home:hover {
        opacity: 0.9;
    }


    @media print {
        .cetak {
            width: 300px;
            margin-top: 0;
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
        <a href="/rajal/pemeriksaan/index" class="text-default">
            <div class="button-home">
                <i class="fa fa-home big-icon"></i>
            </div>
        </a>
    </div>
</body>
