<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecipeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
    
        $categories = array("Petit-déjeuner", "Déjeuner", "Dîner", "Brunch", "Snacks");
        $recipes = array("Smoothie au beurre de cacahuète, cacao et banane", "Rouleaux de sushi au pain", "Poulet parmigiana à faible teneur en glucides", 
        "Les meilleures crêpes", "Rouleaux de pizza Pepperoni");
        $favorites = array(true, false);
        $times = array(5, 25, 20, 10, 30);
        $difficulties = array("Facile", "Difficile", "Moyenne");
        $portions = array(5, 2, 4);

        for($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category -> setTitle($categories[$i])
                      -> setContent("Bienvenue dans la catégorie ".$categories[$i]."! "."Ici vous 
                                    pouvez trouver toutes les recettes relatives à la catégorie ".$categories[$i].".")
                      ->setImage("http://placehold.it/350x150");
            
            $recipe = new Recipe();
            $recipe -> setTitle($recipes[$i])
                    -> setCategory($category)
                    ->setContent("La préparation de la recette: ".$recipes[$i]." se fait de la manière suivante...")
                    ->setCreatedAt(new \DateTime())
                    ->setImage("http://placehold.it/350x150")
                    ->setFavorite($favorites[rand(0, 1)])
                    ->setTime($times[$i])
                    ->setDifficulty($difficulties[rand(0, 2)])
                    ->setPortions($portions[rand(0, 2)]);

            $manager ->persist($recipe);
            $manager ->persist($category);
        }
        $manager->flush();
    }
}
