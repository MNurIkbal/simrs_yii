<?php
use app\modules\v1\models\StokOpnameBarangDetail;

class StokOpnameBarangDetailTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new StokOpnameBarangDetail();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testTableName() {
        $tableName = $this->model->tableSchema->name;
        $this->assertEquals($tableName, 'stokopnamebarangdetail_t');
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
        $this->model->attributes = $this->setPayloadRequired(true);
        $this->assertTrue($this->model->validate());
    }

    public function testRequiredNegatif() {
        $this->model->attributes = $this->setPayloadRequired(false);
        $this->assertFalse($this->model->validate());
    }

    private function setPayloadRequired($isPositif = true)
    {
        return [
            'barang_id' => $isPositif ? 1 : null, 
            'volume_fisik' => $isPositif ? 10000 : null,
            'volume_sistem' => $isPositif ? 10000 : null, 
            'tglkadaluarsa' => $isPositif ? '2022-07-18' : null, 
            'kondisibarang' => $isPositif ? 'Bagus' : null,
            'tglperiksafisik' => $isPositif ? '2022-07-18' : null,
        ];
    }

    private function setPayloadTipeData($isPositif = true) {
        return [
            'stokopnamebarangdetail_id' => $isPositif ? 1 : "A",
            'formsobarangdetail_id' => $isPositif ? 1 : "A",
            'satuankecil_id' => $isPositif ? 1 : "BOX",
            'stokopnamebarang_id' => $isPositif ? 1 : "A",
            'barang_id' => $isPositif ? 1 : "barang",
            'volume_fisik' => $isPositif ? 1000 : "Seribu",
            'volume_sistem' => $isPositif ? 1000 : "Seribu",
            'hargasatuan' => $isPositif ? 1000 : "Seribu",
            'jumlahharga' => $isPositif ? 1000 : "Seribu",
            'harganetto' => $isPositif ? 1000 : "Seribu",
            'jumlahnetto' => $isPositif ? 1000 : "Seribu",
            'tglkadaluarsa' => $isPositif ? '2022-07-18' : 1,
            'kondisibarang' => $isPositif ? 'OKE': 1,
            'tglperiksafisik' => $isPositif ? '2022-07-18' : 1,
            'jmlselisihstok' => $isPositif ? 1000 : "Seribu",
            'revisi_stok' => $isPositif ? 1000 : "Seribu",
            'additional_data' => $isPositif ? '[{"test": "data"}]' : 12,
            'created_date' => $isPositif ? '2022-07-18' : 1,
            'created_by' => $isPositif ? 1 : "admin",
            'is_deleted' => $isPositif ? false : 'false',
            'is_active' => $isPositif ? true : 'true'
        ];
    }
}