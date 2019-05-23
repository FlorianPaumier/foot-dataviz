<?php

namespace App\Controller\Api;

use App\Entity\Club;
use App\Entity\Player;
use App\Model\Representation\Pagination;
use App\Repository\PlayerRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Knp\Component\Pager\PaginatorInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\Route("/player")
 */
class PlayerController extends ApiController
{
    /**
     * @Rest\Get
     * @Rest\View(serializerGroups={"player_light","club_light","country_light", "pagination"}, statusCode=Response::HTTP_OK)
     * @Rest\QueryParam(name="sort", default="id", requirements="name|id|gender")
     * @Rest\QueryParam(name="direction", default="asc", requirements="asc|desc")
     * @Rest\QueryParam(name="page", default=1, requirements="\d+")
     * @Rest\QueryParam(name="limit", default=Pagination::DEFAULT_LIMIT, requirements="\d+")
     * @Rest\QueryParam(name="gender", nullable=true, requirements="\d+")
     * @Rest\QueryParam(name="country", default=0, requirements="\d+")
     * @OA\Get(
     *     path="/player",
     *     tags={"Player"},
     *     summary=DESCRIPTION_GET_ALL,
     *     @OA\Parameter(in="query", name="sort", description="field on which the sort is done", @OA\Schema(type="string", enum={"name","id", "gender"})),
     *     @OA\Parameter(in="query", name="direction", description="direction of the sort", @OA\Schema(type="string", enum={"asc","desc"})),
     *     @OA\Parameter(in="query", name="page", description="the page to return", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="limit", description="the number of result per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="gender", description="Type of gender", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="country", description="id of the country", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Player")))
     * )
     */
    public function index(ParamFetcher $paramFetcher, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();

        $gender = $paramFetcher->get("gender");
        $country = $paramFetcher->get("country");

        $qb = $em->getRepository(Player::class)->createQueryBuilder('p')
            ->leftJoin("p.information", 'i')
            ->leftJoin("p.country", 'c')
            ->leftJoin("p.playerClubs", 'pc')
            ->leftJoin("i.attributs", "a")
            ->leftJoin("a.attributs", "ia");

        if(!is_null($gender)){
            $qb->andWhere("p.gender = :gender")
                ->setParameter("gender", $gender);
        }

        if($country != 0){
            $qb->andWhere("c.id = :id")
                ->setParameter("id" , $country);
        }

        return Pagination::paginate($qb, $paginator, $paramFetcher);
    }

    /**
     * @Rest\Get("/{id}")
     * @Rest\View(serializerGroups={"player","club_light","country_light","attribut","player_attribut","information","pagination"}, statusCode=Response::HTTP_OK)
     * @OA\Get(
     *     path="/player/{id}",
     *     tags={"Player"},
     *     summary=DESCRIPTION_GET,
     *     @OA\Parameter(in="path", name="id", description="Ressource ID", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Player")))
     * )
     */
    public function show(Player $player)
    {
        return $player;
    }

    /**
     * @Rest\Get("/club/{id}")
     * @Rest\View(serializerGroups={"pagination"}, statusCode=Response::HTTP_OK)
     * @Rest\QueryParam(name="sort", default="id", requirements="name|id")
     * @Rest\QueryParam(name="direction", default="asc", requirements="asc|desc")
     * @Rest\QueryParam(name="page", default=1, requirements="\d+")
     * @Rest\QueryParam(name="limit", default=Pagination::DEFAULT_LIMIT, requirements="\d+")
     * @OA\Get(
     *     path="/player/club/{id}",
     *     tags={"Player"},
     *     summary=DESCRIPTION_GET_ALL,
     *     @OA\Parameter(in="query", name="sort", description="field on which the sort is done", @OA\Schema(type="string", enum={"name","id"})),
     *     @OA\Parameter(in="query", name="direction", description="direction of the sort", @OA\Schema(type="string", enum={"asc","desc"})),
     *     @OA\Parameter(in="query", name="page", description="the page to return", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="limit", description="the number of result per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="path", name="id", description="Ressource ID", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Player")))
     * )
     */
    public function getByClub(Club $club, ParamFetcher $paramFetcher, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository(Player::class)->createQueryBuilder('p')
            ->leftJoin("p.playerClubs", "pc")
            ->leftJoin("pc.club", "c")
            ->andWhere("c.id = :id")->setParameter("id", $club->getId());

        return Pagination::paginate($qb, $paginator, $paramFetcher);
    }

    /**
     * @Rest\Get("/gender/{gender}")
     * @Rest\View(serializerGroups={"player_light","club_light","country_light", "pagination"}, statusCode=Response::HTTP_OK)
     * @Rest\QueryParam(name="sort", default="id", requirements="name|id")
     * @Rest\QueryParam(name="direction", default="asc", requirements="asc|desc")
     * @Rest\QueryParam(name="page", default=1, requirements="\d+")
     * @Rest\QueryParam(name="limit", default=Pagination::DEFAULT_LIMIT, requirements="\d+")
     * @OA\Get(
     *     path="/player/gender",
     *     tags={"Player"},
     *     summary=DESCRIPTION_GET_ALL,
     *     @OA\Parameter(in="path", name="query", description="Gender type", @OA\Schema(type="boolean")),
     *     @OA\Parameter(in="query", name="sort", description="field on which the sort is done", @OA\Schema(type="string", enum={"name","id"})),
     *     @OA\Parameter(in="query", name="direction", description="direction of the sort", @OA\Schema(type="string", enum={"asc","desc"})),
     *     @OA\Parameter(in="query", name="page", description="the page to return", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="limit", description="the number of result per page", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Player")))
     * )
     */
    public function getByGender(ParamFetcher $paramFetcher, PaginatorInterface $paginator, $gender)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository(Player::class)->createQueryBuilder('p')
            ->andWhere("p.gender = :gender")->setParameter("gender", $gender);

        return Pagination::paginate($qb, $paginator, $paramFetcher);
    }

    /**
     * @Rest\Get("/country/{country}")
     * @Rest\View(serializerGroups={"player_light","club_light","country_light", "pagination"}, statusCode=Response::HTTP_OK)
     * @Rest\QueryParam(name="sort", default="id", requirements="name|id")
     * @Rest\QueryParam(name="direction", default="asc", requirements="asc|desc")
     * @Rest\QueryParam(name="page", default=1, requirements="\d+")
     * @Rest\QueryParam(name="limit", default=Pagination::DEFAULT_LIMIT, requirements="\d+")
     * @OA\Get(
     *     path="/player/gender",
     *     tags={"Player"},
     *     summary=DESCRIPTION_GET_ALL,
     *     @OA\Parameter(in="path", name="query", description="Country name", @OA\Schema(type="boolean")),
     *     @OA\Parameter(in="query", name="sort", description="field on which the sort is done", @OA\Schema(type="string", enum={"name","id"})),
     *     @OA\Parameter(in="query", name="direction", description="direction of the sort", @OA\Schema(type="string", enum={"asc","desc"})),
     *     @OA\Parameter(in="query", name="page", description="the page to return", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="limit", description="the number of result per page", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Player")))
     * )
     */
    public function getByCountry(ParamFetcher $paramFetcher, PaginatorInterface $paginator, $country)
    {
        $em = $this->getDoctrine()->getManager();

        $countryName = str_replace("-", " ", $country);

        $qb = $em->getRepository(Player::class)->createQueryBuilder('p')
            ->leftJoin("p.country", 'c')
            ->andWhere("c.name = :country")->setParameter("country", $countryName);

        return Pagination::paginate($qb, $paginator, $paramFetcher);
    }

    /**
     * @Rest\Get("-top")
     * @Rest\View(serializerGroups={"player_light", "pagination"}, statusCode=Response::HTTP_OK)
     * @Rest\QueryParam(name="sort", default="id", requirements="name|id")
     * @Rest\QueryParam(name="direction", default="asc", requirements="asc|desc")
     * @Rest\QueryParam(name="page", default=1, requirements="\d+")
     * @Rest\QueryParam(name="gender", nullable=true, requirements="\d+")
     * @Rest\QueryParam(name="contains", nullable=true, requirements="\w+")
     * @OA\Get(
     *     path="/best",
     *     tags={"Player"},
     *     summary=DESCRIPTION_GET_ALL,
     *     @OA\Parameter(in="query", name="sort", description="field on which the sort is done", @OA\Schema(type="string", enum={"name","id"})),
     *     @OA\Parameter(in="query", name="direction", description="direction of the sort", @OA\Schema(type="string", enum={"asc","desc"})),
     *     @OA\Parameter(in="query", name="page", description="the page to return", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="limit", description="the number of result per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="gender", description="type of gender", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="contains", description="string to search in the player name", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/City")))
     * )
     */
    public function getBestPlayer(PaginatorInterface $paginator, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();

        $gender = $paramFetcher->get("gender");
        $contains = $paramFetcher->get("contains");

        $qb = $em->getRepository(Player::class)->createQueryBuilder('p')
            ->select("p.name", "p.gender")
            ->leftJoin("p.information", 'i')
            ->leftJoin("i.attributs", "a")
            ->leftJoin("a.attributs", "ia")
            ->andWhere("i.ova > 50")
            ->orderBy("i.ova", "DESC")
            ->groupBy("p.name", "p.gender", "i.ova")
            ->setMaxResults(100);

        if(!is_null($gender)){
            $qb->andWhere("p.gender = :gender")
                ->setParameter("gender", $gender);
        }

        if(!is_null($contains)){
            $qb->andWhere("p.name LIKE :contains")
                ->setParameter("contains", $gender . "%");
        }

        return $qb->getQuery()->getResult();
    }
}
