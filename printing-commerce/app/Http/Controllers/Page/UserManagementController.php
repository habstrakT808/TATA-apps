<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;
use App\Models\User;
use App\Models\Admin;
use App\Models\Editor;
use App\Models\Auth;

class UserManagementController extends Controller
{
    public function showAll(Request $request)
    {
        // Get regular users
        $users = User::select(
                'users.uuid',
                'users.nama_user as nama',
                'auth.email',
                'users.no_telpon',
                'auth.role',
                'users.foto'
            )
            ->join('auth', 'users.id_auth', '=', 'auth.id_auth')
            ->where('auth.role', 'user')
            ->get();
            
        // Get admin users
        $admins = Admin::select(
                'admin.uuid',
                'admin.nama_admin as nama',
                'auth.email',
                'admin.no_telpon',
                'auth.role',
                DB::raw('NULL as foto')
            )
            ->join('auth', 'admin.id_auth', '=', 'auth.id_auth')
            ->whereIn('auth.role', ['super_admin', 'admin', 'admin_chat', 'admin_pemesanan'])
            ->get();
            
        // Get editors - without joining auth table since it doesn't have id_auth column
        $editors = Editor::select(
                'editor.uuid',
                'editor.nama_editor as nama',
                'editor.email',
                'editor.no_telpon',
                DB::raw("'editor' as role"), // Hardcode role as 'editor'
                DB::raw('NULL as foto')
            )
            ->get();
            
        // Combine all users and format roles
        $allUsers = $users->concat($admins)->concat($editors)->map(function($item) {
            $item->roles = ucwords(str_replace('_', ' ', $item->role));
            return $item;
        });
        
        $dataShow = [
            'userData' => $allUsers,
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'nav' => 'user-management',
        ];
        
        return view('page.user_management.data', $dataShow);
    }
    
    public function showTambah(Request $request)
    {
        $dataShow = [
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'nav' => 'user-management',
        ];
        
        return view('page.user_management.tambah', $dataShow);
    }
    
    public function showDetail(Request $request, $uuid)
    {
        // Try to find user in each table
        $userData = null;
        $userType = null;
        
        // Check if it's a regular user
        $user = User::select(
                'users.uuid',
                'users.nama_user as nama',
                'auth.email',
                'users.no_telpon',
                'auth.role',
                'users.foto'
            )
            ->join('auth', 'users.id_auth', '=', 'auth.id_auth')
            ->where('users.uuid', $uuid)
            ->first();
            
        if ($user) {
            $userData = $user;
            $userType = 'user';
        }
        
        // Check if it's an admin
        if (!$userData) {
            $admin = Admin::select(
                    'admin.uuid',
                    'admin.nama_admin as nama',
                    'auth.email',
                    'admin.no_telpon',
                    'auth.role',
                    DB::raw('NULL as foto')
                )
                ->join('auth', 'admin.id_auth', '=', 'auth.id_auth')
                ->where('admin.uuid', $uuid)
                ->first();
                
            if ($admin) {
                $userData = $admin;
                $userType = 'admin';
            }
        }
        
        // Check if it's an editor - without joining auth table
        if (!$userData) {
            $editor = Editor::select(
                    'editor.uuid',
                    'editor.nama_editor as nama',
                    'editor.email',
                    'editor.no_telpon',
                    DB::raw("'editor' as role"),
                    DB::raw('NULL as foto')
                )
                ->where('editor.uuid', $uuid)
                ->first();
                
            if ($editor) {
                $userData = $editor;
                $userType = 'editor';
            }
        }
        
        if (!$userData) {
            return redirect('/user-management')->with('error', 'User tidak ditemukan');
        }
        
        $userData->roles = ucwords(str_replace('_', ' ', $userData->role));
        
        $dataShow = [
            'userData' => $userData,
            'userType' => $userType,
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'nav' => 'user-management',
        ];
        
        return view('page.user_management.detail', $dataShow);
    }
} 