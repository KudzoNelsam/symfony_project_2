<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ClientSearchDto
{
    #[Assert\Regex(
        pattern: '/^(77|78|76)([0-9]{7})$/',
        message: 'Le numéro de téléphone doit être au format 77XXXXXX ou 78XXXXXX ou 76XXXXXX'
    )]
    public string $telephone;
    public string $surname;

    public function __construct()
    {
        $this->telephone = '';
        $this->surname = '';
    }
}