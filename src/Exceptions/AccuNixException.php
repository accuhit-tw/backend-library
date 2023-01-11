<?php

namespace Accuhit\BackendLibrary\Exceptions;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class AccuNixException extends RequestException implements GuzzleException
{
}
