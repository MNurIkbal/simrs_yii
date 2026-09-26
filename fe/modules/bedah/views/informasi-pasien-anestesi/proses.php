<?php
    use yii\web\View;
    use app\components\DHtml;
    use yii\widgets\Breadcrumbs;
    use app\components\DocoHelpers;

    $this->title = "Proses Pasien Operasi";
    $this->params['breadcrumbs'][] = ['label' => 'Anestesi', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .nav-tab-worklist .nav-link{
        padding: 10px;
        padding-right: 20px !important;
    }
    .nav-tab-worklist .nav-link:hover{
        border: none;
    }
    .nav-tab-worklist .nav-link.active{
        color: #000000;
        background-color: white;
        border-right-color: transparent;
        border-left-color: transparent;
        border-top-color: transparent;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar">
                <?= DocoHelpers::generateToolbar([
                    'back' => [
                        'attributes' => [
                            'href' => '/bedah/informasi-pasien-anestesi'
                        ]
                    ]
                ]) ?>
            </div>
            <div class="panel-body">
                <nav>
                    <div class="nav nav-tabs nav-tab-worklist">
                        <a class="nav-item nav-tab-type nav-link active" data-type="preanesthic" id="tabs-preanestic" data-toggle="tab" href="#view-preanestic" role="tab" aria-controls="nav-home" aria-selected="true"><?=Yii::t('fe', 'Preanestic')?></a>
                        <a class="nav-item nav-tab-type nav-link" data-type="post" id="tabs-intraoperative" data-toggle="tab" href="#view-intraoperative" role="tab" aria-controls="nav-home" aria-selected="true"><?=Yii::t('fe', 'Intraoperative Anesthetic')?></a>
                        <a class="nav-item nav-tab-type nav-link" data-type="post" id="tabs-anesthetic" data-toggle="tab" href="#view-anesthetic" role="tab" aria-controls="nav-home" aria-selected="true"><?=Yii::t('fe', 'Anesthetic')?></a>
                        <a class="nav-item nav-tab-type nav-link" data-type="post" id="tabs-before-condition" data-toggle="tab" href="#view-before-condition" role="tab" aria-controls="nav-home" aria-selected="true"><?=Yii::t('fe', 'Patient Condition Before Leaving Operating Theater')?></a>
                        <a class="nav-item nav-tab-type nav-link" data-type="post" id="tabs-postoperative" data-toggle="tab" href="#view-postoperative" role="tab" aria-controls="nav-home" aria-selected="true"><?=Yii::t('fe', 'Post Operative')?></a>
                    </div>
                </nav>

                <div class="tab-content">
                    <div class="tab-pane active" id="view-preanestic">
                        <div id="content-preanestic"></div>
                    </div>
                    <div class="tab-pane" id="view-intraoperative">
                        <div id="content-intraoperative"></div>
                    </div>
                    <div class="tab-pane active" id="view-anesthetic">
                        <div id="content-anesthetic"></div>
                    </div>
                    <div class="tab-pane active" id="view-before-condition">
                        <div id="content-before-condition"></div>
                    </div>
                    <div class="tab-pane" id="view-postoperative">
                        <div id="content-postoperative"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-bedah" style="overflow-y:auto;" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width: 40%; margin-left: 20%;"></div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/proses.js'), View::POS_END);
 ?>


<?php
$this->registerJs('
    var IntraoperativeLoaded = false;

    $("#tabs-postoperative").on("click", function(e){
        $("#content-postoperative").docoLoad({
            url: "/bedah/informasi-pasien-anestesi/post-operative?id=' . $id . '",
            dataType: "html",
            success: function(data){
                
            },
        })
    });
    $("#tabs-intraoperative").on("click", function(e){
        if (IntraoperativeLoaded === false) {
            $("#content-intraoperative").docoLoad({
                url: "/bedah/informasi-pasien-anestesi/intra-operative?id=' . $id . '",
                dataType: "html",
                success: function(data){
                    IntraoperativeLoaded = true;
                },
            })
        }
    });
    $("#tabs-anesthetic").on("click", function(e){
        $("#content-anesthetic").docoLoad({
            url: "/bedah/informasi-pasien-anestesi/anestesi?pasienmasukpenunjang_id='.$id.'",
            dataType: "html",
            success: function(data){
                
            },
        })
    });
    $("#tabs-preanestic").on("click", function(e){
        $("#content-preanestic").docoLoad({
            url: "/bedah/informasi-pasien-anestesi/pre-anesthetic?id='.$id.'",
            dataType: "html",
            success: function(data){

            },
        })
    });
    loadFirstContent();
    function loadFirstContent(){
        $("#content-preanestic").docoLoad({
            url: "/bedah/informasi-pasien-anestesi/pre-anesthetic?id='.$id.'",
            dataType: "html",
            success: function(data){

            },
        })
    }
    $("#tabs-before-condition").on("click", function(e){
        $("#content-before-condition").docoLoad({
            url: "/bedah/informasi-pasien-anestesi/before-leaving?id='.$id.'",
            dataType: "html",
            success: function(data){
                
            },
        })
    })
', View::POS_END, 'b-index');
?>