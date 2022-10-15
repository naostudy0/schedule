<?php

namespace App\Views\Composers;

use Auth;
use Illuminate\View\View;

class UserComposer
{
    /**
     * データをビューと結合
     *
     * @param  \Illuminate\View\View $view
     * @return void
     */
    public function compose(View $view)
    {
        $user_data = Auth::user();
        $view->with('user_data', $user_data);
    }
}
