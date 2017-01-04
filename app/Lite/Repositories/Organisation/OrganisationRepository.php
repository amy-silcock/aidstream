<?php namespace App\Lite\Repositories\Organisation;

use App\Lite\Contracts\OrganisationRepositoryInterface;
use App\Models\Organization\Organization;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class OrganisationRepository
 * @package App\Lite\Repositories\Organisation
 */
class OrganisationRepository implements OrganisationRepositoryInterface
{
    /**
     * @var Organization
     */
    protected $organisation;

    /**
     * OrganisationRepository constructor.
     * @param Organization $organisation
     */
    public function __construct(Organization $organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * Get all the Organisations of the current Organization.
     *
     * @param $id
     * @return Collection
     */
    public function all($id)
    {
        // TODO: Implement all() method.
    }

    /**
     * Find an Organization by its id.
     *
     * @param $id
     * @return Organization
     */
    public function find($id)
    {
        return $this->organisation->where('id', $id)->first();
    }

    /**
     * Save the Organization data into the database.
     *
     * @param       $id
     * @param array $data
     * @return Organization
     */
    public function save($id, array $data)
    {
        return $this->organisation->updateorCreate(['id' => $id], $data);
    }
}

