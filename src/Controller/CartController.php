<?php

namespace App\Controller;
use App\Entity\Product;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
final class CartController extends AbstractController{
    #[Route('/panier', name: 'app_cart')]
    public function index(CartService $cartService, EntityManagerInterface $em): Response
    {
        $cart = $cartService->getCart();
        $cartDetails = [];

        foreach ($cart as $id => $quantity) {
            $product = $em->getRepository(Product::class)->find($id);
            if ($product) {
                $cartDetails[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }
        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartDetails
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(int $id, CartService $cartService, Request $request):Response
    {
        $cartService->add($id);
        $referer = $request->headers->get('referer'); // URL précédente
        return $this->redirect($referer ?? '/');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/update/{id}', name: 'cart_update', methods: ['POST'])]
    public function update(Request $request, int $id, CartService $cartService): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        $cartService->update($id, $quantity);
        return $this->redirectToRoute('app_cart');
    }
}