<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PengaturanController extends Controller
{
    /**
     * Tampilkan halaman pengaturan
     */
    public function index()
    {
        $pengaturan = Pengaturan::all()->keyBy('kunci');
        $users = User::with('roles')->get();
        $roles = Role::all();

        return view('pengaturan.index', compact('pengaturan', 'users', 'roles'));
    }

    /**
     * Update pengaturan API
     */
    public function updateApi(Request $request)
    {
        $request->validate([
            'groq_api_key' => 'required|string',
            'groq_model' => 'required|string',
        ]);

        Pengaturan::atur('groq_api_key', $request->groq_api_key);
        Pengaturan::atur('groq_model', $request->groq_model);

        return redirect()->route('pengaturan.index')
            ->with('sukses', 'Pengaturan API berhasil disimpan');
    }

    /**
     * Tambah user baru
     */
    public function tambahUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->roles()->attach($request->roles);

        return redirect()->route('pengaturan.index')
            ->with('sukses', 'User berhasil ditambahkan');
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->roles()->sync($request->roles);

        return redirect()->route('pengaturan.index')
            ->with('sukses', 'User berhasil diupdate');
    }

    /**
     * Hapus user
     */
    public function hapusUser(User $user)
    {
        // Jangan hapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('pengaturan.index')
                ->with('error', 'Tidak bisa menghapus akun sendiri');
        }

        $user->delete();

        return redirect()->route('pengaturan.index')
            ->with('sukses', 'User berhasil dihapus');
    }
}
