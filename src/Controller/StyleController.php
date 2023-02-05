<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
 
use App\Form\StyleType ;


use App\Entity\User ;
use App\Entity\Style ;
use App\Entity\ShopCategory ;
use App\Entity\Shop;
use App\Entity\CategoryProduit;
use App\Entity\Produit;



use App\Entity\Comment ; 
use App\Entity\Post;   
use Symfony\Component\Form\Extension\Core\Type\SubmitType ;     

use Symfony\Component\Validator\Constraints\DateTime ; 

class StyleController extends AbstractController
{
    public function __construct(Security $security)
    {
         $this->security = $security;
    }


    /**
     * @Route("/style", name="style")
     */
    public function style(Request $request): Response
    {   
        if ($this->security->getUser()!=null) {
            $user = $this->security->getUser() ;  
            
            $style = $user->getStyle() ; 
            if ($style==null) {
                $style = new Style() ; 
            }
            $form = $this->createForm(StyleType::class, $style) ; 
             
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $style->setUser($user) ;
                $em = $this->getDoctrine()->getManager();
                
                $em->persist($style) ;
                $em->flush() ; 
                return $this->redirectToRoute('style') ;
            }

            return $this->render('admin/style.html.twig', [
                    'form' => $form->createView(),
                    'edit' => 0,
                    'user' => $this->security->getUser()
                ]) ;

        }
        return $this->render('admin/login.html.twig') ;
    }





    /**
     * @Route("/{id}", name="shop")
     */
    public function index($id): Response
    {   
        // === navbar
        $repo = $this->getDoctrine()->getRepository(ShopCategory::class) ;  
        $cats = $repo->findAll() ;

        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shops = $repo->findAll() ;
        // === end navbar

        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shop = $repo->findBy(array('name' => $id)) ;

        $repo = $this->getDoctrine()->getRepository(CategoryProduit::class) ; 
        $categories = $repo->findBy(array('IdShop' => $shop)) ;

        $produits = [] ; 

         
        $repo = $this->getDoctrine()->getRepository(Produit::class) ; 
        $produits = $repo->findBy(array('idShop' => $shop)) ;

         
        $featured = $repo->createQueryBuilder('s')    
                            ->where('s.featured = 1 and s.idShop = '.$shop[0]->getId())  
                            ->getQuery()
                            ->getResult() ;
        
        $shop2 = $shop[0] ; 
        $shop2->setVisits($shop2->getVisits() + 1) ; 
        $em = $this->getDoctrine()->getManager();
        $em->persist($shop2) ;
        $em->flush() ; 
                             

        return $this->render('front/shop.html.twig', [
            'shop' => $shop,
            'produits' => $produits,
            'categories' => $categories,
            'featured' => $featured,
            'cats' => $cats,
            'shops' => $shops,
            'status' => $shop[0]->getStatus()
        ]);
    }

     

}


?>