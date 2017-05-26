<?php

namespace App\Services;

use App\Contracts\Services\GlideInterface;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;

class GlideService implements GlideInterface
{
    public function getImageResponse($path, $params)
    {
        $server = app()['glide'];
        try {
            SignatureFactory::create(env('GLIDE_SIGNATURE_KEY'))->validateRequest($path, $params);

            return $server->getImageResponse($path, $params);
        } catch (SignatureException $e) {
            \Log::error($e);

            return response()->file(public_path('images/default_book.jpg'));
        }
    }
}
