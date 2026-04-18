<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Entity\Ingredient;
use App\Entity\Product;
use App\Request\AddIngredientRequest;
use App\Request\UpdateIngredientRequest;
use App\Event\IngredientChangedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class IngredientController extends AbstractController
{
    #[Route('/v1/ingredients', name: 'ingredients_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of ingredients',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Ingredient::class))
        )
    )]
    #[OA\Tag(name: 'Ingredients')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $ingredients = $entityManager->getRepository(Ingredient::class)->findAll();

        return $this->json($ingredients, context: ['groups' => ['ingredient:read']]);
    }

    #[Route('/v1/ingredients/{uuid}', name: 'ingredient_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a single ingredient',
        content: new Model(type: Ingredient::class)
    )]
    #[OA\Response(response: 404, description: 'Ingredient not found')]
    #[OA\Tag(name: 'Ingredients')]
    public function show(string $uuid, EntityManagerInterface $entityManager): Response
    {
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy(['uuid' => $uuid]);

        if (!$ingredient) {
            return $this->json(['error' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($ingredient, context: ['groups' => ['ingredient:read']]);
    }

    #[Route('/v1/ingredients', name: 'add_ingredient', methods: ['POST'])]
    #[OA\RequestBody(
        content: new Model(type: AddIngredientRequest::class)
    )]
    #[OA\Response(
        response: 201,
        description: 'Ingredient added to meal',
        content: new Model(type: Meal::class)
    )]
    #[OA\Response(response: 404, description: 'Meal or Product not found')]
    #[OA\Tag(name: 'Ingredients')]
    public function add(
        #[MapRequestPayload] AddIngredientRequest $request,
        EntityManagerInterface                    $entityManager,
        EventDispatcherInterface                  $eventDispatcher
    ): Response {
        $meal = $entityManager->getRepository(Meal::class)->findOneBy(['uuid' => $request->mealUuid]);

        if (!$meal) {
            return $this->json(['error' => 'Meal not found'], Response::HTTP_NOT_FOUND);
        }

        $product = $entityManager->getRepository(Product::class)->findOneBy(['uuid' => $request->productUuid]);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $ingredient = new Ingredient();
        $ingredient->setMeal($meal);
        $ingredient->setProduct($product);
        $ingredient->setWeight($request->weight);

        $meal->addIngredient($ingredient);

        $entityManager->persist($ingredient);
        $entityManager->flush();

        $eventDispatcher->dispatch(new IngredientChangedEvent($ingredient), IngredientChangedEvent::CREATED);

        return $this->json($ingredient, Response::HTTP_CREATED, context: ['groups' => ['ingredient:read']]);
    }

    #[Route('/v1/ingredients/{uuid}', name: 'update_ingredient', methods: ['PATCH', 'PUT'])]
    #[OA\RequestBody(
        content: new Model(type: UpdateIngredientRequest::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Ingredient weight updated',
        content: new Model(type: Meal::class)
    )]
    #[OA\Response(response: 404, description: 'Ingredient not found')]
    #[OA\Tag(name: 'Ingredients')]
    public function update(
        string                                       $uuid,
        #[MapRequestPayload] UpdateIngredientRequest $request,
        EntityManagerInterface                       $entityManager,
        EventDispatcherInterface                     $eventDispatcher
    ): Response {
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy(['uuid' => $uuid]);

        if (!$ingredient) {
            return $this->json(['error' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);
        }

        $ingredient->setWeight($request->weight);
        $ingredient->setProtein(null);
        $ingredient->setFat(null);
        $ingredient->setCarbs(null);
        $ingredient->setSugar(null);
        $entityManager->flush();

        $eventDispatcher->dispatch(new IngredientChangedEvent($ingredient), IngredientChangedEvent::UPDATED);

        return $this->json($ingredient->getMeal(), context: ['groups' => ['ingredient:read']]);
    }

    #[Route('/v1/ingredients/{uuid}', name: 'remove_ingredient', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Ingredient removed from meal')]
    #[OA\Response(response: 404, description: 'Ingredient not found')]
    #[OA\Tag(name: 'Ingredients')]
    public function delete(
        string                 $uuid,
        EntityManagerInterface $entityManager
    ): Response {
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy(['uuid' => $uuid]);

        if (!$ingredient) {
            return $this->json(['error' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);
        }

        $meal = $ingredient->getMeal();
        $meal->removeIngredient($ingredient);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
