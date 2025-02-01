<?php

namespace App\Repositories\Eloquent;

use App\Models\Building;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    private $user;

    /**
     * UserRepository constructor.
     *
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Register a new user.
     *
     * @param array $data
     * @return \App\Models\User
     * @throws \Illuminate\Database\QueryException
     */
    public function register(array $data)
    {
        $user = $this->user->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

    /**
     * Attempt to log in a user with provided credentials.
     *
     * @param array $credentials
     * @return \App\Models\User|null
     */
    public function login(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            return Auth::user();
        }

        return null;
    }

    /**
     * Retrieve the user information along with the user's balance.
     * 
     * This method fetches the user record by their ID, and includes the associated balance information.
     * 
     * @param int $user_id The ID of the user whose information is being retrieved.
     * 
     * @return \App\Models\User|null The user record along with the associated balance, or null if the user is not found.
     */
    public function info($user_id)
    {
        return User::select('users.*', 'user_balances.balance')
        ->leftJoin('user_balances', 'users.id', '=', 'user_balances.user_id')
        ->where('users.id', $user_id)
        ->first();
    }
}
