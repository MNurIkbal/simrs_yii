<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use app\modules\v1\models\PesanObatAlkes;

class PesanObatAlkesTest extends \Codeception\Test\Unit {
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before() {
        $this->model = new PesanObatAlkes();
    }

    protected function _after() {
        $this->model = null;
    }

    public function testTableName() {
        $tableName = $this->model->tableSchema->name;
        $this->assertEquals($tableName, 'pesanobatalkes_t');
    }

    public function testTipeDataPositif() {
        $this->model->attributes = $this->setPayloadTipeData(true);
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif() {
        $this->model->attributes = $this->setPayloadTipeData(false);
        $this->assertFalse($this->model->validate());
    }
    
    private function setPayloadTipeData($isPositif = true) {
        return [
            'ruangan_id' => $isPositif ? 1 : "String",
            'mutasiobatruangan_id' => $isPositif ? 1 : "String",
            'ruanganpemesan_id' => $isPositif ? 1 : "String",
            'pegawaipemesan_id' => $isPositif ? 1 : "String",
            'pegawaimengetahui_id' => $isPositif ? 1 : "String",
            'created_by' => $isPositif ? 1 : "String",
            'modified_count' => $isPositif ? 1 : "String",
            'last_modified_by' => $isPositif ? 1 : "String",
            'deleted_by' => $isPositif ? 1 : "String",
            'status_verifikasi' => $isPositif ? 1 : "String",
            'nopemesanan' => $isPositif ? "String" : 2022, 
            'keterangan_pesan' => $isPositif ? "String" : 2022, 
            'additional_data' => $isPositif ? "String" : 2022
        ];
    }
}
