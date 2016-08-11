<?php namespace App\Tz\Aidstream\Repositories\Project;

use App\Models\ActivityPublished;
use App\Tz\Aidstream\Models\Project;


/**
 * Class ProjectRepository
 * @package App\Tz\Aidstream\Repositories\Project
 */
class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * @var Project
     */
    protected $project;
    protected $published;

    /**
     * ProjectRepository constructor.
     * @param Project           $project
     * @param ActivityPublished $activityPublished
     */
    public function __construct(Project $project, ActivityPublished $activityPublished)
    {
        $this->project   = $project;
        $this->published = $activityPublished;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->project->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->project->where('organization_id', '=', session('org_id'))->get();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $projectDetails)
    {
        $project = $this->project->newInstance($projectDetails);
        $project->save();

        return $project->id;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $project = $this->project->findOrFail($id);

        return $project->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, $projectDetails)
    {
        $project = $this->find($id);

        return $project->update($projectDetails);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedFiles($organizationId)
    {
        return $this->project->query()
                             ->join('activity_published', 'activity_data.organization_id', '=', 'activity_published.organization_id')
                             ->where('activity_data.organization_id', '=', $organizationId)
                             ->groupBy('activity_published.id')
                             ->get(['activity_published.*']);
    }

    /**
     * {@inheritdoc}
     */
    public function duplicate(Project $project)
    {
        $projectDetails = array_except($project->toArray(), ['id', 'created_at', 'updated_at', 'activity_workflow']);
        $project        = $this->project->newInstance($projectDetails);

        return $project->save();
    }

    public function getParticipatingOrganizations($id, $orgType)
    {
        $projects      = $this->project->find($id);
        $participating = [];
        foreach ($projects->participating_organization as $participatingOrg) {
            if ($participatingOrg['organization_role'] == $orgType) {
                $participating[] = $participatingOrg;
            }
        }

        return $participating;
    }

    public function getProjectData($projectId)
    {
        if ($projectId) {
//            $project = $this->project->where('activity_workflow', '=', 3)->where('id', '=', $projectId)->first();
            $project = $this->find($projectId);
        } else {
            $project = $this->project->where('activity_workflow', '=', 3)->get();
        }

        return $project;
    }

    public function getProjectsByOrganisationId($orgId)
    {
        if ($orgId && is_numeric($orgId)) {
            return $this->project->where('organization_id', '=', $orgId)->where('activity_workflow', '=', 3)->get();
        }

        return $this->project->where('activity_workflow', '=', 3)->get();
    }

    public function getPublishedProjects($orgId)
    {
        if ($orgId && is_numeric($orgId)) {
            $published = $this->published->getPublishedRowsByOrganization($orgId);
        } else {
            $published = $this->published->getPublishedRows();
        }

        $projects = [];

        foreach ($published as $publish) {
            if($publish->published_activities != null){
                $files = json_decode($publish->published_activities);

                foreach ($files as $filename) {
                    $projectId  = (int) array_last(
                        explode('-', explode('.', $filename)[0]),
                        function ($value) {
                            return true;
                        }
                    );

                    if ($projectId && is_int($projectId)) {
                        $projects[] = $this->project->where('id', '=', $projectId)->with('organization')->first();
                    }
                }
            }
        }

        return $projects;
    }
}
