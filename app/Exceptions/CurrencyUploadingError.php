<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CurrencyUploadingError extends HttpException
{
    /**
     * Constructor.
     *
     * @param \Exception $previous The previous exception
     */
    public function __construct(\Exception $previous = null)
    {
        parent::__construct(Response::HTTP_INTERNAL_SERVER_ERROR, 'Currencies uploading failed', $previous);
    }
}