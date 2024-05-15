<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;
use DateTime;
use Illuminate\Support\Facades\DB;

function HelperNomor($prefix_kode)
{
    // Generate a unique UUID
    $uuid = Uuid::uuid4();
    // Format the UUID as a string
    $kode = $prefix_kode . $uuid->toString();
    // Remove hyphens from the UUID
    $kode = str_replace('-', '', $kode);
    // Return the unique code
    return $kode;
}

function formatDate($dateString, $format = 'Y-m-d')
{
    $date = new DateTime($dateString);
    return $date->format($format);
}

use Illuminate\Support\Str;

function helper_nomor($prefix_kode)
{
    // Dapatkan ID terakhir dari tabel terkait
    $lastId = DB::table('tabel_anda')->max('id');
    // Format nomor kode
    $kode = $prefix_kode . Str::padLeft($lastId + 1, 6, '0');
    // Kembalikan kode unik
    return $kode;
}
//$kode_produk = helper_nomor('PRD-');
