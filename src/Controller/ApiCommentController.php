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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiCommentController extends AbstractController
{
    /**
     * @Route("/api/comment/{id}", name="api_comment_store", methods={"POST","OPTIONS"})
     */
    public function index($id, Request $request, RecipeRepository $recipeRepository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em): Response
    {

        $jsonReceived = $request -> getContent();
        $recipe = $recipeRepository -> find($id);

        try {

            $newComment = $serializer -> deserialize($jsonReceived, Comment::class, 'json');
            $newComment -> setCreatedAt(new \DateTime());
            $newComment -> setRecipe($recipe);
            

            $errors = $validator -> validate($newComment);

            if(count($errors) > 0) {
                $response = new JsonResponse(['message' => $errors]);
                $response->headers->set('Content-Type', 'application/json');
                $response->headers->set('Access-Control-Allow-Origin', '*');
                $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
                $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);
    
                return $response;
            }

            $em -> persist($newComment);
            $em -> flush();

            $response = new JsonResponse();
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $content = $serializer->serialize($newComment, 'json', [
                'circular_reference_handler' => function ($newComment) {
                    return $newComment->getId();
                }
            ]);
    
            $response->setContent($content);
    
            return $response;

        } catch(NotEncodableValueException $e) {

            $response = new JsonResponse(['status' => 400, 'message' => $e->getMessage()]);
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

            return $response;
        }
        
    }
}
