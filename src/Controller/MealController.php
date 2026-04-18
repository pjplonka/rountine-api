<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Request\CreateMealRequest;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class MealController extends AbstractController
{
    #[Route('/v1/meals', name: 'meals', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of meals',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Meal::class))
        )
    )]
    #[OA\Tag(name: 'Meals')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $meals = $entityManager->getRepository(Meal::class)->findAll();

        return $this->json($meals, context: ['groups' => ['meal:read']]);
    }

    #[Route('/v1/meals/{uuid}', name: 'get_meal', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a single meal',
        content: new Model(type: Meal::class)
    )]
    #[OA\Response(response: 404, description: 'Meal not found')]
    #[OA\Tag(name: 'Meals')]
    public function show(string $uuid, EntityManagerInterface $entityManager): Response
    {
        $meal = $entityManager->getRepository(Meal::class)->findOneBy(['uuid' => $uuid]);

        if (!$meal) {
            return $this->json(['error' => 'Meal not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($meal, context: ['groups' => ['meal:read']]);
    }

    #[Route('/v1/meals', name: 'create_meal', methods: ['POST'])]
    #[OA\RequestBody(
        content: new Model(type: CreateMealRequest::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Meal created',
        content: new Model(type: Meal::class)
    )]
    #[OA\Tag(name: 'Meals')]
    public function create(#[MapRequestPayload] CreateMealRequest $productReview, EntityManagerInterface $entityManager): Response
    {
        $meal = new Meal();
        $meal->setName($productReview->name);

        $entityManager->persist($meal);
        $entityManager->flush();

        return $this->json($meal, context: ['groups' => ['meal:read']]);
    }

    #[Route('/v1/meals/{uuid}', name: 'update_meal', methods: ['PUT'])]
    #[OA\RequestBody(
        content: new Model(type: CreateMealRequest::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Meal updated',
        content: new Model(type: Meal::class)
    )]
    #[OA\Response(response: 404, description: 'Meal not found')]
    #[OA\Tag(name: 'Meals')]
    public function update(string $uuid, #[MapRequestPayload] CreateMealRequest $request, EntityManagerInterface $entityManager): Response
    {
        $meal = $entityManager->getRepository(Meal::class)->findOneBy(['uuid' => $uuid]);

        if (!$meal) {
            return $this->json(['error' => 'Meal not found'], Response::HTTP_NOT_FOUND);
        }

        $meal->setName($request->name);
        $entityManager->flush();

        return $this->json($meal, context: ['groups' => ['meal:read']]);
    }

    #[Route('/v1/meals/{uuid}', name: 'delete_meal', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Meal deleted')]
    #[OA\Response(response: 404, description: 'Meal not found')]
    #[OA\Tag(name: 'Meals')]
    public function delete(string $uuid, EntityManagerInterface $entityManager): Response
    {
        $meal = $entityManager->getRepository(Meal::class)->findOneBy(['uuid' => $uuid]);

        if (!$meal) {
            return $this->json(['error' => 'Meal not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($meal);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
