<?php

namespace App\Events;

use App\Eloquent\Book;

class AverageStarBookHandler
{
    protected $data;

    public function handle($data)
    {
        $this->data = $data;

        $currentCountReview = $this->data['book']->reviews->count();
        $newAverageStar = ($this->data['book']->avg_star * $currentCountReview + $this->data['star']) / ($currentCountReview + 1);
        $this->data['book']->owners()->updateExistingPivot($this->data['owner_id'], ['avg_star' => $newAverageStar]);
    }
}
