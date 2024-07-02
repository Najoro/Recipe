<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecetteController extends AbstractController
{
    #[Route('recipe/{slug}-{id}', name: "recipe_show", requirements: ['id' => '\d+', 'slug' => '[A-Za-z0-9-]+'])]
    public function show(string $slug, int $id, RecipeRepository $recipe): Response
    {

        $recipe = $recipe->find($id);
        // dd($recipe);
        if ($slug !== $recipe->getSlug()) {
            return $this->redirectToRoute('recipe_show', ['slug' => $recipe->getSlug(), 'id' => $id]);
        }
        return $this->render('recette/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #RecipeRepository => recupere la base de donne
    #[Route('/recipe', name: 'recipe_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $recipes = $em->getRepository(Recipe::class)->findAll();
        return $this->render('recette/index.html.twig', [
            'recipes' => $recipes
        ]);
    }
    #[Route("/recipe/creat-new-recipe", name : "recipe_creat_new_recipe")]
    public function creatNewRecipe(EntityManagerInterface $em , Request $request) : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $recipe->setCreatedAt(new DateTimeImmutable());
            $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            
            return $this->redirectToRoute('recipe_index');
            
        }

        return $this->render("recette/creat_new_recipe.html.twig", [
            'form' => $form
        ]);
    }
}