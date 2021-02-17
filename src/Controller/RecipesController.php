<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Recipe;

class RecipesController extends AbstractController
{
    /**
     * @Route("/recipes", name="recipes")
     */
    public function index(): Response
    {
        $repo = $this ->getDoctrine()->getRepository(Recipe::class);

        $recipes = $repo -> findAll();

        return $this->render('recipes/index.html.twig', [
            'controller_name' => 'RecipesController',
            'recipes' => $recipes
        ]);
    }
    /**
    * @Route("/recipes/{id}", name="recipe_show") 
    */

    public function show($id) {

        $repo = $this ->getDoctrine() ->getRepository(Recipe::class);

        $recipe = $repo ->find($id);


        return $this ->render('recipes/show.html.twig', [
                'recipe' => $recipe
        ]);

    }

    /**
     * @Route("/", name="home")
     */

     public function home() {

      return $this ->render('recipes/home.html.twig');

     }


    /**
     * @Route("/favorites", name="favorites")
     */

     public function favorites() {

        $repo = $this ->getDoctrine() ->getRepository(Recipe::class);

        $recipes = $repo ->findAll();


        return $this ->render('recipes/favorites.html.twig', [
                'recipes' => $recipes
        ]);
     }


     /**
      * @Route("/categories", name="categories")
      */

      public function categories() {

        $repo = $this ->getDoctrine() ->getRepository(Category::class);

        $categories = $repo ->findAll();

        return $this ->render('recipes/categories.html.twig', [
            'categories' => $categories
    ]);
      }


      /**
       * @Route("/add", name="add")
       */


      public function add() {

        return $this ->render('recipes/add.html.twig');
      }
       
}
