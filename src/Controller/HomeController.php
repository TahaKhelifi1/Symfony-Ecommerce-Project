<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
final class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
    $products = $em->getRepository(Product::class)->findAll();

    return $this->render('home/index.html.twig', [
        'products' => $products,
    ]);
    }
}
