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
        background-color: #58de9e;
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
            <div class="top-date">
                <?= date('d/m/Y H:i:s');?>
            </div>
    
            <div class="no-antrian">
                <h1 class="text-center text-no-antrian"><?= $no_antrian ?></h1>
            </div>
    
            <div class="info-antrian">
                <p class="text-center text-info-antrian"><?= $info ?></p>
                <p class="text-center text-info-antrian"><?= $warning ?></p>
            </div>
    
            <div class="info-rs">
                <h1 class="text-center"><i class="fa fa-hospital-o big-icon"></i> <?= Yii::$app->docoVars->identity('nama_rumahsakit'); ?></h1>
            </div>
        </div>
    </div>
    <div class="col-md-1 home-icon">
        <a href="/antrian/dashboard/jenis-antrian?jenis_id=MTc2" class="text-default">
            <div class="button-home">
                <i class="fa fa-home big-icon"></i>
            </div>
        </a>
    </div>
</body>
