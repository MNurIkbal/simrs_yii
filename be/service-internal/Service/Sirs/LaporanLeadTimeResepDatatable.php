<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Cache\Cache;
use Integrasi\Service\Sirs\Models\LaporanLeadTimeResepView;
use yii\helpers\ArrayHelper;

class LaporanLeadTimeResepDatatable extends \Integrasi\Contracts\DocoImplement
{
   const RUANGAN = 'ruangan';
   const TGL_RESEP = 'tgl_resep';
   const NO_RESEP = 'no_resep';
   const JENIS_RESEP = 'jenis_resep';
   const JUMLAH_R = 'jumlah_r';
   const DOKTER = 'dokter';
   const JUMLAH_ITEM = 'jumlah_item';
   const JAM_RESEP_MASUK = 'jam_resep_masuk';
   const JAM_RESEP_DIBAYAR = 'jam_resep_dibayar';
   const JAM_PRODUCTION = 'jam_production';
   const JAM_DISERAHKAN = 'jam_diserahkan';
   const WAKTU_TUNGGU = 'waktu_tunggu';
   const WAKTU_TUNGGU_FORMAT = 'waktu_tunggu_format';
   const STRTOLOWER = 'strtolower';
   const STRTOUPPER = 'strtoupper';
   const UCFIRST = 'ucfirst';
   const UCWORD = 'ucword';

    public function execute()
    {
        $date = $this->advance_filter['tgl_resep'];
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:00');
        
        if(!empty($date)) {
            $explode = explode(" - ", $date);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
        }
        $data = $this->generateData($start,$end);
        $pdata = [
            'status' => 'finish',
            'data' => $data['data'],
            'header' => $this->generateHeaderColumns()
        ];
        
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'laporan-lead-time-resep-datatable:'.$this->unique_str,
            'message' => json_encode($pdata),
        ]);
        

        return json_encode([
            'service' => 'Sirs-LaporanLeadTimeResepDatatable',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function generateData($start = null, $end = null)
    {
      $advancedFilters = $this->advance_filter;

      $model = new LaporanLeadTimeResepView;
      $query = $model::find()
        ->select([
            'ruangan',
            'tgl_resep',
            'no_resep',
            'jenis_resep',
            'jumlah_r',
            'dokter',
            'jumlah_item',
            'jam_resep_masuk',
            'jam_resep_dibayar',
            'jam_production',
            'jam_diserahkan',
            'waktu_tunggu_format',
        ]);
      // $start = date('Y-m-d 00:00:00');
      // $end = date('Y-m-d 23:59:59');

      $filterDate = 'tgl_resep';
      $orderBy = 'tgl_resep';

      if(!empty($advancedFilters)) {
          if(isset($advancedFilters['ruangan_id']) &&  $advancedFilters['ruangan_id']!='') {
              $term = $advancedFilters['ruangan_id'];
              $query->andWhere(['ruangan_id' => $term]);
          }
      }

      $query->andWhere(['between', new \yii\db\Expression('(tgl_resep::date)'), $start, $end]);
      $query->orderBy([$orderBy => SORT_DESC]);
      $model = $query->asArray()->all();
      $no = 0;
      $result = [];
      foreach($model as $key => $value) {
         $no++;
         $value['no'] = $no;
         $result[] = $value;
      }
      Yii::error($query->createCommand()->getRawSql());

      return ['data'=>$result];
  }

  private static function formatToReadable($string , $format = null)
  {
      switch ($string) {
          case self::RUANGAN:
              $string = 'Ruangan';
              break;
          case self::TGL_RESEP:
              $string = 'Tanggal Resep';
              break;
          case self::NO_RESEP:
              $string = 'No Resep';
              break;
          case self::JENIS_RESEP:
              $string = 'Jenis Resep';
              break;
          case self::JUMLAH_R:
              $string = 'Jumlah R';
              break;
          case self::DOKTER:
              $string = 'Dokter';
              break;
          case self::JUMLAH_ITEM:
              $string = 'Jumlah Item';
              break;
          case self::JAM_RESEP_MASUK:
              $string = 'Jam Resep Masuk';
              break;
          case self::JAM_RESEP_DIBAYAR:
              $string = 'Jam Resep Dibayar';
              break;
          case self::JAM_PRODUCTION:
              $string = 'Jam Production';
              break;
          case self::JAM_DISERAHKAN:
              $string = 'Jam Diserahkan';
              break;
          case self::WAKTU_TUNGGU:
              $string = 'Waktu Tunggu';
              break;
          case self::WAKTU_TUNGGU_FORMAT:
              $string = 'Waktu Tunggu';
              break;
          default:
              $string = $string;
              break;
      }

      switch ($format) {
          case self::UCFIRST:
              $string = ucfirst($string);
              break;
          case self::UCWORD:
              $string = ucwords($string);
              break;
          case self::STRTOUPPER:
              $string = strtoupper($string);
              break;
          case self::STRTOLOWER:
              $string = strtolower($string);
              break;
          default:
              $string = $string;
              break;
      }

      return $string;
  }

    private function generateHeaderColumns() 
    {
      $header = $columns = $tmpCarabayar = $tmpBaru = $tmpLama = $tmpHeader = $newCabar = [];

      $staticHeaderAwal = [
          'no' => 'no',
          self::RUANGAN => self::RUANGAN,
          self::TGL_RESEP => self::TGL_RESEP,
          self::NO_RESEP => self::NO_RESEP,
          self::JENIS_RESEP => self::JENIS_RESEP,
          self::JUMLAH_R => self::JUMLAH_R,
          self::DOKTER => self::DOKTER,
          self::JUMLAH_ITEM => self::JUMLAH_ITEM,
          self::JAM_RESEP_MASUK => self::JAM_RESEP_MASUK,
          self::JAM_RESEP_DIBAYAR => self::JAM_RESEP_DIBAYAR,
          self::JAM_PRODUCTION => self::JAM_PRODUCTION,
          self::JAM_DISERAHKAN => self::JAM_DISERAHKAN,
          self::WAKTU_TUNGGU_FORMAT => self::WAKTU_TUNGGU_FORMAT,
      ];



      $tmpHeader = array_merge($staticHeaderAwal, $tmpBaru);

      foreach($tmpHeader as $k => $v) {
          $visible = true;
          $search = false;
          $title = $k;


          $columns[] =[
              'title' => self::formatToReadable($title, self::UCWORD),
              'data' => $k,
              'searchable' => $search,
              'orderable' => false,
              'visible' => $visible,
          ];
      }


      return [
          'header' => $tmpHeader,
          'columns' => $columns,
      ];
    }
}