<?php

use yii\db\Migration;

/**
 * Class m210322_102008_improvment_view_inputhasillab_v
 */
class m210322_102008_improvment_view_inputhasillab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS inputhasillab_v;
        ');

        $this->execute('
            CREATE VIEW "public"."inputhasillab_v" AS  SELECT tindakanpelayanan_t.tindakanpelayanan_id,
                tindakanpelayanan_t.pasienmasukpenunjang_id,
                pasienkirimkeunitlain_t.catatan_dokterpengirim,
                samplelab_m.nama_sample, 
                daftartindakan_m.daftartindakan_nama,
                hasilpemeriksaanlab_t.is_expertise,
                    CASE pendaftaran_t.instalasi_id
                        WHEN 1 THEN COALESCE(instruksi_t.catatan_instruksi, pasienkirimkeunitlain_t.catatan_dokterpengirim)
                        ELSE instruksi_t.catatan_instruksi
                    END AS catatan_instruksi,
                ambilsample_t.ambilsample_id,
                ambilsample_t.samplelab_id,
                ambilsample_t.tgl_ambilsample,
                ambilsample_t.jam_ambilsample,
                hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
                ambilsample_t.keterangan,
                ambilsample_t.jumlah,
                ambilsample_t.satuan_jumlah,
                satuanlab_m.satuanlab_nama
               FROM ((((((((((tindakanpelayanan_t
                 JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                 LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN ambilsample_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id) AND (pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id))))
                 JOIN samplelab_m ON ((ambilsample_t.samplelab_id = samplelab_m.samplelab_id)))
                 LEFT JOIN instruksi_t ON ((pasienkirimkeunitlain_t.instruksi_id = instruksi_t.instruksi_id)))
                 LEFT JOIN hasilpemeriksaanlab_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id) AND (ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id))))
                 LEFT JOIN satuanlab_m ON ((ambilsample_t.satuan_jumlah = satuanlab_m.satuanlab_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
              WHERE (ruangan_m.instalasi_id = 4)
            UNION ALL
             SELECT tindakanpelayanan_t.tindakanpelayanan_id,
                tindakanpelayanan_t.pasienmasukpenunjang_id,
                pasienkirimkeunitlain_t.catatan_dokterpengirim,
                samplelab_m.nama_sample,
                daftartindakan_m.daftartindakan_nama,
                hasilpemeriksaanlab_t.is_expertise,
                    CASE pendaftaran_t.instalasi_id
                        WHEN 1 THEN COALESCE(instruksi_t.catatan_instruksi, pasienkirimkeunitlain_t.catatan_dokterpengirim)
                        ELSE instruksi_t.catatan_instruksi
                    END AS catatan_instruksi,
                ambilsample_t.ambilsample_id,
                ambilsample_t.samplelab_id,
                ambilsample_t.tgl_ambilsample,
                ambilsample_t.jam_ambilsample,
                hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
                ambilsample_t.keterangan,
                ambilsample_t.jumlah,
                ambilsample_t.satuan_jumlah,
                satuanlab_m.satuanlab_nama
               FROM ((((((((((((tindakanpelayanan_t
                 JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                 LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                 JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN ambilsample_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id) AND (pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id) AND (ambilsample_t.tindakanpaket_id = paketpelayanan_mp.daftartindakan_id))))
                 JOIN samplelab_m ON ((ambilsample_t.samplelab_id = samplelab_m.samplelab_id)))
                 LEFT JOIN instruksi_t ON ((pasienkirimkeunitlain_t.instruksi_id = instruksi_t.instruksi_id)))
                 LEFT JOIN hasilpemeriksaanlab_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id) AND (ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id))))
                 LEFT JOIN satuanlab_m ON ((ambilsample_t.satuan_jumlah = satuanlab_m.satuanlab_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN pendaftaran_t ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
              WHERE (ruangan_m.instalasi_id = 4)
            UNION ALL
             SELECT tindakanpelayanan_t.tindakanpelayanan_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                NULL::text AS catatan_dokterpengirim,
                samplelab_m.nama_sample,
                detail.detail_3 AS daftartindakan_nama,
                hasilpemeriksaanlab_t.is_expertise,
                NULL::text AS catatan_instruksi,
                ambilsample_t.ambilsample_id,
                ambilsample_t.samplelab_id,
                ambilsample_t.tgl_ambilsample,
                ambilsample_t.jam_ambilsample,
                hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
                ambilsample_t.keterangan,
                ambilsample_t.jumlah,
                ambilsample_t.satuan_jumlah,
                satuanlab_m.satuanlab_nama
               FROM (((((((tindakanpelayanan_t
                 JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
                 JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                        paketpelayanan_mp.paketdetail_id AS detail_2id,
                        paket_detail.tipepaket_nama AS detail_2,
                        paket_detail.daftartindakan_id AS detail_3id,
                        paket_detail.daftartindakan_nama AS detail_3
                       FROM ((tipepaket_m
                         JOIN paketpelayanan_mp ON (((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                         JOIN ( SELECT a.tipepaket_id,
                                a.tipepaket_nama,
                                daftartindakan_m.daftartindakan_id,
                                daftartindakan_m.daftartindakan_nama
                               FROM ((tipepaket_m a
                                 JOIN paketpelayanan_mp paketpelayanan_mp_1 ON ((a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id)))
                                 JOIN daftartindakan_m ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id)))) paket_detail ON ((paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id)))
                      WHERE (tipepaket_m.is_deleted = false)
                    UNION ALL
                     SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                        NULL::integer AS detail_2id,
                        NULL::character varying AS detail_2,
                        paketpelayanan_mp.daftartindakan_id AS detail_3id,
                        tindakan_detail.daftartindakan_nama AS detail_3
                       FROM ((tipepaket_m
                         JOIN paketpelayanan_mp ON (((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                         JOIN daftartindakan_m tindakan_detail ON ((paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id)))
                      WHERE (tipepaket_m.is_deleted = false)) detail ON ((tindakanpelayanan_t.tipepaket_id = detail.detail_1id)))
                 JOIN ambilsample_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id) AND (pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id) AND (ambilsample_t.tindakanpaket_id = detail.detail_3id))))
                 JOIN samplelab_m ON ((ambilsample_t.samplelab_id = samplelab_m.samplelab_id)))
                 LEFT JOIN hasilpemeriksaanlab_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id) AND (ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id))))
                 LEFT JOIN satuanlab_m ON ((ambilsample_t.satuan_jumlah = satuanlab_m.satuanlab_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
              WHERE (ruangan_m.instalasi_id = 4);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210322_102008_improvment_view_inputhasillab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210322_102008_improvment_view_inputhasillab_v cannot be reverted.\n";

        return false;
    }
    */
}
