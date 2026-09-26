<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::$app->docoVars->identity("nama_rumahsakit");
?>
<div class="col-sm-12 text-right" style="font-size: 20px;">
    <a class="fa fa-chevron-up mr-2 hd-up" onclick="hideHeader()" ></a>
    <a class="fa fa-chevron-down mr-2 hd-down" onclick="showHeader()" style="display:none"></a>
</div>
<style type="text/css">
    .list-antrian div:hover, .layar div:hover{
        cursor: auto !important;
        border-color: #58de9e; /*#ddd;*/
        box-shadow: 0px 0px 0px 0px #d5d5
    }
    
</style>

<div class="site-index">
    <div class="data-module" id="pengambilan">
        <!-- List styles -->
        <h2 class="content-group text-semibold">
            <?= strtoupper(Yii::t('fe','Pilih Kelas pelayanan'))?>
        </h2>

        <div class="row pd-20">
            <div class="col-sm-4 text-left">
                <a href="#antrianCaraousel" role="button" data-slide="prev" style="display: block;"> 
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    <?= strtoupper(Yii::t('fe','Daftar Sebelumnya'))?>
                </a>
            </div>
            <div class="col-sm-4">
                &nbsp;
            </div>
            <div class="col-sm-4 text-right">
                <a href="#antrianCaraousel" role="button" data-slide="next" style="display: block;"> 
                    <?= strtoupper(Yii::t('fe','Daftar Selanjutnya'))?>
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </a>
            </div>
        </div>

        <div class="row pd-20">
            <div id="antrianCaraousel" class="carousel slide" data-interval="false" style="display: block;">
                <div class="carousel-inner">
                    <?php foreach ($list_jenis_kelas as $key => $val): ?>
                        <div class="item <?= $key== 0 ? 'active' : '' ?>">
                            <center>
                                <div class="carousel-item ">
                                    <?php foreach ($val as $keyz => $value): ?>
                                        <div class="col-sm-4 list-antrian">
                                            <div class="panel text-center layar" style="padding: 40px 4px 40px 4px;">
                                                <div class="text-center" style="padding: 40px 4px 40px 4px;">
                                                    <a href="<?php echo $value['url']; ?>" class="btn btn-info btn-more">
                                                        <?php echo $value['icon']; ?>
                                                        <br>
                                                        <?= Yii::t('fe','Layar Dashboard')?>
                                                    </a>
                                                </div>
                                                <h6 class="no-margin text-semibold jenis-antrian">
                                                    <?= strtoupper($value['name_header']); ?>
                                                </h6>

                                                <a href="<?php echo $value['url']; ?>" class="btn btn-info btn-more">
                                                    <?php //echo strtoupper($value['name']); ?>
                                                    <?= Yii::t('fe','Pilih Kelas')?>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </center>
                        </div>
                    <?php endforeach ?>
               </div>
            </div>
        </div>
        
        <!-- /list styles -->
    </div>
</div>

<!--begin carousel-->

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        
       
    }, false);

    function showmore () {
        $("#pengambilan").css('display','none');
        $("#layar").css('display','block');
        $(".btn-layar").css('display','none');
        $(".btn-antrian").css('display','block');
    }

    function showless () {
        $("#pengambilan").css('display','block');
        $("#layar").css('display','none');
        $(".btn-layar").css('display','block');
        $(".btn-antrian").css('display','none');
    }
</script>

<?php 
    // $this->registerJs($this->render('../assets/js/index.js'));
?>