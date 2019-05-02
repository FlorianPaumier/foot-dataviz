<?php

namespace App\Command;

use App\Entity\Attribut;
use App\Entity\Player;
use App\Entity\PlayerAttribut;
use App\Entity\PlayerInformation;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RewriteInformationCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'RewriteInformation';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->setName("app:rewrite-player");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("doctrine")->getManager();
        $players = $em->getRepository(Player::class)->findAll();
        $attr = $em->getRepository(Attribut::class)->findOneBy(["libelle" => "OVA"]);

        /** @var Player $player */
        foreach ($players as $player) {
            $informations = $player->getInformation();

            /** @var PlayerInformation $information */
            foreach ($informations as $information) {
                /** @var PlayerAttribut $ova */
                $ova = $em->getRepository(PlayerAttribut::class)->findOneBy([
                    "attributs" => $attr,
                    "playerInformation" => $information
                ]);
                $information->setOVA(intval($ova->getScore()));
                $em->persist($information);
            }
            $em->flush();
        }

    }
}
