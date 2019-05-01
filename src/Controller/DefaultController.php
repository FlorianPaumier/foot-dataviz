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
        $baseUrl = [
            "https://sofifa.com/players?v=19gender=0",
            "https://sofifa.com/players?v=19gender=0",
            "https://sofifa.com/players?v=19gender=0",
            "https://sofifa.com/players?v=19gender=1",
            "https://sofifa.com/players?v=19gender=1",
            "https://sofifa.com/players?v=19gender=1",
        ];

        $label = ["International Reputation","Weak Foot", "Skill Moves", "Work Rate", "Jersey Number"];

        $client = new Client();

        $em = $this->getDoctrine()->getManager();

        $offset = 0;
        $close = false;

        foreach ($baseUrl as $position => $url) {
            $crawler = $client->request("GET", $url);
            do {
                $rows = $crawler->filter("tbody tr");

                foreach ($rows as $key => $row) {

                    /** @var \DOMElement[] $cols */
                    $cols = $row->getElementsByTagName("td");
                    /** @var \DOMElement $node */
                    $node = $cols[1]->getElementsByTagName('a')[1]->firstChild->data;
                    $link = $crawler->selectLink($node)->link();
                    $playerName = $cols[1]->getElementsByTagName('a')[1]->getAttribute("title");
                    $player = $em->getRepository(Player::class)->findOneBy(["name" => $playerName]);

                    dump($player);
                    $clientPlayer = new Client();
                    $crawlerPlayer = $clientPlayer->click($link);

                    $list = $crawlerPlayer->filter("ul.pl li")->getIterator();

                    /** @var \DOMElement[] $node */
                    foreach ($list as $index => $node){
                        /** @var \DOMElement[] $line */
                        $line = $node->childNodes;
                        $attr = null;
                        $value = null;

                        for($i = 0; $i < $line->length; $i++){
                            if($line[$i]->nodeName == "label" && in_array($line[$i]->firstChild->data, $label)){
                                $attr = $line[$i]->childNodes[0]->data;

                                if(!is_null($line[2]) && $line[2]->nodeName == "span"){
                                    $value = $line[2]->firstChild->data;
                                }elseif(!is_null($line[2])){
                                    $value = $line[2]->data;
                                }

                                if($attr == "Jersey Number"){
                                    $value = $line[1]->data;
                                }
                            }
                        }
                    }

                    die();
                }

                if($offset > 120){
                    $close = true;
                }
            } while (!$close);
        };

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
