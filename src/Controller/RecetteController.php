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

#[Route('/recipe')]
class RecetteController extends AbstractController
{

    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
       $this->em = $em;
    }
 

    #RecipeRepository => recupere la base de donne
    #[Route('/', name: 'recipe_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $recipes = $em->getRepository(Recipe::class)->findAll();
        return $this->render('recette/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/{slug}-{id}', name: "recette_show", requirements: ['id' => '\d+', 'slug' => '[A-Za-z0-9-]+'])]
    public function show(string $slug, int $id, RecipeRepository $recipe): Response
    {

        $recipe = $recipe->find($id);
        // dd($recipe);
        if ($slug !== $recipe->getSlug()) {
            return $this->redirectToRoute('recette.show', ['slug' => $recipe->getSlug(), 'id' => $id]);
        }
        return $this->render('recette/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route("/creat-recipe", name : "creat_recipe")]
    public function creatNewRecipe() : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe, [
            "action" => $this->generateUrl("recipe_created")
        ]);
        return $this->render("recette/creat_recipe.html.twig", [
            'form' => $form
        ]);
    }
    #[route("/creat-recipe/created" , name: "recipe_created")]
    public function RecipeCreated(Request $request){
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe, [
            "action" => $this->generateUrl("recipe_created")
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $recipe->setCreatedAt(new DateTimeImmutable());
            $recipe->setUpdatedAt(new DateTimeImmutable());
            $this->em->persist($recipe);
            $this->em->flush();
            
            return $this->redirectToRoute('recipe_index');
            
        }

        return $this->render("recette/creat_recipe.html.twig", [
            'form' => $form
        ]);
    }
}