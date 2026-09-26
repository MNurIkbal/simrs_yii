<?php
use app\modules\v1\models\PurchaseRequisitionBarangDetail;

class PurchaseRequisitionBarangDetailTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new PurchaseRequisitionBarangDetail();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'purchasereqbrgdetail_t');
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'barang_id' => 1,
            'purchasereqbrg_id' => 1,
            'satuan_id' => 1,
            'satuankonversi_id' => 1,
            'qty_input' => 60,
            'qty_konversi' => 70,
            'catatan' => "TESS",
            'status' => 712,
            'alasan' => "TESS",
            'additional_data' => "TESS",
            'created_date' => "2022-07-21",
            'is_deleted' => false,
            'is_active' => true,
            'qty_pr' => 2,
            'qty_saatini' => 40,
            'stok_gudang' => 90,
            'stok_ruanganlain' => 90,
            'qty_outstanding' => 80,
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }
}