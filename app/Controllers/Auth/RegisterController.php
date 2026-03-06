<?php

namespace App\Controllers\Auth;

use CodeIgniter\Shield\Controllers\RegisterController as ShieldRegister;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;


class RegisterController extends ShieldRegister
{
    // ─────────────────────────────────────────────────────
    //  Show register view
    //  No changes needed here — this was already correct
    // ─────────────────────────────────────────────────────
    public function registerView(): RedirectResponse|string
    {
        if (auth()->loggedIn()) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register', [
            'errors' => session()->getFlashdata('errors') ?? []
        ]);
    }

    // ─────────────────────────────────────────────────────
    //  Handle registration form submission
    // ─────────────────────────────────────────────────────
    public function registerAction(): RedirectResponse
    {
        // Validation rules
        $rules = [
            'username'         => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'            => 'required|valid_email|is_unique[auth_identities.secret]',
            'mobile'           => 'required|min_length[10]|max_length[15]',
            'gender'           => 'required|in_list[male,female,other,prefer_not_to_say]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $profilePic = $this->uploadProfilePic();

        $user = new User([
            'username'    => $this->request->getPost('username'),
            'email'       => $this->request->getPost('email'),
            'password'    => $this->request->getPost('password'),
            'mobile'      => $this->request->getPost('mobile'),
            'gender'      => $this->request->getPost('gender'),
            'profile_pic' => $profilePic,
        ]);

        // ── Save via Shield's UserModel ──
        $users = new UserModel();
        $users->save($user);

        $userId      = $users->getInsertID();
        $savedUser   = $users->findById($userId);
        $savedUser->addGroup('user');

        auth()->login($savedUser);

        return redirect()->to('/dashboard')
            ->with('message', 'Welcome to Cozy Reads! 📚');
    }

    // ─────────────────────────────────────────────────────
    //  Handle profile picture upload
    //  Returns filename string to store in DB
    // ─────────────────────────────────────────────────────
    private function uploadProfilePic(): string
    {
        $file = $this->request->getFile('profile_pic');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return 'default.png';
        }
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (! in_array($file->getMimeType(), $allowedMimes)) {
            return 'default.png';
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return 'default.png';
        }

        $newName = $file->getRandomName();
        $file->move(ROOTPATH . 'public/uploads/avatars', $newName);

        return $newName;
    }
}