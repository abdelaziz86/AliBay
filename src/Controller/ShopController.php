<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Form\ShopType ;
use App\Entity\User ;
use App\Entity\Shop ; 
use App\Entity\Post;
use App\Entity\CategoryProduit ; 
use App\Entity\ShopCategory ; 
use App\Entity\Produit ; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType ; 
use Symfony\Component\Filesystem\Filesystem; 
use App\Form\PostType;
use Symfony\Component\Validator\Constraints\DateTime ; 

class ShopController extends AbstractController
{
    public function __construct(Security $security)
    {
         $this->security = $security;
    }

    

    /**
     * @Route("/partenariat", name="partenariat")
     */
    public function partenariat(): Response
    {   
        // === navbar
        $repo = $this->getDoctrine()->getRepository(ShopCategory::class) ;  
        $cats = $repo->findAll() ;

        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shops = $repo->findAll() ;
        // === end navbar

         
                             

        return $this->render('front/partenariat.html.twig', [ 
            'cats' => $cats,
            'shops' => $shops
        ]);
    }

    


    /**
     * @Route("/affiliate", name="affiliate")
     */
    public function affiliate(): Response
    {   
        // === navbar
        $repo = $this->getDoctrine()->getRepository(ShopCategory::class) ;  
        $cats = $repo->findAll() ;

        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shops = $repo->findAll() ;
        // === end navbar

         
                             

        return $this->render('front/affiliate.html.twig', [ 
            'cats' => $cats,
            'shops' => $shops
        ]);
    }



    /**
     * @Route("/article/{id}", name="detail_produit")
     */
    public function detail($id): Response
    {   
        // === navbar
        $repo = $this->getDoctrine()->getRepository(ShopCategory::class) ;  
        $cats = $repo->findAll() ;

        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shops = $repo->findAll() ;
        // === end navbar

        $repo = $this->getDoctrine()->getRepository(Produit::class) ; 
        $produits = $repo->findBy(array('id' => $id)) ;

        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shop = $repo->findBy(array('id' => $produits[0]->getIdShop())) ;

        $repo = $this->getDoctrine()->getRepository(Produit::class) ; 
         

         
        $featured = $repo->createQueryBuilder('s')  
                            ->join('s.idShop', 'r')  
                            ->where('r.id = '.$shop[0]->getId())  
                            ->getQuery()
                            ->getResult() ;

        $produit = $repo->findOneById($id) ;
        $produit->setVisits($produit->getVisits() + 1) ; 
        $em = $this->getDoctrine()->getManager();
        $em->persist($produit) ;
        $em->flush() ; 
                             

        return $this->render('front/product_page.html.twig', [
            'shop' => $shop,
            'produits' => $produits ,
            'featured' => $featured,
            'cats' => $cats,
            'shops' => $shops

        ]);
    }


    /**
     * @Route("/modifierBoutique", name="modifierBoutique")
     */
    public function editShop(Request $request){
        $user = $this->security->getUser() ;
        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shop = $repo->findOneById($request->get('id')) ;
        
        
        if (($this->security->isGranted('ROLE_ADMIN'))&&($user == $shop->getIdUser() )) {
                $form = $this->createForm(ShopType::class, $shop) ; 
                $form->add('Modifier', SubmitType::class,[
                        'attr' => [
                            'class'=>'btn btn-success waves-effect waves-light'
                        ]
                    ]) ;

                $form->handleRequest($request);
     
                if ($form->isSubmitted() && $form->isValid()) {
                    echo "done" ; 
                    $file = $form['image']->getData(); 
                    if ($file != null) {
                        $ex = $file->guessExtension() ; 
                        if (($ex!="png")&&($ex!="jpeg")&&($ex!="jfif")&&($ex!="jpg")&&($ex!="gif")) {
                            return $this->redirectToRoute('modifierBoutique', [
                                'error' => 1
                            ]) ; 
                        }
                        $uploads_directory = $this->getParameter('kernel.project_dir').'/public/uploads/Shop';
                        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                        $filesystem = new Filesystem();

                    if ($this->checkNameShopDifferentThanDefault($shop->getImage())) {
                        $filesystem->remove($uploads_directory.'/'.$shop->getImage());
                    }

                        $file_name = 'shop'.str_replace(" ","",$shop->getName()).'.'.$file->guessExtension() ;
                        $file->move(
                            $uploads_directory,
                            $file_name
                        ) ; 
                        $shop->setImage($file_name) ;
                    }  

                    $shop->setIdUser($user) ; 
                     
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($shop) ; 
                    $em->flush() ; 
                    return $this->redirectToRoute('admin') ; 
                }
                return $this->render('admin/editShop.html.twig',[
                    'shop' => $shop,
                    'formShop' => $form->createView()
                ]) ;
        }
         return $this->redirectToRoute('admin')  ;
    }






    



    function checkNameShopDifferentThanDefault(String $name) {
        if (($name=="default_cafeShop.png") || ($name=="default_restaurantShop.jpg")
            || ($name=="efault_electroniqueShop.jpg")
            || ($name=="efault_patisserieShop.jpg")
        ) {
            return false ; 
        }   
        return true ; 
    } 



    
}
