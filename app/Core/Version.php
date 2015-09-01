<?php
namespace App\Core;

use App;
use Auth;

class Version
{
    protected $activity;
    protected $organization;
    protected $version;
    protected $activityElement;
    protected $organizationElement;
    protected $settingsElement;
    protected $repository;
    protected $formElement;
    protected $iatiAttibutes = array();

    public function __construct()
    {
        $this->setVersion();
    }

    /**
     * @return mixed
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param mixed $activity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion()
    {
        $this->version = "V201";//will be stored in database
//        $this->activityElement = App::make("App\Core\\$this->version\IatiActivity");
//        $this->organizationElement = App::make("App\Core\\$this->version\IatiOrganization");
        $this->settingsElement = App::make("App\Core\\$this->version\IatiSettings");
        return $this;
    }

    /**
     * @return array
     */
    public function getIatiAttibutes()
    {
        return $this->iatiAttibutes;
    }

    /**
     * @param array $iatiAttibutes
     */
    public function setIatiAttibutes($iatiAttibutes)
    {
        $this->iatiAttibutes = $iatiAttibutes;
    }

    public function getActivityElement()
    {
        return $this->activityElement;
    }

    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param mixed $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    public function getOrganizationElement()
    {
        return $this->organizationElement;
    }

    public function getAddMoreForm()
    {
        return $this->formElement;
    }

    public function getSettingsElement()
    {
        return $this->settingsElement;
    }

}