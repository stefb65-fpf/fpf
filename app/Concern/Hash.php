<?php


namespace App\Concern;


use App\Models\Personne;

trait Hash
{
    public function encodePwd($password) {
        return hash('sha512', env('SALT_KEY').$password);
    }

    public function encodeShortReinit() {
        return md5(uniqid());
    }

    public function checkPersonneLogin($email, $password) {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $personne = Personne::where('email', $email)->first();
        if (!$personne) {
            return false;
        }

        if (hash('sha512', env('SALT_KEY').$password) !== $personne->password) {
            return false;
        }
        return true;
    }
}
