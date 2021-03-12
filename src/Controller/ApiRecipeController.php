<?php

namespace App\Controller;

use App\Entity\Recipe;
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
 * @Route("/api/recipe", name="api_recipe_store", methods={"POST"})
 */
    public function store(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator) {
        
        $jsonRecu = $request->getContent();


        try {
            $recipe = $serializer->deserialize($jsonRecu, Recipe::class, 'json');
            $recipe ->setCreatedAt(new \DateTime());

            $errors = $validator->validate($recipe);

            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }
            
            $em->persist($recipe);
            $em->flush();
    
            return $this->json($recipe, 201, [], ['groups'=>'post:read']);

        } catch(NotEncodableValueException $e) {

            return $this->json([
                'status' => 400,
                'message' => $e ->getMessage()
            ], 400);
            
        }
    }
}
