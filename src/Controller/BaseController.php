<?php
namespace App\Controller;

use App\DataObjects\ResponseDTO;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class BaseController
 */
class BaseController extends AbstractController
{
    /**
     * Validates the posted token and returns a response accordingly.
     *
     * @param string $key    : The key of the CSRF session to validate against.
     * @param string $token  : The posted token to validate.
     * @param int $errorCode : The error code to add to the response.
     * @param bool $isAjax   : Indicates whether to retrieve a json response or not.
     *
     * @param string|null $redirectRoute
     *
     * @return JsonResponse|Response|null
     */
    protected function validateCsrf(string $key, ?string $token, int $errorCode, bool $isAjax = false, string $redirectRoute = null)
    {
        if ($this->isCsrfTokenValid($key, $token)) {
            return null;
        }

        if ($isAjax) {
            $response = new ResponseDTO(false, 'Invalid token', $errorCode);
            return new JsonResponse($response->toArray(), 403);
        }

        return new Response('Invalid token', 403);
    }

    /**
     * @param \Throwable|null $ex
     * @param string|null $message
     * @param int|null $errorCode
     * @param array $params
     * @param int|null $httpStatus
     *
     * @return JsonResponse
     */
    protected function handleError(?\Throwable $ex = null, ?string $message = null,
                                   ?int $errorCode = null, array $params = [], ?int $httpStatus = 500)
    {
        $error = new ResponseDTO(
            success: false,
            message: $message ?? $ex->getMessage(),
            errorCode: $errorCode ?? $ex->getCode(),
            status: $httpStatus,
            params: $params
        );

        return $error->response();
    }
}