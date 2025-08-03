<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/{slug}', name: 'app_category_show')]
    public function show(Category $category): Response
    {
        // Les articles de la catégorie sont automatiquement chargés 
        // grâce à la relation OneToMany dans l'entité Category
        $articles = $category->getArticles();

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'articles' => $articles,
        ]);
    }

    #[Route('/categories', name: 'app_category_list')]
    public function list(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }
}
