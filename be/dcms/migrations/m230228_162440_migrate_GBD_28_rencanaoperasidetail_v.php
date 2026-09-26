<?php

use yii\db\Migration;

/**
 * Class m230228_162440_migrate_GBD_28_rencanaoperasidetail_v
 */
class m230228_162440_migrate_GBD_28_rencanaoperasidetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."rencanaoperasidetail_v";');
        $this->execute("
        CREATE OR REPLACE VIEW public.rencanaoperasidetail_v
        AS SELECT rencanaoperasi_t.rencanaoperasi_id,
            rencanaoperasi_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            rencanaoperasi_t.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t.no_orderkeunitlain,
            rencanaoperasi_t.tgl_permintaan,
            pasienkirimkeunitlain_t.pegawai_id AS dr_perujuk_id,
            dr_perujuk.nama_pegawai AS dr_perujuk,
            rencanaoperasi_t.dr_operator_id,
            dr_operator.nama_pegawai AS dr_operator,
            rencanaoperasi_t.dr_anastesi_id,
            dr_anastesi.nama_pegawai AS dr_anastesi,
            rencanaoperasi_t.jam_rencana_mulai,
            rencanaoperasi_t.jam_rencana_selesai,
            pasienkirimkeunitlain_t.catatan_dokterpengirim,
            permintaankepenunjang_t.permintaankepenunjang_id,
            permintaankepenunjang_t.daftartindakan_id,
            permintaankepenunjang_t.tipepaket_id,
            golonganoperasi_m.golonganoperasi_nama,
            kegiatanoperasi_m.kegiatanoperasi_nama,
            operasi_m.operasi_nama,
            permintaankepenunjang_t.is_cyto,
            fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS status,
            app.nama_pegawai AS disetujui_oleh,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tanggal_disetujui,
            rencanaoperasi_t.created_date
        FROM rencanaoperasi_t
            JOIN pendaftaran_t ON rencanaoperasi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN pasienkirimkeunitlain_t ON rencanaoperasi_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
            JOIN pegawai_m dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
            JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
            LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
            LEFT JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
            LEFT JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
            LEFT JOIN operasi_m ON permintaankepenunjang_t.operasi_id = operasi_m.operasi_id
            LEFT JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
            LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
            LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
            LEFT JOIN loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
            LEFT JOIN pegawai_m app ON loginpemakai_k.pegawai_id = app.pegawai_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230228_162440_migrate_GBD_28_rencanaoperasidetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230228_162440_migrate_GBD_28_rencanaoperasidetail_v cannot be reverted.\n";

        return false;
    }
    */
}
