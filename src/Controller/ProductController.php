<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ShopCategory;
use App\Event\ProductChangedEvent;
use App\Request\ProductRequest;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProductController extends AbstractController
{
    #[Route('/v1/products', name: 'products_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of products',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class))
        )
    )]
    #[OA\Tag(name: 'Products')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->json($products, context: ['groups' => ['product:read']]);
    }

    #[Route('/v1/products/{uuid}', name: 'product_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a single product',
        content: new Model(type: Product::class)
    )]
    #[OA\Response(response: 404, description: 'Product not found')]
    #[OA\Tag(name: 'Products')]
    public function show(string $uuid, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->findOneBy(['uuid' => $uuid]);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($product, context: ['groups' => ['product:read']]);
    }

    #[Route('/v1/products', name: 'product_create', methods: ['POST'])]
    #[OA\RequestBody(
        content: new Model(type: ProductRequest::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Product created',
        content: new Model(type: Product::class)
    )]
    #[OA\Tag(name: 'Products')]
    public function create(
        #[MapRequestPayload] ProductRequest $request,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $product = new Product();
        $this->updateProductFromRequest($product, $request, $entityManager);

        $entityManager->persist($product);
        $entityManager->flush();

        $eventDispatcher->dispatch(new ProductChangedEvent($product), ProductChangedEvent::CREATED);

        return $this->json($product, context: ['groups' => ['product:read']]);
    }

    #[Route('/v1/products/{uuid}', name: 'product_update', methods: ['PUT'])]
    #[OA\RequestBody(
        content: new Model(type: ProductRequest::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Product updated',
        content: new Model(type: Product::class)
    )]
    #[OA\Response(response: 404, description: 'Product not found')]
    #[OA\Tag(name: 'Products')]
    public function update(
        string $uuid,
        #[MapRequestPayload] ProductRequest $request,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $product = $entityManager->getRepository(Product::class)->findOneBy(['uuid' => $uuid]);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $this->updateProductFromRequest($product, $request, $entityManager);
        $entityManager->flush();

        $eventDispatcher->dispatch(new ProductChangedEvent($product), ProductChangedEvent::UPDATED);

        return $this->json($product, context: ['groups' => ['product:read']]);
    }

    #[Route('/v1/products/{uuid}', name: 'product_delete', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Product deleted')]
    #[OA\Response(response: 404, description: 'Product not found')]
    #[OA\Tag(name: 'Products')]
    public function delete(string $uuid, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->findOneBy(['uuid' => $uuid]);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function updateProductFromRequest(Product $product, ProductRequest $request, EntityManagerInterface $entityManager): void
    {
        $product->setName($request->name);
        $product->setPrice($request->price);
        $product->setProtein($request->protein);
        $product->setFat($request->fat);
        $product->setCarbs($request->carbs);
        $product->setSugar($request->sugar);

        if ($request->shopCategoryUuid) {
            $shopCategory = $entityManager->getRepository(ShopCategory::class)->findOneBy(['uuid' => $request->shopCategoryUuid]);
            $product->setShopCategory($shopCategory);
        } else {
            $product->setShopCategory(null);
        }
    }
}
