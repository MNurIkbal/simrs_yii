<?php
use app\modules\v1\models\StokOpnameBarang;

class StokOpnameBarangTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new StokOpnameBarang();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testTableName() {
        $tableName = $this->model->tableSchema->name;
        $this->assertEquals($tableName, 'stokopnamebarang_t');
    }

    public function testTipeDataPositif() {
        $this->model->attributes = $this->setPayloadTipeData(true);
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif() {
        $this->model->attributes = $this->setPayloadTipeData(false);
        $this->assertFalse($this->model->validate());
    }

    public function testRequiredPositif()
    {
        $this->model->attributes = [
            'ruangan_id' => 1
        ];
        $this->assertTrue($this->model->validate());
    }

    public function testRequiredNegatif() {
        $this->model->attributes = [
            'ruangan_id' => null
        ];
        $this->assertFalse($this->model->validate());
    }

    private function setPayloadTipeData($isPositif = true) {
        return [
            'ruangan_id' => $isPositif ? 1 : "FARMASI",
            'formsobarang_id' => $isPositif ? 1 : "1",
            'tglstokopname' => $isPositif ? '2022-07-18' : 1,
            'nostokopname' => $isPositif ? 'AAAA01' : 1,
            'jenisstokopname' =>  $isPositif ? 'jenis' : 1,
            'keterangan_opname' => $isPositif ? 'keterangan' : 1,
            'totalharga_fisik' => $isPositif ? 120000 : 'Seratus lima puluh ribu',
            'totalharga_sistem' => $isPositif ? 120000 : 'Seratus lima puluh ribu',
            'pegmengetahui_id' => $isPositif ? 1 : "PENGHUNU",
            'additional_data' => $isPositif ? '[{"test": "data"}]' : 12,
            'created_date' => $isPositif ? '2022-07-18' : 1,
            'created_by' => $isPositif ? 1 : "admin",
            'is_deleted' => $isPositif ? false : 'false',
            'is_active' =>  $isPositif ? true : 'true'
        ];
    }
}