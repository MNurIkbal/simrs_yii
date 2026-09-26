<?php
use app\modules\v1\models\InfoStokOpnameView;

class InfoStokOpnameTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new InfoStokOpnameView();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'infostokopname_v');
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'instalasi_id' => 1,
            'instalasi_nama' => 'tes',
            'ruangan_id' => 1,
            'ruangan_nama' => 'tes',
            'formulirstokopname_id' => 1,
            'tglformulir' => '2022-07-18',
            'noformulir' => '2as4',
            'stokopname_id' => 1,
            'tglstokopname' => '2022-07-18',
            'nostokopname' => '2r4f',
            'isstokawal' => true,
            'jenisstokopname' => 'tes',
            'keterangan_opname' => 'tes',
            'petugas1_id' => 1,
            'petugas1_nip' => 'a4d6',
            'petugas1_noidentitas' => 'tes',
            'petugas1_gelardepan' => 'tes',
            'petugas1_nama' => 'tes',
            'petugas1_gelarbelakang' => 'tes',
            'petugas2_id' => 1,
            'petugas2_nip' => 'tes',
            'petugas2_noidentitas' => 'tes',
            'petugas2_gelardepan' => 'tes',
            'petugas2_nama' => 'tes',
            'petugas2_gelarbelakang' => 'tes',
            'pegawaimengetahui_id' => 1,
            'pegawaimengetahui_nip' => 'tes',
            'pegawaimengetahui_noidentitas' => 'tes',
            'pegawaimengetahui_gelardepan' => 'tes',
            'pegawaimengetahui_nama' => 'tes',
            'pegawaimengetahui_gelarbelakang' => 'tes',
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'instalasi_id' => 'tes',
            'formulirstokopname_id' => 'tes',
            'ruangan_id' => 'tes',
            'stokopname_id' => 'tes',
            'petugas2_id' => 'tes',
            'petugas1_id' => 'tes',
            'pegawaimengetahui_id' => 'tes',
            'isstokawal' => 'tes',
            'instalasi_nama' => 1,
            'ruangan_nama' => 1,
            'tglformulir' => 2,
            'noformulir' => 2,
            'tglstokopname' => 2,
            'nostokopname' => 2,
            'jenisstokopname' => 1,
            'keterangan_opname' => 1,
            'petugas1_nip' => 'a4d6',
            'petugas1_noidentitas' => 1,
            'petugas1_gelardepan' => 1,
            'petugas1_nama' => 1,
            'petugas1_gelarbelakang' => 1,
            'petugas2_nip' => 1,
            'petugas2_noidentitas' => 1,
            'petugas2_gelardepan' => 1,
            'petugas2_nama' => 1,
            'petugas2_gelarbelakang' => 1,
            'pegawaimengetahui_nip' => 1,
            'pegawaimengetahui_noidentitas' => 1,
            'pegawaimengetahui_gelardepan' => 1,
            'pegawaimengetahui_nama' => 1,
            'pegawaimengetahui_gelarbelakang' => 1,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}