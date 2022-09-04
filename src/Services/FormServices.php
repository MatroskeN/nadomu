<?php

namespace App\Services;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Security\Core\Security as CoreSecurity;

class FormServices
{
    private CoreSecurity $security;

    public function __construct(CoreSecurity $security)
    {
        $this->security = $security;
    }
    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     * 
     * @return [type]
     */
    public function validateFileId($value, ExecutionContextInterface $context)
    {
        $user = $this->security->getUser();
        if ($value && $value->getUser()->getId() != $user->getId())
            return $context
                ->buildViolation('Данный файл вам не принадлежит!')
                ->addViolation();
    }

    /**
     * Проверка 
     * 
     * @param mixed $array
     * 
     * @return bool
     */
    public function validateWorkTime($array, ExecutionContextInterface $context)
    {
        $result = array_reduce($array, function ($a, $b) {
            static $stored = array();

            $hash = md5(serialize($b));

            if (!in_array($hash, $stored)) {
                $stored[] = $hash;
                $a[] = $b;
            }

            return $a;
        }, array());
        if ($array != $result) {
            return $context
                ->buildViolation('Дата и время сопадает')
                ->addViolation();
        }
    }
}
