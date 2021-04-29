<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiCategoryController extends AbstractController
{
    /**
     * @Route("/api/categories", name="api_category_index", methods={"GET"})
     */
    public function index(): Response
    {

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->findAll();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $content = $serializer->serialize($categories, 'json', [
            'circular_reference_handler' => function ($categories) {
                return $categories->getId();
            }
        ]);

        $response->setContent($content);

        return $response;
    }

    /**
     * @Route("/api/category/{id}", name="api_recipes_per_category", methods={"GET"})
     */
    public function category($id): Response
    {

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS");
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

        $em = $this->getDoctrine()->getManager();
        $recipes = $em->getRepository(Category::class)->find($id);

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
    }
}
