<?php namespace App\Lite\Services\Validation;

use App\Lite\Services\Validation\Rules\RulesProvider;
use Illuminate\Validation\Factory;

/**
 * Class ValidationService
 * @package App\Lite\Services\Validation
 */
class ValidationService
{
    /**
     * @var
     */
    protected $data;

    /**
     * @var
     */
    protected $entity;

    /**
     * @var
     */
    protected $version;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var
     */
    protected $validator;

    /**
     * @var RulesProvider
     */
    protected $rulesProvider;

    /**
     * ValidationService constructor.
     *
     * @param Factory       $factory
     * @param RulesProvider $rulesProvider
     */
    public function __construct(Factory $factory, RulesProvider $rulesProvider)
    {
        $this->factory       = $factory;
        $this->rulesProvider = $rulesProvider;
    }

    /**
     * Checks if the validation passes
     *
     * @param array $data
     * @param       $entityType
     * @param       $version
     * @return mixed
     */
    public function passes(array $data, $entityType, $version)
    {
        $this->validator = $this->factory->make(
            $data,
            $this->rulesProvider->getRules($version, $entityType),
            $this->rulesProvider->getMessages($version, $entityType)
        );

        return $this->validator->passes();
    }

    /**
     * Returns errors if validation fails.
     *
     * @return mixed
     */
    public function errors()
    {
        if ($this->validator) {
            return $this->validator->errors();
        }
    }
}
