<?php

use yii\db\Migration;

/**
 * Class m220509_012142_migrate_skema_fisio_lapkunjunganpasienfisiorj_v
 */
class m220509_012142_migrate_skema_fisio_lapkunjunganpasienfisiorj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.lapkunjunganpasienfisiorj_v;');
        $this->execute("
            CREATE VIEW \"public\".\"lapkunjunganpasienfisiorj_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.pasien_id,
            pasien_m.jeniskelamin AS jeniskelamin_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            look_jenkel.lookup_name AS jeniskelamin_nama,
            look_jenkel.lookup_kode AS jeniskelamin_kode,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pasien_m.alamat_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            dokterdpjp.pegawai_id AS dokterdpjp_id,
            dokterdpjp.nama_pegawai AS dokterdpjp_nama,
            pendaftaran_t.status_periksa AS status_periksa_id,
            look_status_periksa.lookup_name AS status_periksa_nama,
            pasien_m.no_telepon_pasien,
            programterapi_t.status_fisio AS status_fisio_id,
            look_status_fisio.lookup_name AS status_fisio_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama
            FROM ((((((((((((((((pendaftaran_t
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.jeniskelamin,
            a.tanggal_lahir,
            a.no_rekam_medik,
            a.alamat_pasien,
            a.no_telepon_pasien
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
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
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name,
            a.lookup_kode
            FROM lookup_m a) look_jenkel ON (((pasien_m.jeniskelamin)::integer = look_jenkel.lookup_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_status_periksa ON (((pendaftaran_t.status_periksa)::integer = look_status_periksa.lookup_id)))
            WHERE ((look_status_periksa.lookup_id <> 628) AND (pendaftaran_t.instalasi_id = ( SELECT lookuptransaksi_m.kode_id
            FROM lookuptransaksi_m
            WHERE ((lookuptransaksi_m.kode_transaksi)::text = 'instalasi_fisio'::text))))
            ;");
        $this->execute('
            ALTER TABLE public.lapkunjunganpasienfisiorj_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220509_012142_migrate_skema_fisio_lapkunjunganpasienfisiorj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220509_012142_migrate_skema_fisio_lapkunjunganpasienfisiorj_v cannot be reverted.\n";

        return false;
    }
    */
}
