<?php

namespace App\Controller\Api;


use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\ControllerTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class ApiController extends AbstractFOSRestController
{
    use ControllerTrait;

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    protected function errorNotFound($message = null, $code = 404)
    {
        if ($message === null) {
            $translator = $this->translator;
            $message = $translator->trans('api.not_found', [], 'errors');
        }

        return $this->errorResponse($message, $code);
    }
}