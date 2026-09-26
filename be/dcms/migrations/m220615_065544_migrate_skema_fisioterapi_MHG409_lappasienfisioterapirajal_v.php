<?php

use yii\db\Migration;

/**
 * Class m220615_065544_migrate_skema_fisioterapi_MHG409_lappasienfisioterapirajal_v
 */
class m220615_065544_migrate_skema_fisioterapi_MHG409_lappasienfisioterapirajal_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."jadwalterapifisio_t" 
            ADD COLUMN IF NOT EXISTS "soapfisioterapi_id" int4;
            ');

        $this->execute('DROP VIEW if exists public.lappasienfisioterapirajal_v;');
        $this->execute("
            CREATE VIEW \"public\".\"lappasienfisioterapirajal_v\" AS
            SELECT programterapi_t.pendaftaran_id,
            programterapi_t.pasien_id,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_pendaftaran,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            look_jeniskelamin.jeniskelamin_kode,
            dokterdpjp.pegawai_id AS dokterperujuk_id,
            dokterdpjp.nama_pegawai AS dokterperujuk_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            terapis.terapis_id,
            terapis.nama_pegawai AS terapis_nama,
            programterapi_t.frekuensi,
            programterapi_t.realisasi,
            programterapi_t.sisa,
            programterapi_t.ketidakhadiran AS jumlah_ketidakhadiran,
            programterapi_t.status_program_fisio AS status_program_fisio_id,
            look_status_program_fisio.lookup_name AS status_program_fisio_nama,
            jadwalterapifisio_t.tgl_realisasi,
            jadwalterapifisio_t.tgl_penjadwalan_awal,
            jadwalterapifisio_t.tgl_penjadwalan_akhir,
            jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
            jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama,
            array_to_string(array_agg(daftartindakan_m.daftartindakan_id), ','::text) AS terapi_id,
            array_to_string(array_agg(daftartindakan_m.daftartindakan_nama), ','::text) AS terapi_nama
            FROM ((((((((((((((programterapi_t
            JOIN ( SELECT a.programterapi_id,
            a.daftartindakan_id
            FROM programterapidetail_t a) programterapidetail_t ON ((programterapi_t.programterapi_id = programterapidetail_t.programterapi_id)))
            LEFT JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
            FROM daftartindakan_m a) daftartindakan_m ON ((programterapidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            JOIN ( SELECT a.pendaftaran_id,
            a.ruangan_id
            FROM pendaftaran_t a) pendaftaran_t ON ((programterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            JOIN ( SELECT a.programterapi_id,
            a.soapfisioterapi_id,
            a.tgl_penjadwalan_awal,
            a.tgl_penjadwalan_akhir,
            a.tgl_realisasi
            FROM jadwalterapifisio_t a) jadwalterapifisio_t ON ((programterapi_t.programterapi_id = jadwalterapifisio_t.programterapi_id)))
            LEFT JOIN ( SELECT a.soapfisioterapi_id,
            a.terapis_id,
            a.programterapi_id
            FROM soapfisioterapi_t a) soapfisioterapi_t ON ((jadwalterapifisio_t.soapfisioterapi_id = soapfisioterapi_t.soapfisioterapi_id)))
            JOIN ( SELECT pt.pasienmasukpenunjang_id,
            pt.pendaftaran_id,
            pt.programterapi_id,
            pt.tglmasukpenunjang,
            pt.ruangan_id
            FROM pasienmasukpenunjang_t pt
            WHERE (pt.programterapi_id IS NOT NULL)) pasienmasukpenunjang_t ON ((programterapi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.jeniskelamin,
            a.tanggal_lahir,
            a.no_rekam_medik,
            a.alamat_pasien
            FROM pasien_m a) pasien_m ON ((programterapi_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.lookup_id,
            a.lookup_name AS jeniskelamin_nama,
            a.lookup_kode AS jeniskelamin_kode
            FROM lookup_m a) look_jeniskelamin ON (((pasien_m.jeniskelamin)::integer = look_jeniskelamin.lookup_id)))
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dokterdpjp ON ((programterapi_t.dokterperujuk_id = dokterdpjp.pegawai_id)))
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT a.pegawai_id AS terapis_id,
            a.nama_pegawai
            FROM pegawai_m a) terapis ON ((soapfisioterapi_t.terapis_id = terapis.terapis_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_status_program_fisio ON (((programterapi_t.status_program_fisio)::integer = look_status_program_fisio.lookup_id)))
            JOIN ( SELECT a.daftartindakan_id,
            a.jenispemeriksaanfisio_id,
            a.pemeriksaanfisio_nama
            FROM pemeriksaanfisio_m a) pemeriksaanfisio_m ON ((programterapidetail_t.daftartindakan_id = pemeriksaanfisio_m.daftartindakan_id)))
            JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
            FROM jenispemeriksaanfisio_m a) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
            GROUP BY programterapi_t.pendaftaran_id, programterapi_t.pasien_id, pasienmasukpenunjang_t.tglmasukpenunjang, pasien_m.nama_pasien, pasien_m.no_rekam_medik, look_jeniskelamin.jeniskelamin_kode, dokterdpjp.pegawai_id, dokterdpjp.nama_pegawai, ruangan_m.ruangan_id, ruangan_m.ruangan_nama, terapis.terapis_id, terapis.nama_pegawai, programterapi_t.frekuensi, programterapi_t.realisasi, programterapi_t.sisa, programterapi_t.ketidakhadiran, programterapi_t.status_program_fisio, look_status_program_fisio.lookup_name, jadwalterapifisio_t.tgl_realisasi, jadwalterapifisio_t.tgl_penjadwalan_awal, jadwalterapifisio_t.tgl_penjadwalan_akhir, jenispemeriksaanfisio_m.jenispemeriksaanfisio_id, jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama
            ORDER BY jadwalterapifisio_t.tgl_penjadwalan_akhir
            ;");
        $this->execute('
            ALTER TABLE public.lappasienfisioterapirajal_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220615_065544_migrate_skema_fisioterapi_MHG409_lappasienfisioterapirajal_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220615_065544_migrate_skema_fisioterapi_MHG409_lappasienfisioterapirajal_v cannot be reverted.\n";

        return false;
    }
    */
}
