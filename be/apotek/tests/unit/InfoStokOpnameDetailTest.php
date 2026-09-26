<?php
use app\modules\v1\models\InfoStokOpnameDetailView;

class InfoStokOpnameDetailTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new InfoStokOpnameDetailView();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'infostokopnamedetail_v');
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'instalasi_id' => 1,
            'obatalkes_id' => 1,
            'ruangan_id' => 1,
            'formulirstokopname_id' => 1,
            'stokopname_id' => 1,
            'petugas1_id' => 1,
            'petugas2_id' => 1,
            'pegawaimengetahui_id' => 1,
            'jenisstokopname' => 'tes',
            'tglformulir' => '2022-07-18',
            'tglstokopname' => '2022-07-18',
            'instalasi_nama' => 'tes',
            'ruangan_nama' => '2as4',
            'petugas1_nama' => '2r4f',
            'petugas2_nama' => 'tes',
            'pegawaimengetahui_nama' => 'tes',
            'petugas1_nip' => 'a4d6',
            'petugas2_nip' => 'tes',
            'pegawaimengetahui_nip' => 'tes',
            'pegawaimengetahui_noidentitas' => 'tes',
            'petugas1_noidentitas' => 'tes',
            'petugas1_gelardepan' => 'tes',
            'pegawaimengetahui_gelardepan' => 'tes',
            'petugas1_gelarbelakang' => 'tes',
            'petugas2_noidentitas' => 'tes',
            'petugas2_gelardepan' => 'tes',
            'petugas2_gelarbelakang' => 'tes',
            'pegawaimengetahui_gelarbelakang' => 'tes',
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'instalasi_id' => 'tes',
            'obatalkes_id' => 'tes',
            'ruangan_id' => 'tes',
            'formulirstokopname_id' => 'tes',
            'stokopname_id' => 'tes',
            'petugas1_id' => 'tes',
            'petugas2_id' => 'tes',
            'pegawaimengetahui_id' => 'tes',
            'tglformulir' => 2,
            'tglstokopname' => 2,
            'instalasi_nama' => 2,
            'jenisstokopname' => 2,
            'ruangan_nama' => 1,
            'petugas1_nama' => 1,
            'petugas2_nama' => 1,
            'pegawaimengetahui_nama' => 1,
            'petugas1_nip' => 'a4d6',
            'petugas2_nip' => 1,
            'pegawaimengetahui_nip' => 1,
            'pegawaimengetahui_noidentitas' => 1,
            'petugas1_noidentitas' => 1,
            'petugas1_gelardepan' => 1,
            'pegawaimengetahui_gelardepan' => 1,
            'petugas1_gelarbelakang' => 1,
            'petugas2_noidentitas' => 1,
            'petugas2_gelardepan' => 1,
            'petugas2_gelarbelakang' => 1,
            'pegawaimengetahui_gelarbelakang' => 1,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}