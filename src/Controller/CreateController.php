<?php
/**
 * @Created by PhpStorm.
 * @Project: Zf3Cmf
 * @User: CooPoo
 * @Copy Right: 2016
 * @Date: 2016-06-30
 * @Time: 11:48
 * @QQ: 259522
 * @FileName: CreateController.php
 */
namespace CmfTool\Controller;


use CmfTool\Model\Skeleton;
use Zend\Console\ColorInterface as Color;
use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;
use Zend\Code\Generator;
use Zend\Mvc\Console\Controller\AbstractConsoleController;
use Zend\View\Model\ConsoleModel;

class CreateController extends AbstractConsoleController
{

    public function controllerAction()
    {
        $console = $this->getConsole();
        $tmpDir = sys_get_temp_dir();
        $request = $this->getRequest();
        $name = $request->getParam('name');

        $module = $request->getParam('module');



        $module = Skeleton::preModule($module);
        $moduleName = $module['name'];
        $moduleFolder = $module['moduleFolder'];
        $viewFolder = $module['viewFolder'];

        $path = $request->getParam('path', '.');

        if (!file_exists("$path/module/$moduleFolder") || !file_exists("$path/config/application.config.php")) {
            return $this->sendError(
                "The path $path doesn't contain a ZF2 application. I cannot create a module here."
            );
        }

        $controller = ucfirst($name) . 'Controller';
        $controllerFile = $path . '/module/' . $moduleFolder . '/src/Controller/' . $controller . '.php';
        if (file_exists($controllerFile)) {
            return $this->sendError(
                "The controller $name already exists in module $moduleName."
            );
        }

        $code = new Generator\ClassGenerator();
        $code->setNamespaceName($moduleName . '\Controller')
            ->addUse('Zend\Mvc\Controller\AbstractActionController')
			->addUse('Cmf\Base\Service\ControllerServiceTrait');

        $code->setName($controller)
            ->addMethods([
				new Generator\MethodGenerator(
					'__construct',
					['controllerService = null'],
					Generator\MethodGenerator::FLAG_PUBLIC,
					'$this->controllerService = $controllerService;'
				),
                new Generator\MethodGenerator(
                    'indexAction',
                    [],
                    Generator\MethodGenerator::FLAG_PUBLIC,
                    'return [];'
                ),
			])
			->addTrait('ControllerServiceTrait')
            ->setExtendedClass('Zend\Mvc\Controller\AbstractActionController');

        $file = new Generator\FileGenerator([
                'classes' => [$code],
        ]);

        $filter = new CamelCaseToDashFilter();
        $dir = $path . "/module/$moduleFolder/view/$viewFolder/" . strtolower($filter->filter($name));
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $phtml = false;
        $phtmlPath = $dir . "/index.phtml";
        $phtmlContent = <<<EOD
<?php
/**
 * @Created by PhpStorm.
 * @Project: mvc2016
 * @User: CooPoo
 * @Copy Right: 2015
 * @QQ: 259522
 * @FileName: index.phtml
 */
/**
 * @var \Zend\View\Renderer\PhpRenderer \$this
 */
 echo 'page holder'; 
 
EOD;

        if (file_put_contents($phtmlPath, $phtmlContent)) {
            $phtml = true;
        }

        if (file_put_contents($controllerFile, $file->generate()) && $phtml == true) {
            $console->writeLine("The controller $name has been created in module $moduleName.", Color::GREEN);
        } else {
            $console->writeLine("There was an error during controller creation.", Color::RED);
        }
    }



    public function moduleAction()
    {
        $console = $this->getConsole();
        $tmpDir = sys_get_temp_dir();
        $request = $this->getRequest();
        $name = $request->getParam('name');

        $module = Skeleton::preModule($name);
        $name = $module['name'];
        $moduleFolder = $module['moduleFolder'];
        $viewFolder = $module['viewFolder'];

        $path = rtrim($request->getParam('path'), '/');
        if (empty($path)) {
            $path = '.';
        }

        if (!file_exists("$path/module") || !file_exists("$path/config/application.config.php")) {
            return $this->sendError(
                "The path $path doesn't contain a ZF2 application. I cannot create a module here."
            );
        }

        if (file_exists("$path/module/$moduleFolder")) {
            return $this->sendError(
                "The module $name already exists."
            );
        }

        mkdir("$path/module/$moduleFolder/src/Controller", 0777, true);
        mkdir("$path/module/$moduleFolder/src/Entity", 0777, true);
        mkdir("$path/module/$moduleFolder/src/Event", 0777, true);
        mkdir("$path/module/$moduleFolder/src/Form", 0777, true);
        mkdir("$path/module/$moduleFolder/src/InputFilter", 0777, true);
        mkdir("$path/module/$moduleFolder/src/Listener", 0777, true);
        mkdir("$path/module/$moduleFolder/src/Mapper", 0777, true);
        mkdir("$path/module/$moduleFolder/src/Model", 0777, true);
        mkdir("$path/module/$moduleFolder/src/Options/Factory", 0777, true);
        mkdir("$path/module/$moduleFolder/src/Service/Factory", 0777, true);
        mkdir("$path/module/$moduleFolder/src/View/Helper", 0777, true);
        mkdir("$path/module/$moduleFolder/view/$viewFolder", 0777, true);

        // Create the Module.php
        file_put_contents("$path/module/$moduleFolder/src/Module.php", Skeleton::getModule($name));

        // Create the module.config.php
        file_put_contents("$path/module/$moduleFolder/src/ConfigProvider.php", Skeleton::getConfigProvider($name));

        // Add the module in application.config.php
        $modules = require "$path/config/modules.config.php";
        if (!in_array($name, $modules)) {
            $modules[] = $name;
            $content = <<<EOD
<?php

EOD;

            $content .= 'return ' . Skeleton::exportConfig($modules) . ";\n";
            file_put_contents("$path/config/modules.config.php", $content);
        }
        if ($path === '.') {
            $console->writeLine("The module $name has been created", Color::GREEN);
        } else {
            $console->writeLine("The module $name has been created in $path", Color::GREEN);
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