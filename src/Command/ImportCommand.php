<?php
/**
 * Created by PhpStorm.
 * User: florianpaumier
 * Date: 2019-03-18
 * Time: 17:43
 */

namespace App\Command;

use App\Entity\Actor;
use App\Entity\Director;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\People;
use App\Entity\Writer;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ImportCommand extends ContainerAwareCommand
{

    private $urlOmdb = "http://www.omdbapi.com/";
    private $key = "3915d4f1";
    private $path;
    private $idsFile;
    private $manager;

    /** @var EntityManagerInterface $em */
    private $em;

    private $menu = [
        "1" => "Get Ids",
        "2" => "Import Data",
        "3" => "Import Movie From tmdb",
        "4" => "Import Actor From tmdb",
        "5" => "Import Director From tmdb",
        "6" => "Import Writer From tmdb",
        "7" => "Exit"
    ];

    protected function configure()
    {
        $dir = dirname(__DIR__, 2) . "/public";

        $this->path = $dir . "/data.tsv";
        $this->idsFile = $dir . "/id.txt";

        // the name of the command (the part after "bin/console")
        $this->setName('app:import-data');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = true;

        $this->em = $this->getContainer()->get('doctrine')->getEntityManager();
        //Menu
        do {
            $output->writeln("====================");
            $output->writeln("Import Movies Data : ");
            $output->writeln("====================");
            $question = new ChoiceQuestion("Choose a task", $this->menu);

            $helper = $this->getHelper("question");
            $option = $helper->ask($input, $output, $question);
            $index = array_search($option, $this->menu);
            //Lunch Function
            switch ($index) {
                case 1:
                    $result = $this->getIdMovies($output);
                    break;
                case 2:
                    $result = $this->getOmdbMovies($output);
                    break;
                case 3:
                    $result = $this->getTmdbMovies($output);
                    break;
                case 4:
                    $result = $this->getTmdbPeopleActor($output);
                    break;
                case 5:
                    $result = $this->getTmdbPeopleDirector($output);
                    break;
                case 6:
                    $result = $this->getTmdbPeopleWriter($output);
                    break;
                case 7:
                    $result = !$result;
                    break;
            }

        } while ($result);

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

            if(is_null($movie)) {
                $output->writeln("create the uri the id : " . $id);
                $url = $this->urlOmdb . "?i=" . $id . "&apikey=" . $this->key;

                $output->writeln("Request");
                do{
                    $exp = false;
                    try{
                        $res = $client->request("GET", $url);
                    }catch (RequestException $exception){
                        $output->writeln($exception->getMessage());
                        $exp = true;
                    }
                    $output->writeln($res->getStatusCode());
                }while($exp);

                $json = $res->getBody()->getContents();

                $output->writeln("Parsing into object");
                $content = json_decode($json);

                $movie = new Movie();

                $release = $content->Released !== "N/A" ? new \DateTime($content->Released) : null;
                $revenu = isset($content->BoxOffice) ? ( $content->BoxOffice === "N/A" ? 0 : intval(str_replace("$", "", $content->BoxOffice))) : 0;
                $movie->setTitle($content->Title)
                    ->setImdbID($content->imdbID)
                    ->setReleaseDate($release)
                    ->setRevenue($revenu);

                $output->writeln("Add Genre");
                $gendersLib = explode(",", $content->Genre);
                foreach ($gendersLib as $genderLib){
                    $genre = $this->em->getRepository(Genre::class)->findOneBy([
                        "name" => $genderLib
                    ]);
                    if(is_null($genre)){
                        $genre = new Genre();
                        $genre->setName($genderLib);
                        $this->em->persist($genre);
                    };

                    $movie->addGenre($genre);
                }

                $output->writeln("Add Actor");
                $actors = explode(",", $content->Actors);
                foreach ($actors as $actorName){
                    $actor = $this->em->getRepository(Actor::class)
                        ->findByName($actorName);

                    if(is_null($actor)){
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
                foreach ($directors as $directorName){
                    $director = $this->em->getRepository(Director::class)->findByName($directorName);

                    if(is_null($director)){
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
                foreach ($writers as $writerName){
                    $writerName = trim(explode("(", $writerName)[0]);

                    $writer = $this->em->getRepository(Writer::class)->findByName($writerName);

                    if(is_null($writer)){
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
            }

            $output->writeln("========================\n\r");

            array_shift($usedData);
            if($key % 100 === 0) {
                $this->em->flush();

                $output->writeln("Create the new file IDs");
                file_put_contents($this->idsFile, join(";", $usedData));
            }

            $output->writeln("========================\n\r");
        }

        $output->writeln("End");
        file_put_contents($this->idsFile, join(";",$usedData));

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
}

