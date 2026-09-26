<?php 

	/**
	 * @author Chacha Nurholis (chacha@sirs.co.id)
	 * A product of PT Citra Raya Nusatama
	 * Powered by Sirs
	 */

	namespace Doco\pengadaan\controllers;

	use app\components\DocoController;
	use app\components\DocoDatatableHelper;

	class LaporanRekapPurchaseOrderObatController extends DocoController {
		public $_module        = '/pengadaan/laporan-rekap-purchase-order-obat/';

		public function init() {
			parent::init();
		}

		public function actions() {
			return [
				'export-excel'  => 'Doco\pengadaan\actions\LaporanRekapPurchaseOrderObat\ExportExcelAction',
				'index'         => 'Doco\pengadaan\actions\LaporanRekapPurchaseOrderObat\IndexAction',
				'get-data'      => 'Doco\pengadaan\actions\LaporanRekapPurchaseOrderObat\GetDataAction'
			];
		}

		public function getFilter($request) {
			$yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
			
			if (!isset($yiiRestfulParams['advanced-filter']['tgl_pr'])) {
				$yiiRestfulParams['advanced-filter']['tgl_pr'] = date('d-m-Y').' - '.date('d-m-Y');
			}

			return $yiiRestfulParams;
		}
  }

?>