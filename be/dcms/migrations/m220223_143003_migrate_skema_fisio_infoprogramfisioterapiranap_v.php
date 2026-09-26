<?php

use yii\db\Migration;

/**
 * Class m220223_143003_migrate_skema_fisio_infoprogramfisioterapiranap_v
 */
class m220223_143003_migrate_skema_fisio_infoprogramfisioterapiranap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infoprogramfisioterapiranap_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infoprogramfisioterapiranap_v\" AS
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
            programterapi_t.tipe_instalasi,
            kamarruangan_m.kamarruangan_nokamar AS kamar,
            kamartempattidur_m.no_tempattidur,
            kamartempattidur_m.kamartempattidur_id,
            soapfisioterapi_t.is_edit,
            kamarruangan_m.kamarruangan_id AS kamar_id,
            programterapi_t.is_active,
            ruangan_m.ruangan_nama,
            ruangan_m.ruangan_id,
            programterapi_t.status_program_fisio AS status_program_fisio_id,
            lookup_m.lookup_name AS status_program_fisio_nama,
            programterapi_t.pasienkirimkeunitlain_id,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            programterapi_t.is_paket,
            array_to_string(array_agg(programterapidetail_t.daftartindakan_id), ','::text) AS daftartindakan_id,
            array_to_string(array_agg(programterapidetail_t.daftartindakan_nama), ','::text) AS daftartindakan_nama,
            array_to_string(array_agg(programterapidetail_t.daftartindakan_nama), '<br>'::text) AS daftartindakan_nama_view,
            COALESCE(jadwalterapifisio_t.jumlah_ketidakhadiran, (0)::bigint) AS jumlah_ketidakhadiran
            FROM ((((((((((((((programterapi_t
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
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id
            FROM pendaftaran_t a) pendaftaran_t ON ((programterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.ruangan_id,
            a.carabayar_id
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
            FROM carabayar_m a) carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
            JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
            FROM kamartempattidur_m a) kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) lookup_m ON (((programterapi_t.status_program_fisio)::integer = lookup_m.lookup_id)))
            LEFT JOIN ( SELECT a.programterapi_id,
            count(a.status_kunjungan_fisio) AS jumlah_ketidakhadiran
            FROM jadwalterapifisio_t a
            WHERE (a.status_kunjungan_fisio = 1208)
            GROUP BY a.programterapi_id) jadwalterapifisio_t ON ((programterapi_t.programterapi_id = jadwalterapifisio_t.programterapi_id)))
            WHERE ((programterapi_t.tipe_instalasi)::text = '3'::text)
            GROUP BY programterapi_t.pendaftaran_id, programterapi_t.pasien_id, programterapi_t.programterapi_id, dok_perujuk.pegawai_id, dok_perujuk.nama_pegawai, look_jenkel.lookup_kode, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, soapfisioterapi_t.jumlah, pembayaran.bayar, kamarruangan_m.kamarruangan_nokamar, soapfisioterapi_t.is_edit, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, kamartempattidur_m.no_tempattidur, kamartempattidur_m.kamartempattidur_id, kamarruangan_m.kamarruangan_id, ruangan_m.ruangan_nama, ruangan_m.ruangan_id, lookup_m.lookup_name, jadwalterapifisio_t.jumlah_ketidakhadiran
            ;");

        $this->execute('
            ALTER TABLE public.infoprogramfisioterapiranap_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220223_143003_migrate_skema_fisio_infoprogramfisioterapiranap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220223_143003_migrate_skema_fisio_infoprogramfisioterapiranap_v cannot be reverted.\n";

        return false;
    }
    */
}
