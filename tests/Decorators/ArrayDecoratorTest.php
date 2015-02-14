<?php

/*
 * This file is part of Laravel Auto Presenter.
 *
 * (c) Shawn McCool <shawn@heybigname.com>
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace McCool\Tests\Decorators;

use McCool\LaravelAutoPresenter\Decorators\ArrayDecorator;
use McCool\Tests\AbstractTestCase;
use McCool\Tests\Stubs\DecoratedAtom;
use McCool\Tests\Stubs\DecoratedAtomPresenter;
use Mockery as m;

class ArrayDecoratorTest extends AbstractTestCase
{
    private $arrayDecorator;

    protected function start()
    {
        $container = m::mock('Illuminate\Contracts\Container\Container')->makePartial();
        $container->shouldReceive('make')->with('autopresenter')->andReturn($this->app->make('autopresenter'));
        $this->arrayDecorator = new ArrayDecorator($container);
    }

    public function testCanDecorateCollection()
    {
        $array = ['a','b','c'];

        $this->assertTrue($this->arrayDecorator->canDecorate($array));
        $this->assertFalse($this->arrayDecorator->canDecorate('garbage stuff yo'));
    }

    public function testDecorationOfAnArrayOfAtoms()
    {
        $array = [ new DecoratedAtom(), new DecoratedAtom() ];
        $array = $this->arrayDecorator->decorate($array);

        $this->assertInstanceOf(DecoratedAtomPresenter::class, $array[0]);
        $this->assertInstanceOf(DecoratedAtomPresenter::class, $array[1]);
    }

    public function testDecorationOfAnArrayOfCollections()
    {
        $collection = m::mock('Illuminate\Support\Collection')->makePartial();
        $collection->shouldReceive('put')->with(2, 'something');

        $array = [ $collection, $collection ];
        $this->arrayDecorator->decorate($array);
    }

    public function testDecorationOfAnArrayOfModelsAndCollections()
    {
        $collection = m::mock('Illuminate\Support\Collection')->makePartial();
        $collection->shouldReceive('put')->with(2, 'something');

        $array = [ new DecoratedAtom(), $collection ];

        $array = $this->arrayDecorator->decorate($array);
        $this->assertInstanceOf(DecoratedAtomPresenter::class, $array[0]);
    }

    public function testDecorationOfAnArrayOfMoreArrays()
    {
        $arrayOfAtoms = [ new DecoratedAtom(), new DecoratedAtom() ];
        $array = [ $arrayOfAtoms, $arrayOfAtoms ];

        $array = $this->arrayDecorator->decorate($array);

        $this->assertInstanceOf(DecoratedAtomPresenter::class, $array[0][0]);
        $this->assertInstanceOf(DecoratedAtomPresenter::class, $array[0][1]);
        $this->assertInstanceOf(DecoratedAtomPresenter::class, $array[1][0]);
        $this->assertInstanceOf(DecoratedAtomPresenter::class, $array[1][1]);
    }
}
