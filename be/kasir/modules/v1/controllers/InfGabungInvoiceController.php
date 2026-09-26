<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\InfGabungInvoiceView;
use app\modules\v1\models\InvoiceGabungView;
use app\modules\v1\models\InvoiceGabungDetailView;
use app\modules\v1\models\InvoiceGabung;
use app\modules\v1\models\InvoiceGabungDetail;
use app\modules\v1\models\InfoDataPendaftaran;
use Doco\models\kasir\InfoPasienRiView;
use Doco\models\kasir\PasienView;
use Doco\models\ProfilRsView;
use Doco\components\DocoConstansId;
use Doco\models\KonfigSystem;
use Doco\Services\InternalService;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;

class InfGabungInvoiceController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InfGabungInvoiceView';

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

	public function getData()
	{
		$model = new InfGabungInvoiceView;
		$query = $model::find(true);
		$start = date('Y-m-d 00:00:00');
		$end = date('Y-m-d 23:59:59');
		if(isset($_GET['advanced-filter'])) {
			if(isset($_GET['advanced-filter']['tgl_invoicegabung'])) {
					$explode = explode(" - ", $_GET['advanced-filter']['tgl_invoicegabung']);
					if(count($explode) == 2) {
						$start = date('Y-m-d 00:00:00', strtotime($explode[0]));
						$end = date('Y-m-d 23:59:59', strtotime($explode[1]));
					}
					unset($_GET['advanced-filter']['tgl_invoicegabung']);
			}
		}
		$query->andWhere(['between', 'tgl_invoicegabung', $start, $end]);
		return DocoRestActiveFilter::advancedFilter($model, $query);
	}

	public function actionFilters()
	{
		$request = Yii::$app->request;
		$type = $request->get('type', null);
		$result = $resultData = [];
		$pasien_id = $request->get('pasien_id', null);
		$pendaftaran_id = $request->get('pendaftaran_id', null);
		$strPendaftaranId = '';
		if(!empty($pendaftaran_id)){
			$strPendaftaranId = implode("," ,$pendaftaran_id);
		}
		if (!empty($type)) {
			$term = $request->get('term', null);
			$page = $request->get('page', 1);
			$limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
			switch ($type) {
					case 'no_rekam_medik':
						$result = Pasien::find()
							->select([
									'pasien_id as id', 
									new \yii\db\Expression("CONCAT(nama_pasien, ' - ', no_rekam_medik) as text"), 
									'no_rekam_medik', 
									'nama_pasien'
							])
							->where(['is_deleted' => false])
							->andWhere(['IS NOT', 'nama_pasien', NULL]);

						if (!empty($term)) {
							$result->andWhere(['like', 'LOWER(nama_pasien)', strtolower($term)]);
							$result->orWhere(['like', 'LOWER(no_rekam_medik)', strtolower($term)]);
						}
						$result->orderBy(['pasien_id' => SORT_DESC]);
						break;

					case 'no_pendaftaran':
						$qlimit = ($limit + 1);
						$qoffset = $limit * ($page - 1);
						$where = "pasien_id = {$pasien_id}";
						if (!empty($strPendaftaranId)) {
							$where .= " AND pendaftaran_id IN ($strPendaftaranId)";
						}
						$where .= " AND no_pendaftaran ilike '%{$term}%'";
						$result = Yii::$app->db->createCommand("
							SELECT * FROM (
								SELECT DISTINCT ON (no_pendaftaran) no_pendaftaran, 
								pendaftaran_id AS id, 
								CONCAT(no_pendaftaran, ' - ', TO_CHAR(tgl_pendaftaran, 'DD-MON-YYYY')) as text, 
								tgl_pendaftaran,
								nama_pasien,
								no_rekam_medik 
								FROM invoicegabung_v 
								WHERE {$where}
								)
							AS sub
							ORDER BY tgl_pendaftaran ASC
							limit {$qlimit} offset {$qoffset}
						")->queryAll();
						break;

					case 'no_invoice':
						if (!empty($pendaftaran_id)) {
							$result = InvoiceGabungView::find()
							->select(['pembayaran_id AS id', 'total_tagihan AS text', 'tgl_pembayaran', 'no_pembayaran', 'tagihan', 'pendaftaran_id', 'no_pendaftaran', 'penjamin_nama'])
							->where(['in','pendaftaran_id', $pendaftaran_id]);
							if (!empty($term)) {
									$result->andWhere(['like', 'LOWER(no_pembayaran)', strtolower($term)]);
							}
							$result->orderBy(['no_pembayaran' => SORT_ASC]);
							$result = $result->asArray()->all();
						}
						break;
					case 'penjamin':
						$result = Pendaftaran::find()
								->select([
									'pendaftaran_t.penjamin_id as id',
									'penjamin_m.penjamin_nama as text',
								])
								->innerJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
								->innerJoin('penjamin_m', 'penjamin_m.penjamin_id = pendaftaran_t.penjamin_id')
								->where(['pendaftaran_t.is_deleted' => false]);

						if (!empty($pendaftaran_id) && $pendaftaran_id != null) {
							$result->andWhere(['in','pendaftaran_id', $pendaftaran_id]);
						}
						$result->orderBy(['pendaftaran_t.tgl_pendaftaran' => SORT_DESC]);
						break;

					default:
						# code...
						break;
			}
			if (!empty($result)) {
					if($type != 'no_invoice' && $type != 'no_pendaftaran') {
						$result->limit(($limit + 1))->offset($limit * ($page - 1));
						$resultData = $result->asArray()->all();
					}
					else {
						$resultData = $result;
					}
			} else {
					$resultData = [];
			}
		}
		$newArr = [];
		if($type == 'no_invoice') {
			foreach ($resultData as $key => $value) {
					$text = isset($value['text']) ? $value['text'] : '';
					if(!empty($text)) {
						$exp = explode(' - ', $text);
						if(count($exp) == 2) {
							if(isset($exp[1])) {
									$exp[1] = DocoHelpers::formatNumber($exp[1]);
							}
						}
					}
					$value['text'] = $exp[0].' - '.$exp[1];
					$newArr[] = $value;
					$resultData = $newArr;
			}
		}
		return $resultData;
	}

	public function actionSimpanInvoice()
	{
		$request = Yii::$app->request;
		$data = $request->post('data');
		$detail = $request->post('detail');
		$model = new InvoiceGabung;
		$model->attributes = $data;
		$model->no_invoicegabung = null;
		$cacheData = $data['cache_data'];
		if(empty($cacheData)) {
			return [
				'message' => 'Data Detail Invoice harus diisi.',
				'status' => 500
			];
		}
		$dataDetail = [];
		foreach ($detail as $key => $value) {
			$dataDetail[$value] = $value;
		}
		$total = 0;
		$newData = [];
		foreach ($cacheData as $key => $value) {
			if(isset($dataDetail[$value['pembayaran_id']])) {
				$newData[] = $value;
				$total += isset($value['total_invoice']) ? $value['total_invoice'] : 0;
			}
		}
		// $total = DocoHelpers::convertToNumber($total);
		// $total = DocoHelpers::convertCommaToPoint($total);
		$model->total_invoicegabung = $total;
		$model->tgl_invoicegabung = date('Y-m-d', strtotime($data['tgl_invoicegabung']));
		try {
			$connection = Yii::$app->db;
			$transaction = $connection->beginTransaction();
			if($model->validate() && $model->save()) {
				$dataInsert = [];
				foreach ($newData as $key => $value) {
					$total += isset($value['total_invoice']) ? $value['total_invoice'] : 0;
					$total_invoice = isset($value['total_invoice']) ? str_replace('"', "", $value['total_invoice']) : 0;
					// $total_invoice = DocoHelpers::convertToNumber($total_invoice);
					// $total_invoice = DocoHelpers::convertCommaToPoint($total_invoice);
					$dataInsert[] = [
						'invoicegabung_id' => $model->invoicegabung_id,
						'pendaftaran_id' => isset($value['pendaftaran_id']) ? str_replace('"', "", $value['pendaftaran_id']) : null,
						'pembayaran_id' => isset($value['pembayaran_id']) ? str_replace('"', "", $value['pembayaran_id']) : null,
						'no_pembayaran' => isset($value['no_invoice']) ? str_replace('"', "", $value['no_invoice']) : null,
						'tgl_invoice' => isset($value['tgl_invoicegabung']) ? date('Y-m-d H:i:s', strtotime(str_replace('"', "", $value['tgl_invoicegabung']))) : null,
						'total_invoice' => isset($value['total_invoice']) ? $total_invoice : 0,
					];
				}
				InvoiceGabungDetail::batchInsert($dataInsert);
				$transaction->commit();
				$status = ['status' => 200, 'message' => 'Data Berhasil di simpan'];
			}
			else {
				$transaction->rollBack();
				$errors = DocoHelpers::parseError($model->errors,'InvoiceGabungForm');
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

	/**
 * @controller actionCetak
	* @attribute #tgl_pendaftaran# => tanggal daftar
	* @attribute #no_rekam_medik# => no rekam medik
	* @attribute #no_pendaftaran# => no pendaftaran
	* @attribute #nama_pasien# => nama pasien
	* @attribute #nama_dok_rj_rd# => dokter
	* @attribute #rua_nama# => ruangan
	* @attribute #kelaspelayanan_nama# => kelas pelayanan
	* @attribute #penjamin_nama# => penjamin
	* @attribute #carabayar_nama# => cara bayar
	* @attribute #status_bayar# => status bayar
	* @attribute #table# => table detail
	**/
	public function actionCetak()
	{
		return Yii::$app->docoPlugin->execute('cetak_invoice_gabung');
	}

	public function actionCetakDetail()
	{
		$request = Yii::$app->request;
		$xOwner = $request->getHeaders()->get('X-Owner');
		$auth = $request->getHeaders()->get('Authorization');
		$invoicegabung_id = $request->get('id', null);
		$nama_pegawai = $request->get('nama_pegawai', null);
		$fetchLimit = 20;
		$countData = $this->actionGetDataInvoiceDetail($invoicegabung_id);
		$randString = DocoHelpers::generateRandomString();
		$totalPerPage = ceil($countData/$fetchLimit);

		(new InternalService)->sendTo([
			'Sirs' => [ 
				'DetailInvoiceGabung' => [
					'token' => $auth,
					'xOwner' => $xOwner,
					'unique_str' => $randString,
					'invoicegabung_id' => $invoicegabung_id,
					'nama_pegawai' => $nama_pegawai,
				]
			]
		], true);

		(new InternalService)->sendTo([
			'Sirs' => [ 
				'CetakDetailInvoiceGabung' => [
					'token' => $auth,
					'xOwner' => $xOwner,
					'unique_str' => $randString,
				]
			]
		], true);

		(new InternalService)->sendTo([
			'Sirs' => [ 
				'UploadDetailInvoiceGabung' => [
					'token' => $auth,
					'xOwner' => $xOwner,
					'unique_str' => $randString,
				]
			]
		], true);
		
		return [
			'totalPerPage' => $totalPerPage,
			'unique_str' => $randString,
			'countData' => $countData,
		];
	}

	public function actionGetDataInvoiceDetail($invoicegabung_id)
   	{
		
		$db = Yii::$app->db;
		$sql = "SELECT invoicegabung_t.invoicegabung_id, 
			invoicegabungdetail_t.pembayaran_id,
			pembayaran_t.pasienadmisi_id
			FROM invoicegabung_t 
			INNER JOIN invoicegabungdetail_t ON invoicegabungdetail_t.invoicegabung_id = invoicegabung_t.invoicegabung_id
			INNER JOIN pembayaran_t ON pembayaran_t.pembayaran_id = invoicegabungdetail_t.pembayaran_id
			WHERE invoicegabung_t.invoicegabung_id = {$invoicegabung_id}";
		
		$data = $db->createCommand($sql)->queryAll();
		$pembayaranId = [];
		if(!empty($data)) {
			foreach ($data as $key => $value) {
				$pembayaranId[] = isset($value['pembayaran_id']) ? $value['pembayaran_id'] : null;
			}
		}
		$pembayaranId = "(" . implode(",", $pembayaranId) . ")";
		$data = Yii::$app->db->createCommand("
			SELECT *
			FROM invoicesudahbayardetail_v 
			WHERE pembayaran_id IN {$pembayaranId} 
		")->queryAll();

		return count($data);
   	}

	public function actionSendFile()
    {
		$request = Yii::$app->request;
		$filePath = $request->get('filePath', null);
		$model = new UploadPayload;
		if($request->isPost) {
			$files = UploadedFile::getInstanceByName('file');
			$fileName = $files->getBaseName();
			$ext = $files->getExtension();
			$model->file = $fileName.'.'.$ext;
			$path = 'uploads/'. $filePath;
			if (!file_exists($path)) {
				mkdir($path, 0755, true);
			}
			$nameFile = $path.'/'.$model->file;
			if($files->saveAs($nameFile)) {
				return [
					'path' => $path,
					'message' => 'Upload File Berhasil'
				];
			}
		}
    }

	public function actionDownloadInvoice()
   	{
		$request = Yii::$app->request;
		$fileName = $request->get('fileName', null);
		$rootPath = 'uploads';
		$file = $rootPath.'/'.$fileName.'.pdf';
		if(file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/pdf');
			header("Content-Disposition: inline; filename=$file");
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			ob_clean();
			flush();
			readfile($file);
			unlink($file);
			die();
		}
   	}

	public function actionCetakKwitansi()
	{
		return Yii::$app->docoPlugin->execute('cetak_kwitansi_gabung');
	}

	public function actionDataDetailInvoiceGabung()
	{
		return Yii::$app->docoPlugin->execute('cetak_detail_invoice_gabung');
	}

	public function actionBatalInvoice()
	{
		$request = Yii::$app->request;
		$invoicegabung_id = $request->post('invoicegabung_id');
		$is_batal = $request->post('is_batal');

		$connection = Yii::$app->db;
		$transaction = $connection->beginTransaction();

		try {
			$jwt = Yii::$app->jwt;
			$pegawai_id = !empty($jwt->user->loginpemakai_id) ? $jwt->user->loginpemakai_id : null;
			$date = date('Y-m-d H:i:s', time());
			$invoiceGabungan = InvoiceGabung::find()->where(['invoicegabung_id' => $invoicegabung_id])->one();
			$invoiceGabunganDetail = Yii::$app->db->createCommand("UPDATE invoicegabungdetail_t SET is_deleted = true, deleted_by = {$pegawai_id}, deleted_date = '{$date}' WHERE invoicegabung_id = '{$invoicegabung_id}'");

			if ($is_batal === true) {
				$transaction->rollBack();
					$status = [
						'data' => 'Data sudah dibatalkan',
						'status' => 422
					];
			} else {
				if ($invoiceGabungan->delete() && $invoiceGabunganDetail->execute()) {
					$transaction->commit();
					$status = [
						'status' => 200, 
						'message' => 'Data Berhasil di simpan'
					];
				} else {
					$transaction->rollBack();
					$status = [
						'data' => 'Data Gagal di simpan',
						'status' => 422
					];
				} 
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

	public function actionDetailInvoice()
	{
		$request = Yii::$app->request;
		$invoicegabung_id = $request->post('invoicegabung_id');
		$query = InvoiceGabungDetailView::find()->where(['invoicegabung_id' => $invoicegabung_id]);
		return DocoHelpers::rupiahDisplay($query->sum('total_invoice'));
	}

	public function actionDetailInvoiceGetData()
	{
		$request = Yii::$app->request;
		$invoicegabung_id = $request->post('invoicegabung_id');
		$query = InvoiceGabungDetailView::find()->where(['invoicegabung_id' => $invoicegabung_id])->orderBy(['no_pendaftaran' => SORT_ASC]);
		return new ActiveDataProvider([
			'query' => $query,
		]);
	}

	public function actionGetPenjamin($pembayaran_id)
    {
        $results = [];
		if ($pembayaran_id) {
            $results = Yii::$app->db->createCommand("
			SELECT
				pp.penjamin_id,
				penjamin_m.penjamin_nama,
				TRUE AS is_penjaminutama
			FROM pembayaranpelayanan_t pp
			JOIN penjamin_m on penjamin_m.penjamin_id = pp.penjamin_id
			WHERE pp.pembayaran_id IN ({$pembayaran_id})")->queryAll();
        }
		return $results;
    }
}