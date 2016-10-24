<?php
/**
 * @Created by PhpStorm.
 * @Project: Zf3Cmf
 * @User: CooPoo
 * @Copy Right: 2016
 * @Date: 2016-06-30
 * @Time: 12:10
 * @QQ: 259522
 * @FileName: RemoveController.php
 */


namespace CmfTool\Controller;


use CmfTool\Model\Skeleton;
use Zend\Console\ColorInterface as Color;
use Zend\Mvc\Console\Controller\AbstractConsoleController;
use Zend\View\Model\ConsoleModel;

class RemoveController extends AbstractConsoleController
{
    public function moduleAction()
    {
        $console = $this->getConsole();

        $request = $this->getRequest();
        $name = $request->getParam('name');
        $path = rtrim($request->getParam('path'), '/');

        $module = Skeleton::preModule($name);
        $name = $module['name'];
        $moduleFolder = $module['moduleFolder'];

        if (empty($path)) {
            $path = '.';
        }
        if (!file_exists("$path/module/$moduleFolder")) {
            return $this->sendError(
                "The module $name does not exist."
            );
        }

        // remove the module in application.config.php
        $modules = require "$path/config/modules.config.php";
        if (in_array($name, $modules)) {
            $modules =array_diff($modules,[$name]) ;
            $content = <<<EOD
<?php

EOD;
            $content .= 'return ' . Skeleton::exportConfig($modules) . ";\n";
            file_put_contents("$path/config/modules.config.php", $content);
        }

        //删除文件
        Skeleton::flush("$path/module/$moduleFolder");

        if ($path === '.') {
            $console->writeLine("The module $name has been removed", Color::GREEN);
        } else {
            $console->writeLine("The module $name has been removed in $path", Color::GREEN);
        }
    }

    protected function sendError($msg)
    {
        $m = new ConsoleModel();
        $m->setErrorLevel(2);
        $m->setResult($msg . PHP_EOL);
        return $m;
    }

}