<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
//use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RecipeType;
use App\Form\CommentType;
use App\Form\SearchType;


class RecipesController extends AbstractController
{



    /**
     * @Route("/", name="home")
     */

    public function home() {

      return $this ->render('recipes/home.html.twig');

     }

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
       * @Route("/recipes/{id}/comment", name="comment_create")
       */

      public function comment($id ,Request $request, EntityManagerInterface $manager) {

        $repo = $this ->getDoctrine() ->getRepository(Recipe::class);
        $recipe = $repo ->find($id);

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

    // les infos envoyées dans le formulaire se trouvent dans la request
        $form ->handleRequest($request);
    
    // est-ce que le form a été soumis et est-ce que le form est valide?
        if($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setRecipe($recipe);
            // faire persister le comment
            $manager->persist($comment);
            // faire la requête
            $manager->flush();

          // rédiriger vers l'article créé
          return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
    }

    return $this ->render('recipes/comment.html.twig',[
          'formComment' => $form->createView(), // créer l'aspect affichage au formulaire
    ]);
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
   * @Route("/categories/{id}", name="category_show") 
   */

      public function showCategory($id) {

        $repo = $this ->getDoctrine() ->getRepository(Category::class);

        $category = $repo ->find($id);


        return $this ->render('recipes/category.html.twig', [
                'category' => $category
        ]);

      }


      /**
       * @Route("/new", name="recipe_create")
       * @Route("/{id}/edit", name="recipe_edit") //Route pour mettre à jour un article
       */


      public function form(Recipe $recipe= null,Request $request, EntityManagerInterface $manager) {


        if(!$recipe) {
            $recipe = new Recipe(); // si je n'ai pas de recette j'ai envie de créer une instance
        }


        $form = $this->createForm(RecipeType::class, $recipe);

        // les infos envoyées dans le formulaire se trouvent dans la request
        $form ->handleRequest($request);
        
        // est-ce que le form a été soumis et est-ce que le form est valide?
        if($form->isSubmitted() && $form->isValid()) {
          if(!$recipe->getId()){
            //Donner sa date de création si l'article n'a pas d'id (n'existe pas)
            $recipe->setCreatedAt(new \DateTime());
            $recipe->setFavorite(0);
          }
          
            // faire persister la recette
            $manager->persist($recipe);
            // faire la requête
            $manager->flush();

            // rédiriger vers l'article créé
            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
        }

        return $this ->render('recipes/create.html.twig',[
              'formRecipe' => $form->createView(), // créer l'aspect affichage au formulaire
              'editMode' => $recipe->getId() !== null
        ]);
      }

      //recherche recette sur base du nom de la recette

      /**
       * @Route("/search",name="search")
       */

       public function search(Request $request) {

        $repo = $this ->getDoctrine()->getRepository(Recipe::class);
        $recipes = $repo -> findAll();
        $form = $this->createForm(SearchType::class);

        // les infos envoyées dans le formulaire se trouvent dans la request
        $form ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

          return $this->render('recipes/search_result.html.twig', [
            'recipeResearch' => $request->request->get('search'), 
            'recipes' => $recipes
            ]);

        }

      return $this ->render('recipes/search_form.html.twig',[
          'formResearch' => $form->createView() // créer l'aspect affichage au formulaire
    ]);
       }
       /**
        * @Route("recipes/{id}/delete", name="delete")
        */

        public function delete($id, EntityManagerInterface $manager) {

          $repo = $this ->getDoctrine() ->getRepository(Recipe::class);
          $recipe = $repo ->find($id);

          $manager->remove($recipe);
          // faire la requête
          $manager->flush();

          return $this ->render('recipes/delete_notification.html.twig', [
            'recipe' => $recipe
          ]);
        }
}
