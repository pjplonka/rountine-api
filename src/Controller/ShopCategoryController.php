<?php

namespace App\Controller;

use App\Entity\ShopCategory;
use App\Request\ShopCategoryRequest;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class ShopCategoryController extends AbstractController
{
    #[Route('/v1/shop-categories', name: 'shop_categories_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of shop categories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ShopCategory::class))
        )
    )]
    #[OA\Tag(name: 'Shop Categories')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(ShopCategory::class)->findBy([], ['order' => 'ASC']);

        return $this->json($categories, context: ['groups' => ['shop_category:read']]);
    }

    #[Route('/v1/shop-categories/{uuid}', name: 'shop_category_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a single shop category',
        content: new Model(type: ShopCategory::class)
    )]
    #[OA\Response(response: 404, description: 'Shop category not found')]
    #[OA\Tag(name: 'Shop Categories')]
    public function show(string $uuid, EntityManagerInterface $entityManager): Response
    {
        $category = $entityManager->getRepository(ShopCategory::class)->findOneBy(['uuid' => $uuid]);

        if (!$category) {
            return $this->json(['error' => 'Shop category not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($category, context: ['groups' => ['shop_category:read']]);
    }

    #[Route('/v1/shop-categories', name: 'shop_category_create', methods: ['POST'])]
    #[OA\RequestBody(
        content: new Model(type: ShopCategoryRequest::class)
    )]
    #[OA\Response(
        response: 201,
        description: 'Shop category created',
        content: new Model(type: ShopCategory::class)
    )]
    #[OA\Tag(name: 'Shop Categories')]
    public function create(
        #[MapRequestPayload] ShopCategoryRequest $request,
        EntityManagerInterface $entityManager
    ): Response {
        $category = new ShopCategory();
        $category->setName($request->name);
        $category->setOrder($request->order);

        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json($category, Response::HTTP_CREATED, context: ['groups' => ['shop_category:read']]);
    }

    #[Route('/v1/shop-categories/{uuid}', name: 'shop_category_update', methods: ['PUT', 'PATCH'])]
    #[OA\RequestBody(
        content: new Model(type: ShopCategoryRequest::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Shop category updated',
        content: new Model(type: ShopCategory::class)
    )]
    #[OA\Response(response: 404, description: 'Shop category not found')]
    #[OA\Tag(name: 'Shop Categories')]
    public function update(
        string $uuid,
        #[MapRequestPayload] ShopCategoryRequest $request,
        EntityManagerInterface $entityManager
    ): Response {
        $category = $entityManager->getRepository(ShopCategory::class)->findOneBy(['uuid' => $uuid]);

        if (!$category) {
            return $this->json(['error' => 'Shop category not found'], Response::HTTP_NOT_FOUND);
        }

        $category->setName($request->name);
        $category->setOrder($request->order);
        $entityManager->flush();

        return $this->json($category, context: ['groups' => ['shop_category:read']]);
    }

    #[Route('/v1/shop-categories/{uuid}', name: 'shop_category_delete', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Shop category deleted')]
    #[OA\Response(response: 404, description: 'Shop category not found')]
    #[OA\Tag(name: 'Shop Categories')]
    public function delete(string $uuid, EntityManagerInterface $entityManager): Response
    {
        $category = $entityManager->getRepository(ShopCategory::class)->findOneBy(['uuid' => $uuid]);

        if (!$category) {
            return $this->json(['error' => 'Shop category not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
