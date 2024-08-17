<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/category', name : "admin_category_")]
class CategoryController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
        $this->em  = $em;
    }


    
    #[Route('/', name: 'show')]    
    /**
     * show : voir tous les category disponible
     *
     * @return Response
     */
    public function show(): Response
    {
        $categories = $this->em->getRepository(Category::class);
        
        return $this->render('admin/category/show.html.twig', [
            "categories" => $categories->findAll(),
        ]);
    }



    #[Route('/create', name: 'create')]        
    /**
     * create : creer une nouvelle categoey
     *
     * @param  mixed $request
     * @return Response
     */
    public function create(Request $request) : Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class , $category );

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash(
               'success',
               'une Category est creer avec success'
            );
            return $this->redirectToRoute('admin_category_show');
        }

        return $this->render("admin/category/creat.html.twig", [
            "form" => $form,
        ]);

    }



    #[Route('/edit/{id}', name: 'edit' , requirements: ["id" => Requirement::DIGITS])]    
    public function edit(Category $category , Request $request) : Response
    {
        $form = $this->createForm(CategoryType::class , $category);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->em->flush();
            $this->addFlash(
               'success',
               'Modification reussit'
            );
            return $this->redirectToRoute('admin_category_show');
        }
        return $this->render('admin/category/edit.html.twig',[
            "form" => $form
        ]);
    }

    #[Route('/delete/{id}', name: 'delete' , requirements: ["id" => Requirement::DIGITS])]    
    public function delete(Category $category) : Response
    {
        $this->em->remove($category);
        $this->em->flush();
        $this->addFlash(
           'success',
           'suppression reussit'
        );
        return $this->redirectToRoute('admin_category_show');

    }

}
