<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\League;
use App\Model\Representation\Pagination;
use App\Repository\MatchRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Knp\Component\Pager\PaginatorInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Rest\Route("/game")
 */
class GameController extends ApiController
{
    /**
     * @Rest\Get
     * @Rest\View(serializerGroups={"pagination"}, statusCode=Response::HTTP_OK)
     * @Rest\QueryParam(name="sort", default="id", requirements="playing_date|id")
     * @Rest\QueryParam(name="direction", default="asc", requirements="asc|desc")
     * @Rest\QueryParam(name="page", default=1, requirements="\d+")
     * @Rest\QueryParam(name="limit", default=Pagination::DEFAULT_LIMIT, requirements="\d+")
     * @OA\Get(
     *     path="/game",
     *     tags={"Game"},
     *     summary=DESCRIPTION_GET_ALL,
     *     @OA\Parameter(in="query", name="sort", description="field on which the sort is done", @OA\Schema(type="string", enum={"playing_date","id"})),
     *     @OA\Parameter(in="query", name="direction", description="direction of the sort", @OA\Schema(type="string", enum={"asc","desc"})),
     *     @OA\Parameter(in="query", name="page", description="the page to return", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="limit", description="the number of result per page", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Game")))
     * )
     */
    public function index(MatchRepository $matchRepository, ParamFetcher $paramFetcher, PaginatorInterface $paginator)
    {
        $qb = $matchRepository->createQueryBuilder('q');

        return Pagination::paginate($qb, $paginator, $paramFetcher);

    }

    /**
     * @Rest\Get("/{id}")
     * @Rest\View(serializerGroups={"city_light", "pagination"}, statusCode=Response::HTTP_OK)
     * @OA\Get(
     *     path="/game/{id}",
     *     tags={"Game"},
     *     summary=DESCRIPTION_GET,
     *     @OA\Parameter(in="path", name="id", description="Ressource ID", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(ref="#/components/schemas/Game"))
     * )
     */
    public function show(Game $game): Response
    {
        return $this->render('game/show.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Rest\Get("/league/{id}")
     * @Rest\View(serializerGroups={"pagination"}, statusCode=Response::HTTP_OK)
     * @Rest\QueryParam(name="sort", default="id", requirements="playing_date|id")
     * @Rest\QueryParam(name="direction", default="asc", requirements="asc|desc")
     * @Rest\QueryParam(name="page", default=1, requirements="\d+")
     * @Rest\QueryParam(name="limit", default=Pagination::DEFAULT_LIMIT, requirements="\d+")
     * @OA\Get(
     *     path="/game/league/{id}",
     *     tags={"Game"},
     *     summary=DESCRIPTION_GET,
     *     @OA\Parameter(in="path", name="id", description="Ressource ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="sort", description="field on which the sort is done", @OA\Schema(type="string", enum={"playing_date","id"})),
     *     @OA\Parameter(in="query", name="direction", description="direction of the sort", @OA\Schema(type="string", enum={"asc","desc"})),
     *     @OA\Parameter(in="query", name="page", description="the page to return", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="limit", description="the number of result per page", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(ref="#/components/schemas/Game"))
     * )
     */
    public function getByLeague(League $league, ParamFetcher $paramFetcher, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository(Game::class)->createQueryBuilder('q')
        ->leftJoin('q.league', "l")
        ->andWhere("l.id = :id")->setParameter("id", $league->getId());

        return Pagination::paginate($qb, $paginator, $paramFetcher);
    }

}
