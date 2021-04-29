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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiRecipeController extends AbstractController
{
    /**
     * @Route("/api/recipes", name="api_recipe_index", methods={"GET"})
     */
    public function index(RecipeRepository $recipeRepository, NormalizerInterface $normalizer): Response
    {
        $recipes = $recipeRepository->findAll();

        $recipesNormalisés  = $normalizer -> normalize($recipes, null, ['groups' => 'recipe:read']);

        $json = json_encode($recipesNormalisés);
        //$recipeRepository->findAll(), 200, [], ['groups' => 'recipe:read'];
       //$test = $this->json($recipeRepository->findAll(), 200, [], ['groups' => 'recipe:read']);

        $response = new JsonResponse();
        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);
        
        return $response;
    }


    /**
     * @Route("/api/recipe/{id}", name="api_recipe_by_id", methods={"GET"})
     */
    public function recipe(RecipeRepository $recipeRepository, $id): Response
    {

        return $this->json($recipeRepository->find($id), 200, [], ['groups' => 'recipe:read']);
    }

    /**
     * @Route("/api/recipe", name="api_recipe_store", methods={"POST"})
     */
    public function store(CategoryRepository $categoryRepository, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {

        $jsonReceived = $request->getContent();

        try {

            $recipe = $serializer->deserialize($jsonReceived, Recipe::class, 'json');

            $categoryValues = $recipe->getCategory();
            $categoryTitle = $categoryValues->getTitle();
            // extraire l'objet catégorie sur base du titre
            $category = $categoryRepository->findOneBy(['title' => $categoryTitle]);
            // setCategory à la nouvelle recette créée
            $recipe->setCategory($category);
            $recipe->setCreatedAt(new \DateTime());
            $errors = $validator->validate($recipe);


            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $em->persist($recipe);
            $em->flush();

            return $this->json($recipe, 201, [], ['groups' => 'recipe:read']);
            // si le format json remis n'est pas correctement écrit "Syntax error"
        } catch (NotEncodableValueException $e) {

            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route("/api/recipe/{id}", name="api_recipe_delete", methods={"DELETE"})
     */

    public function delete($id, RecipeRepository $recipeRepository, EntityManagerInterface $em)
    {

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

        $toDelete = $recipeRepository->find($id);

        $em->remove($toDelete);
        $em->flush();

        $response = "The recipe '" . $toDelete->getTitle() . "' has been successfully deleted!";

        return $this->json($response, 200, [], ['groups' => 'recipe:read']);
    }

    /**
     * @Route("/api/recipe/{id}", name="api_recipe_put", methods={"PUT"})
     */

    public function put($id, Request $request, CategoryRepository $categoryRepository, RecipeRepository $recipeRepository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em)
    {


        $jsonReceived = $request->getContent();
        $toModify = $recipeRepository->find($id);

        try {
            $deserializedReceived = $serializer->deserialize($jsonReceived, Recipe::class, 'json');
            $categoryRecipe = $deserializedReceived->getCategory();
            $categoryRepo = $categoryRepository->findOneBy(["title" => $categoryRecipe->getTitle()]);
            // setCategory à la nouvelle recette créée
            $toModify->setCategory($categoryRepo);
            $toModify->setTitle($deserializedReceived->getTitle());
            $toModify->setContent($deserializedReceived->getContent());
            $toModify->setImage($deserializedReceived->getImage());
            $toModify->setFavorite($deserializedReceived->getFavorite());
            $toModify->setTime($deserializedReceived->getTime());
            $toModify->setDifficulty($deserializedReceived->getDifficulty());
            $toModify->setPortions($deserializedReceived->getPortions());


            //return $this->json($toModify, 400);

            $errors = $validator->validate($deserializedReceived);

            // vérifier si le validator n'a pas d'erreurs
            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $em->persist($toModify);
            $em->flush();

            return $this->json($toModify, 200, [], ['groups' => 'recipe:read']);
        } catch (NotEncodableValueException $e) {

            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
