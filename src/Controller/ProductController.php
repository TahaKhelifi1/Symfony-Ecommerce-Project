<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(EntityManagerInterface $em): Response
    {
        $products = $em->getRepository(Product::class)->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/new', name: 'app_product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $price = $request->request->get('price');

            if ($name && $description && $price) {
                $product = new Product();
                $product->setName($name);
                $product->setDescription($description);
                $product->setPrice($price);
                $product->setCreatedAt(new \DateTimeImmutable());

                $em->persist($product);
                $em->flush();

                $this->addFlash('success', 'Produit ajouté avec succès!');
                return $this->redirectToRoute('app_products');
            } else {
                $this->addFlash('error', 'Veuillez remplir tous les champs!');
            }
        }

        return $this->render('product/new.html.twig');
    }

    #[Route('/product/{id}/edit', name: 'app_product_edit')]
    public function edit(Request $request, EntityManagerInterface $em, Product $product): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $price = $request->request->get('price');

            if ($name && $description && $price) {
                $product->setName($name);
                $product->setDescription($description);
                $product->setPrice($price);

                $em->flush();

                $this->addFlash('success', 'Produit modifié avec succès!');
                return $this->redirectToRoute('app_products');
            } else {
                $this->addFlash('error', 'Veuillez remplir tous les champs!');
            }
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/delete', name: 'app_product_delete')]
    public function delete(EntityManagerInterface $em, Product $product): Response
    {
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Produit supprimé avec succès!');
        return $this->redirectToRoute('app_products');
    }
}
