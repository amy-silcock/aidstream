<?php namespace App\Lite\Services\Data\V202\Profile;

use App\Lite\Services\Data\Contract\MapperInterface;
use App\Models\Role;

/**
 * Class ProfileData
 * @package App\Lite\Services\Data\Profile
 */
class Profile implements MapperInterface
{

    /**
     * Raw data holder for Profile entity.
     *
     * @var array
     */
    protected $rawData = [];

    /**
     * Data template for Profile.
     *
     * @var array
     */
    protected $template = [];

    /**
     * @var
     */
    protected $userId;

    /**
     * ProfileData constructor.
     *
     * @param array $rawData
     */
    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * {@inheritdoc}
     */
    public function map()
    {
        $timeZone     = explode(' : ', $this->rawData['timeZone']);
        $time_zone_id = getVal($timeZone, ['0'], null);
        $time_zone    = getVal($timeZone, ['1'], null);

        $profile['profile'] = [
            'first_name'      => getVal($this->rawData, ['firstName'], null),
            'last_name'       => getVal($this->rawData, ['lastName'], null),
            'email'           => getVal($this->rawData, ['email'], null),
            'time_zone_id'    => $time_zone_id,
            'time_zone'       => $time_zone,
            'profile_picture' => getVal($this->rawData, ['fileName'], null),
            'profile_url'     => !($fileUrl = getVal($this->rawData, ['fileUrl'], null)) ?: url($fileUrl),
        ];

        $profile['organisation'] = [
            'secondary_contact' => [
                [
                    'first_name' => getVal($this->rawData, ['secondaryFirstName'], ''),
                    'last_name'  => getVal($this->rawData, ['secondaryLastName'], ''),
                    'email'      => getVal($this->rawData, ['secondaryEmail'], '')
                ]
            ]
        ];

        return $profile;
    }

    /**
     * Map database data into frontend compatible format.
     *
     * @return array
     */
    public function reverseMap()
    {
        $roles = $this->getRole();

        $formModel = [
            'firstName'          => getVal($this->rawData, ['first_name'], ''),
            'lastName'           => getVal($this->rawData, ['last_name'], ''),
            'userName'           => getVal($this->rawData, ['username'], ''),
            'email'              => getVal($this->rawData, ['email'], ''),
            'timeZone'           => getVal($this->rawData, ['time_zone_id'], '') . ' : ' . getVal($this->rawData, ['time_zone'], ''),
            'permission'         => getVal($roles, [getVal($this->rawData, ['role_id'], '')], ''),
            'secondaryFirstName' => getVal($this->rawData, ['secondary_contact', 0, 'first_name'], ''),
            'secondaryLastName'  => getVal($this->rawData, ['secondary_contact', 0, 'last_name'], ''),
            'secondaryEmail'     => getVal($this->rawData, ['secondary_contact', 0, 'email'], ''),
            'picture'            => getVal($this->rawData, ['profile_url'], '')
        ];

        return $formModel;
    }

    /**
     * provides roles
     *
     * @return array
     */
    protected function getRole()
    {
        $roles      = json_decode(Role::select('id', 'role')->get(), true);
        $permission = [];

        foreach ($roles as $role) {
            $permission[$role['id']] = $role['role'];
        }

        return $permission;
    }
}
