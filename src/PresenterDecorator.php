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

namespace McCool\LaravelAutoPresenter;

class PresenterDecorator
{
    /**
     * The decorators.
     *
     * @var array
     */
    protected $decorators = [];

    /**
     * Add a decorator to the list of usable decorators
     *
     * @param $key
     * @param $decorator
     */
    public function addDecorator($key, $decorator)
    {
        $this->decorators[$key] = $decorator;
    }

    /**
     * Things go in, get decorated (or not) and are returned.
     *
     * @param mixed $subject
     *
     * @return mixed
     */
    public function decorate($subject)
    {
        foreach ($this->decorators as $decorator) {
            if ($decorator->canDecorate($subject)) {
                return $decorator->decorate($subject);
            }
        }

        return $subject;
    }
}
