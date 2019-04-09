<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Info(title="Symfony 4 REST API", version="1.0", @OA\Contact(name="Guillaume ADAM", email="guillaume@gadam.fr"))
 * @OA\Server(url="http://127.0.0.1/api", description="Production environment")
 * @OA\Server(url="http://127.0.0.1/app_dev.php/api/", description="Development environment")
 * @OA\SecurityScheme(type="http", bearerFormat="JWT", scheme="bearer", securityScheme="bearerAuth")
 */
class DocumentationController extends AbstractController
{
    /**
     * @Route("/doc", name="documentation")
     */
    public function index()
    {
        return $this->render('documentation/index.html.twig');
    }

    /**
     * @Route(path="/doc.json", name="documentation-json")
     */
    public function jsonDocAction(KernelInterface $kernel)
    {
        $openapi = \OpenApi\scan($kernel->getProjectDir().'/src');

        return Response::create($openapi->toJson());
    }
}
