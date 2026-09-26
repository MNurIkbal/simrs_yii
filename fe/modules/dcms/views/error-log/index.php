<?php
$this->title = $title;
?>
<div class="module-section" id="module-section">
    <div class="row row-flex">
        <div class="col-sm-6">
            <div class="col-md-12">
                <h2 class="text-center">Frontend</h2>
            </div>

            <div class="col-md-1 list-module" data-key="frontend">
                <div class="panel panel-body text-center isi-module" tabindex="0">
                    <img src="<?= $listProject['frontend']['icon'] ?>">
                    <h6 class="no-margin text-semibold"><?= $listProject['frontend']['name'] ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="col-md-12">
                <h2 class="text-center">Backend</h2>
            </div>
            <?php
                foreach ($listProject['backend'] as $keyProject => $project) :
            ?>
                <div class="col-md-1 list-module" data-key="<?= $keyProject ?>" data-name="<?= $project['name'] ?>">
                    <div class="panel panel-body text-center isi-module" tabindex="0">
                        <img src="<?= $project['icon'] ?>">
                        <h6 class="no-margin text-semibold"><?= $project['name'] ?></h6>
                    </div>
                </div>
            <?php
                endforeach;
            ?>
        </div>
    </div>
</div>
<div class="row" id="detail-section" style="display: none;">
    <div class="col-sm-12">
        <div class="panel panel-white">
            <div class="panel-heading">
              <!-- breadcrumbs replace with this -->
                <div class="row">
                    <h3 class="panel-title text-center" id="header-page" style="font-weight: bold;"></h3>
                </div>
              <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <button type="button" id="back-btn" class="btn btn-info btn-labeled btn-xs"><b><i class="fa fa-arrow-left"></i></b>Kembali</button>
                <button type="button" id="refresh-btn" class="btn btn-info btn-labeled btn-xs"><b><i class="fa fa-undo"></i></b>Muat Ulang</button>
            </div>
            <div class="panel-body" style="min-height: 400px">
                <table class="table table-hover table-gradient table-row-clickable" id="table-detail">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>URL endpoint</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJsFile(
    '/js/app/dcms/error-log.js',
    [
        'depends' => [
            'app\assets\AppAsset',
        ],
    ]
);
?>