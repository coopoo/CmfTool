<?php
/**
 * @Created by PhpStorm.
 * @Project: Zf3Cmf
 * @User: CooPoo
 * @Copy Right: 2016
 * @Date: 2016-06-30
 * @Time: 11:54
 * @QQ: 259522
 * @FileName: Skeleton.php
 */


namespace CmfTool\Model;


use GlobIterator;
use Zend\Code\Generator\ValueGenerator;
use Zend\Stdlib\ErrorHandler;
use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;

class Skeleton
{

    protected static $valueGenerator;

    public static function preModule($name)
    {
        $nameArray = explode('\\',$name);
        $name = [];
        $moduleFolder = [];
        $filter = new CamelCaseToDashFilter();
        foreach ($nameArray as $item){
            $method  = str_replace(['.', '-', '_'], ' ', $item);
            $method  = ucwords($method);
            $method  = str_replace(' ', '', $method);
            $name[]=$method;
            $moduleFolder[] = strtolower($filter->filter($method));
        }
        return [
            'name'=>implode('\\',$name),
            'moduleFolder'=>implode('-',$moduleFolder),
            'viewFolder' => implode('\\',$moduleFolder)
        ];
    }

    public static function getModule($name)
    {
        return <<<EOD
<?php
namespace $name;


use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
    
/**
 * Class Module
 * @package {$name}
 */
class Module implements BootstrapListenerInterface,
ConfigProviderInterface
{
    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface|MvcEvent \$event
     * @return array
     */
    public function onBootstrap(EventInterface \$event)
    {
        \$application = \$event->getApplication();
        \$oEventManager =\$application->getEventManager();
    }
    /**
     * @return array
     */
    public function getConfig()
    {
        \$provider = new ConfigProvider();
        return \$provider();
    }
}

EOD;
    }

    public static function getConfigProvider($name)
    {
        return <<<EOD
<?php
namespace {$name};


use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

/**
 * Class ConfigProvider
 * @package {$name}
 */
class ConfigProvider
{
     /**
     * @return array
     */
    public function __invoke()
    {
        return [
            'service_manager' => \$this->getDependencyConfig(),
            'view_helpers'    => \$this->getViewHelperConfig(),
            'view_manager'    => \$this->getViewManagerConfig(),
            'router'          => \$this->getRouteConfig(),
            'module_layouts'   => [
                // __NAMESPACE__ => 'layout/layout'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'abstract_factories'=>[],
            'factories'=>[],
        ];
    }
    
    /**
     * @return array
     */
    public function getViewHelperConfig()
    {
        return [
            'factories'=>[]
        ];
    }

    /**
     * @return array
     */
    public function getViewManagerConfig()
    {
        return [
            'template_path_stack' => [
                __NAMESPACE__ =>__DIR__ . '/../view',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getRouteConfig()
    {
        return [
            'routes' => [
                 // '{$name}' => [
                    // 'type' => Literal::class,
                    // 'options' => [
                        // 'route'    => '/',
                        // 'defaults' => [
                            // 'controller' => Controller\IndexController::class,
                            // 'action'     => 'index',
                        // ],
                    // ],
                // ],
				// '{$name}' => [
				   // 'type' => Segment::class,
				   // 'options' => [
					   // 'route' => '/{$name}[/:action[/:id][/page_:page]].html',
					   // 'constraints' => [
						   // 'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						   // 'id' => '[a-zA-Z][a-zA-Z0-9]*',
						   // 'page' => '\d+',
					   // ],
					   // 'defaults' => [
						   // 'controller' => Controller\IndexController::class,
						   // 'action' => 'index',
					   // ],
				   // ],
				// ],
            ],
        ];
    }
}
EOD;

    }

    public static function exportConfig($config, $indent = 0)
    {
        if (empty(static::$valueGenerator)) {
            static::$valueGenerator = new ValueGenerator();
        }
        static::$valueGenerator->setValue($config);
        static::$valueGenerator->setArrayDepth($indent);

        return static::$valueGenerator;
    }

    public static function flush($dir)
    {
        $flags = GlobIterator::SKIP_DOTS | GlobIterator::CURRENT_AS_PATHNAME;
        $clearFolder = function () {
        };
        $clearFolder = function ($dir) use (& $clearFolder, $flags) {
            $it = new GlobIterator($dir . DIRECTORY_SEPARATOR . '*', $flags);
            foreach ($it as $pathname) {
                if ($it->isDir()) {
                    $clearFolder($pathname);
                    rmdir($pathname);
                } else {
                    unlink($pathname);
                }
            }
        };
        ErrorHandler::start();
        $clearFolder($dir);
        rmdir($dir);
        $error = ErrorHandler::stop();
        if ($error) {
            throw new \RuntimeException("Flushing directory '{$dir}' failed", 0, $error);
        }

        return true;
    }
}