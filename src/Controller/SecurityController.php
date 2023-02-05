<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Entity\User ; 
use App\Entity\Produit;
use App\Form\ShopType ;
use App\Entity\ShopCategory;
use App\Entity\Post ; 
use App\Form\RegistrationType ; 
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType ; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\DateTime ; 

class SecurityController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
         $this->security = $security;
    }


    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        if ($this->security->getUser()!=null) {
            return $this->redirectToRoute('admin') ; 
        }
        $user = new User() ; 
        $form = $this->createForm(RegistrationType::class, $user) ;
        $form->handleRequest($request) ; 
        
        if ($form->isSubmitted() && $form->isValid()) {

            // ============ Check promo code 
            if ($user->getRefered() != null) {
                $repo = $this->getDoctrine()->getRepository(User::class) ;  
                $referal = $repo->findBy(array('username' => $user->getRefered())) ;

                if ($referal == null) {
                    return $this->render('admin/register.html.twig',[
                        'form' => $form->createView(),
                        'error' => 1
                    ]) ; 
                } else {
                    $referal[0]->setNbrRefs($referal[0]->getNbrRefs() + 1) ; 
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($referal[0]) ;
                    $em->flush() ;
                }
            }  


            $hash = $encoder->encodePassword($user, $user->getPassword()) ; 

            $user->setPassword($hash) ;
            $user->setRoles(["ROLE_USER"]) ; 
            $em = $this->getDoctrine()->getManager();
            $em->persist($user) ;
            $em->flush() ; 
            

            $repo = $this->getDoctrine()->getRepository(User::class) ;  
            $connected = $repo->findBy(array('username' => $user->getUsername())) ;
             
            
            return $this->render('admin/login.html.twig', [
                'new' => 1
            ]) ; 

            //return $this->redirectToRoute('admin') ; 

        }
        return $this->render('admin/register.html.twig',[
            'form' => $form->createView()
        ]) ; 
    }


    /**
     * @Route("/", name="home1")
     */
    public function home1(){
        // === navbar
        $repo = $this->getDoctrine()->getRepository(ShopCategory::class) ;  
        $cats = $repo->findAll() ;

        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shops = $repo->findAll() ;
        // === end navbar
        $repo = $this->getDoctrine()->getRepository(Produit::class) ; 
        $produits = $repo->findBy(array('idShop' => 4)) ;

         
        /*$featured = $repo->createQueryBuilder('s')  
                            ->join('s.idShop', 'r')  
                            ->where('r.id = 4')  
                            ->getQuery()
                            ->getResult() ;*/

        $repo = $this->getDoctrine()->getRepository(Produit::class) ; 
        $featuredHome = $repo->findBy(array('featuredHome' => 1)) ;

        $repo = $this->getDoctrine()->getRepository(Post::class) ;  
        $posts = $repo->findBy(array(), array('createdAt' => 'DESC') , 2) ;

        return $this->render('security/home.html.twig', [
            //'featured' => $featured,
            'featuredHome' => $featuredHome,
            'cats' => $cats,
            'shops' => $shops,
            'posts' => $posts
        ]) ; 
    }


     /**
     * @Route("/admin", name="admin")
     */
    public function home(Request $request){
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $user = $this->security->getUser() ; 
            $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
            $shop = $repo->findBy(array('idUser' => $user)) ;
            
            // ====== ajouter shop 
            if ($shop == null) {
                 
                $shop1 = new Shop();
                $form = $this->createForm(ShopType::class, $shop1) ; 
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
                            return $this->redirectToRoute('admin', [
                                'error' => 1
                            ]) ; 
                        }
                        $uploads_directory = $this->getParameter('kernel.project_dir').'/public/uploads/Shop';
                        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $file_name = 'shop'.str_replace(" ","",$shop1->getName()).'.'.$file->guessExtension() ;
                        $file->move(
                            $uploads_directory,
                            $file_name
                        ) ; 
                    } else {
                        if (($shop1->getIdShopCategory())->getNom() == "Cafe") {
                            $file_name = "default_cafeShop.png" ; 
                        } else if (($shop1->getIdShopCategory())->getNom() == "Restaurant") {
                            $file_name = "default_restaurantShop.jpg" ; 
                        } else if (($shop1->getIdShopCategory())->getNom() == "Electronique") {
                            $file_name = "default_electroniqueShop.jpg" ; 
                        } else if (($shop1->getIdShopCategory())->getNom() == "Pâtisserie") {
                            $file_name = "default_patisserieShop.jpg" ; 
                        }
                    } 
                           
                    $date = date('m/d/Y h:i:s a', time());
                    $date2 = date('Y-m-d H:i:s', strtotime($date. ' + 90 days'));

                    $shop1->setEcheance(new \DateTime($date2)) ; 
                    $shop1->setIdUser($user) ; 
                    $shop1->setImage($file_name) ; 
                    $shop1->setStatus(1) ; 
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($shop1) ; 
                    $em->flush() ; 
                    return $this->redirectToRoute('admin') ; 
                }
                        
                
                
                return $this->render('admin/dashboard.html.twig', [
                    'shop' => $shop, 
                    'formShop' => $form->createView(), 
                    'user' => $this->security->getUser()
                ]) ;
            }

            $dateNow = strtotime(date('m/d/Y h:i:s', time()));
            $dateInt = $shop[0]->getEcheance(); 
            $result = ($dateInt->format('m/d/Y h:i:s')) ; 
            $dateFin = strtotime($result) ; 
            $diff = $dateFin - $dateNow;
            $days =  round($diff / 86400);

            /*$currentDate = new DateTime();
                $status = 0 ; 
                if ($shop[0]->getEcheance() < $currentDate) {
                    $status = 1 ;
                }
                */
            return $this->render('admin/dashboard.html.twig', [
                'shop' => $shop, 
                'days' => $days,
                'user' => $this->security->getUser()
            ]) ;

        } else if ($this->security->isGranted('ROLE_USER')) {

            // === navbar
            $repo = $this->getDoctrine()->getRepository(ShopCategory::class) ;  
            $cats = $repo->findAll() ;

            $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
            $shops = $repo->findAll() ;

            $repo = $this->getDoctrine()->getRepository(Produit::class) ; 
            $produits = $repo->findBy(array('idShop' => 4)) ;

            
            $featured = $repo->createQueryBuilder('s')  
                            ->join('s.idShop', 'r')  
                            ->where('r.id = 4')  
                            ->getQuery()
                            ->getResult() ;
            // === end navbar           
             return $this->render('admin/dashboard.html.twig',[
                'cats' => $cats,
                'shops' => $shops,
                'featured' => $featured,
                'owner'=> $this->security->isGranted('ROLE_ADMIN'),
                'user' => $this->security->getUser()
             ]) ;
         }
         
         return $this->render('admin/login.html.twig') ; 
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(){
        if ($this->security->getUser()!=null) {
            
            $user = $this->security->getUser() ; 
            $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
            $shop = $repo->findOneBy(array('idUser' => $user)) ;
            return $this->redirectToRoute('admin') ; 
        }
        return $this->render('admin/login.html.twig') ; 
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout(){
         
    }



    
    
}
