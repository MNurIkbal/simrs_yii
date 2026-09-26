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

    .antrian-header {
        display: flex;
        margin: 0px 70px 0px 70px;
        font-size: 18px;
        font-weight: 600;
        color: #000000;
    }

    .nama-rs {
        margin-top: 15px;
    }

    .detail-header {
        display: flex;
        justify-content: center;
        text-align: center;
        margin: 0px 20px 0px 20px;
    }

    .info-header {
        display: flex;
        justify-content: space-between;
        margin: 10px 20px 20px 20px;
        border-top: 1px solid;
        border-bottom: 1px solid;
    }

    .no-antrian {
        font-size: 28px;
        color: #000000;
    }

    .label-bottom {
        border-top: 1px solid;
        margin: 20px;
    }

    .label-bottom-text {
        margin-top: 10px;
    }

    .label-bottom-text p {
        margin: 0;
        font-size: 12px;
    }

    @media print {
        .cetak {
            width: 300px;
            margin-top: 0;
            border: none;
        }

        .home-icon,
        embed {
            display: none;
        }

        body {
            background-color: none;
        }

    }
</style>

<body>
    <div class="row">
        <div class="col-md-12 " style="display: flex; justify-content: center;">
            <div style="width: 100%; height: 100%; max-width: 350px; max-height: 500px">
                <iframe id="pdf-frame" src="print-antrian-poli?pendaftaran_id=<?= $pendaftaran_id ?>" style="width:100%; height:100%; min-height: 700px" frameborder="0"></iframe>
            </div>
            <div style="display: flex; align-items: center;">
                <div>
                    <div style="margin: 10px">
                        <button class="btn btn-primary" style="border-radius: 10px;" id="print">
                            <i class="fa fa-print big-icon"></i>
                        </button>
                    </div>
                    <div style="margin: 10px">
                        <a type="button" onclick="goBack()" class="text-default">
                            <div class="btn btn-success"  style="border-radius: 10px;">
                                <i class="fa fa-home big-icon"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    const frame = document.getElementById('pdf-frame');
    const btn = document.getElementById('print');
    const urlBack = "<?= $url_kembali ?>";

    frame.onload = function() {
        frame.contentWindow.focus();
        frame.contentWindow.print();
    };

    function goBack() {
        window.location.replace(urlBack);
    }

    btn.addEventListener('click', () => {
        frame.contentWindow.focus();
        frame.contentWindow.print();
    });
</script>