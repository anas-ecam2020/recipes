<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiRecipeController extends AbstractController
{
    /**
     * @Route("/api/recipe", name="api_recipe_index", methods={"GET"})
     */
    public function index(RecipeRepository $recipeRepository): Response {

        return $this->json($recipeRepository->findAll(), 200, [], ['groups' =>'recipe:read']);

    }


    /**
     * @Route("/api/recipe/{id}", name="api_recipe_by_id", methods={"GET"})
     */
    public function recipe(RecipeRepository $recipeRepository, $id): Response {

        return $this->json($recipeRepository->find($id), 200, [], ['groups' =>'recipe:read']);

    }

/**
 * @Route("/api/recipe", name="api_recipe_store", methods={"POST"})
 */
    public function store(CategoryRepository $categoryRepository, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator) {
        
        $jsonReceived = $request->getContent();

        try {

            $recipe = $serializer->deserialize($jsonReceived, Recipe::class, 'json');

            $categoryValues = $recipe -> getCategory();
            $categoryTitle = $categoryValues -> getTitle();

            $category = $categoryRepository -> findOneBy(['title' => $categoryTitle]);
            $recipe -> setCategory($category);
            $recipe ->setCreatedAt(new \DateTime());
            $errors = $validator->validate($recipe);


            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }
            
            $em->persist($recipe);
            $em->flush();
    
            return $this->json($recipe, 201, [], ['groups'=>'recipe:read']);
        // si le format json remis n'est pas correctement écrit "Syntax error"
        } catch(NotEncodableValueException $e) {

            return $this->json([
                'status' => 400,
                'message' => $e ->getMessage()
            ], 400);
            
        }
    }

    /**
     * @Route("/api/recipe/{id}", name="api_recipe_delete", methods={"DELETE"})
     */

    public function delete($id, RecipeRepository $recipeRepository, EntityManagerInterface $em) {

        /*$jsonReceived = $request->getContent();
        try {

           /* $toDelete = $serializer->deserialize($jsonReceived, Recipe::class, 'json');
            $toDeleteTitle = $toDelete -> getTitle();
            $recipe = $recipeRepository -> findOneBy(['title' => $toDeleteTitle]); 
            

            if($recipe == null) {

                $errdb = "Sorry, this recipe does not exist on the database.";
                return $this->json($errdb, 400); 
             } else {
                $em->remove($recipe);
                $em->flush();
                $deleteResponse = "The recipe '".$recipe->getTitle()."' has been successfully deleted!";
                return $this->json($deleteResponse, 200);

            }
        // si le format json remis n'est pas correctement écrit "Syntax error"
        } catch(NotEncodableValueException $e) {

            return $this->json([
                'status' => 400,
                'message' => $e ->getMessage()
            ], 400);

        }*/

        $toDelete = $recipeRepository -> find($id);

        $em ->remove($toDelete);
        $em ->flush();

        $response = "The recipe '".$toDelete->getTitle()."' has been successfully deleted!";

        return $this -> json($response, 200, [], ['groups' =>'recipe:read']);
    }

    /**
     * @Route("/api/recipe", name="api_recipe_put", methods={"PUT"})
     */

     public function put(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em) {

        $jsonReceived = $request -> getContent();

        try {
            $toModify = $serializer ->deserialize($jsonReceived, Recipe::class, 'json');

            $errors = $validator->validate($toModify);

            // vérifier si le validator n'a pas d'erreurs
            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $em->persist($toModify);
            $em->flush();
    
            return $this->json($toModify, 200, [], ['groups'=>'recipe:read']);


        } catch(NotEncodableValueException $e) {

            return $this->json([
                'status' => 400,
                'message' => $e ->getMessage()
            ], 400);

        }
     }
}