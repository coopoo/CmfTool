<?php
/**
 * @Created by PhpStorm.
 * @Project: CmfTool
 * @User: CooPoo
 * @Copy Right: 2016
 * @Date: 2016-07-06
 * @Time: 10:31
 * @QQ: 259522
 * @FileName: ConfigProvider.php
 */


namespace CmfTool;


use Zend\Mvc\Controller\LazyControllerAbstractFactory;

/**
 * Class ConfigProvider
 * @package CmfTool
 */
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke()
    {
        return [
            'controllers' => $this->getControllersConfig(),
            'console'=>[
                'router'=>$this->getRouterConfig()
            ]
        ];
    }

    /**
     * @return array
     */
    public function getControllersConfig()
    {
        return [
            'abstract_factories'=>[
                LazyControllerAbstractFactory::class
            ]
        ];
    }

    /**
     * @return array
     */
    public function getRouterConfig()
    {
        return [
            'routes' => [
                'cmf-tool-test' => [
                    'options' => [
                        'route'    => 'test',
                        'defaults' => [
                            'controller' => Controller\ConsoleController::class,
                            'action'     => 'test',
                        ],
                    ],
                ],
                'cmf-tool-config-list' => array(
                    'options' => array(
                        'route' => 'config list [--local|-l]:local',
                        'defaults' => array(
                            'controller' => Controller\ConfigController::class,
                            'action' => 'list',
                        ),
                    ),
                ),
                'cmf-tool-create-module' => array(
                    'options' => array(
                        'route' => 'create module <name> [<path>]',
                        'defaults' => array(
                            'controller' => Controller\CreateController::class,
                            'action' => 'module',
                        ),
                    ),
                ),
                'cmf-tool-create-controller' => array(
                    'options' => array(
                        'route' => 'create controller <name> <module> [<path>]',
                        'defaults' => array(
                            'controller' => Controller\CreateController::class,
                            'action' => 'controller',
                        ),
                    ),
                ),
                'cmf-tool-remove-module' => array(
                    'options' => array(
                        'route' => 'remove module <name> [<path>]',
                        'defaults' => array(
                            'controller' => Controller\RemoveController::class,
                            'action' => 'module',
                        ),
                    ),
                ),
            ],
        ];
    }
}