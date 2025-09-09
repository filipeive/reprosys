<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserActivity;
use App\Models\TemporaryPassword;
use App\Traits\LogsActivity; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends AppBaseController
{
    use LogsActivity; 

    public function index(Request $request)
    {
        $query = User::with(['role', 'activeTemporaryPasswords'])->orderBy('name');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', fn($q) => $q->where('name', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Aplicar ordenação
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['name', 'email', 'created_at', 'last_login_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'admin' => User::whereHas('role', fn($q) => $q->where('name', 'admin'))->count(),
            'manager' => User::whereHas('role', fn($q) => $q->where('name', 'manager'))->count(),
            'staff' => User::whereHas('role', fn($q) => $q->where('name', 'staff'))->count(),
            'with_temp_password' => User::whereHas('activeTemporaryPasswords')->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $data = $request->except('photo', 'password_confirmation');

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('user-photos', 'public');
        }

        $data['password'] = Hash::make($request->password);
        $data['is_active'] = true;

        $user = User::create($data);

        // Registrar log
        $this->logActivity('create', "Criou novo usuário: {$user->name}", $user);

        return $this->success('users.index', 'Usuário criado com sucesso!');
    }

    public function show(User $user)
    {
        $user->load(['role', 'activities' => function($query) {
            $query->latest()->limit(5);
        }, 'temporaryPasswords' => function($query) {
            $query->latest()->limit(3);
        }]);

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        //$this->authorize('canEdit', $user);
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        //$this->authorize('canEdit', $user);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        $oldName = $user->name;
        $data = $request->except('photo', 'password_confirmation');

        if ($request->hasFile('photo')) {
            if ($user->photo_path) {
                Storage::disk('public')->delete($user->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('user-photos', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            
            // Invalidar senhas temporárias quando senha for alterada manualmente
            $user->activeTemporaryPasswords()->update([
                'used' => true,
                'used_at' => now()
            ]);
        }

        $user->update($data);

        // Registrar log
        $this->logActivity('update', "Atualizou usuário de '{$oldName}' para '{$user->name}'", $user);

        return $this->success('users.index', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $this->authorize('canDelete', $user);

        $userName = $user->name;

        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }

        $user->delete();

        // Registrar log
        $this->logActivity('delete', "Excluiu usuário: {$userName}", $user);

        return $this->success('users.index', 'Usuário excluído com sucesso!');
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user)
    {
        if (!auth()->user()->canEdit($user)) {
            return response()->json([
                'success' => false,
                'error' => 'Você não tem permissão para alterar o status deste usuário.'
            ], 403);
        }

        $currentStatus = $user->is_active;
        $user->update(['is_active' => !$currentStatus]);

        // Se desativado, invalidar senhas temporárias
        if (!$currentStatus === false) {
            $user->activeTemporaryPasswords()->update([
                'used' => true,
                'used_at' => now()
            ]);
        }

        // Registrar log
        $this->logActivity('status_change', "Alterou status do usuário '{$user->name}' para " . (!$currentStatus ? 'ativo' : 'inativo'), $user);

        return response()->json([
            'success' => true,
            'message' => "Usuário {$user->name} " . (!$currentStatus ? 'ativado' : 'desativado') . " com sucesso!",
            'new_status' => !$currentStatus
        ]);
    }

    /**
     * Display user activity log.
     */
    public function activity(User $user)
    {
        if (!auth()->user()->canView($user)) {
            return $this->error('users.index', 'Você não tem permissão para visualizar a atividade deste usuário.');
        }

        $activities = $user->activities()->paginate(20);

        return view('users.activity', compact('user', 'activities'));
    }
    
    /**
     * Reset user password with temporary password system.
     */
    public function resetPassword(User $user)
    {
        if (!auth()->user()->canEdit($user)) {
            return response()->json([
                'success' => false,
                'error' => 'Você não tem permissão para resetar a senha deste usuário.'
            ], 403);
        }

        try {
            // Gerar senha temporária
            $temporaryPassword = $this->generateSecurePassword();
            
            // Criar registro de senha temporária
            $tempPassword = TemporaryPassword::createForUser($user, $temporaryPassword, 24);
            
            // Atualizar senha do usuário com a temporária
            $user->update([
                'password' => Hash::make($temporaryPassword),
            ]);

            // Registrar log
            $this->logActivity('password_reset', "Resetou senha do usuário: {$user->name} (Expira em 24h)", $user);

            return response()->json([
                'success' => true,
                'password' => $temporaryPassword,
                'expires_at' => $tempPassword->expires_at->format('d/m/Y H:i'),
                'message' => 'Senha resetada com sucesso! A senha temporária expira em 24 horas.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor ao resetar a senha.'
            ], 500);
        }
    }

    /**
     * Show temporary passwords for a user.
     */
    public function temporaryPasswords(User $user)
    {
        if (!auth()->user()->canView($user)) {
            return $this->error('users.index', 'Você não tem permissão para visualizar as senhas temporárias deste usuário.');
        }

        $temporaryPasswords = $user->temporaryPasswords()->with('createdBy')->paginate(10);

        return view('users.temporary-passwords', compact('user', 'temporaryPasswords'));
    }

    /**
     * Invalidate all temporary passwords for a user.
     */
    public function invalidateTemporaryPasswords(User $user)
    {
        if (!auth()->user()->canEdit($user)) {
            return response()->json([
                'success' => false,
                'error' => 'Você não tem permissão para invalidar as senhas temporárias deste usuário.'
            ], 403);
        }

        $count = $user->activeTemporaryPasswords()->count();
        
        $user->activeTemporaryPasswords()->update([
            'used' => true,
            'used_at' => now()
        ]);

        // Registrar log
        $this->logActivity('password_invalidate', "Invalidou {$count} senha(s) temporária(s) do usuário: {$user->name}", $user);

        return response()->json([
            'success' => true,
            'message' => "Invalidadas {$count} senha(s) temporária(s) com sucesso!"
        ]);
    }

    /**
     * Generate a secure temporary password.
     */
    private function generateSecurePassword(int $length = 12): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
        $password = '';
        
        // Garantir pelo menos um de cada tipo
        $password .= substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 1); // Minúscula
        $password .= substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1); // Maiúscula
        $password .= substr(str_shuffle('0123456789'), 0, 1); // Número
        $password .= substr(str_shuffle('!@#$%&*'), 0, 1); // Símbolo
        
        // Completar o resto
        for ($i = 4; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return str_shuffle($password);
    }
}