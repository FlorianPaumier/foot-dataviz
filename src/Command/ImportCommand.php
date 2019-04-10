<?php
/**
 * Created by PhpStorm.
 * User: florianpaumier
 * Date: 2019-03-18
 * Time: 17:43
 */

namespace App\Command;

use App\Entity\Club;
use App\Entity\Game;
use App\Entity\League;
use App\Entity\MatchInformation;
use App\Entity\MatchParameters;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends ContainerAwareCommand
{

    /** @var EntityManagerInterface $em */
    private $em;
    private $path;
    private $logFile;

    protected function configure()
    {
        $dir = dirname(__DIR__, 2) . "/public";

        $this->path = $dir . "/datasets";
        $this->logFile = "/log.txt";

        // the name of the command (the part after "bin/console")
        $this->setName('app:import-data');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = true;

        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $dirs = scandir($this->path);

        foreach ($dirs as $dir) {
            if (!in_array($dir, ['.', '..', ".DS_Store"])) {
                $files = scandir($this->path . '/' . $dir);

                $league = $this->em->getRepository(League::class)->findOneBy([
                    "name" => trim($dir)
                ]);

                if(is_null($league)){
                    $league = (new League())->setName($dir);
                    $this->em->persist($league);
                }


                foreach ($files as $file) {
                    if (!in_array($file, ['.', '..', ".DS_Store"])) {

                        if (pathinfo($this->path . '/' . $dir . '/' . $file)["extension"] === "csv") {
                            $rows = explode("\n", file_get_contents($this->path . '/' . $dir . '/' . $file));

                            $head = [];

                            foreach ($rows as $key => $row) {

                                if ($key > 0) {
                                    $data = str_replace("\r", "", explode(",", $row));
                                    if (count($data) > 1) {
                                        $match = new Game();
                                        $match->setLeague($league);


                                        try {
                                            $date = explode("/", $data[0]);

                                            $match->setPlayingDate(new \DateTime("20" . $date[2] . '-' . $date[1] . '-' . $date[0]));
                                        } catch (\Exception $e) {
                                            dump($e);
                                            break;
                                        }

                                        $clubHome = $this->em->getRepository(Club::class)->findOneBy([
                                            "name" => trim($data[1])
                                        ]);


                                        $clubAway = $this->em->getRepository(Club::class)->findOneBy([
                                            "name" => trim($data[2])
                                        ]);

                                        if (is_null($clubHome)) {
                                            $clubHome = (new Club())->setName($data[1]);
                                            $this->em->persist($clubHome);
                                        }


                                        if (is_null($clubAway)) {
                                            $clubAway = (new Club())->setName($data[2]);
                                            $this->em->persist($clubAway);
                                        }

                                        $match->setAwayTeam($clubAway);
                                        $match->setHomeTeam($clubHome);

                                        for ($i = 3; $i < count($data); $i++) {
                                            $param = new MatchInformation();
                                            $param->setScore($data[$i]);

                                            /** @var MatchParameters $parameter */
                                            $parameter = $this->em->getRepository(MatchParameters::class)->findOneBy([
                                                "name" => $head[$i]
                                            ]);

                                            $param->setParameter($parameter);
                                            $param->setGame($match);

                                            $this->em->persist($param);
                                        }

                                        $this->em->persist($match);
                                        try {
                                            $this->em->flush();
                                        } catch (Exception $e) {
                                            dump($e);
                                            break;
                                        }
                                    }
                                } else {
                                    $head = str_replace("\r", "", explode(",", $row));
                                }
                            }
                        }
                    }
                }

            };
        }
    }

}

