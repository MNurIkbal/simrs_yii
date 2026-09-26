<?php 
use yii\web\View;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
			<div class="panel-toolbar clearfix">
				<div class="panel-body">
					<pre id="<?= $id ?>" style="white-space:break-spaces;">
					</pre>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
$this->registerJs("
var _res = ".$results.";
var _id = '".$id."';
var _text = JSON.stringify(_res, undefined, 4);
$('#' + _id).text(_text);

   ", View::POS_END, 'js-pencarian')
?>