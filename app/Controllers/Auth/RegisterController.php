<?php
// app/Controllers/Auth/RegisterController.php
namespace App\Controllers\Auth;

use CodeIgniter\Shield\Controllers\RegisterController as ShieldRegister;
use \CodeIgniter\HTTP\RedirectResponse as RedirectResponse;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;

class RegisterController extends ShieldRegister
{
    // Show your custom register view
    public function registerView():RedirectResponse | string
    {
        if (auth()->loggedIn()) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register', [
            'errors' => session()->getFlashdata('errors') ?? []
        ]);
    }

    // Handle registration form submission
    public function registerAction(): RedirectResponse
    {
        // Validation rules (add your custom fields here)
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

        // Create the user via Shield
        $users = new UserModel();

        $user = new User([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ]);

        $users->save($user);
        $userId = $users->getInsertID();

        // Save your custom fields in user_profiles table
        $db = \Config\Database::connect();
        $db->table('user_profiles')->insert([
            'user_id'    => $userId,
            'mobile'     => $this->request->getPost('mobile'),
            'gender'     => $this->request->getPost('gender'),
            'profile_pic'=> $this->uploadProfilePic(), // helper method below
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Assign default group
        $user = $users->findById($userId);
        $user->addGroup('user');

        // Log them in automatically after register
        auth()->login($user);

        return redirect()->to('/dashboard')->with('message', 'Welcome to Cozy Reads! 📚');
    }

    // Handle profile pic upload
    private function uploadProfilePic(): string
    {
        $file = $this->request->getFile('profile_pic');

        if (! $file || ! $file->isValid()) {
            return 'default.png'; // fallback avatar
        }

        $newName = $file->getRandomName();
        $file->move(ROOTPATH . 'public/uploads/avatars', $newName);

        return $newName;
    }
}