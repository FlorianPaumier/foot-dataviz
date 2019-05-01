<?php

namespace App\Command;

use App\Entity\Attribut;
use App\Entity\Player;
use App\Entity\PlayerAttribut;
use App\Entity\PlayerInformation;
use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPlayerSofifaCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:import-player-sofifa';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseUrl = [
            "https://sofifa.com/players?v=19gender=0",
            "https://sofifa.com/players?v=18gender=0",
            "https://sofifa.com/players?v=17gender=0",
            "https://sofifa.com/players?v=19gender=1",
            "https://sofifa.com/players?v=18gender=1",
            "https://sofifa.com/players?v=17gender=1",
        ];

        $label = ["International Reputation","Weak Foot", "Skill Moves", "Work Rate", "Jersey Number"];

        $client = new Client();

        $em = $this->getContainer()->get("doctrine")->getManager();

        $offset = 0;

        foreach ($baseUrl as $position => $url) {
            $crawler = $client->request("GET", $url);

            $close = false;
            do {
                dump($offset);
                $rows = $crawler->filter("tbody tr");

                foreach ($rows as $key => $row) {

                    /** @var \DOMElement[] $cols */
                    $cols = $row->getElementsByTagName("td");
                    /** @var \DOMElement $node */
                    $node = $cols[1]->getElementsByTagName('a')[1]->firstChild->data;
                    $link = $crawler->selectLink($node)->link();
                    $playerName = $cols[1]->getElementsByTagName('a')[1]->getAttribute("title");

                    /** @var Player $player */
                    $player = $em->getRepository(Player::class)->findOneBy(["name" => $playerName]);
                    $playerInformation = $player->getInformation();
                    $currentInfo = null;

                    foreach ($playerInformation as $information){
                        if($position % 3 == 0){
                            if($information->getEffectiveDate() == new \DateTime("2019-01-01")){
                                $currentInfo = $information;
                            }
                        }

                        if($position % 3 == 1){
                            if($information->getEffectiveDate() ==  new \DateTime("2018-01-01")){
                                $currentInfo = $information;
                            }
                        }

                        if($position % 3 == 2){
                            if($information->getEffectiveDate() ==  new \DateTime("2017-01-01")){
                                $currentInfo = $information;
                            }
                        }
                    }

                    if(!is_null($currentInfo)){
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

                                    $attibut = $em->getRepository(Attribut::class)->findOneBy(["libelle" => trim($attr)]);

                                    if(is_null($attibut)){
                                        $attibut = (new Attribut())
                                            ->setLibelle(trim($attr))
                                            ->setType("profil")
                                            ->setDescription(trim($attr));

                                        $em->persist($attibut);

                                        $em->flush();
                                    }

                                    if(!is_null($line[2]) && $line[2]->nodeName == "span"){
                                        $value = $line[2]->firstChild->data;
                                    }elseif(!is_null($line[2])){
                                        $value = $line[2]->data;
                                    }

                                    if($attr == "Jersey Number"){
                                        $value = $line[1]->data;
                                    }

                                    $playerAttr = (new PlayerAttribut())
                                        ->setScore(trim($value))
                                        ->setPlayerInformation($currentInfo)
                                        ->setAttributs($attibut);

                                    $em->persist($playerAttr);
                                }
                            }
                        }
                    }
                }

                $em->flush();
                $btn = $crawler->selectLink('Next');

                if ($btn->count()) {
                    $lnk = $crawler->selectLink('Next')->link();

                    $crawler = $client->click($lnk);

                } else {
                    $close = true;
                }

                $offset += 60;
                if($offset > 240){
                    $offset = 0;
                    $close = true;
                }
            } while (!$close);

            break;
        };
    }
}
