<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationList;
use Throwable;

class RuntimeConstraintExceptionListener
{

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $code = $this->getCode($exception);

        $message = $this->getErrors($exception);

        $event->setResponse(new JsonResponse([
            'code' => $code,
            'message' => $message,
        ] , $code));
    }

    public function getCode(Throwable $exception) : int
    {
        $status = method_exists($exception, 'getStatusCode');

        if ($status) {
            return $exception->getStatusCode();
        }

        if (is_int($exception->getCode()) && $exception->getCode() > 0){
            return $exception->getCode();
        }

        return Response::HTTP_UNPROCESSABLE_ENTITY;

    }

    public function getErrors(Throwable $exception): array
    {
        $errors = [];

        $constraint = method_exists($exception, "getConstraintViolationList");

        if ($constraint) {
            foreach ($exception->getConstraintViolationList() as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $errors;
        }

        $decoded = json_decode($exception->getMessage(), true);

        if (is_array($decoded)) {
                if (isset($decoded['data']['errors'])) {
                    return $decoded['data']['errors'];
                }
                return $decoded;
            }

            return ['error' => $exception->getMessage()];
        }
}