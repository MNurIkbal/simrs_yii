<?php

use app\modules\v1\models\DaftarTindakan;

class DaftarTindakanTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new DaftarTindakan;
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidateRequiredPositif()
    {
        $payload = [
            'daftartindakan_kode' => 'GG',
            'daftartindakan_namalainnya' => 'GIGI',
            'daftartindakan_nama' => 'Periksa Gigi',
            'kelompoktindakan_id' => 2,
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'daftartindakan_kode' => null,
            'daftartindakan_namalainnya' => null,
            'daftartindakan_nama' => null,
            'kelompoktindakan_id' => null,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'daftartindakan_kode' => 'GG',
            'daftartindakan_namalainnya' => 'GIGI',
            'daftartindakan_nama' => 'Periksa Gigi',
            'kelompoktindakan_id' => 2,
            'kategoritindakan_id' => 14,
            'jeniskegiatantindakan_id' => 5,
            'additional_data' => 'Periksa Gigi Rutin',
            'tindakanmedis_nama' => 'Poli gigi',
            'daftartindakan_katakunci' => 'gigi',
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'daftartindakan_kode' => (int) 9191,
            'daftartindakan_namalainnya' => (int) 7777,
            'daftartindakan_nama' => (int) 9360,
            'kelompoktindakan_id' => 'Dua',
            'kategoritindakan_id' => "Empat Belas",
            'jeniskegiatantindakan_id' => 'Lima',
            'additional_data' => ['data' => 'Periksa Gigi Rutin'],
            'tindakanmedis_nama' => (int) 9191,
            'daftartindakan_katakunci' => (int) 73,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}