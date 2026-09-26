<?php
/**
 * @author: [Budi][budi@sirs.co.id]
 * A product of Sirs
 * Powered by Sirs
 */

namespace Doco\radiologi\controllers;

use Yii;
use app\components\DocoController;

class LapRekapRadiologiController extends DocoController
{
   protected $allowAction = ['*'];
   public $_title = "Laporan Rekapitulasi Pemeriksaan Radiologi";
   public $_module = '/radiologi/lap-rekap-radiologi/';
   public $_namespace = 'Doco\radiologi\actions\LapRekapRadiologi';
   public $_endpoint = 'lap-rekap-radiologi/';

   public function init()
   {
      parent::init();
   }

   public function actions()
   {
      return [
         'index' => $this->_namespace . '\IndexAction',
         'get-data' => $this->_namespace . '\GetDataAction',
         'show-popup'  => $this->_namespace . '\ShowPopupAction',
         'process-sync' => $this->_namespace . '\ProcessSyncAction',
         'download-file' => $this->_namespace . '\DownloadFileAction',
         'filters' => $this->_namespace . '\FiltersAction',
      ];
   }
}
