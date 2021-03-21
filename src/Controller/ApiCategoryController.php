<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiCategoryController extends AbstractController
{
    /**
     * @Route("/api/category", name="api_category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->json($categoryRepository->findAll(), 200, [], ['groups' =>'recipe:read']);
    }

    /**
     * @Route("/api/category/{id}", name="api_recipes_per_category", methods={"GET"})
     */
    public function category($id, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find($id);
        $recipes = $category ->getRecipes();

        return $this->json($recipes, 200, [], ['groups' =>'recipe:read']);
    }
}
