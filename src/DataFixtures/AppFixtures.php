<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Recipe;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // variable faker est une instance de la classe Faker
        $faker = \Faker\Factory::create('fr_FR');
        $categories = array("Petit-déjeuner", "Déjeuner", "Dîner", "Brunch", "Snacks");
        $recipes = array("Smoothie au beurre de cacahuète, cacao et banane", "Rouleaux de sushi au pain", "Poulet parmigiana à faible teneur en glucides", 
        "Les meilleures crêpes", "Rouleaux de pizza Pepperoni");
        $catimages = array("/images/petit.png","/images/dejeuner.png", "/images/diner.png","/images/brunch.png","/images/snacks.png");
        $recimages = array("/images/smoothie.png", "/images/rouleaux.png", "/images/poulet.png", "/images/crepes.png", "/images/pizza.png");
        $favorites = array(true, false);
        $times = array(5, 25, 20, 10, 30);
        $difficulties = array("Facile", "Difficile", "Moyenne");
        $portions = array(5, 2, 4);

        

        for($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category ->setTitle($categories[$i])
                      ->setContent("Bienvenue dans la catégorie ".$categories[$i]."! "."Cliquez sur le bouton ci-dessous 
            pour consulter toutes les recettes relatives à la catégorie ".$categories[$i].".")
                      ->setImage($catimages[$i]);

            $manager ->persist($category);

            $recipe = new Recipe();
            $recipe ->setTitle($recipes[$i])
                    ->setContent($faker->paragraph())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category)
                    ->setImage($recimages[$i])
                    ->setFavorite($favorites[rand(0, 1)])
                    ->setTime($times[$i])
                    ->setDifficulty($difficulties[rand(0, 2)])
                    ->setPortions($portions[rand(0, 2)]);

            $manager ->persist($recipe);

            for($j = 0; $j <= rand(0, 5) ; $j++) {
                $comment = new Comment();
                $comment -> setAuthor($faker->name)
                         -> setContent($faker->paragraph)
                         -> setCreatedAt(new \DateTime())
                         -> setRecipe($recipe);

                $manager ->persist($comment);

            }
        }

        $manager->flush();
    }
}
