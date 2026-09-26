<?php 
namespace tests\unit\models;
use app\modules\v1\models\LaporanOperasi;

class LaporanOperasiTest extends \Codeception\Test\Unit
{
   public function testPositiveCase()
   {
      $model = new LaporanOperasi;
      $model->attributes = [
         'pasienmasukpenunjang_id' => 21,
         'laporanoperasi_id' => 1,
         'dokter_id' => 1,
         'mulai_operasi' => '2022-06-24 13:15',
         'selesai_operasi' => '2022-06-24 13:15',
      ];
      $this->assertTrue($model->validate(), json_encode($model->getErrors()));
   }

   public function testIntegerCase()
   {
      $model = new LaporanOperasi;
      $model->attributes = [
         'pasienmasukpenunjang_id' => 1,
         'laporanoperasi_id' => 1,
         'dokter_id' => 2,
         'mulai_operasi' => '2022-06-24 13:15',
         'selesai_operasi' => '2022-06-24 13:15',
      ];
      $this->assertTrue($model->validate(), json_encode($model->getErrors()));
   }

   public function testStringCase()
   {
      $model = new LaporanOperasi;
      $model->attributes = [
         'pasienmasukpenunjang_id' => 21,
         'perdarahan' => 'ss',
         'posisi_pasien' => 1,
         'dokter_id' => 1,
         'diagnosis_prabedah' => 'sssssss',
         'mulai_operasi' => '2022-06-24 13:15',
         'selesai_operasi' => '2022-06-24 13:15',
      ];
      $this->assertTrue($model->validate(), json_encode($model->getErrors()));
   }

   public function testBooleanCase()
   {
      $model = new LaporanOperasi;
      $model->attributes = [
         'pasienmasukpenunjang_id' => 21,
         'is_kirimkepatologi' => true,
         'is_active' => true,
         'dokter_id' => 1,
         'mulai_operasi' => '2022-06-24 13:15',
         'selesai_operasi' => '2022-06-24 13:15',
      ];
      $this->assertTrue($model->validate(), json_encode($model->getErrors()));
   }

   public function testNegativeCase()
   {
      $model = new LaporanOperasi;
      $model->attributes = [
         'pasienmasukpenunjang_id' => 2,
         'laporanoperasi_id' => 3,
         'is_kirimkepatologi' => false,
         'is_active' => true,
         'kategori_operasi' => 'Test',
         'dokter_id' => 1,
         'mulai_operasi' => '',
         'selesai_operasi' => '2022-06-24 13:15',
      ];
      $this->assertTrue($model->validate(), json_encode($model->getErrors()));
   }
}