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

namespace McCool\LaravelAutoPresenter\Decorators;

class ArrayDecorator extends BaseDecorator implements DecoratorInterface
{
    /**
     * Can the subject be decorated?
     *
     * If the subject is an array, then it can be decorated.
     *
     * @param mixed $subject
     *
     * @return bool
     */
    public function canDecorate($subject)
    {
        return is_array($subject);
    }

    /**
     * Decorate an array. As we don't know what might be in
     * the array, we defer to the PresenterDecorator class to check
     * all the available decorators
     *
     * @param array $subject
     *
     * @return array
     */
    public function decorate($subject)
    {
        foreach ($subject as $key => $value) {
            $subject[$key] = $this->getPresenterDecorator()->decorate($value);
        }

        return $subject;
    }
}
