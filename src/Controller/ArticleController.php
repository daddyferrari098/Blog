<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'app_article_list')]
    public function list(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findBy(
            [],
            ['createdAt' => 'DESC']
        );

        return $this->render('article/list.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/new', name: 'app_article_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer automatiquement le slug à partir du titre
            $slug = $slugger->slug($article->getTitle())->lower();
            $article->setSlug($slug);
            
            // Définir la date de création
            $article->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès !');

            return $this->redirectToRoute('app_article_show', [
                'slug' => $article->getSlug()
            ]);
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/article/{slug}/edit', name: 'app_article_edit')]
    public function edit(Article $article, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Régénérer le slug si le titre a changé
            $slug = $slugger->slug($article->getTitle())->lower();
            $article->setSlug($slug);

            $entityManager->flush();

            $this->addFlash('success', 'Article modifié avec succès !');

            return $this->redirectToRoute('app_article_show', [
                'slug' => $article->getSlug()
            ]);
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/article/{slug}', name: 'app_article_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/{slug}/delete', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article supprimé avec succès !');
        }

        return $this->redirectToRoute('app_article_list');
    }
}
