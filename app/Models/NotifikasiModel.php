<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table            = 'notifikasi';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id', 'kendaraan_id', 'aktif', 'sebelum_hari', 'interval_hari',
        'jam_pengingat', 'kanal', 'tujuan', 'created_at', 'updated_at',
    ];
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;
    protected $returnType       = 'object';

    protected $validationRules = [
        'kendaraan_id'  => 'required|integer',
        'sebelum_hari'  => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[365]',
        'interval_hari' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[1095]',
        'jam_pengingat' => 'required|regex_match[/^([01]?\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/]',
        'kanal'         => 'required|in_list[app,email,wa]',
    ];

    /**
     * Setting notifikasi milik user: satu baris per kendaraan.
     *
     * @return array<int, object>
     */
    public function milikUser(int $userId): array
    {
        return $this->where('user_id', $userId)->findAll();
    }

    /**
     * Ambil setting untuk kendaraan tertentu (atau null).
     */
    public function forKendaraan(int $userId, int $kendaraanId): ?object
    {
        return $this->where('user_id', $userId)->where('kendaraan_id', $kendaraanId)->first();
    }

    /**
     * Data pengingat: menggabungkan jadwal service terakhir dengan setting notifikasi.
     *
     * @return array<int, object> objek: kendaraan, aktif, jatuh_tempo, sisa_hari, sebelum_hari, jam, kanal, tujuan, sumber
     */
    public function daftarPengingat(int $userId): array
    {
        $kendaraan   = $this->db->table('kendaraan')->where('user_id', $userId)->get()->getResult();
        $settingRows = $this->milikUser($userId);
        $setting     = [];
        foreach ($settingRows as $s) {
            $setting[(int) $s->kendaraan_id] = $s;
        }

        $hasil = [];
        foreach ($kendaraan as $k) {
            $sid = (int) $k->id;

            $last = $this->db->table('service')
                ->where('kendaraan_id', $sid)
                ->where('status', 'selesai')
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()->getRow();

            $s   = $setting[$sid] ?? null;
            $due = null;
            $sumber = null;

            if ($last !== null && $last->next_service_date) {
                $due    = $last->next_service_date;
                $sumber = 'jadwal service';
            } elseif ($last !== null) {
                $interval = $s !== null ? (int) $s->interval_hari : 180;
                $due      = date('Y-m-d', strtotime($last->tanggal . ' +' . $interval . ' days'));
                $sumber   = 'interval ' . $interval . ' hari';
            }

            $obj = (object) [
                'kendaraan'    => $k,
                'aktif'        => $s !== null ? (bool) $s->aktif : false,
                'ada_setting'  => $s !== null,
                'jatuh_tempo'  => $due,
                'sumber'       => $sumber,
                'sebelum_hari' => $s !== null ? (int) $s->sebelum_hari : 7,
                'jam'          => $s !== null ? substr((string) $s->jam_pengingat, 0, 5) : '08:00',
                'kanal'        => $s !== null ? $s->kanal : 'app',
                'tujuan'       => $s !== null ? $s->tujuan : null,
                'service_terakhir' => $last,
            ];

            if ($due !== null) {
                $obj->sisa_hari = (int) (new \DateTime('today'))->diff(new \DateTime($due))->format('%r%a');
            } else {
                $obj->sisa_hari = null;
            }

            $hasil[] = $obj;
        }

        return $hasil;
    }
}
