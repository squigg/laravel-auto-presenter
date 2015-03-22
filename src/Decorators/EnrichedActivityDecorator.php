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
use McCool\LaravelAutoPresenter\Exceptions\PresenterNotFound;

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

        // Decorate the actor, object and target
        $toDecorate = ['actor','object','target'];

        foreach ($toDecorate as $field) {
            if (array_key_exists($field, $subject)) {
                $subject[$field] = $this->getPresenterDecorator()->decorate($subject[$field]);
            }
        }

        return $subject;
    }

}
