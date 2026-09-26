<style>
   .tbl-bordered {
      border-collapse: collapse;
      font-size: 12px;
      font-family: Tahoma, Geneva, sans-serif;
   }

   .tbl-bordered thead th {
      border: 0px solid black;
      padding: 5px;
   }

   .tbl-bordered tbody td {
      border: 1px solid black;
      padding: 5px;
   }

   .bold {
      font-weight: bold;
   }

   .tbl-bordered tfoot td {
      padding: 3px;
   }
</style>
<table class="tbl-bordered" width="100%">
   <tbody>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Mulai Operasi (Surgery Start)"); ?> : </td>
         <td><?= !empty($data['mulai_operasi']) ? date('d M Y H:i', strtotime($data['mulai_operasi'])) : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Selesai Operasi (Surgery End)"); ?> : </td>
         <td><?= !empty($data['selesai_operasi']) ? date('d M Y H:i', strtotime($data['selesai_operasi'])) : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Lama Pembedahan"); ?></td>
         <td><?= !empty($data['lama_pembedahan']) ? $data['lama_pembedahan'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Dokter Bedah (Surgeon)"); ?> : </td>
         <td><?= !empty($data['dokter_bedah']) ? $data['dokter_bedah'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Asisten (Assistant)"); ?> : </td>
         <td><?= !empty($data['asisten']) ? $data['asisten'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Asisten Instrumen (Instruments Assistant)"); ?> : </td>
         <td><?= !empty($data['asisten_instrumen']) ? $data['asisten_instrumen'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Dokter Anasthesi"); ?> : </td>
         <td><?= !empty($data['dokte_anastesi']) ? $data['dokte_anastesi'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Kategori Operasi (Surgery Category)"); ?> : </td>
         <td><?= !empty($data['kategori_operasi']) ? $data['kategori_operasi'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Diagnosa Pra Bedah (Pra Surgery Diagnosis)"); ?> : </td>
         <td><?= !empty($data['diagnosis_prabedah']) ? $data['diagnosis_prabedah'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Diagnosa Paska Bedah (Post Surgery Diagnosis)"); ?> : </td>
         <td><?= !empty($data['diagnosis_paskabedah']) ? $data['diagnosis_paskabedah'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Nama Prosedur Bedah (Procedure)"); ?> : </td>
         <td><?= !empty($data['nama_prosedur']) ? $data['nama_prosedur'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Jenis Operasi"); ?> : </td>
         <td><?= !empty($data['jenis_operasi']) ? $data['jenis_operasi'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Cara Pembiusan"); ?> : </td>
         <td><?= !empty($data['cara_pembiusan']) ? $data['cara_pembiusan'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Mulai Pembiusan (Anasthesi Start)"); ?> : </td>
         <td><?= !empty($data['mulai_pembiusan']) ? date('d M Y H:i', strtotime($data['mulai_pembiusan'])) : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Selesai Pembiusan (Anasthesi End)"); ?> : </td>
         <td><?= !empty($data['selesai_pembiusan']) ? date('d M Y H:i', strtotime($data['selesai_pembiusan'])) : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Operasi Ke"); ?> : </td>
         <td><?= !empty($data['operasi_ke']) ? $data['operasi_ke'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Uraian Pembedahan"); ?> : </td>
         <td><?= !empty($data['uraian']) ? $data['uraian'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Komplikasi (Complications)"); ?> : </td>
         <td><?= !empty($data['komplikasi']) ? $data['komplikasi'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Perdarahan (Bleeding)"); ?> : </td>
         <td><?= !empty($data['perdarahan']) ? $data['perdarahan'] : '-' ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Jaringan dikirim ke Patologi (Pathology Tissues)"); ?> : </td>
         <td><?= (!empty($data['is_kirimkepatologi']) && !$data['is_kirimkepatologi']) ? "Tidak" : "Ya" ?></td>
      </tr>
      <tr>
         <td class="bold"><?= \Yii::t("app", "Asal Jaringan (Origins of Pathology Tissues)"); ?> : </td>
         <td><?= !empty($data['asal_jaringan']) ? $data['asal_jaringan'] : '-' ?></td>
      </tr>
   </tbody>
</table>