<?php
namespace MIA3\VarAccess\Tests\Unit\Fixtures;

/**
 */
class DummyObject {

    public $publicProperty;

    protected $protectedProperty;

    private $privateProperty;

    protected $propertyWithGetSet;

    public function __construct($publicProperty, $protectedProperty, $privateProperty, $propertyWithGetSet) {
        $this->publicProperty = $publicProperty;
        $this->protectedProperty = $protectedProperty;
        $this->privateProperty = $privateProperty;
        $this->propertyWithGetSet = $propertyWithGetSet;
    }

    public function getPropertyWithGetSet() {
        return $this->propertyWithGetSet;
    }

    public function setPropertyWithGetSet($value) {
        $this->propertyWithGetSet = $value;
    }

}
