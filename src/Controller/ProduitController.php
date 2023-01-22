<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User ;
use App\Entity\Shop ; 
use App\Entity\Produit ; 
use App\Entity\CategoryProduit ;
use App\Entity\ShopCategory ; 
use Symfony\Component\Security\Core\Security;
use App\Form\CategoryProduitType ;
use App\Form\ProduitType ;
use Symfony\Component\Form\Extension\Core\Type\SubmitType ;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\Filesystem\Filesystem; 


class ProduitController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
         $this->security = $security;
    }
    /**
     * @Route("/produit", name="produit")
     */
    public function produit(): Response
    {
         if ($this->security->isGranted('ROLE_ADMIN')) {
            $user = $this->security->getUser() ; 

            $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
            $shop = $repo->findOneBy(array('idUser' => $user)) ;

            $repo = $this->getDoctrine()->getRepository(Produit::class) ; 
            $produits = $repo->findBy(array('idShop' => $shop)) ;

            return $this->render('admin/produit.html.twig', [
                    'produits' => $produits 
                ]) ;

        }
        return $this->render('admin/login.html.twig') ;
    }

    

    /**
     * @Route("/ajouterProduit", name="ajouterProduit")
     */
    public function ajouterProduit(Request $request): Response
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $user = $this->security->getUser() ; 
            $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
            $shop = $repo->findOneBy(array('idUser' => $user)) ;

            $produit = new Produit();
            $form = $this->createForm(ProduitType::class, $produit) ; 
            $form->add('Enregistrer', SubmitType::class,[
                    'attr' => [
                        'class'=>'btn btn-success waves-effect waves-light'
                    ]
                ]) ;

            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {

                $file = $form['image']->getData(); 
                
                if ($file != null ) { 
                    $ex = $file->guessExtension() ; 
                    if (($ex!="png")&&($ex!="jpeg")&&($ex!="jfif")&&($ex!="jpg")&&($ex!="gif")) {
                        return $this->redirectToRoute('modifierCategory', [
                            'error' => 1
                        ]) ; 
                    }
                    $uploads_directory = $this->getParameter('kernel.project_dir').'/public/uploads/Produit';
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $file_name = $shop->getName().$shop->getId().'produit'.str_replace(" ","",$produit->getName()).'.'.$file->guessExtension() ;
                    $file->move(
                        $uploads_directory,
                        $file_name
                    ) ; 
                    $produit->setImage($file_name) ; 
                } else {
                    if (($shop->getIdShopCategory())->getNom() =="Cafe") {
                        $produit->setImage("default_cafe.png") ;
                        //https://www.google.com/search?q=cafe+icon+cartoonized&sxsrf=APq-WBsvDdOTq_SxXDRDCzgJv2Eui3T4pg:1649685036679&source=lnms&tbm=isch&sa=X&ved=2ahUKEwiLsejUk4z3AhV8if0HHWMwCiAQ_AUoAXoECAEQAw&biw=1536&bih=754&dpr=1.25#imgrc=m-yGDequ31kpFM&imgdii=ahMDhUYOx6zpNM
                    } else if (($shop->getIdShopCategory())->getNom() =="Restaurant") {
                        $produit->setImage("default_restaurant.png") ;
                    } else if (($shop->getIdShopCategory())->getNom() =="Electronique") {
                        $produit->setImage("default_electronique.jpg") ;
                    } else if (($shop->getIdShopCategory())->getNom() =="Pâtisserie") {
                        $produit->setImage("default_patisserie.jpg") ;
                    }
                }

                $produit->setIdShop($shop) ;
                $em = $this->getDoctrine()->getManager();
                $em->persist($produit) ;
                $em->flush() ; 
                return $this->redirectToRoute('produit') ;
            }

            return $this->render('admin/ajouterProduit.html.twig', [
                    'shop' => $shop,
                    'formProduit' => $form->createView(),
                    'edit' => 0
                ]) ;

        }
        return $this->render('admin/login.html.twig') ;
    }


    /**
     * @Route("/DeleteProduit", name="DeleteProduit")
     */
    public function DeleteProduit(Request $request): Response
    {     
        if ($this->security->isGranted('ROLE_ADMIN')) {
            
            $repo = $this->getDoctrine()->getRepository(Produit::class) ; 
            $produit = $repo->findOneById($request->get('id')) ;
            
            $uploads_directory = $this->getParameter('kernel.project_dir').'/public/uploads/Produit';
            $filesystem = new Filesystem();
            
            if ($this->checkNameProduitDifferentThanDefault($produit->getImage())) {
                $filesystem->remove($uploads_directory.'/'.$produit->getImage());
            }


            $em = $this->getDoctrine()->getManager() ; 
            $em->remove($produit) ; 
            $em->flush() ; 

            return $this->redirectToRoute('produit') ; 

        }
        return $this->render('admin/login.html.twig') ;
    }


    /**
     * @Route("/modifierProduit", name="modifierProduit")
     */
    public function modifierProduit(Request $request): Response
    {
        $user = $this->security->getUser() ; 
        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shop = $repo->findOneBy(array('idUser' => $user)) ;

        $repo = $this->getDoctrine()->getRepository(Produit::class) ; 
        $produit = $repo->findOneById($request->get('id')) ;

         
        $p = $repo->findById($request->get('id')) ;

        if (($this->security->isGranted('ROLE_ADMIN'))&&($produit->getIdShop() == $shop)) {
            
 
            $form = $this->createForm(ProduitType::class, $produit) ; 
            $form->add('Enregistrer', SubmitType::class,[
                    'attr' => [
                        'class'=>'btn btn-success waves-effect waves-light'
                    ]
                ]) ;

            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {

                $file = $form['image']->getData(); 
                
                if ($file!=null) {
                    $ex = $file->guessExtension() ; 
                    if (($ex!="png")&&($ex!="jpeg")&&($ex!="jfif")&&($ex!="jpg")&&($ex!="gif")) {
                        return $this->redirectToRoute('modifierCategory', [
                            'error' => 1
                        ]) ; 
                    }
                      

                    $uploads_directory = $this->getParameter('kernel.project_dir').'/public/uploads/Produit';
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    
                    $filesystem = new Filesystem();
                    

                     
                    if ($this->checkNameProduitDifferentThanDefault($produit->getImage())) {
                        $filesystem->remove($uploads_directory.'/'.$produit->getImage());
                    }

                    $file_name = $shop->getName().$shop->getId().'produit'.str_replace(" ","",$produit->getName()).'.'.$file->guessExtension() ;
                   
                        $file->move(
                            $uploads_directory,
                            $file_name
                        ) ; 
                    
                    $produit->setImage($file_name) ; 
                
                } 

                $produit->setIdShop($shop) ;
                $em = $this->getDoctrine()->getManager();
                $em->persist($produit) ;
                $em->flush() ; 
                return $this->redirectToRoute('produit') ;
            }

            return $this->render('admin/ajouterProduit.html.twig', [
                    'shop' => $shop,
                    'formProduit' => $form->createView(),
                    'edit' => 1,
                    'produit' => $p
                ]) ;

        }
        return $this->redirectToRoute('admin') ;
    }


    function checkNameProduitDifferentThanDefault(String $name) {
        if (($name=="default_cafe.png") || ($name=="default_restaurant.png")
            || ($name=="default_electronique.jpg")
            || ($name=="default_patisserie.jpg")
        ) {
            return false ; 
        }   
        return true ; 
    }
 




}
