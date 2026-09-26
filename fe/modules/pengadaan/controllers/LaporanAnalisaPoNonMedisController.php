<?php

/**
 * ? @author : Budi (budi@sirs.co.id)
 * ? Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use Yii;
use app\components\DocoController;

class LaporanAnalisaPoNonMedisController extends DocoController
{
   protected $allowAction = ['*'];
   public $_title = "Laporan Analisa PO Non Medis";
   public $_module = 'pengadaan/laporan-analisa-po-non-medis/';
   public $_namespace = 'Doco\pengadaan\actions\LaporanAnalisaPoNonMedis';
   public $_endpoint = 'laporan-analisa-po-non-medis/';
   public $_restPengadaan;

   public function init() {
      parent::init();
      $this->_restPengadaan = Yii::$app->docoRest->pengadaan;
   }

   public function actions()
   {
      return [
         'export-excel'  => $this->_namespace . '\ExportExcelAction',
         'index'         => $this->_namespace . '\IndexAction',
         'get-data'      => $this->_namespace . '\GetDataAction',
         'download-excel' => $this->_namespace . '\DownloadExcelAction',
         'export-excel-bg-process' => $this->_namespace . '\ModalExcelBgProcessAction',
         'process-sync-excel'    => $this->_namespace . '\ProcessSyncExcelAction',
         'download-file-excel' => $this->_namespace . '\DownloadFileExcelAction',
      ];
   }
}
