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

use GetStream\StreamLaravel\EnrichedActivity;

class EnrichedActivityDecorator extends BaseDecorator implements DecoratorInterface
{
    /**
     * Can the subject be decorated?
     *
     * If the subject is an eloquent model, or implements has presenter, then
     * it can be decorated.
     *
     * @param mixed $subject
     *
     * @return bool
     */
    public function canDecorate($subject)
    {
        return ($subject instanceof EnrichedActivity);
    }

    /**
     * Decorate an EnrichedActivity instance.
     *
     * @param object $subject
     * @return object
     */
    public function decorate($subject)
    {
        if (! $subject instanceof EnrichedActivity) {
            return $subject;
        }

        // Decorate the 'actor'
        $toDecorate = ['actor','object'];

        foreach ($toDecorate as $field) {

            $subject[$field] = $this->getPresenterDecorator()->decorate($subject[$field]);
        }

        return $subject;
    }

}
