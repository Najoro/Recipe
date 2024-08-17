<?php

namespace App\Controller\Admin;

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
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/recette' , name: "admin_recette_")]
class RecetteController extends AbstractController
{

    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
       $this->em = $em;
    }
 



    #[Route('/', name: 'index')]    
    /**
     * index : afficher tous les recette disponible
     *
     * @param  mixed $em
     * @return Response
     */
    public function index(EntityManagerInterface $em): Response
    {
        $recipes = $em->getRepository(Recipe::class)->findAll();
        
        return $this->render('admin/recette/index.html.twig', [
            'recipes' => $recipes
        ]);
    }



    #[Route('/{id}', name: "show", requirements: ['id' =>  Requirement::DIGITS])]    
    /**
     * show : Afficher chaque recette (single)
     *
     * @param  mixed $slug
     * @param  mixed $id
     * @param  mixed $recipe
     * @return Response
     */
    public function show(int $id, RecipeRepository $recipe): Response
    {

        $recipe = $recipe->find($id);
        // dd($recipe);

        return $this->render('admin/recette/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route("/create", name : "create")]    
    /**
     * creatNewRecipe : Creer uns nouvelle recette
     *
     * @return Response
     */
    public function creatNewRecipe() : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, null, [
            "action" => $this->generateUrl("admin_recette_created")
        ]);
        return $this->render("admin/recette/creat_recipe.html.twig", [
            'form' => $form
        ]);
    }
    


    #[route("/creat/created" , name: "created")]    
    /**
     * RecipeCreated : confirmer la nouvelle recette creer precedament
     *
     * @param  mixed $request
     * @return void
     */
    public function RecipeCreated(Request $request)
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe, [
            "action" => $this->generateUrl("admin_recette_created")
        ]); 
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $recipe->setCreatedAt(new DateTimeImmutable());
            $recipe->setUpdatedAt(new DateTimeImmutable());
            $this->em->persist($recipe);
            $this->em->flush();
            
            $this->addFlash("success" , "Votre recette a bien ete creer");
            return $this->redirectToRoute('admin_recette_index');
            
        }

        return $this->render("admin/recette/creat_recipe.html.twig", [
            'form' => $form
        ]);
    }



    
    #[Route("/{id}/edit" , name: "edit")]    
    /**
     * recipeEdit : Editer une recette 
     *
     * @param  mixed $recipe
     * @param  mixed $request
     * @return Response
     */
    public function recipeEdit(Recipe $recipe, Request $request): Response
    {
        
        $form = $this->createForm(RecipeType::class , $recipe);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 

            $this->em->flush();
            $this->addFlash("success" , "Votre Recette a bien ete modifier");
            return $this->redirectToRoute('admin_recette_index');
        }

        return $this->render("admin/recette/edit-recipe.html.twig" , [
            "form" =>$form,
        ]);
    }



    #[Route("/{id}/delete", name : "delete")]    
    /**
     * recipeDelete : Supprimer Une recette 
     *
     * @param  mixed $recipe
     * @return Response
     */
    public function recipeDelete(Recipe $recipe): Response 
    {   
        return $this->render('admin/recette/confirme-delete.html.twig',[
            "recipe" => $recipe,
        ]);
    }



    #[Route("/{id}/delete/confirm", name : "delete_confirm")]    
    /**
     * recipeConfirmDelete : confirmer la suppression de la recette
     *
     * @param  mixed $recipe
     * @return Response
     */
    public function recipeConfirmDelete(Recipe $recipe): Response 
    {   
        $this->em->remove($recipe);
        $this->em->flush();
        $this->addFlash("danger" , "Votre recette a bien ete supprimer");
        return $this->redirectToRoute('admin_recette_index');
    }
}