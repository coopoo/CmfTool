<?php
/**
 * @Created by PhpStorm.
 * @Project: Zf3Cmf
 * @User: CooPoo
 * @Copy Right: 2016
 * @Date: 2016-06-30
 * @Time: 9:56
 * @QQ: 259522
 * @FileName: ConsoleController.php
 */


namespace CmfTool\Controller;


use Zend\Console\Adapter\AdapterInterface as Console;
use RuntimeException;
use Zend\Mvc\Console\Controller\AbstractConsoleController;

class ConsoleController extends AbstractConsoleController
{
    public function  testAction()
    {
        $console = $this->getConsole();
        if (! $console instanceof Console) {
            throw new RuntimeException('Cannot obtain console adapter. Are we running in a
console?');
        }
        //todo

        echo $console->getTitle();
    }

}