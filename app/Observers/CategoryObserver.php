<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function created($category)
    {
        $this->refreshCategoryCache();
    }

    /**
     * Handle the Category "updated" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function updated($category)
    {
        $this->refreshCategoryCache();
    }

    /**
     * Handle the Category "deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function deleted($category)
    {
        $this->refreshCategoryCache();
    }

    public function restored($category)
    {
        $this->refreshCategoryCache();
    }

    public function forceDeleted($category)
    {
        $this->refreshCategoryCache();
    }

    private function refreshCategoryCache()
    {
        Cache::forget(CATEGORIES_WITH_CHILDES);
    }
}
