<?php
/**
 * Created by PhpStorm.
 * User: florianpaumier
 * Date: 2019-03-21
 * Time: 10:03
 */

namespace App\Controller;

use App\Entity\Attribut;
use App\Entity\Club;
use App\Entity\Country;
use App\Entity\Player;
use App\Entity\PlayerAttribut;
use App\Entity\PlayerClub;
use App\Entity\PlayerInformation;
use FOS\RestBundle\Controller\Annotations as Rest;
use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{

    /**
     * @Rest\Get("/import-data/sofifa")
     * @throws \Exception
     */
    public function scrap()
    {


    }


    /**
     * @Rest\Get("/test")
     */
    public function index()
    {
        phpinfo();
        return $this->render("view.html.twig");
    }

    /**
     * @Rest\Post("/add_product", name="add_product")
     */
    public function addProduct(Request $request)
    {
        $name = $request->request->get("product");
        $price = $request->request->get("price");

        $tva = 1.2;
        $ship = 2;

        $ttc = $this->getTTC($price, $tva, $ship);

        return $this->render("render.html.twig", [
            "product" => $name,
            "price" => $price,
            "tva" => $tva,
            "ship" => $ship,
            "ttc" => $ttc,
        ]);
    }


    public function getTTC($price, $tva, $ship)
    {
        return ($price + $ship) * $tva;
    }
}
