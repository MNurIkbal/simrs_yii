<?php


class DashboardApiCest
{
    public function _before(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIiwianRpIjoiYWI0YWUxNmJjZGUzNDM3MGFiNjgzODM4NjhmNzNmYmU2YzYyMDUzNmVmZDQ4YzM1MDYzN2Q4OWMyYmMwNTM1YiIsImlzX21vYmlsZSI6MCwiaXNfYWxsX2V4cGVydGlzZV9sYWIiOnRydWUsInNpZ25hdHVyZV9wYXRoIjoiNjY1LnBuZyJ9.iGI5Qk-3NO-q5E6M56mLzF81bkQItqeghsqd6-_xp2o');
        $I->haveHttpHeader('Accept', 'application/json');
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
    }

    // tests function Index
    public function testIndex(ApiTester $I)
    {
        $I->sendGET('dashboard');
        $I->seeResponseCodeIs(200);
    }

    /* tests DashboardController@actionCreateAntrianV2
    -- (+) Case Pasien Umum Berhasil **/
    public function testCreateAntrianV2PasienUmum(ApiTester $I)
    {
        $polireguler_id = $I->grabFromDatabase('lookup_m', 'lookup_id', [
            'lookup_type' => 'jenis_antrian',
            'lookup_value' => 'Pasien Lama BPJS'
        ]);

        $I->sendPOST('dashboard/create-antrian-v2', [
            'carabayar_id' => '5', // umum
            'ruangan_id' => '5', // pendaftaran rajal
            'jenisantrian_id' => '177', // jenis pendaftaran
            'statuspasien' => '310', // pasien baru
            'antrian_jenis_id' => $polireguler_id, // lookup_id poli executive
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Proses Berhasil!',
        ]);
    }

    /* tests DashboardController@actionCreateAntrianV2
    -- (+) Case Pasien Umum dari pendaftaran executive Berhasil **/
    public function testCreateAntrianV2PasienUmumPendaftaranExecutive(ApiTester $I)
    {
        $poliexecutive_id = $I->grabFromDatabase('lookup_m', 'lookup_id', [
            'lookup_type' => 'jenis_antrian',
            'lookup_value' => 'Pasien Poli Executive'
        ]);

        $I->sendPOST('dashboard/create-antrian-v2', [
            'carabayar_id' => '5', // umum
            'ruangan_id' => '5', // pendaftaran rajal
            'jenisantrian_id' => '177', // jenis pendaftaran
            'statuspasien' => '310', // pasien baru
            'antrian_jenis_id' => $poliexecutive_id, // lookup_id poli executive
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'response' => ['message' => 'Proses Berhasil!'],
        ]);
    }

    /* tests DashboardController@actionCreateAntrianV2
    -- (+) Case Pasien BPJS berhasil **/
    public function testCreateAntrianV2PasienBpjs(ApiTester $I)
    {
        $polireguler_id = $I->grabFromDatabase('lookup_m', 'lookup_id', [
            'lookup_type' => 'jenis_antrian',
            'lookup_value' => 'Pasien Lama BPJS'
        ]);

        $I->sendPOST('dashboard/create-antrian-v2', [
            'carabayar_id' => '6', // bpjs
            'ruangan_id' => '5', // pendaftaran rajal
            'jenisantrian_id' => '177', // jenis pendaftaran
            // 'statuspasien' => '310', // pasien baru untuk bpjs tidak menggunakan ini
            'antrian_jenis_id' => $polireguler_id, // lookup_id poli executive
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'response' => [
                'message' => 'Proses Berhasil!',
                'data' => ['cetak' => ['is_bpjs' => true]]
            ],
        ]);
    }

    /* tests DashboardController@actionCreateAntrianV2 
    -- (-) Case Payload Kosong **/
    public function testCreateAntrianV2EmptyPayload(ApiTester $I)
    {
        $I->sendPOST('dashboard/create-antrian-v2');

        $I->seeResponseCodeIs(500);
        $I->seeResponseContainsJson([    // Check expected fields
            'response' => ['message' => 'Data Tidak Ditemukan'],
        ]);
    }

    /* tests DashboardController@actionCreateAntrianV2 
    -- (-) Case Payload jenisantrian_id null **/
    public function testCreateAntrianV2WithoutJenisantrianId(ApiTester $I)
    {
        $I->sendPOST('dashboard/create-antrian-v2', [
            'carabayar_id' => '5', // umum
            'ruangan_id' => '5', // pendaftaran rajal
            'statuspasien' => '310', // pasien baru
            'antrian_jenis_id' => '2121', // lookup_id poli executive
        ]);

        $I->seeResponseCodeIs(500);
        $I->seeResponseContainsJson([
            'response' => ['message' => 'Jenis Antrian Tidak Ditemukan'],
        ]);
    }

    /* tests DashboardController@actionCreateAntrianV2
    -- (-) Case Konfig Antrian Not Found **/
    public function testCreateAntrianV2KonfigAntrianNotFound(ApiTester $I)
    {
        $I->sendPOST('dashboard/create-antrian-v2', [
            'carabayar_id' => '6', // umum
            'ruangan_id' => '5', // pendaftaran rajal
            'jenisantrian_id' => '177', // jenis pendaftaran
            'statuspasien' => '310', // pasien baru
            'antrian_jenis_id' => '2121', // lookup_id poli executive
        ]);

        $I->seeResponseCodeIs(500);
        $I->seeResponseContainsJson([
            'response' => ['message' => 'Konfig Antrian Tidak Ditemukan'],
        ]);
    }

    /* tests DashboardController@actionCreateAntrianV2
    -- (-) Case Model failed validation **/
    public function testCreateAntrianV2ModelAntrianFailedValidation(ApiTester $I)
    {
        $I->sendPOST('dashboard/create-antrian-v2', [
            'carabayar_id' => '26', // undefined
            'ruangan_id' => 'asdasdsdasdasdasdasd', // pendaftaran rajal
            'jenisantrian_id' => '177', // jenis pendaftaran
            'statuspasien' => '310', // pasien baru
            'antrian_jenis_id' => '2121', // lookup_id poli executive
        ]);

        $I->seeResponseCodeIs(500);
        $I->seeResponseContainsJson([
            'response' => ['message' => 'Gagal Validasi Antrian'],
        ]);
    }
}
