<?php 
namespace app\extensions\kasir;

use Yii;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\Traits\ControllerHelperTrait;

class LaporanJasaMedis extends \app\components\DocoBaseProcessExtension
{
	use ControllerHelperTrait;
	protected function processFlow($controller)
	{
		$path = '@app/extensions/kasir/views/laporan_jasa_medis';
		$endPoint = "lap-rekap-jasa-dokter/get-jasa-medis";
      return [
			'path' => $path,
			'endPoint' => $endPoint,
		];
	}
}