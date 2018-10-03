<?php
namespace App\Auth;

use App\Models\User;
class Auth{

    protected $container;

    function __construct($container){
        $this->container = $container;
    }

    public function user(){
        return User::find($_SESSION['userid']);
    }

    public function role(){
        $id = $this->user()->ID;
        $role = User::join('usermeta', 'users.ID', '=', 'usermeta.user_id')
        ->select('usermeta.meta_value')
        ->where([
            ['usermeta.meta_key', '=', 'wpfw_capabilities'],
            ['users.ID', '=', 4]
        ])->first();
        $role = unserialize($role->meta_value);
        $array = array_keys($role);

        return $array;
    }

    public function check(){
        return isset($_SESSION['user']);
    }

    public function attempt($email, $password){
        $user = User::where('user_email', $email)->first();

        //  if user is null then return it false 
        //  and if something fetched then check if password
        //  match using wordpress hashing framework 
        if(!$user){
            return false;
        }
        if($this->container->passwordhasher->checkPassword($password, $user->user_pass)){
            $_SESSION['userid'] = $user->ID;
            return true;
        }
        return false;
    }

}