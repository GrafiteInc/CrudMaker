<?php

namespace Yab\CrudMaker\Services;

class TestService
{
    /**
     * Determine if given template filename is a service only template.
     *
     * @param string $filename
     *
     * @return bool
     */
    public function isServiceTest($filename)
    {
        $allowedTypes = [
            'Repository',
            'Service',
        ];

        foreach ($allowedTypes as $allowedType) {
            if (strpos($filename, $allowedType) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filter the tests.
     *
     * @param array        $templates
     * @param string|array $serviceOnly
     * @param string|array $apiOnly
     * @param string|array $withApi
     *
     * @return array
     */
    public function filterTestTemplates($templates, $serviceOnly, $apiOnly, $withApi)
    {
        $serviceTests = $this->getServiceTestTemplates($templates, $serviceOnly);

        if (!$serviceOnly) {
            $apiTests = $this->getApiTestTemplates($templates, $apiOnly, $withApi);
            $regularTests = $this->getRegularTestTemplates($templates, $apiOnly);

            return array_merge($serviceTests, $apiTests, $regularTests);
        }

        return $serviceTests;
    }

    /**
     * Filter the services.
     *
     * @param array        $templates
     * @param string|array $serviceOnly
     *
     * @return array
     */
    public function getServiceTestTemplates($templates, $serviceOnly)
    {
        $filteredTemplates = [];

        foreach ($templates as $template) {
            if ($this->isServiceTest($template)) {
                $filteredTemplates[] = $template;
            }
        }

        return $filteredTemplates;
    }

    /**
     * Filter the Api tests.
     *
     * @param array        $templates
     * @param string|array $apiOnly
     * @param string|array $withApi
     *
     * @return array
     */
    public function getApiTestTemplates($templates, $apiOnly, $withApi)
    {
        $filteredTemplates = [];

        foreach ($templates as $template) {
            if (stristr($template->getBasename(), 'Api')) {
                if ($apiOnly || $withApi) {
                    $filteredTemplates[] = $template;
                }
            }
        }

        return $filteredTemplates;
    }

    /**
     * Filter the Api tests.
     *
     * @param array        $templates
     * @param string|array $apiOnly
     * @param string|array $withApi
     *
     * @return array
     */
    public function getRegularTestTemplates($templates, $apiOnly)
    {
        $filteredTemplates = [];

        foreach ($templates as $template) {
            if (stristr($template->getBasename(), 'AcceptanceTest')) {
                if (!$apiOnly) {
                    $filteredTemplates[] = $template;
                }
            } else {
                $filteredTemplates[] = $template;
            }
        }

        return $filteredTemplates;
    }
}
