<?php namespace App\Services\XmlImporter\Foundation;

use App\Services\XmlImporter\Foundation\Mapper\XmlMapper;
use App\Services\XmlImporter\Foundation\Support\Providers\TemplateServiceProvider;

/**
 * Class XmlProcessor
 * @package App\Services\XmlImporter\Foundation
 */
class XmlProcessor
{
//    use MapperFactory;

    /**
     * @var TemplateServiceProvider
     */
    protected $templateServiceProvider;

    /**
     * @var array
     */
    protected $transactions = [];

    /**
     * @var
     */
    protected $xmlMapper;

    /**
     * Xml constructor.
     * @param TemplateServiceProvider $templateServiceProvider
     * @param XmlMapper               $xmlMapper
     */
    public function __construct(TemplateServiceProvider $templateServiceProvider, XmlMapper $xmlMapper)
    {
        $this->templateServiceProvider = $templateServiceProvider;
        $this->xmlMapper               = $xmlMapper;
    }

    /**
     * Process the uploaded Xml data into AidStream compatible data format.
     *
     * @param array $xml
     * @param       $version
     */
    public function process(array $xml, $version)
    {
        $this->xmlMapper->assign($version)
                        ->map($xml, $this->templateServiceProvider->load());

        dd($this->xmlMapper);
    }
}
