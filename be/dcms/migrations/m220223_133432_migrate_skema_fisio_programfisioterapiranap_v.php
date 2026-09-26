<?php

use yii\db\Migration;

/**
 * Class m220223_133432_migrate_skema_fisio_programfisioterapiranap_v
 */
class m220223_133432_migrate_skema_fisio_programfisioterapiranap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_id IN (1210);
        ");
        
        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1210, 'status_program_fisio', 'DROP OUT', 'DROP OUT', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
            ");

        $this->execute('DROP VIEW if exists public.programfisioterapiranap_v;');
        $this->execute("
            CREATE VIEW \"public\".\"programfisioterapiranap_v\" AS
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
            WHEN (programterapi_t.frekuensi = (soapfisioterapi_t.jumlah)::double precision) THEN 'CLOSE'::text
            ELSE 'OPEN'::text
            END AS status,
            COALESCE(programterpilih.jumlah, (0)::bigint) AS programterpilih,
            COALESCE((programterapi_t.frekuensi - COALESCE((programterpilih.jumlah)::double precision, (0)::double precision)), (0)::double precision) AS sisa_frekuensi,
            programterapidetail_t.terapi_nama,
            pasienadmisi_t.status_ranap AS status_periksa_id,
            lookup_m.lookup_name AS status_periksa_nama,
            soapfisioterapi_t.is_edit,
            programterapi_t.status_program_fisio AS status_program_fisio_id,
            lookup_statusprogramfisio.lookup_name AS status_program_fisio_nama,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            COALESCE(jadwalterapifisio_t.jumlah_ketidakhadiran, (0)::bigint) AS jumlah_ketidakhadiran
            FROM ((((((((((((programterapi_t
            LEFT JOIN ( SELECT pt.programterapi_id,
            count(pt.pasienmasukpenunjang_id) AS jumlah
            FROM pasienmasukpenunjang_t pt
            GROUP BY pt.programterapi_id) programterpilih ON ((programterpilih.programterapi_id = programterapi_t.programterapi_id)))
            LEFT JOIN ( SELECT count(a.soapfisioterapi_id) AS jumlah,
            a.pasien_id,
            a.programterapi_id,
            a.pendaftaran_id,
            a.tipe_instalasi,
            a.is_edit,
            a.is_deleted
            FROM soapfisioterapi_t a
            GROUP BY a.pasien_id, a.pendaftaran_id, a.programterapi_id, a.tipe_instalasi, a.is_edit, a.is_deleted) soapfisioterapi_t ON (((programterapi_t.programterapi_id = soapfisioterapi_t.programterapi_id) AND (programterapi_t.pendaftaran_id = soapfisioterapi_t.pendaftaran_id) AND (soapfisioterapi_t.is_edit IS NOT TRUE) AND (soapfisioterapi_t.is_deleted IS NOT TRUE))))
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
            JOIN ( SELECT a.pendaftaran_id,
            a.status_ranap,
            a.carabayar_id
            FROM pasienadmisi_t a) pasienadmisi_t ON ((programterapi_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
            FROM carabayar_m a) carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) lookup_m ON ((pasienadmisi_t.status_ranap = lookup_m.lookup_id)))
            JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) lookup_statusprogramfisio ON (((programterapi_t.status_program_fisio)::integer = lookup_statusprogramfisio.lookup_id)))
            LEFT JOIN ( SELECT a.programterapi_id,
            count(a.status_kunjungan_fisio) AS jumlah_ketidakhadiran
            FROM jadwalterapifisio_t a
            WHERE (a.status_kunjungan_fisio = 1208)
            GROUP BY a.programterapi_id) jadwalterapifisio_t ON ((programterapi_t.programterapi_id = jadwalterapifisio_t.programterapi_id)))
            WHERE ((programterapi_t.tipe_instalasi)::text = '3'::text)
            ;");
            $this->execute('
                ALTER TABLE public.programfisioterapiranap_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220223_133432_migrate_skema_fisio_programfisioterapiranap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220223_133432_migrate_skema_fisio_programfisioterapiranap_v cannot be reverted.\n";

        return false;
    }
    */
}
