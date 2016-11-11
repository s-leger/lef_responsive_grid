<?php

  namespace LEF\LefResponsiveGrid\Configuration;

abstract class AbstractConfigurationManager implements \TYPO3\CMS\Core\SingletonInterface
{
    protected $configuration = array();

    protected $typoScriptService;

    public function __construct()
    {
        $this->typoScriptService = GeneralUtility::makeInstance('\\TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
    }

    public function getByPath($path)
    {
        $res = $this->configuration;

        $keys = explode('.', $path);
        foreach ($keys as $k=>$key) {
            if (array_key_exists($res, $key)) {
                if (is_array($res[$key])) {
                    $res = $res[$key];
                } else {
                    return $res[$key];
                }
            } else {
                break;
            }
        }
        return $res;
    }

    public function setConfiguration($configuration = array())
    {
        $this->configuration = $this->typoScriptService->convertTypoScriptArrayToPlainArray($configuration);
    }

    public function getConfiguration()
    {
    }

    abstract protected function getTypoScriptSetup();

    abstract protected function getExtensionConfiguration();
}
