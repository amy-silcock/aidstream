<?php namespace App\Lite\Repositories\Users;


use App\Lite\Contracts\UserRepositoryInterface;
use App\User;

/**
 * Class UserRepository
 *
 * @package App\Lite\Repositories\Users
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $user;

    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Find Specific user.
     * @param $userId
     * @return mixed
     */
    public function find($userId)
    {
        return $this->user->findOrFail($userId);
    }

    /**
     * Store the user details.
     * @param array $user
     * @return User
     */
    public function save(array $user)
    {
        $user['verified'] = true;
        $user['password'] = bcrypt($user['password']);
        $user['org_id']   = session('org_id');

        return $this->user->create($user);
    }

    /**
     * Returns all the users present in the organisation.
     * @param $orgId
     * @return mixed
     */
    public function all($orgId)
    {
        return $this->user->where('org_id', $orgId)->get();
    }

    /**
     * Update the details of the user.
     *
     * @param       $userId
     * @param array $parameters
     * @return mixed
     */
    public function update($userId, array $parameters)
    {
        $user = $this->find($userId);

        foreach ($parameters as $key => $value) {
            $user->{$key} = $value;
        }

        return $user->save();
    }

    /**
     * Delete the user.
     *
     * @param $userId
     * @return bool
     */
    public function delete($userId)
    {
        $user = $this->find($userId);

        return $user->delete();
    }
}
