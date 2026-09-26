<?php

use yii\db\Migration;

/**
 * Class m220921_092829_view_gateway_layananlab
 */
class m220921_092829_view_gateway_layananlab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_layananlab_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_layananlab_v\"
        AS SELECT pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_pendaftaran,
            pasienmasukpenunjang_t.no_masukpenunjang AS no_rujukan,
            ruangan_m.ruangan_nama,
            pasien_m.no_identitas_pasien AS no_identitas,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            COALESCE(asalrujukan_m.asalrujukan_nama, 'Datang Sendiri'::character varying) AS asal_rujukan,
            pegawai_m.nama_pegawai AS dokter_perujuk,
            penjamin_m.penjamin_nama AS penjamin,
            carabayar_m.carabayar_nama AS cara_bayar,
            status_periksa.lookup_name AS status_pengajuan,
            hasilpemeriksaanlab_t.hasil_lab
        FROM pasienmasukpenunjang_t
            JOIN ( SELECT DISTINCT a.pendaftaran_id,
                    a.penjamin_id,
                    a.carabayar_id,
                    a.pegawai_id,
                    a.rujukan_id
                FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            JOIN ( SELECT DISTINCT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                FROM ruangan_m a
                WHERE a.instalasi_id = 4) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
            JOIN ( SELECT DISTINCT a.pasien_id,
                    a.no_identitas_pasien,
                    a.no_rekam_medik,
                    a.nama_pasien
                FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
            LEFT JOIN ( SELECT DISTINCT a.penjamin_id,
                    a.penjamin_nama
                FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
            LEFT JOIN ( SELECT DISTINCT a.carabayar_id,
                    a.carabayar_nama
                FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
            LEFT JOIN ( SELECT DISTINCT a.lookup_id,
                    a.lookup_name
                FROM lookup_m a) status_periksa ON pasienmasukpenunjang_t.status_periksa::integer = status_periksa.lookup_id
            LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
            LEFT JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id
                FROM rujukan_t a) rujukan_m ON pendaftaran_t.rujukan_id = rujukan_m.rujukan_id
            LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                FROM asalrujukan_m a) asalrujukan_m ON rujukan_m.asalrujukan_id = asalrujukan_m.asalrujukan_id
            LEFT JOIN ( SELECT hasil.pasienmasukpenunjang_id,
                    ( SELECT array_to_json(array_agg(row_to_json(d.*))) AS array_to_json
                        FROM ( SELECT a.tgl_hasilpemeriksaanlab::text AS tgl_hasilpemeriksaanlab,
                                    pegawai_m_1.nama_pegawai AS dokter_lab,
                                    samplelab_m.nama_sample,
                                    nilairujukan_m.nama_rujukan,
                                    hasilpemeriksaanlabdetail_t.nilai_rujukan,
                                    hasilpemeriksaanlabdetail_t.hasil,
                                    nilairujukan_m.nilai_rujukan
                                FROM hasilpemeriksaanlab_t a
                                    JOIN ( SELECT c.hasilpemeriksaanlab_id,
                                            c.hasil,
                                            c.nilai_rujukan,
                                            c.petugas_verifikasi,
                                            c.nilairujukan_id
                                        FROM hasilpemeriksaanlabdetail_t c) hasilpemeriksaanlabdetail_t ON a.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id
                                    LEFT JOIN ( SELECT e.nilairujukan_id,
                                            e.nilai_rujukan,
                                            e.nama_rujukan
                                        FROM nilairujukan_m e) nilairujukan_m ON hasilpemeriksaanlabdetail_t.nilairujukan_id = nilairujukan_m.nilairujukan_id
                                    JOIN ( SELECT f.pasienmasukpenunjang_id,
                                            f.pegawai_id
                                        FROM pasienmasukpenunjang_t f) pasienmasukpenunjang_t_1 ON a.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                                    LEFT JOIN ( SELECT h.pegawai_id,
                                            h.nama_pegawai
                                        FROM pegawai_m h) pegawai_m_1 ON COALESCE(hasilpemeriksaanlabdetail_t.petugas_verifikasi, pasienmasukpenunjang_t_1.pegawai_id) = pegawai_m_1.pegawai_id
                                    LEFT JOIN ( SELECT h.samplelab_id,
                                            h.nama_sample
                                        FROM samplelab_m h) samplelab_m ON a.samplelab_id = samplelab_m.samplelab_id
                                WHERE a.is_deleted = false AND hasil.pasienmasukpenunjang_id = a.pasienmasukpenunjang_id) d(tgl_hasilpemeriksaanlab, dokter_lab, nama_sample, nama_rujukan, nilai_rujukan, hasil, nilai_rujukan_1)) AS hasil_lab
                FROM hasilpemeriksaanlab_t hasil
                GROUP BY hasil.pasienmasukpenunjang_id) hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_092829_view_gateway_layananlab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_092829_view_gateway_layananlab cannot be reverted.\n";

        return false;
    }
    */
}
