<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\Category\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\CategoryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/categorie/liste/{offset}", name="admin_category")
     */
    public function home(CategoryRepository $repo, int $offset = null): Response
    {
        $offset = max(0, $offset);
        $paginator = $repo->getPaginator($offset);
        $perPage = CategoryRepository::ADMIN_PAGINATOR_PER_PAGE;

        return $this->render('admin/category/home.html.twig', [
            'categories' => $paginator,
            'prev' => $offset - $perPage,
            'next' => min(count($paginator), $offset + $perPage),
        ]);
    }

    /**
     * @Route("/admin/categorie/nouveau", name="admin_category_new")
     */
    public function new(Request $request, CategoryManager $manager): Response
    {
        $form = $this->createForm(CategoryType::class, new Category());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('admin_category');
        }

        return $this->render('admin/category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/categorie/modifier/{id}", name="admin_category_edit")
     */
    public function edit(Request $request, Category $category, CategoryManager $manager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('admin_category');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/categorie/supprimer/{id}", name="admin_category_del")
     */
    public function del(Category $category, CategoryManager $manager): Response
    {
        $manager->del($category);

        return $this->redirectToRoute('admin_category');
    }
}
