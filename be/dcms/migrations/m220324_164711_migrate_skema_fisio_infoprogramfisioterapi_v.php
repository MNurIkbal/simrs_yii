<?php

use yii\db\Migration;

/**
 * Class m220324_164711_migrate_skema_fisio_infoprogramfisioterapi_v
 */
class m220324_164711_migrate_skema_fisio_infoprogramfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."programterapi_t" 
            ADD COLUMN IF NOT EXISTS "is_paket" bool DEFAULT false;
          ');

        $this->execute('ALTER TABLE "public"."programterapidetail_t" 
          ADD COLUMN IF NOT EXISTS "is_paketfisio" bool DEFAULT false; 
          ');

        $this->execute('ALTER TABLE "public"."daftartindakan_m" 
            ADD COLUMN IF NOT EXISTS "is_paketfisio" bool DEFAULT false;
          ');

        $this->execute('ALTER TABLE "public"."programterapi_t" 
          ADD COLUMN IF NOT EXISTS "pasienkirimkeunitlain_id" int4;
          ');

        $this->execute('DROP VIEW if exists public.infoprogramfisioterapi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infoprogramfisioterapi_v\" AS
            SELECT programterapi_t.pendaftaran_id,
            programterapi_t.pasien_id,
            programterapi_t.programterapi_id,
            dok_perujuk.pegawai_id AS dokter_perujuk_id,
            dok_perujuk.nama_pegawai AS dokter_perujuk,
            look_jenkel.lookup_kode AS jenis_kelamin,
            programterapi_t.tgl_permintaan AS tgl_rujukan,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            programterapi_t.frekuensi,
            COALESCE(soapfisioterapi_t.jumlah, (0)::bigint) AS realisasi,
            COALESCE(((programterapi_t.frekuensi - COALESCE((soapfisioterapi_t.jumlah)::double precision, (0)::double precision)) - (COALESCE(jadwalterapifisio_t.jumlah_ketidakhadiran, (0)::bigint))::double precision), (0)::double precision) AS sisa,
            COALESCE(pembayaran.bayar, (0)::bigint) AS bayar,
            CASE
            WHEN ((pendaftaran_t.status_periksa)::integer = 433) THEN 'CLOSE'::text
            WHEN (programterapi_t.frekuensi = (soapfisioterapi_t.jumlah)::double precision) THEN 'CLOSE'::text
            ELSE 'OPEN'::text
            END AS status,
            pendaftaran_t.status_periksa AS status_periksa_id,
            lookup_status.lookup_name AS status_periksa_nama,
            programterapi_t.tipe_instalasi,
            kamarruangan_m.kamarruangan_nokamar AS kamar,
            soapfisioterapi_t.is_edit,
            programterapi_t.is_active,
            programterapi_t.is_paket,
            programterapi_t.status_program_fisio AS status_program_fisio_id,
            lookup_status_program_fisio.lookup_name AS status_program_fisio_nama,
            programterapi_t.pasienkirimkeunitlain_id,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            array_to_string(array_agg(programterapidetail_t.daftartindakan_id), ','::text) AS daftartindakan_id,
            array_to_string(array_agg(programterapidetail_t.daftartindakan_nama), ','::text) AS daftartindakan_nama,
            array_to_string(array_agg(programterapidetail_t.daftartindakan_nama), '<br>'::text) AS daftartindakan_nama_view,
            COALESCE(jadwalterapifisio_t.jumlah_ketidakhadiran, (0)::bigint) AS jumlah_ketidakhadiran
            FROM (((((((((((((programterapi_t
            JOIN ( SELECT a.programterapi_id,
            a.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            a.is_deleted
            FROM (programterapidetail_t a
            JOIN ( SELECT b.daftartindakan_id,
            b.daftartindakan_nama
            FROM daftartindakan_m b) daftartindakan_m ON ((a.daftartindakan_id = daftartindakan_m.daftartindakan_id)))) programterapidetail_t ON (((programterapi_t.programterapi_id = programterapidetail_t.programterapi_id) AND (programterapidetail_t.is_deleted = false))))
            LEFT JOIN ( SELECT count(a.soapfisioterapi_id) AS jumlah,
            a.pasien_id,
            pasienmasukpenunjang_t_1.programterapi_id,
            a.is_edit,
            a.is_deleted
            FROM (soapfisioterapi_t a
            JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((a.pendaftaran_id = pasienmasukpenunjang_t_1.pendaftaran_id)))
            GROUP BY a.pasien_id, pasienmasukpenunjang_t_1.programterapi_id, a.is_edit, a.is_deleted) soapfisioterapi_t ON (((programterapi_t.programterapi_id = soapfisioterapi_t.programterapi_id) AND (programterapi_t.pasien_id = soapfisioterapi_t.pasien_id) AND (soapfisioterapi_t.is_edit IS NOT TRUE) AND (soapfisioterapi_t.is_deleted IS NOT TRUE))))
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.tanggal_lahir,
            a.no_rekam_medik,
            a.jeniskelamin
            FROM pasien_m a) pasien_m ON ((programterapi_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dok_perujuk ON ((programterapi_t.dokterperujuk_id = dok_perujuk.pegawai_id)))
            LEFT JOIN ( SELECT pasienmasukpenunjang_t_1.programterapi_id,
            sum(
            CASE
            WHEN (tindakanpelayanan_t.tindakan = tindakanpelayanan_t.jml_bayar) THEN 1
            ELSE 0
            END) AS bayar
            FROM (pasienmasukpenunjang_t pasienmasukpenunjang_t_1
            JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id,
            tindakanpelayanan_t_1.is_deleted,
            count(tindakanpelayanan_t_1.tindakanpelayanan_id) AS tindakan,
            sum(
            CASE
            WHEN (tindakanpelayanan_t_1.tindakansudahbayar_id IS NOT NULL) THEN 1
            ELSE 0
            END) AS jml_bayar
            FROM tindakanpelayanan_t tindakanpelayanan_t_1
            WHERE ((tindakanpelayanan_t_1.is_deleted = false) AND (tindakanpelayanan_t_1.instalasi_id = ( SELECT lookuptransaksi_m.kode_id
            FROM lookuptransaksi_m
            WHERE ((lookuptransaksi_m.kode_transaksi)::text = 'instalasi_fisio'::text))))
            GROUP BY tindakanpelayanan_t_1.is_deleted, tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON ((pasienmasukpenunjang_t_1.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
            GROUP BY pasienmasukpenunjang_t_1.programterapi_id) pembayaran ON ((programterapi_t.programterapi_id = pembayaran.programterapi_id)))
            JOIN ( SELECT a.lookup_id,
            a.lookup_kode,
            a.lookup_name
            FROM lookup_m a) look_jenkel ON (((pasien_m.jeniskelamin)::integer = look_jenkel.lookup_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.status_periksa,
            a.carabayar_id
            FROM pendaftaran_t a) pendaftaran_t ON ((programterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
            FROM carabayar_m a) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.kamarruangan_id
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
            LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
            FROM lookup_m) lookup_status ON (((pendaftaran_t.status_periksa)::integer = lookup_status.lookup_id)))
            LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
            FROM lookup_m) lookup_status_program_fisio ON (((programterapi_t.status_program_fisio)::integer = lookup_status_program_fisio.lookup_id)))
            LEFT JOIN ( SELECT a.programterapi_id,
            count(a.status_kunjungan_fisio) AS jumlah_ketidakhadiran
            FROM jadwalterapifisio_t a
            WHERE (a.status_kunjungan_fisio = 1208)
            GROUP BY a.programterapi_id) jadwalterapifisio_t ON ((programterapi_t.programterapi_id = jadwalterapifisio_t.programterapi_id)))
            WHERE ((programterapi_t.tipe_instalasi)::text = '1'::text)
            GROUP BY programterapi_t.pendaftaran_id, programterapi_t.pasien_id, programterapi_t.programterapi_id, dok_perujuk.pegawai_id, dok_perujuk.nama_pegawai, look_jenkel.lookup_kode, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, soapfisioterapi_t.jumlah, pembayaran.bayar, pendaftaran_t.status_periksa, lookup_status.lookup_name, kamarruangan_m.kamarruangan_nokamar, soapfisioterapi_t.is_edit, lookup_status_program_fisio.lookup_name, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, jadwalterapifisio_t.jumlah_ketidakhadiran
            ;");


        $this->execute('
            ALTER TABLE public.infoprogramfisioterapi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220324_164711_migrate_skema_fisio_infoprogramfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220324_164711_migrate_skema_fisio_infoprogramfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
