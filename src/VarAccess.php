<?php
namespace MIA3\VarAccess;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 */
class VarAccess {

    protected static $accessCache = array();

    protected $variable;

    public function __construct(&$variable) {
        $this->variable = &$variable;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function get($path) {
        return $this->getInternal($this->variable, explode('.', $path));
    }

    /**
     * @param $variable
     * @param $pathSegments
     * @return mixed
     */
    protected function getInternal($variable, $pathSegments) {
        $currentSegment = array_shift($pathSegments);

        switch(TRUE) {
            case is_array($variable):
                $value = $variable[$currentSegment];
                break;

            case is_object($variable):
                $value = $this->getPropertyFromObject($variable, $currentSegment);
                break;
        }

        if (empty($pathSegments)) {
            return $value;
        }

        return $this->getInternal($value, $pathSegments);
    }

    /**
     * @param string $path
     * @param mixed $value
     * @return void
     */
    public function set($path, $value) {
        $this->setInternal($this->variable, explode('.', $path), $value);
    }

    /**
     * @param mixed $variable
     * @param array $pathSegments
     * @param mixed $value
     * @return void
     */
    protected function setInternal(&$variable, $pathSegments, $value) {
        $targetSegment = array_pop($pathSegments);
        if (count($pathSegments) > 0) {
            foreach ($pathSegments as $pathSegment) {

            }
        } else {
            $targetVariable = &$variable;
        }
        switch(TRUE) {
            case is_array($targetVariable):
                $targetVariable[$targetSegment] = $value;
                break;

            case is_object($targetVariable):
                $this->setPropertyOnObject($targetVariable, $targetSegment, $value);
                break;
        }
    }

    /**
     * @param $path
     * @return bool
     */
    public function has($path) {
        return $this->hasInternal($this->variable, explode('.', $path));
    }

    /**
     * @param $variable
     * @param $pathSegments
     * @return bool
     */
    protected function hasInternal($variable, $pathSegments) {
        $currentSegment = array_shift($pathSegments);

        $hasSegment = FALSE;
        switch(TRUE) {
            case is_array($variable):
                $hasSegment = isset($$variable[$currentSegment]);
                break;

            case is_object($variable):
                $hasSegment = $this->isPropertyGettable($variable, $currentSegment);
                break;
        }

        if ($hasSegment === FALSE) {
            return FALSE;
        }

        if (empty($pathSegments)) {
            return $hasSegment;
        }

        return $this->hasInternal($value, $pathSegments);
    }

    /**
     * @param $object
     * @param $property
     * @return mixed
     */
    protected function getPropertyFromObject($object, $property) {
        $access = $this->getObjectAccess($object);
        if ($this->isPropertyGettable($object, $property)) {
            return $access[$property]['get']($object);
        }
    }

    /**
     * @param $object
     * @param $property
     * @return mixed
     */
    protected function setPropertyOnObject(&$object, $property, $value) {
        $access = $this->getObjectAccess($object);
        if ($this->isPropertySettable($object, $property)) {
            $access[$property]['set']($object, $value);
        }
    }

    /**
     * @param $object
     * @param $property
     * @return bool
     */
    protected function isPropertyGettable($object, $property) {
        $access = $this->getObjectAccess($object);
        return isset($access[$property]['get']);
    }

    /**
     * @param $object
     * @param $property
     * @return bool
     */
    protected function isPropertySettable($object, $property) {
        $access = $this->getObjectAccess($object);
        return isset($access[$property]['set']);
    }

    /**
     * @param $object
     * @return array
     */
    public function getObjectAccess($object) {
        if ($object instanceof \stdClass) {
            $access = array();
            $properties = array_keys(get_object_vars($object));
            foreach ($properties as $property) {
                $access[$property]['get'] = function ($object) use ($property) {
                    return $object->$property;
                };
                $access[$property]['set'] = function ($object, $value) use ($property) {
                    return $object->$property = $value;
                };
            }
            return $access;
        }

        $access = array();
        $classReflection = new \ReflectionClass($object);
        foreach ($classReflection->getProperties() as $propertyReflection) {
            $propertyName = $propertyReflection->getName();
            switch (TRUE) {
                case $propertyReflection->isPublic():
                    $access[$propertyName]['get'] = function ($object) use ($propertyName) {
                        return $object->$propertyName;
                    };
                    $access[$propertyName]['set'] = function ($object, $value) use ($propertyName) {
                        return $object->$propertyName = $value;
                    };
                    break;

                default:
                    $uppercasePropertyName = ucfirst($propertyName);
                    $getMethodName = 'get' . $uppercasePropertyName;
                    if (is_callable(array($object, $getMethodName))) {
                        $access[$propertyName]['get'] = function ($object) use ($getMethodName) {
                            return $object->$getMethodName();
                        };
                    }

                    $setMethodName = 'set' . $uppercasePropertyName;
                    if (is_callable(array($object, $setMethodName))) {
                        $access[$propertyName]['set'] = function($object, $value) use($setMethodName) {
                            $object->$setMethodName($value);
                        };
                    }

//                    $isMethodName = 'is' . $uppercasePropertyName;
//                    if (is_callable(array($object, $$isMethodName))) {
//                        $access[$propertyName]['is'] = function($object) use($isMethodName) {
//                            return $object->$isMethodName();
//                        };
//                    }
//                    $hasMethodName ='has' . $uppercasePropertyName;
//                    if (is_callable(array($object, $hasMethodName))) {
//                        $access[$propertyName]['has'] = function($object) use($hasMethodName) {
//                            return $object->$hasMethodName();
//                        };
//                    }
            }
        }

        return $access;
    }
}
