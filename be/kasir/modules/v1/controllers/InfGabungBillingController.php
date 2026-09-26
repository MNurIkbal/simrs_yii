<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use app\modules\v1\models\InfoGabungTagihanView;
use app\modules\v1\models\GabungPelayananDetail;
use app\modules\v1\models\InfoGabungTagihanDetailView;
use app\modules\v1\models\InfoGabungTransaksiView;
use app\modules\v1\models\FindPendaftaranGabungView;
use Doco\models\kasir\InvoiceSudahBayarDetailView;
use Doco\models\kasir\Pembayaran;

class InfGabungBillingController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\PembayaranPelayanan';
	public function verbs()
	{
		$verbs = parent::verbs();
		return $verbs;
	}

	public function actions()
	{
		$actions = parent::actions();
		unset($actions['index']);
		unset($actions['create']);
		unset($actions['update']);
		unset($actions['delete']);
		return $actions;
	}

	public function actionIndex()
	{
		$query = $this->getData();
		return new ActiveDataProvider([
			'query' => $query,
		]);
	}

	private function getData()
	{
		$model = new InfoGabungTagihanView;
		$query = $model::find(true);
		$start = date('Y-m-d 00:00:00');
		$end = date('Y-m-d 23:59:59');
		if(isset($_GET['advanced-filter'])) {
			$advancedFilter = $_GET['advanced-filter'];
			if(isset($advancedFilter['tgl_gabung'])) {
				$dates = $advancedFilter['tgl_gabung'];
				$dateRange = DocoHelpers::parsingRangeDate($dates);
				$start = ArrayHelper::getValue($dateRange, 'startDate');
				$end = ArrayHelper::getValue($dateRange, 'endDate');
				unset($_GET['advanced-filter']['tgl_gabung']);
			}
			if(isset($advancedFilter['nama_pasien'])) {
				$term = $advancedFilter['nama_pasien'];
				$query->andWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($term)])
					->orWhere(['ILIKE', 'no_rekam_medik', $term]);
				unset($_GET['advanced-filter']['nama_pasien']);
			}
		}
		$query->andWhere(['between', 'tgl_gabung', $start, $end]);
		return DocoRestActiveFilter::advancedFilter($model, $query);
	}

	public function actionFilters()
	{
		$type = Yii::$app->request->get('type', null);
		$payload = Yii::$app->request->get('payload', []);
		$page = isset($payload['page']) ? $payload['page'] : 1;
		$limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
		$term = isset($payload['term']) ? $payload['term'] : null;
		$result = $resultData = [];
		$pasien_id = isset($payload['pasien_id']) ? $payload['pasien_id'] : null;
		if (!empty($type)) {
			switch ($type) {
				case 'no_rekam_medik':
					$result = FindPendaftaranGabungView::find()
						->select([
							'pasien_id as id', 
							new \yii\db\Expression("CONCAT(nama_pasien, ' - ', no_rekam_medik) as text"),
							'no_rekam_medik', 
							'nama_pasien'
						])
						->where(['is_gabung' => false]);

					if (!empty($term)) {
						$result->andWhere(['like', 'LOWER(nama_pasien)', strtolower($term)]);
						$result->orWhere(['like', 'LOWER(no_rekam_medik)', strtolower($term)]);
					}
					$result->groupBy(['pasien_id', 'nama_pasien', 'no_rekam_medik']);
					$result->orderBy(['pasien_id' => SORT_DESC]);
					break;

				case 'no_pendaftaran':
					$result = FindPendaftaranGabungView::find()
						->select([
							'pendaftaran_id as id', 
							new \yii\db\Expression("CONCAT(no_pendaftaran, ' - ', TO_CHAR(tgl_pendaftaran, 'DD-MON-YYYY')) as text"),
							'no_pendaftaran',
							'pasien_id',
							'no_rekam_medik',
							'nama_pasien',
							'penjamin_id'
						])
						->where(['is_gabung' => FALSE]);

					if (!empty($term)) {
						$result->andWhere(['like', 'LOWER(no_pendaftaran)', strtolower($term)]);
					}
					if (!empty($pasien_id)) {
						$result->andWhere(['pasien_id' => $pasien_id]);
					}
					$result->groupBy(['pendaftaran_id', 'no_pendaftaran', 'tgl_pendaftaran', 'pasien_id', 'nama_pasien', 'no_rekam_medik', 'penjamin_id']);
					$result->orderBy(['tgl_pendaftaran' => SORT_DESC]);
					break;

				case 'no_pendaftaran_tujuan':
					$result = FindPendaftaranGabungView::find()
						->select([
							'pendaftaran_id as id', 
							new \yii\db\Expression("CONCAT(no_pendaftaran, ' - ', TO_CHAR(tgl_pendaftaran, 'DD-MON-YYYY')) as text"),
							'no_pendaftaran',
							'pasien_id',
							'no_rekam_medik',
							'nama_pasien',
							'penjamin_id'
						])
						->where(['is_gabung' => FALSE])
						->andWhere(['!=', 'instalasi_id', 21]);

						if (!empty($term)) {
							$result->andWhere(['like', 'LOWER(no_pendaftaran)', strtolower($term)]);
						}
						if (!empty($pasien_id)) {
							$result->andWhere(['pasien_id' => $pasien_id]);
						}
						$result->groupBy(['pendaftaran_id', 'no_pendaftaran', 'tgl_pendaftaran', 'pasien_id', 'nama_pasien', 'no_rekam_medik', 'penjamin_id']);
						$result->orderBy(['tgl_pendaftaran' => SORT_DESC]);
						break;

				default:
					break;
			}
			if (!empty($result)) {
				$resultData = $result->limit($limit + 1)
				->offset(($page - 1) * $limit)
				->asArray()
				->all();
			} else {
				$resultData = [];
			}
		}
		
		return $resultData;
	}

	public function actionDetailTagihan()
	{
		$request = Yii::$app->request;
		$no_pendaftaran = $request->get('no_pendaftaran', null);
		$no_pendaftaran_tujuan = $request->get('no_pendaftaran_tujuan', null);
		$model = new InfoGabungTransaksiView;
		$detailPendaftaran = $model::find()
			->where(['pendaftaran_id' => $no_pendaftaran])
			->all();
		
		$detailPendaftaranTujuan = $model::find()
			->where(['pendaftaran_id' => $no_pendaftaran_tujuan])
			->all();
		
		$total_dijamin = 0;
		$total_dibayar = 0;
		$total_tagihan = 0;
		$total_dijamin_tujuan = 0;
		$total_dibayar_tujuan = 0;
		$total_tagihan_tujuan = 0;

		if(!empty($detailPendaftaran)) {
			foreach ($detailPendaftaran as $key => $value) {
				$total_dijamin += isset($value['tarif_dijamin']) ? $value['tarif_dijamin'] : 0;
				$total_dibayar += isset($value['tarif_dibayarkan']) ? $value['tarif_dibayarkan'] : 0;
				$total_tagihan += isset($value['sub_total']) ? $value['sub_total'] : 0;
			}
		}
		if(!empty($detailPendaftaranTujuan)) {
			foreach ($detailPendaftaranTujuan as $key => $value) {
				$total_dijamin_tujuan += isset($value['tarif_dijamin']) ? $value['tarif_dijamin'] : 0;
				$total_dibayar_tujuan += isset($value['tarif_dibayarkan']) ? $value['tarif_dibayarkan'] : 0;
				$total_tagihan_tujuan += isset($value['sub_total']) ? $value['sub_total'] : 0;
			}
		}
		$sumTotalDijamin = $total_dijamin + $total_dijamin_tujuan;
		$sumTotalDibayar = $total_dibayar + $total_dibayar_tujuan;
		$sumTotalTagihan = $total_tagihan + $total_tagihan_tujuan;

		return [
			'data_pendaftaran' => $detailPendaftaran,
			'data_pendaftaran_tujuan' => $detailPendaftaranTujuan,
			'total_dijamin' => $sumTotalDijamin,
			'total_dibayar' => $sumTotalDibayar,
			'total_tagihan' => $sumTotalTagihan,
		];
	}

	public function actionSimpan()
	{
		$request = Yii::$app->request;
		$post = $request->post();
		$model = new GabungPelayananDetail;
		$model->pendaftaran_id = isset($post['no_pendaftaran']) ? $post['no_pendaftaran'] : null;
		$model->ref_pendaftaran_id = isset($post['no_pendaftaran_tujuan']) ? $post['no_pendaftaran_tujuan'] : null;
		try {
			$connection = Yii::$app->db;
			$transaction = $connection->beginTransaction();
			if($model->validate() && $model->save()) {
				$transaction->commit();
				$status = ['status' => 200, 'message' => 'Data Berhasil di simpan'];
			}
			else {
				$transaction->rollBack();
				$errors = DocoHelpers::parseError($model->errors, 'GabungBillingForm');
				$status = [
					'data' => $errors,
					'status' => 422
				];
			}
			return $status;
		} catch (\yii\db\Exception $e) {
			$transaction->rollBack();
			\Yii::$app->response->statusCode = 500;
			return ['message' => $e->getMessage()];
		} catch (\Exception $e) {
			$transaction->rollBack();
			\Yii::$app->response->statusCode = 500;
			return ['message' => $e->getMessage()];
		}
	}

	public function actionDetailGabung()
	{
		$request = Yii::$app->request;
		$pendaftaran_id = $request->get('pendaftaran_id', null);
		$pembayaran_id = $request->get('pembayaran_id', null);
		$model = !empty($pembayaran_id) ? new InvoiceSudahBayarDetailView : new InfoGabungTagihanDetailView;
		$query = $model::find();
		if(!empty($pembayaran_id)) {
			$query->where(['pembayaran_id' => $pembayaran_id, 'is_diskon' => FALSE]);
		}
		else {
			$query->where(['pendaftaran_id' => $pendaftaran_id]);
		}
		return new ActiveDataProvider([
			'query' => $query,
		]);
	}

	public function actionSummaryDetailGabung()
	{
		$request = Yii::$app->request;
		$pendaftaran_id = $request->get('pendaftaran_id', null);
		$pembayaran_id = $request->get('pembayaran_id', null);
		if(!empty($pembayaran_id)) {
			$model = InvoiceSudahBayarDetailView::find()
				->select(['SUM(sub_total) AS sub_total', 'SUM(tarif_dijamin) AS tarif_dijamin', 'SUM(tarif_dibayarkan) AS tarif_dibayarkan'])
				->where(['pembayaran_id' => $pembayaran_id, 'is_diskon' => FALSE])
				->one();
		}
		else {
			$model = InfoGabungTagihanDetailView::find()
			->select(['SUM(sub_total) AS sub_total', 'SUM(tarif_dijamin) AS tarif_dijamin', 'SUM(tarif_dibayarkan) AS tarif_dibayarkan'])
			->where(['pendaftaran_id' => $pendaftaran_id])
			->one();
		}

		return [
			'sub_total' => isset($model['sub_total']) ? $model['sub_total'] : 0,
			'tarif_dijamin' => isset($model['tarif_dijamin']) ? $model['tarif_dijamin'] : 0,
			'tarif_dibayarkan' => isset($model['tarif_dibayarkan']) ? $model['tarif_dibayarkan'] : 0
		];
	}

	public function actionBatal()
	{
		$request = Yii::$app->request;
		$gabungpelayanandetail_id = $request->get('gabungpelayanandetail_id', null);
		$alasan_batal = $request->get('alasan_batal', null);
		$connection = \Yii::$app->db;
		$helpers = new DocoHelpers;
		try {
			$refPendaftaranId = null;
			if(empty($gabungpelayanandetail_id)) {
				return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
					'text' => 'Data Tidak Ditemukan'
				]);
			}
			
			$dataGabung = GabungPelayananDetail::findOne($gabungpelayanandetail_id);
			if(!empty($dataGabung)) {
				$refPendaftaranId = ArrayHelper::getValue($dataGabung, 'ref_pendaftaran_id');
				if(!empty($refPendaftaranId)) {
					//cek status pembayaran
					$pembayaran = Pembayaran::find()->where(['pendaftaran_id' => $refPendaftaranId])->one();
					if(!empty($pembayaran)) {
						return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
							'text' => 'Gabung Billing Tidak Dapat Dibatalkan, karena Sudah Ada Pembayaran.'
						]);
					}
					$dataGabung->is_deleted = true;
					$dataGabung->save();
				}
			}
			return [
				'status' => 200,
				'title' => 'Proses Berhasil',
				'text' => 'Batal Gabung Tagihan Berhasil'
			];
		} catch (\Exception $e) {
			\Yii::$app->response->statusCode = 500;
			return [
				'message' => $e->getMessage()
			];
		}
	}
}
