<?php
namespace PDOMocker;

class Generator extends \PHPUnit_Framework_MockObject_Generator
{
   protected function convertNamespace($className)
    {
        if (isset($className[0]) && $className[0] == '\\') {
            $className = substr($className, 1);
        }

        $classNameParts = explode('\\', $className);
        if (count($classNameParts) > 1) {
            $className          = array_pop($classNameParts);
            $namespaceName = join('\\', $classNameParts);
            if ($namespaceName[0] == '\\') {
                $namespaceName = substr($namespaceName, 1);
            }
        } else {
            $namespaceName = '';
        }
        return array(
            'namespaceName'=>$namespaceName,
            'className'=>$className
        );
    }
    protected function generateMock($type, $methods, $mockClassName, $callOriginalClone, $callAutoload, $cloneArguments, $callOriginalMethods)
    {
        $namespaceParts = $this->convertNamespace($mockClassName);
        $parent = parent::generateMock($type, $methods, $namespaceParts['className'], $callOriginalClone, $callAutoload, $cloneArguments, $callOriginalMethods);
        if(strlen(trim($namespaceParts['namespaceName'])) > 0) {
            $code = 'namespace '.$namespaceParts['namespaceName'].' {'.PHP_EOL;
            $code .= preg_replace('/^(class '.$namespaceParts['className'].' extends )('.$type.')/','$1\\\\$2',$parent['code']).PHP_EOL;
            $code .= '}';
            $parent['code'] = str_replace('PHPUnit_Framework','\\PHPUnit_Framework',$code);
            $parent['mockClassName'] = $mockClassName;
        }
        return $parent;
    }
}