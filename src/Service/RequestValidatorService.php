<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(array $data, array $rules): array
    {
        $constraint = new Assert\Collection($rules);
        $violations = $this->validator->validate($data, $constraint);
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }
        return $errors;
    }

    /**
     * Validate that the given fields are not blank in the data array.
     * @param array $data
     * @param array $fields
     * @return array
     */
    public function validateNotBlankFields(array $data, array $fields): array
    {
        $rules = [];
        foreach ($fields as $field) {
            $rules[$field] = new Assert\NotBlank();
        }
        return $this->validate($data, $rules);
    }
}
