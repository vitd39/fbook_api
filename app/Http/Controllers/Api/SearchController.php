<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\GoogleBookInterface;
use App\Http\Requests\Api\Search\GoogleBookRequest;

class SearchController extends ApiController
{
    public function search(GoogleBookRequest $request, GoogleBookInterface $service)
    {
        $data = $request->only([
            'title', 'inauthor', 'subject', 'q', 'maxResults'
        ]);

        return $this->requestAction(function () use ($data, $service) {
            $this->compacts['items'] = $service->search($data);
        });
    }
}
