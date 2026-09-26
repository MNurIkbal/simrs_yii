<?php

use yii\db\Migration;

/**
 * Class m201125_085201_migrate_20201125_infoinpostoperasidetail_v
 */
class m201125_085201_migrate_20201125_infoinpostoperasidetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infoinpostoperasidetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoinpostoperasidetail_v\" AS  SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    inpostoperasidetail_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    golonganoperasi_m.golonganoperasi_nama,
    kegiatanoperasi_m.kegiatanoperasi_id,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    pasienmasukpenunjang_t.status_periksa,
    operasi_m.operasi_id,
    golonganoperasi_m.golonganoperasi_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id,
    jenisoperasi.golonganoperasi_nama AS jenis_operasi,
    pegawai_m.nama_pegawai AS nama_dokter,
    inpostoperasidetail_t.is_cyto,
    inpostoperasidetail_t.is_penyulit,
    inpostoperasidetail_t.harga,
    NULL::text AS tarif_satuan,
    NULL::text AS tarif_tindakan,
    inpostoperasidetail_t.dokter_id AS pegawai_id
   FROM pasienmasukpenunjang_t
     JOIN inpostoperasi_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id
     JOIN inpostoperasidetail_t ON inpostoperasi_t.inpostoperasi_id = inpostoperasidetail_t.inpostoperasi_id
     LEFT JOIN operasi_m ON inpostoperasidetail_t.operasi_id = operasi_m.operasi_id
     LEFT JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
     LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
     LEFT JOIN golonganoperasi_m jenisoperasi ON inpostoperasidetail_t.golonganoperasi_id = jenisoperasi.golonganoperasi_id
     LEFT JOIN pegawai_m ON inpostoperasidetail_t.dokter_id = pegawai_m.pegawai_id
     LEFT JOIN daftartindakan_m ON inpostoperasidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id;");
        
        $this->execute('ALTER TABLE "public"."infoinpostoperasidetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_085201_migrate_20201125_infoinpostoperasidetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_085201_migrate_20201125_infoinpostoperasidetail_v cannot be reverted.\n";

        return false;
    }
    */
}
