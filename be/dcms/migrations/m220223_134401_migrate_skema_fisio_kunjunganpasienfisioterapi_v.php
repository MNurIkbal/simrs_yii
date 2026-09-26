<?php

use yii\db\Migration;

/**
 * Class m220223_134401_migrate_skema_fisio_kunjunganpasienfisioterapi_v
 */
class m220223_134401_migrate_skema_fisio_kunjunganpasienfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          ALTER TABLE "public"."soapfisioterapi_t" 
          ADD COLUMN IF NOT EXISTS "is_paket" bool DEFAULT false;
        ');

        $this->execute('
          ALTER TABLE "public"."programterapi_t" 
          ADD COLUMN IF NOT EXISTS "is_paket" bool DEFAULT false;
        ');

        $this->execute('DROP VIEW if exists public.kunjunganpasienfisioterapi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"kunjunganpasienfisioterapi_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            soapfisioterapi_t.parent_pendaftaran_id,
            soapfisioterapi_t.parent_pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.status_periksa AS status_periksa_id,
            dokterdpjp.pegawai_id AS dokterperujuk_id,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.nama_pasien,
            pasien_m.jeniskelamin AS jeniskelamin_id,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin_nama,
            pasien_m.tanggal_lahir,
            pasien_m.no_rekam_medik,
            programterapidetail_t.terapi_nama,
            soapfisioterapi_t.terapis_nama,
            dokterdpjp.nama_pegawai AS dokterperujuk_nama,
            soapfisioterapi_t.tindakansudahbayar_id,
            pendaftaran_t.status_bayar AS statusbayar_id,
            fgetnamalookup(pendaftaran_t.status_bayar) AS statusbayar_nama,
            programterapi_t.programterapi_id,
            pendaftaran_t.no_pendaftaran,
            pasien_m.alamat_pasien,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.umur,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            '-'::text AS kamar,
            '-'::text AS dokterdpjp_nama,
            programterapi_t.status_fisio AS status_fisio_id,
            look_status_fisio.lookup_name AS status_fisio_nama,
            soapfisioterapi_t.is_paket,
            soapfisioterapi_t.is_edit,
            programterapi_t.status_program_fisio AS status_program_fisio_id,
            look_status_program_fisio.lookup_name AS status_program_fisio_nama
            FROM ((((((((((((pendaftaran_t
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.jeniskelamin,
            a.tanggal_lahir,
            a.no_rekam_medik,
            a.alamat_pasien
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ( SELECT pt.pasienmasukpenunjang_id,
            pt.pendaftaran_id,
            pt.programterapi_id
            FROM pasienmasukpenunjang_t pt
            WHERE (pt.programterapi_id IS NOT NULL)) pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.programterapi_id,
            a.pendaftaran_id,
            a.pasienmasukpenunjang_id,
            a.dokterperujuk_id,
            a.status_fisio,
            a.status_program_fisio
            FROM programterapi_t a) programterapi_t ON ((pasienmasukpenunjang_t.programterapi_id = programterapi_t.programterapi_id)))
            LEFT JOIN ( SELECT a.programterapi_id,
            string_agg((dm.daftartindakan_nama)::text, ','::text) AS terapi_nama,
            string_agg(DISTINCT (terapis.nama_pegawai)::text, ','::text) AS terapis_nama,
            string_agg(DISTINCT (dokterperujuk.nama_pegawai)::text, ','::text) AS dokterperujuk_nama
            FROM (((programterapidetail_t a
            LEFT JOIN daftartindakan_m dm ON ((dm.daftartindakan_id = a.daftartindakan_id)))
            LEFT JOIN pegawai_m terapis ON ((terapis.pegawai_id = a.terapis_id)))
            LEFT JOIN pegawai_m dokterperujuk ON ((dokterperujuk.pegawai_id = a.dokterperujuk_id)))
            WHERE (a.is_deleted = false)
            GROUP BY a.programterapi_id) programterapidetail_t ON ((programterapi_t.programterapi_id = programterapidetail_t.programterapi_id)))
            LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
            FROM carabayar_m a) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
            FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dokterdpjp ON ((pendaftaran_t.pegawai_id = dokterdpjp.pegawai_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.terapis_id,
            terapis.nama_pegawai AS terapis_nama,
            a.programterapi_id,
            parent.pendaftaran_id AS parent_pendaftaran_id,
            parent.pasienmasukpenunjang_id AS parent_pasienmasukpenunjang_id,
            parent.is_paket,
            parent_bayar.tindakansudahbayar_id,
            a.is_edit,
            a.is_deleted
            FROM (((soapfisioterapi_t a
            LEFT JOIN pegawai_m terapis ON ((terapis.pegawai_id = a.terapis_id)))
            LEFT JOIN programterapi_t parent ON ((parent.programterapi_id = a.programterapi_id)))
            LEFT JOIN tindakanpelayanan_t parent_bayar ON (((parent.pasienmasukpenunjang_id = parent_bayar.pasienmasukpenunjang_id) AND (parent.pendaftaran_id = parent_bayar.pendaftaran_id))))) soapfisioterapi_t ON (((pendaftaran_t.pendaftaran_id = soapfisioterapi_t.pendaftaran_id) AND (soapfisioterapi_t.is_deleted = false) AND (soapfisioterapi_t.is_edit = false))))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_status_fisio ON (((programterapi_t.status_fisio)::integer = look_status_fisio.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_status_program_fisio ON (((programterapi_t.status_program_fisio)::integer = look_status_program_fisio.lookup_id)))
            WHERE (pendaftaran_t.instalasi_id = ( SELECT lookuptransaksi_m.kode_id
            FROM lookuptransaksi_m
            WHERE ((lookuptransaksi_m.kode_transaksi)::text = 'instalasi_fisio'::text)))
            ;");
        $this->execute('
            ALTER TABLE public.kunjunganpasienfisioterapi_v OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220223_134401_migrate_skema_fisio_kunjunganpasienfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220223_134401_migrate_skema_fisio_kunjunganpasienfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
