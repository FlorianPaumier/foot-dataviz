<?php
/**
 * Created by PhpStorm.
 * User: florianpaumier
 * Date: 2019-03-18
 * Time: 17:43
 */

namespace App\Command;

use App\Entity\Actor;
use App\Entity\Club;
use App\Entity\Director;
use App\Entity\Genre;
use App\Entity\League;
use App\Entity\Match;
use App\Entity\MatchInformation;
use App\Entity\MatchParameters;
use App\Entity\Movie;
use App\Entity\People;
use App\Entity\Writer;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

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

        $this->em = $this->getContainer()->get('doctrine')->getEntityManager();
        $dirs = scandir($this->path);

        foreach ($dirs as $dir){
            if(!in_array($dir, ['.', '..', ".DS_Store"])){
                $files = scandir($this->path.'/'.$dir);
                $league = (new League())->setName($dir);

                foreach ($files as $file){
                    if(!in_array($file, ['.', '..', ".DS_Store"])){

                        if(pathinfo($this->path.'/'.$dir.'/'.$file)["extension"] === "csv"){
                            $rows = explode("\n",file_get_contents($this->path.'/'.$dir.'/'.$file));

                            $head = [];

                            foreach ($rows as $key => $row){

                                if($key > 0){
                                    $data = str_replace("\r","",explode(",", $row));
                                    $match = new Match();

                                    dump($data);
                                    try {
                                        $match->setPlayingDate(new \DateTime($data[0]));

                                    } catch (\Exception $e) {
                                    }

                                    $clubHome = $this->em->getRepository(Club::class)->findOneBy([
                                        "name" => $data[1]
                                    ]);


                                    $clubAway = $this->em->getRepository(Club::class)->findOneBy([
                                        "name" => $data[2]
                                    ]);

                                    if(is_null($clubHome)){
                                        $clubHome = (new Club())->setName($data[1]);
                                    }


                                    if(is_null($clubAway)){
                                        $clubAway = (new Club())->setName($data[1]);
                                    }

                                    $match->setAwayTeam($clubAway);
                                    $match->setHomeTeam($clubHome);

                                    for($i = 3; $i < count($data); $i++){
                                        $param = new MatchInformation();
                                        $param->setScore($data[$i]);

                                        /** @var MatchParameters $parameter */
                                        $parameter = $this->em->getRepository(MatchParameters::class)->findOneBy([
                                            "name" => $head[$i]
                                        ]);

                                        $param->setParameter($parameter);
                                        $param->setGame($match);

                                    }
                                    break;
                                }else{
                                    $head = str_replace("\r","",explode(",", $row));
                                }
                            }
                        }
                    }
                }

                break;
            };
        }
    }


    private function getIdMovies(OutputInterface $output)
    {
        $data = [];

        $output->writeln("Get the file");
        $file = file_get_contents($this->path);

        $output->writeln("Parsing file");
        $rows = explode("\n", $file);

        foreach ($rows as $key => $row) {
            $split = explode("\t", $row);
            array_push($data, $split[0]);
        }

        $string = join(";", $data);

        $output->writeln("Create the new file IDs");
        file_put_contents($this->idsFile, $string);

        return true;
    }

    private function getOmdbMovies(OutputInterface $output)
    {
        $output->writeln("Init Params");
        $data = explode(";", file_get_contents($this->idsFile));
        $client = new Client();
        $usedData = $data;

        foreach ($data as $key => $id) {

            $output->writeln("Look if the movies exist : " . $id);
            $movie = $this->em->getRepository(Movie::class)->findOneBy([
                "imdbID" => $id
            ]);

            if (is_null($movie)) {
                $movie = new Movie();
            }

            $output->writeln("create the uri the id : " . $id);
            $url = $this->urlOmdb . "?i=" . $id . "&apikey=" . $this->key;

            $output->writeln("Request");
            do {
                $exp = false;
                try {
                    $res = $client->request("GET", $url);
                } catch (RequestException $exception) {
                    $output->writeln($exception->getMessage());
                    $exp = true;
                }
                $output->writeln($res->getStatusCode());
            } while ($exp);

            $json = $res->getBody()->getContents();

            $output->writeln("Parsing into object");
            $content = json_decode($json);

            $release = $content->Released !== "N/A" ? new \DateTime($content->Released) : null;
            $revenu = isset($content->BoxOffice) ? ($content->BoxOffice === "N/A" ? 0 : floatval(str_replace("$", "", $content->BoxOffice))) : 0;
            $movie->setTitle($content->Title)
                ->setImdbID($content->imdbID)
                ->setReleaseDate($release)
                ->setRevenue($revenu);

            $output->writeln("Add Genre");
            $gendersLib = explode(",", $content->Genre);
            foreach ($gendersLib as $genderLib) {
                $genre = $this->em->getRepository(Genre::class)->findOneBy([
                    "name" => trim($genderLib)
                ]);

                if (is_null($genre)) {
                    $genre = new Genre();
                    $genre->setName($genderLib);
                    $this->em->persist($genre);
                };

                $movie->addGenre($genre);
            }

            $output->writeln("Add Actor");
            $actors = explode(",", $content->Actors);
            foreach ($actors as $actorName) {
                $actor = $this->em->getRepository(Actor::class)
                    ->findByName(trim($actorName));

                if (is_null($actor)) {
                    $people = new People();
                    $people->setName($actorName);

                    $actor = new Actor();
                    $actor->setType("Acting");
                    $actor->setPeople($people);

                    $this->em->persist($actor);
                };

                $movie->addActor($actor);
            }

            $output->writeln("Add Director");
            $directors = explode(",", $content->Director);
            foreach ($directors as $directorName) {
                $director = $this->em->getRepository(Director::class)->findByName(trim($directorName));

                if (is_null($director)) {
                    $people = new People();
                    $people->setName($directorName);

                    $director = new Director();
                    $director->setType("Directing")
                        ->setPeople($people);

                    $this->em->persist($director);
                };

                $movie->addDirector($director);
            }

            $output->writeln("Add Writer");
            $writers = explode(",", $content->Writer);
            foreach ($writers as $writerName) {
                $writerName = trim(explode("(", $writerName)[0]);

                $writer = $this->em->getRepository(Writer::class)->findByName($writerName);

                if (is_null($writer)) {
                    $people = new People();
                    $people->setName($writerName);

                    $writer = new Writer();
                    $writer->setType("Writing");
                    $writer->setPeople($people);

                    $this->em->persist($writer);
                };

                $movie->addwriter($writer);
            }


            $this->em->persist($movie);

            $this->em->flush();
        }

        $output->writeln("========================\n\r");

        array_shift($usedData);
        if ($key % 100 === 0) {
            $this->em->flush();

            $output->writeln("Create the new file IDs");
            file_put_contents($this->idsFile, join(";", $usedData));
        }

        $output->writeln("========================\n\r");


        $output->writeln("End");
        file_put_contents($this->idsFile, join(";", $usedData));

        return true;
    }

    private function getTmdbMovies(OutputInterface $output)
    {
        $movies = $this->em->getRepository(Movie::class)->findAll();
    }

    private function getTmdbPeopleActor(OutputInterface $output)
    {
        $actors = $this->em->getRepository(Actor::class)->findAll();
    }

    private function getTmdbPeopleDirector(OutputInterface $output)
    {
        $directors = $this->em->getRepository(Director::class)->findAll();
    }

    private function getTmdbPeopleWriter(OutputInterface $output)
    {
        $writers = $this->em->getRepository(Writer::class)->findAll();
    }

    private function cleanActor(EntityManagerInterface $manager)
    {

        $actors = $manager->getRepository(Actor::class)->findAll();

        /** @var Actor $actor */
        foreach ($actors as $actor) {
            file_put_contents($this->logFile, $manager->getRepository(Actor::class)->find($actor->getId())->getId(), FILE_APPEND);
            if ($manager->getRepository(Actor::class)->find($actor->getId())) {

                $name = trim($actor->getPeople()->getName());

                $same = $manager->getRepository(Actor::class)->findDuplicateByName($name, $actor->getId());

                /** @var Actor $item */
                foreach ($same as $item) {
                    $movies = $item->getMovies();

                    foreach ($movies as $movie) {
                        if (!$actor->getMovies()->contains($movie)) {
                            $actor->addMovie($movie);
                        }
                        $item->removeMovie($movie);
                    }

                    $manager->remove($item);
                    file_put_contents($this->logFile, $item->getId() . PHP_EOL, FILE_APPEND);
                }

            }
            file_put_contents(dirname(__DIR__, 2) . "/public/log.txt", "Flush \n\r", FILE_APPEND);
            $manager->flush();
        }
    }

    private function cleanDirector(EntityManagerInterface $manager)
    {
        $directors = $manager->getRepository(Director::class)->findAll();


        foreach ($directors as $director) {
            $name = $director->getPeople()->getName();

            $same = $manager->getRepository(Director::class)->findDuplicateByName($name, $director->getId());

            dump($same);
        }
    }

    private function cleanWriter(EntityManagerInterface $manager)
    {
        $writers = $manager->getRepository(Writer::class)->findAll();


        foreach ($writers as $writer) {
            $name = $writer->getPeople()->getName();

            $same = $manager->getRepository(Writer::class)->findDuplicateByName($name, $writer->getId());

            dump($same);
        }
    }
}

