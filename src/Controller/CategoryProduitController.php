<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType ;
use Symfony\Component\HttpFoundation\Request; 
use App\Entity\User ;
use App\Entity\Shop ; 
use App\Entity\CategoryProduit ;
use App\Entity\ShopCategory ; 
use Symfony\Component\Security\Core\Security;
use App\Form\CategoryProduitType ;

class CategoryProduitController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
         $this->security = $security;
    }

    /**
     * @Route("/modifierCategory", name="modifierCategory")
     */
    public function modifierCategory(Request $request): Response
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $user = $this->security->getUser() ; 
            $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
            $shop = $repo->findOneBy(array('idUser' => $user)) ;

            $category = new CategoryProduit();
            $form = $this->createForm(CategoryProduitType::class, $category) ; 
            $form->add('Enregistrer', SubmitType::class,[
                    'attr' => [
                        'class'=>'btn btn-success waves-effect waves-light'
                    ]
                ]) ;

            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $category->setIdShop($shop) ; 
                $em = $this->getDoctrine()->getManager();
                $em->persist($category) ;
                $em->flush() ; 
                return $this->redirectToRoute('categorieProduit') ;
            }

            return $this->render('admin/modifierCategory.html.twig', [
                    'shop' => $shop,
                    'formCategory' => $form->createView(),
                    'edit' => 0
                ]) ;

        }
        return $this->render('admin/login.html.twig') ;
    }



    /**
     * @Route("/categorieProduit", name="categorieProduit")
     */
    public function categorieProduit(): Response
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $user = $this->security->getUser() ; 

            $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
            $shop = $repo->findOneBy(array('idUser' => $user)) ;

            $repo = $this->getDoctrine()->getRepository(CategoryProduit::class) ; 
            $categories = $repo->findBy(array('IdShop' => $shop)) ;

            return $this->render('admin/categoryProduit.html.twig', [
                    'categories' => $categories 
                ]) ;

        }
        return $this->render('admin/login.html.twig') ;
    }


    /**
     * @Route("/DeleteCategory", name="DeleteCategory")
     */
    public function deleteCategory(Request $request): Response
    {     
        if ($this->security->isGranted('ROLE_ADMIN')) {
            
            $repo = $this->getDoctrine()->getRepository(CategoryProduit::class) ; 
            $categories = $repo->findOneById($request->get('id')) ;
            
            $em = $this->getDoctrine()->getManager() ; 
            $em->remove($categories) ; 
            $em->flush() ; 

            return $this->redirectToRoute('categorieProduit') ; 

        }
        return $this->render('admin/login.html.twig') ;
    }


    /**
     * @Route("/editCategory", name="editCategory")
     */
    public function editCategory(Request $request): Response
    {
            $user = $this->security->getUser() ; 
            $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
            $shop = $repo->findOneBy(array('idUser' => $user)) ;

            $repo = $this->getDoctrine()->getRepository(CategoryProduit::class) ; 
            $category = $repo->findOneById($request->get('id')) ;

        if (($this->security->isGranted('ROLE_ADMIN'))&&($category->getIdShop() == $shop)) {
            

            $form = $this->createForm(CategoryProduitType::class, $category) ; 
            $form->add('Enregistrer', SubmitType::class,[
                    'attr' => [
                        'class'=>'btn btn-success waves-effect waves-light'
                    ]
                ]) ;

            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $category->setIdShop($shop) ; 
                $em = $this->getDoctrine()->getManager();
                $em->persist($category) ;
                $em->flush() ; 
                return $this->redirectToRoute('categorieProduit') ;
            }

            return $this->render('admin/modifierCategory.html.twig', [
                    'shop' => $shop,
                    'formCategory' => $form->createView(),
                    'edit' => 1
                ]) ;

        }
        return $this->redirectToRoute('admin') ;
    }


}



    