<?php
/**
 * @Created by PhpStorm.
 * @Project: Zf3Cmf
 * @User: CooPoo
 * @Copy Right: 2016
 * @Date: 2016-06-30
 * @Time: 11:01
 * @QQ: 259522
 * @FileName: ConfigController.php
 */


namespace CmfTool\Controller;


use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Console\ColorInterface as Color;
use Zend\Config\Writer\Ini as IniWriter;
use Zend\Mvc\Console\Controller\AbstractConsoleController;

class ConfigController extends AbstractConsoleController
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function __construct(ServiceManager $serviceManager = null)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    public function listAction()
    {
        $console = $this->getConsole();
        $sm = $this->getServiceLocator();
        $isLocal = $this->params()->fromRoute('local');
        if ($isLocal) {
            $appdir = getcwd();
            echo $appdir;
            if (file_exists($appdir . '/config/autoload/local.php')) {
                $config = include $appdir . '/config/autoload/local.php';
            } else {
                echo 'FILE NO EXIST' . PHP_EOL;
                $config = array();
            }
        } else {
            $config = $sm->get('Configuration');
        }

        if (!is_array($config)) {
            $config = ArrayUtils::iteratorToArray($config, true);
        }

        $console->writeLine('Configuration:', Color::GREEN);
        // print_r($config);
        $ini = new IniWriter;
        echo $ini->toString($config);
    }
}