<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan pengguna terotentikasi
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Ambil peran pertama dari pengguna yang sedang login
        $role = auth()->user()->roles->pluck('name')->first();

        // Cek apakah pengguna memiliki peran 'super_admin' atau 'admin'
        if ($role === 'super_admin' || $role === 'admin') {
            // Pengguna memiliki akses ke menu tenant, lanjutkan ke permintaan berikutnya
            return $next($request);
        }

        // Pengguna tidak memiliki akses ke menu tenant, alihkan atau lakukan tindakan lain sesuai kebijakan Anda
        return redirect()->route('forbidden');
    }
}
