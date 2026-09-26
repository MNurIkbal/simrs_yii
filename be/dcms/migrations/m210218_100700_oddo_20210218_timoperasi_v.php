<?php

use yii\db\Migration;

/**
 * Class m210218_100700_oddo_20210218_timoperasi_v
 */
class m210218_100700_oddo_20210218_timoperasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('DROP VIEW IF exists "public"."timoperasi_v";');

    $this->execute("
        CREATE VIEW \"public\".\"timoperasi_v\" AS  SELECT timoperasi_t.timoperasi_id,
    timoperasi_t.pasienmasukpenunjang_id,
    timoperasi_t.inpostoperasi_id,
    operasi_m.operasi_id,
    operasi_m.operasi_nama,
    golonganoperasi_m.golonganoperasi_id AS jenisoperasi_id,
    golonganoperasi_m.golonganoperasi_nama AS jenisoperasi_nama,
    inpostoperasidetail_t.daftartindakan_id AS klasifikasioperasi_id,
    daftartindakan_m.daftartindakan_nama AS klasifikasioperasi_nama,
    jasa.daftartindakan_nama AS nama_jasa,
    pegawai_m.nama_pegawai,
    fgetnamalookup(timoperasi_t.posisi_tim) AS posisi_tim,
    inpostoperasidetail_t.is_cyto,
    inpostoperasidetail_t.is_penyulit,
    inpostoperasidetail_t.harga AS harga_operasi,
    timoperasi_t.persentase,
    timoperasi_t.harga AS harga_persentase,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    pegawai_m.pegawai_id AS dokter_id,
    timoperasi_t.posisi_tim AS posisi_operasi,
    pegawai_m.pegawai_id,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    login_pemakai.nama_pegawai AS created_by
   FROM timoperasi_t
     JOIN inpostoperasi_t ON timoperasi_t.inpostoperasi_id = inpostoperasi_t.inpostoperasi_id AND timoperasi_t.pasienmasukpenunjang_id = timoperasi_t.pasienmasukpenunjang_id
     JOIN inpostoperasidetail_t ON inpostoperasi_t.inpostoperasi_id = inpostoperasidetail_t.inpostoperasi_id AND timoperasi_t.pegawai_id = inpostoperasidetail_t.dokter_id
     JOIN tindakanoperasi_mp ON timoperasi_t.posisi_tim = tindakanoperasi_mp.timoperasi_id
     JOIN daftartindakan_m jasa ON tindakanoperasi_mp.daftartindakan_id = jasa.daftartindakan_id
     LEFT JOIN operasi_m ON inpostoperasidetail_t.operasi_id = operasi_m.operasi_id
     LEFT JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
     LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
     LEFT JOIN golonganoperasi_m jenisoperasi ON inpostoperasidetail_t.golonganoperasi_id = jenisoperasi.golonganoperasi_id
     LEFT JOIN pegawai_m ON timoperasi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN daftartindakan_m ON inpostoperasidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m_1.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m pegawai_m_1 ON loginpemakai_k.pegawai_id = pegawai_m_1.pegawai_id) login_pemakai ON timoperasi_t.created_by = login_pemakai.loginpemakai_id;");
    
    $this->execute('ALTER TABLE "public"."timoperasi_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210218_100700_oddo_20210218_timoperasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210218_100700_oddo_20210218_timoperasi_v cannot be reverted.\n";

        return false;
    }
    */
}
