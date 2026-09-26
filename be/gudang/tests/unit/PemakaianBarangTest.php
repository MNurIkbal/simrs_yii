<?php

use app\modules\v1\models\PemakaianBarang;

class PemakaianBarangTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before() {
        $this->model = new PemakaianBarang();
    }

    protected function _after() {
        $this->model = null;
    }

    public function testTableName() {
        $tableName = $this->model->tableSchema->name;
        $this->assertEquals($tableName, 'pemakaianbarang_t');
    }

    public function testTipeDataPositif() {
        $this->model->attributes = $this->setPayloadTipeData(true);
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif() {
        $this->model->attributes = $this->setPayloadTipeData(false);
        $this->assertFalse($this->model->validate());
    }

    public function testMaxStringNoMutasiPositif() {
        $nomutasi_barang = strlen('PMB202207260001');
        $this->assertLessThanOrEqual(20, $nomutasi_barang);
    }

    public function testMaxStringNoMutasiNegatif() {
        $nomutasi_barang = strlen('PMB2022072600019999999');
        $this->assertGreaterThan(20, $nomutasi_barang);
    }

    public function testRequiredPositif() {
        $this->model->attributes = [
            'ruangan_id' => 1,
            'pegawai_id' => 10,
            'tgl_pemakaianbarang' => '2022-07-26',
            'untuk_keperluan' => 'Pemakaian ruangan'
        ];
        $this->assertTrue($this->model->validate());
    }

    public function testRequiredNegatif() {
        $this->model->attributes = [
            'ruangan_id' => null,
            'pegawai_id' => null,
            'tgl_pemakaianbarang' => null,
            'untuk_keperluan' => null,
        ];
        $this->assertFalse($this->model->validate());
    }

    private function setPayloadTipeData($isPositif = true) {
        return [
            'ruangan_id' => $isPositif ? 1 : null,
            'pegawai_id' => $isPositif ? 10 : null,
            'tgl_pemakaianbarang' => $isPositif ? '2022-07-26' : null,
            'untuk_keperluan' => $isPositif ? 'Pemakaian ruangan' : null,
            'keteranganpakai' => $isPositif ? 'Untuk ruangan A' : 123,
            'no_pemakaianbarang' => $isPositif ? 'PMB202207260001' : 20220726
        ];
    }
}