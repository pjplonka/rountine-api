<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Entity\Product;
use App\Entity\ShoppingList;
use App\Request\ShoppingListRequest;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class ShoppingListController extends AbstractController
{
    #[Route('/v1/shopping-lists', name: 'shopping_lists_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of shopping list items',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ShoppingList::class))
        )
    )]
    #[OA\Tag(name: 'ShoppingList')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $items = $entityManager->getRepository(ShoppingList::class)->findAll();

        return $this->json($items, context: ['groups' => ['shopping_list:read']]);
    }

    #[Route('/v1/shopping-lists/{uuid}', name: 'shopping_list_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a single shopping list item',
        content: new Model(type: ShoppingList::class)
    )]
    #[OA\Response(response: 404, description: 'Shopping list item not found')]
    #[OA\Tag(name: 'ShoppingList')]
    public function show(string $uuid, EntityManagerInterface $entityManager): Response
    {
        $item = $entityManager->getRepository(ShoppingList::class)->findOneBy(['uuid' => $uuid]);

        if (!$item) {
            return $this->json(['error' => 'Shopping list item not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($item, context: ['groups' => ['shopping_list:read']]);
    }

    #[Route('/v1/shopping-lists', name: 'shopping_list_create', methods: ['POST'])]
    #[OA\RequestBody(
        content: new Model(type: ShoppingListRequest::class)
    )]
    #[OA\Response(
        response: 201,
        description: 'Shopping list item created',
        content: new Model(type: ShoppingList::class)
    )]
    #[OA\Tag(name: 'ShoppingList')]
    public function create(
        #[MapRequestPayload] ShoppingListRequest $request,
        EntityManagerInterface $entityManager
    ): Response {
        $item = new ShoppingList();
        $this->updateItemFromRequest($item, $request, $entityManager);

        $entityManager->persist($item);
        $entityManager->flush();

        return $this->json($item, Response::HTTP_CREATED, context: ['groups' => ['shopping_list:read']]);
    }

    #[Route('/v1/shopping-lists/{uuid}', name: 'shopping_list_update', methods: ['PUT', 'PATCH'])]
    #[OA\RequestBody(
        content: new Model(type: ShoppingListRequest::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Shopping list item updated',
        content: new Model(type: ShoppingList::class)
    )]
    #[OA\Response(response: 404, description: 'Shopping list item not found')]
    #[OA\Tag(name: 'ShoppingList')]
    public function update(
        string $uuid,
        #[MapRequestPayload] ShoppingListRequest $request,
        EntityManagerInterface $entityManager
    ): Response {
        $item = $entityManager->getRepository(ShoppingList::class)->findOneBy(['uuid' => $uuid]);

        if (!$item) {
            return $this->json(['error' => 'Shopping list item not found'], Response::HTTP_NOT_FOUND);
        }

        $this->updateItemFromRequest($item, $request, $entityManager);
        $entityManager->flush();

        return $this->json($item, context: ['groups' => ['shopping_list:read']]);
    }

    #[Route('/v1/shopping-lists/{uuid}', name: 'shopping_list_delete', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Shopping list item deleted')]
    #[OA\Response(response: 404, description: 'Shopping list item not found')]
    #[OA\Tag(name: 'ShoppingList')]
    public function delete(string $uuid, EntityManagerInterface $entityManager): Response
    {
        $item = $entityManager->getRepository(ShoppingList::class)->findOneBy(['uuid' => $uuid]);

        if (!$item) {
            return $this->json(['error' => 'Shopping list item not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($item);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function updateItemFromRequest(ShoppingList $item, ShoppingListRequest $request, EntityManagerInterface $entityManager): void
    {
        $item->setMeal(null);
        $item->setServings(null);
        $item->setProduct(null);
        $item->setWeight(null);
        $item->setCustomName(null);

        if ($request->mealUuid && $request->servings) {
            $meal = $entityManager->getRepository(Meal::class)->findOneBy(['uuid' => $request->mealUuid]);
            if ($meal) {
                $item->setMeal($meal);
                $item->setServings($request->servings);
            }
        } elseif ($request->productUuid && $request->weight) {
            $product = $entityManager->getRepository(Product::class)->findOneBy(['uuid' => $request->productUuid]);
            if ($product) {
                $item->setProduct($product);
                $item->setWeight($request->weight);
            }
        } elseif ($request->customName) {
            $item->setCustomName($request->customName);
        }
    }
}
