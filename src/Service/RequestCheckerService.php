<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestCheckerService
{

    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function checkFields (array $content, array $fields): void
    {
        if (!$content) {
            throw new BadRequestHttpException('Request body is empty');
        }

        $missing = array_diff($fields, array_keys($content));

        if (!empty($missing)) {
            throw new BadRequestHttpException('Missing required fields: ' . implode(', ', $missing));
        }

    }

    public function validate(object|array $data, ?array $constraints = null): void
    {

        $valid = null;

        if(is_array($data) && !empty($constraints)) {
            $valid = new Collection($constraints);
        }

        $errors = $this->validator->validate($data, $valid);

        if (count($errors) === 0) {
            return;
        }

        $validErr = [];

        foreach ($errors as $error) {
            $key = $error->getPropertyPath();

            $validErr[$key] = $error->getMessage();
        }

        throw new UnprocessableEntityHttpException(json_encode($validErr));

    }

}