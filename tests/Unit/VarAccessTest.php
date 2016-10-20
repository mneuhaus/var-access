<?php
namespace MIA3\VarAccess\Tests\Unit;

use MIA3\VarAccess\Tests\Unit\Fixtures\DummyObject;
use MIA3\VarAccess\VarAccess;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 * Class SimpleFileCacheTest
 */
class VarAccessTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function simpleArrayGetTest() {
        $variable = array(
            'name' => 'toni',
            'company' => array(
                'name' => 'ACME'
            ),
            'projects' => array(
                array(
                    'name' => 'some project'
                )
            )
        );
        $varAccess = new VarAccess($variable);

        $this->assertEquals($varAccess->get('company'), $variable['company']);
        $this->assertEquals($varAccess->get('name'), $variable['name']);
        $this->assertEquals($varAccess->get('company.name'), $variable['company']['name']);
        $this->assertEquals($varAccess->get('projects.0'), $variable['projects'][0]);
        $this->assertEquals($varAccess->get('projects.0.name'), $variable['projects'][0]['name']);
    }

    /**
     * @test
     */
    public function publicPropertyFromStdClassTest() {
        $variable = (object) array(
            'name' => 'toni',
            'company' => (object) array(
                'name' => 'ACME'
            ),
            'projects' => array(
                (object) array(
                    'name' => 'some project'
                )
            )
        );
        $varAccess = new VarAccess($variable);
        $varAccess->get('foo');
        $varAccess->set('foo', 'foobar');
        $varAccess->has('foo');

        $this->assertEquals($varAccess->get('company'), $variable->company);
        $this->assertEquals($varAccess->get('name'), $variable->name);
        $this->assertEquals($varAccess->get('company.name'), $variable->company->name);
        $this->assertEquals($varAccess->get('projects.0'), $variable->projects[0]);
        $this->assertEquals($varAccess->get('projects.0.name'), $variable->projects[0]->name);
    }

    /**
     * @test
     */
    public function publicPropertyFromDummyObjectTest() {
        $variable = new DummyObject('donald', 'tick', 'trick', 'track');
        $varAccess = new VarAccess($variable);

        $this->assertEquals($varAccess->get('publicProperty'), $variable->publicProperty);
        $this->assertEquals($varAccess->get('propertyWithGetSet'), $variable->getPropertyWithGetSet());
    }

    /**
     * @test
     */
    public function simpleArraySetTest() {
        $variable = array(
            'name' => NULL,
            'company' => array(
                'name' => NULL
            ),
            'projects' => array(
                array(
                    'name' => NULL
                )
            )
        );
        $varAccess = new VarAccess($variable);

        $varAccess->set('name', 'name');
        $varAccess->set('company.name', 'companyName');
        $varAccess->set('projects.0.name', 'projectName');
        $this->assertEquals($variable['name'], 'name');
        $this->assertEquals($variable['company']['name'], 'companyName');
        $this->assertEquals($variable['projects'][0]['name'], 'projectName');
    }

    /**
     * @test
     */
    public function setDummyObjectTest() {
        $variable = new DummyObject('donald', 'tick', 'trick', 'track');
        $varAccess = new VarAccess($variable);

        $this->assertEquals($varAccess->get('publicProperty'), $variable->publicProperty);
        $this->assertEquals($varAccess->get('propertyWithGetSet'), $variable->getPropertyWithGetSet());
    }
}
