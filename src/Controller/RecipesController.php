<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Recipe;
//use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



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
       * @Route("/new", name="recipe_create")
       * @Route("/{id}/edit", name="recipe_edit") //Route pour mettre à jour un article
       */


      public function form(Recipe $recipe= null,Request $request, EntityManagerInterface $manager) {


        if(!$recipe) {
            $recipe = new Recipe(); // si je n'ai pas de recette j'ai envie de créer une instance
        }

        $form = $this->createFormBuilder($recipe)
                      ->add('title')
                      ->add('category', EntityType::class, [
                        "class" => Category::class,
                        "choice_label" => 'title',
                      ])
                      ->add('image')
                      ->add('time', ChoiceType::class, [
                        "choices"=> array(
                          '5 minutes' => 5,
                          '10 minutes' => 10,
                          '15 minutes' => 15,
                          '20 minutes' => 20,
                          '25 minutes' => 25,
                          '30 minutes' => 30,
                        )
                      ])
                      ->add('difficulty', ChoiceType::class, [
                        "choices"=> array(
                          'Facile' => 'Facile',
                          'Moyenne' => 'Moyenne',
                          'Difficile' => 'Difficile',
                        )
                      ])
                      ->add('portions', ChoiceType::class, [
                        "choices"=> array(
                          '1 portion' => 1,
                          '2 portions' => 2,
                          '3 portions' => 3,
                          '4 portions' => 4,
                          '5 portions' => 5,
                        )
                      ])
                      ->add('content')
                      ->add('save', SubmitType::class)                    
                      ->getForm();

        // les infos envoyées dans le formulaire se trouvent dans la request
        $form ->handleRequest($request);
        
        // est-ce que le form a été soumis et est-ce que le form est valide?
        if($form->isSubmitted() && $form->isValid()) {
          if(!$recipe->getId()){
            //Donner sa date de création si l'article n'a pas d'id (n'existe pas)
            $recipe->setCreatedAt(new \DateTime());
          }

            $recipe->setFavorite(0);

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
       
}
