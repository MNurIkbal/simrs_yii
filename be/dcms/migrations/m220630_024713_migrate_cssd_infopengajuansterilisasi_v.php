<?php

use yii\db\Migration;

/**
 * Class m220630_024713_migrate_cssd_infopengajuansterilisasi_v
 */
class m220630_024713_migrate_cssd_infopengajuansterilisasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopengajuansterilisasi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopengajuansterilisasi_v\" AS
            SELECT cssd_t.cssd_id,
            cssd_t.ruanganasal_id,
            ruangan_asal.ruangan_nama AS ruanganasal_nama,
            cssd_t.ruangantujuan_id,
            ruangan_tujuan.ruangan_nama AS ruangantujuan_nama,
            instalasi_asal.instalasi_id AS instalasiasal_id,
            instalasi_asal.instalasi_nama AS instalasiasal_nama,
            instalasi_tujuan.instalasi_id AS instalasitujuan_id,
            instalasi_tujuan.instalasi_nama AS instalasitujuan_nama,
            cssd_t.tgl_pengajuan_sterilisasi,
            cssd_t.no_pengajuan_sterilisasi,
            cssd_t.status_cssd,
            look_status_cssd.lookup_name AS status_cssd_nama,
            cssdsterilisasidet_t.no_sterilisasi AS no_proses_sterilisasi,
            cssd_t.tgl_terima,
            cssdpenerimaanunit_t.peg_mengetahui_id,
            peg_mengetahui.nama_pegawai AS peg_mengetahui_nama,
            cssdpenerimaanunit_t.peg_menyetujui_id,
            peg_menyetujui.nama_pegawai AS peg_menyetujui_nama,
            cssd_t.tgl_terima_unit,
            cssd_t.peg_menerima_id,
            peg_menerima.nama_pegawai AS peg_menerima_nama,
            peg_batal.nama_pegawai AS peg_batal_nama,
            cssd_t.alasan_batal,
            cssd_t.tgl_batal
            FROM (((((((((((cssd_t
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_asal ON ((cssd_t.ruanganasal_id = ruangan_asal.ruangan_id)))
            LEFT JOIN ( SELECT a.peg_mengetahui_id,
            a.peg_menyetujui_id,
            a.cssd_id
            FROM cssdpenerimaanunit_t a
            WHERE ((a.is_deleted = false) AND (a.is_active = true))) cssdpenerimaanunit_t ON ((cssdpenerimaanunit_t.cssd_id = cssd_t.cssd_id)))
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_tujuan ON ((cssd_t.ruangantujuan_id = ruangan_tujuan.ruangan_id)))
            JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_status_cssd ON ((cssd_t.status_cssd = look_status_cssd.lookup_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_asal ON ((ruangan_asal.instalasi_id = instalasi_asal.instalasi_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) peg_mengetahui ON ((cssdpenerimaanunit_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) peg_menyetujui ON ((cssdpenerimaanunit_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) peg_menerima ON ((cssd_t.peg_menerima_id = peg_menerima.pegawai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) peg_batal ON ((cssd_t.peg_batal_id = peg_batal.pegawai_id)))
            LEFT JOIN ( SELECT a.cssdsterilisasi_id,
            a.cssd_id,
            cssdsterilisasi_t.no_sterilisasi
            FROM (cssdsterilisasidet_t a
            JOIN ( SELECT b.cssdsterilisasi_id,
            b.no_sterilisasi
            FROM cssdsterilisasi_t b
            WHERE ((b.is_active = true) AND (b.is_deleted = false))) cssdsterilisasi_t ON ((a.cssdsterilisasi_id = cssdsterilisasi_t.cssdsterilisasi_id)))
            WHERE ((a.is_deleted = false) AND (a.is_active = true))) cssdsterilisasidet_t ON ((cssdsterilisasidet_t.cssd_id = cssd_t.cssd_id)))
            WHERE ((cssd_t.is_active = true) AND (cssd_t.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.infopengajuansterilisasi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220630_024713_migrate_cssd_infopengajuansterilisasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220630_024713_migrate_cssd_infopengajuansterilisasi_v cannot be reverted.\n";

        return false;
    }
    */
}
