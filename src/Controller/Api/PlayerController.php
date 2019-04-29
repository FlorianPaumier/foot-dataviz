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
     * @OA\Get(
     *     path="/player",
     *     tags={"Player"},
     *     summary=DESCRIPTION_GET_ALL,
     *     @OA\Parameter(in="query", name="sort", description="field on which the sort is done", @OA\Schema(type="string", enum={"name","id", "gender"})),
     *     @OA\Parameter(in="query", name="direction", description="direction of the sort", @OA\Schema(type="string", enum={"asc","desc"})),
     *     @OA\Parameter(in="query", name="page", description="the page to return", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="limit", description="the number of result per page", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Player")))
     * )
     */
    public function index(PlayerRepository $playerRepository, ParamFetcher $paramFetcher, PaginatorInterface $paginator)
    {
        $qb = $playerRepository->createQueryBuilder("p");

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
}
