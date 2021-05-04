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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiRecipeController extends AbstractController
{
    /**
     * @Route("/api/recipes", name="api_recipe_index", methods={"GET"})
     */
    public function index(): Response
    {
        
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

        $em = $this->getDoctrine()->getManager();
        $recipes = $em->getRepository(Recipe::class)->findAll();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $content = $serializer->serialize($recipes, 'json', [
            'circular_reference_handler' => function ($recipes) {
                return $recipes->getId();
            }
        ]);

        $response->setContent($content);

        return $response;
    
        //return $this->json($recipeRepository->findAll(), 200, [], ['groups' => 'recipe:read']);

    }


    /**
     * @Route("/api/recipe/{id}", name="api_recipe_by_id", methods={"GET"})
     */
    public function recipe($id): Response
    {

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

        $em = $this->getDoctrine()->getManager();
        $recipes = $em->getRepository(Recipe::class)->find($id);

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $content = $serializer->serialize($recipes, 'json', [
            'circular_reference_handler' => function ($recipes) {
                return $recipes->getId();
            }
        ]);

        $response->setContent($content);

        return $response;

        //return $this->json($recipeRepository->find($id), 200, [], ['groups' => 'recipe:read']);
    }

    /**
     * @Route("/api/recipe", name="api_recipe_store", methods={"POST"})
     */
    public function store(CategoryRepository $categoryRepository, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {

        $jsonReceived = $request->getContent();

        try {

            $newRecipe = $serializer->deserialize($jsonReceived, Recipe::class, 'json');

            $categoryValue = $newRecipe->getCategory();
            $categoryTitle = $categoryValue->getTitle();
            // extraire l'objet catégorie sur base du titre
            $category = $categoryRepository->findOneBy(['title' => $categoryTitle]);
            // setCategory à la nouvelle recette créée
            $newRecipe->setCategory($category);
            $newRecipe->setCreatedAt(new \DateTime());
            $errors = $validator->validate($newRecipe);

            if (count($errors) > 0) {

                $response = new JsonResponse(['message' => $errors]);
                $response->headers->set('Content-Type', 'application/json');
                $response->headers->set('Access-Control-Allow-Origin', '*');
                $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
                $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);
    
                return $response;
            }

            $em->persist($newRecipe);
            $em->flush();

            $response = new JsonResponse();
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $content = $serializer->serialize($newRecipe, 'json', [
                'circular_reference_handler' => function ($newRecipe) {
                    return $newRecipe->getId();
                }
            ]);
    
            $response->setContent($content);
    
            return $response;

            //return $this->json($recipe, 201, [], ['groups' => 'recipe:read']);

            // si le format json remis n'est pas correctement écrit "Syntax error"
        } catch (NotEncodableValueException $e) {
            
            $response = new JsonResponse(['status' => 400, 'message' => $e->getMessage()]);
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

            return $response;
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

        $message = "The recipe '" . $toDelete->getTitle() . "' has been successfully deleted!";

        $response = new JsonResponse(['message' => $message]);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

        return $response;
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
            $toModify->setCreatedAt(new \DateTime());
            $toModify->setDifficulty($deserializedReceived->getDifficulty());
            $toModify->setPortions($deserializedReceived->getPortions());
            
            $errors = $validator->validate($deserializedReceived);

            // vérifier si le validator n'a pas d'erreurs
            if (count($errors) > 0) {

                $response = new JsonResponse(['message' => $errors]);
                $response->headers->set('Content-Type', 'application/json');
                $response->headers->set('Access-Control-Allow-Origin', '*');
                $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
                $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

                return $response;
            }

            $em->persist($toModify);
            $em->flush();

            $message = "The recipe '" . $toModify->getTitle() . "' has been successfully modified!";

            $response = new JsonResponse(['message' => $message]);
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

            return $response;

        } catch (NotEncodableValueException $e) {

            $response = new JsonResponse(['status' => 400, 'message' => $e->getMessage()]);
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

            return $response;
        }
    }
}
