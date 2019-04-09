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

class DefaultController extends AbstractController
{

    /**
     * @Rest\Get("/")
     */
    public function scrap()
    {
        $baseUrl = "https://sofifa.com/players?wil=110&wih=269&crl=0&fil=0&hel=0&shl=0&vol=0&drl=0&drh=99&cul=0&cuh=99&frl=0&frh=99&lol=0&loh=99&bll=0&blh=99&acl=0&ach=99&spl=0&sph=99&agl=0&agh=99&rel=0&reh=99&bal=0&bah=99&sol=0&soh=99&jul=0&juh=99&stl=0&sth=99&srl=0&srh=99&lnl=0&lnh=99&arl=0&arh=99&inl=0&inh=99&pol=0&poh=99&vil=0&vih=99&pel=0&peh=99&cml=0&cmh=99&mal=0&mah=99&sal=0&sah=99&sll=0&slh=99&gdl=0&gdh=99&ghl=0&ghh=99&gkl=0&gkh=99&gpl=0&gph=99&grl=0&grh=99&showCol=bp&offset=";
        $client = new Client();

        $em = $this->getDoctrine()->getManager();

        $offset = 0;
        $close = false;
        $firstName = null;

        do {
            $crawler = $client->request("GET", $baseUrl . $offset);

            $rows = $crawler->filter("tbody tr");

            foreach ($rows as $key => $row) {
                if ($key == 0) {
                    $firstName = null;
                }

                $player = new Player();
                $playerData = new PlayerInformation();

                $player->addInformation($playerData);

                $cols = $row->getElementsByTagName("td");

                for ($i = 0; $i < $cols->length; $i++) {
                    /** @var \DOMElement $col */
                    $col = $cols[$i];

                    switch ($i) {
                        case 0:
                            $img = $col->getElementsByTagName("img")[0];
                            /** @var \DOMElement $img */
                            $player->setPictureLink($img->getAttribute("src"));
                            break;
                        case 1:

                            $links = $col->getElementsByTagName('a');
                            $linkFlag = $col->getElementsByTagName('img')[0]->getAttribute("src");
                            $nameFlag = $links[0]->getAttribute("title");

                            $country = $em->getRepository(Country::class)->findOneBy([
                                "name" => trim($nameFlag)
                            ]);

                            if (is_null($country)) {
                                $country = new Country();
                                $country->setName(trim($nameFlag));
                                $country->setFlag(trim($linkFlag));

                                $em->persist($country);
                            }

                            $playerName = $links[1]->getAttribute("title");
                            $player->setName($playerName);
                            $player->setCountry($country);

                            break;
                        case 2:
                            $age = $col->getElementsByTagName('div')[0]->firstChild->data;
                            $playerData->setAge(intval($age));
                            break;
                        case 3:
                            $ova = $col->getElementsByTagName("span")[0]->firstChild->data;

                            $attribut = $em->getRepository(Attribut::class)->findOneBy([
                                "libelle" => "OVA"
                            ]);

                            if (is_null($attribut)) {
                                $attribut = new Attribut();
                                $attribut->setLibelle("OVA");
                                $attribut->setType("Global");
                                $attribut->setDescription("Note Global");

                                $em->persist($attribut);
                            }

                            $playerAttr = new PlayerAttribut();
                            $playerAttr->setScore(trim($ova));
                            $playerAttr->setAttributs($attribut);

                            $playerData->addAttribut($playerAttr);

                            $em->persist($playerAttr);

                            break;
                        case 4:
                            $pot = $col->getElementsByTagName("span")[0]->firstChild->data;

                            $attribut = $em->getRepository(Attribut::class)->findOneBy([
                                "libelle" => "POT"
                            ]);

                            if (is_null($attribut)) {
                                $attribut = new Attribut();
                                $attribut->setLibelle("POT");
                                $attribut->setType("Global");
                                $attribut->setDescription("Potentiel");

                                $em->persist($attribut);
                            }

                            $playerAttr = new PlayerAttribut();
                            $playerAttr->setScore(trim($pot));
                            $playerAttr->setAttributs($attribut);

                            $playerData->addAttribut($playerAttr);

                            $em->persist($playerAttr);

                            break;
                        case 5:
                            $flagClub = $col->getElementsByTagName("img")[0]->getAttribute("src");
                            $clubName = $col->getElementsByTagName("a")[0]->firstChild->data;

                            $club = $em->getRepository(Club::class)->findOneBy([
                                "name" => trim($clubName)
                            ]);

                            if (is_null($club)) {
                                $club = new Club();
                            }

                            $club->setFlag($flagClub);

                            $dates = explode(" ~ ", $col->getElementsByTagName('div')[1]->firstChild->data);

                            $startDate = new \DateTime($dates[0] . "01-01");
                            $endDate = new \DateTime($dates[1] . "01-01");

                            $playerClub = new PlayerClub();
                            $playerClub->setClub($club);
                            $playerClub->setEndedDate($endDate);
                            $playerClub->setStaredDate($startDate);
                            $playerClub->setPlayer($player);
                            $player->addPlayerClub($playerClub);

                            $em->persist($playerClub);
                            $em->persist($club);

                            break;
                        case 6:
                            $weight = $col->getElementsByTagName('div')[0]->firstChild->data;
                            $playerData->setWeight($weight);
                            break;
                        case 7:
                            $pos = $col->getElementsByTagName('span')[0]->firstChild->data;;
                            $playerData->setPosition($pos);
                            break;
                        case 8:
                            $val = $col->getElementsByTagName('div')[0]->firstChild->data;
                            $indice = null;

                            if (strpos($val, "M")) {
                                $indice = 1000000;
                            } elseif (strpos($val, "K")) {
                                $indice = 1000;
                            } else {
                                $indice = 0;
                            }

                            $val = intval(str_replace(["€", "M"], "", $val)) * $indice;
                            $playerData->setValue($val);
                            break;
                        case 9:
                            $val = $col->getElementsByTagName('div')[0]->firstChild->data;

                            $indice = null;

                            if (strpos($val, "M")) {
                                $indice = 1000000;
                            } elseif (strpos($val, "K")) {
                                $indice = 1000;
                            } else {
                                $indice = 0;
                            }

                            $val = (intval(str_replace(["€", "M"], "", $val)) * $indice) * 12;

                            $playerData->setSalary($val);

                            break;
                    }

                    if ($i > 9 && $i < 44) {
                        $playerAttr = new PlayerAttribut();

                        $attr = strtoupper($col->attributes[1]->value);

                        $attribut = $em->getRepository(Attribut::class)->findOneBy([
                            "libelle" => $attr
                        ]);

                        if($i > 9 && $i < 15){
                            if(is_null($attribut)){
                                $attribut = new Attribut();
                                $attribut->setLibelle($attr);
                                $attribut->setType("Offensives");

                                $em->persist($attribut);
                            }
                        }
                        elseif($i >= 15 && $i < 20){
                            if(is_null($attribut)){
                                $attribut = new Attribut();
                                $attribut->setLibelle($attr);
                                $attribut->setType("Techniques");

                                $em->persist($attribut);
                            }
                        }
                        elseif($i >= 20 && $i < 25){
                            if(is_null($attribut)){
                            $attribut = new Attribut();
                            $attribut->setLibelle($attr);
                            $attribut->setType("Mouvement");

                            $em->persist($attribut);
                        }
                        }
                        elseif($i >= 25 && $i < 30){
                            if(is_null($attribut)){
                                $attribut = new Attribut();
                                $attribut->setLibelle($attr);
                                $attribut->setType("Puissance");

                                $em->persist($attribut);
                            }
                        }
                        elseif($i >= 30 && $i < 36){
                            if(is_null($attribut)){
                                $attribut = new Attribut();
                                $attribut->setLibelle($attr);
                                $attribut->setType("État d'esprit");

                                $em->persist($attribut);
                            }
                        }elseif($i >= 36 && $i < 39){
                            if(is_null($attribut)){
                                $attribut = new Attribut();
                                $attribut->setLibelle($attr);
                                $attribut->setType("Défense");

                                $em->persist($attribut);
                            }
                        }elseif($i >= 39 && $i < 44){if(is_null($attribut)){
                            $attribut = new Attribut();
                            $attribut->setLibelle($attr);
                            $attribut->setType("Gardien");

                            $em->persist($attribut);
                        }
                        }

                        $value = $col->getElementsByTagName('span')[0]->firstChild->data;

                        $playerAttr->setAttributs($attribut);
                        $playerAttr->setScore(intval($value));
                        $playerAttr->setPlayerInformation($playerData);

                        $playerData->addAttribut($playerAttr);
                        $em->persist($playerAttr);
                    }
                }

                $em->persist($playerData);

                dump($player);
                die();
            };
        } while (!$close);


        die();
    }
}