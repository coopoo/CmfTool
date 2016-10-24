<?php
/**
 * @Created by PhpStorm.
 * @Project: Zf3Cmf
 * @User: CooPoo
 * @Copy Right: 2016
 * @Date: 2016-06-30
 * @Time: 9:54
 * @QQ: 259522
 * @FileName: Module.php
 */


namespace CmfTool;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

class Module implements ConsoleUsageProviderInterface
{

    /**
     * @return array
     */
    public function getConfig()
    {
        $provider = new ConfigProvider();
        return $provider();
    }

    public function getConsoleBanner(Console $console)
    {
        return  'Application 1.0';
    }

    public function getConsoleUsage(Console $console)
    {
        return [
            'Basic information:',
            'modules [list]'              => 'show loaded modules',
            'version | --version'         => 'display current Zend Framework version',
            'test' => 'console test',

            'Application configuration:',
            'config list' => 'list all configuration options',
            'config get <name>'         => 'display a single config value, i.e. "config get db.host"',
            'config set <name> <value>' => 'set a single config value (use only to change scalar values)',

            'Project creation:',
            'create project <path>'     => 'create a skeleton application',
            array('<path>', 'The path of the project to be created'),

            'Module creation:',
            'create module <name> [<path>]' => 'create a module',
            ['<name>', 'The name of the module to be created'],
            ['<path>', 'The root path of a ZF2 application where to create the module'],

            'remove module <name> [<path>]' => 'remove a module',
            ['<name>', 'The name of the module to be removed'],
            ['<path>', 'The root path of a ZF2 application where to remove the module'],

            'Controller creation:',
            'create controller <name> <module> [<path>]' => 'create a controller in module',
            array('<name>', 'The name of the controller to be created'),
            array('<module>', 'The module in which the controller should be created'),
            array('<path>', 'The root path of a ZF2 application where to create the controller'),

            'Action creation:',
            'create action <name> <controllerName> <module> [<path>]' => 'create an action in a controller',
            array('<name>', 'The name of the action to be created'),
            array('<controllerName>', 'The name of the controller in which the action should be created'),
            array('<module>', 'The module containing the controller'),
            array('<path>', 'The root path of a ZF2 application where to create the action'),

            'Classmap generator:',
            'classmap generate <directory> <classmap file> [--append|-a] [--overwrite|-w]' => '',
            array('<directory>',        'The directory to scan for PHP classes (use "." to use current directory)'),
            array('<classmap file>',    'File name for generated class map file  or - for standard output. '.
                 'If not supplied, defaults to autoload_classmap.php inside <directory>.'),
            array('--append | -a',      'Append to classmap file if it exists'),
            array('--overwrite | -w',   'Whether or not to overwrite existing classmap file'),

            'Zend Framework 2 installation:',
            'install zf <path> [<version>]' => '',
            array('<path>', 'The directory where to install the ZF2 library'),
            array('<version>', 'The version to install, if not specified uses the last available'),
        ];
    }


}