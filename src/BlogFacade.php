<?php

namespace Virtualorz\Blog;

use Illuminate\Support\Facades\Facade;

/**
 * @see Virtualorz\Cate\Cate
 */
class BlogFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'blog';
    }

}
