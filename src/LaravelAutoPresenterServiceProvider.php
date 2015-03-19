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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;
use McCool\LaravelAutoPresenter\Decorators\ArrayDecorator;
use McCool\LaravelAutoPresenter\Decorators\AtomDecorator;
use McCool\LaravelAutoPresenter\Decorators\CollectionDecorator;
use McCool\LaravelAutoPresenter\Decorators\PaginatorDecorator;
use McCool\LaravelAutoPresenter\Decorators\EnrichedActivityDecorator;

class LaravelAutoPresenterServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupEventFiring($this->app);

        $this->setupEventListening($this->app);
    }

    /**
     * Setup the event firing.
     *
     * Every time a view is rendered, fire a new event.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function setupEventFiring(Application $app)
    {
        $app['view']->composer('*', function ($view) use ($app) {
            if ($view instanceof View) {
                $app['events']->fire('content.rendering', [$view]);
            }
        });
    }

    /**
     * Setup the event listening.
     *
     * Every time the event fires, decorate the bound data.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function setupEventListening(Application $app)
    {
        $defaultKeys = ['__env','app'];

        $app['events']->listen('content.rendering', function (View $view) use ($app, $defaultKeys) {

            if ($viewData = array_merge($view->getFactory()->getShared(), $view->getData())) {
                $decorator = $app['autopresenter'];

                foreach ($viewData as $key => $value) {
                    if (in_array($key, $defaultKeys)) {
                        continue;
                    }
                    $view[$key] = $decorator->decorate($value);
                }
            }

        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAtomDecorator($this->app);
        $this->registerCollectionDecorator($this->app);
        $this->registerPaginatorDecorator($this->app);
        $this->registerPresenterDecorator($this->app);
        $this->registerArrayDecorator($this->app);
        $this->registerEnrichedActivityDecorator($this->app);
    }

    /**
     * Register the atom decorator.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function registerAtomDecorator(Application $app)
    {
        $app->singleton('autopresenter.atom', function (Application $app) {
            return new AtomDecorator($app);
        });

        $app->alias('autopresenter.atom', 'McCool\LaravelAutoPresenter\Decorators\AtomDecorator');
    }

    /**
     * Register the array decorator.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function registerArrayDecorator(Application $app)
    {
        $app->singleton('autopresenter.array', function (Application $app) {
            return new ArrayDecorator($app);
        });

        $app->alias('autopresenter.array', 'McCool\LaravelAutoPresenter\Decorators\ArrayDecorator');
    }

    /**
     * Register the collection decorator.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function registerCollectionDecorator(Application $app)
    {
        $app->singleton('autopresenter.collection', function (Application $app) {
            return new CollectionDecorator($app);
        });

        $app->alias('autopresenter.collection', 'McCool\LaravelAutoPresenter\Decorators\CollectionDecorator');
    }

    /**
     * Register the collection decorator.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function registerEnrichedActivityDecorator(Application $app)
    {
        $app->singleton('autopresenter.enrichedactivity', function (Application $app) {
            return new EnrichedActivityDecorator($app);
        });

        $app->alias('autopresenter.enrichedactivity', 'McCool\LaravelAutoPresenter\Decorators\EnrichedActivityDecorator');
    }

    /**
     * Register the paginator decorator.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function registerPaginatorDecorator(Application $app)
    {
        $app->singleton('autopresenter.paginator', function (Application $app) {
            return new PaginatorDecorator($app);
        });

        $app->alias('autopresenter.paginator', 'McCool\LaravelAutoPresenter\Decorators\PaginatorDecorator');
    }

    /**
     * Register the presenter decorator.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function registerPresenterDecorator(Application $app)
    {
        $app->singleton('autopresenter', function (Application $app) {

            $atom = $app['autopresenter.atom'];
            $collection = $app['autopresenter.collection'];
            $paginator = $app['autopresenter.paginator'];
            $array = $app['autopresenter.array'];
            $enrichedActivity = $app['autopresenter.enrichedactivity'];

            $presenter = new PresenterDecorator();
            $presenter->addDecorator('atom', $atom);
            $presenter->addDecorator('collection', $collection);
            $presenter->addDecorator('paginator', $paginator);
            $presenter->addDecorator('array', $array);
            $presenter->addDecorator('enrichedactivity', $enrichedActivity);

            return $presenter;
        });

        $app->alias('autopresenter', 'McCool\LaravelAutoPresenter\PresenterDecorator');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'autopresenter',
            'autopresenter.atom',
            'autopresenter.collection',
            'autopresenter.paginator',
            'autopresenter.array',
            'autopresenter.enrichedactivity',
        ];
    }
}
