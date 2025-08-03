<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository, CategoryRepository $categoryRepository): Response
    {
        // Récupérer les 6 derniers articles
        $latestArticles = $articleRepository->findBy(
            [], 
            ['createdAt' => 'DESC'], 
            6
        );

        // Récupérer toutes les catégories
        $categories = $categoryRepository->findAll();

        return $this->render('home/index.html.twig', [
            'latest_articles' => $latestArticles,
            'categories' => $categories,
        ]);
    }
}
