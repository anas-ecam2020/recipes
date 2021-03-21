<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Comment;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiCommentController extends AbstractController
{
    /**
     * @Route("/api/comment/{id}", name="api_comment_store", methods={"POST"})
     */
    public function index($id, Request $request, RecipeRepository $recipeRepository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em): Response
    {

        $jsonReceived = $request -> getContent();
        $recipe = $recipeRepository -> find($id);

        try {

            $content = $serializer -> deserialize($jsonReceived, Comment::class, 'json');
            $content -> setCreatedAt(new \DateTime());
            $content -> setRecipe($recipe);
            

            $errors = $validator -> validate($content);

            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $em -> persist($content);
            $em -> flush();

            return $this->json($content, 201, [], ['groups' =>'recipe:read']);

        } catch(NotEncodableValueException $e) {

            return $this->json([
                'status' => 400,
                'message' => $e ->getMessage()
            ], 400);

        }
        
    }
}
