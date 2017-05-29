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

            if (!$server->sourceFileExists($path) && isset($params['p'])) {
                if ($params['p'] === 'thumbnail') {
                    return response()->file(public_path('images/book_thumb_default.jpg'));
                }

                return response()->file(public_path('images/default_book.jpg'));
            }

            return $server->getImageResponse($path, $params);
        } catch (SignatureException $e) {
            \Log::error($e);

            return response()->file(public_path('images/default_book.jpg'));
        }
    }
}
