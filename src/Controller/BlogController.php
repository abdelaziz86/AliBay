<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Form\CommentType ;
use App\Form\PostType ;

use App\Entity\User ;
use App\Entity\ShopCategory ;
use App\Entity\Shop; 

use App\Entity\Comment ; 
use App\Entity\Post;   
use Symfony\Component\Form\Extension\Core\Type\SubmitType ;     

use Symfony\Component\Validator\Constraints\DateTime ; 

class BlogController extends AbstractController
{
    public function __construct(Security $security)
    {
         $this->security = $security;
    }


    /**
     * @Route("/blog", name="blog")
     */
    public function blog(Request $request): Response
    {   
        // === navbar
        $repo = $this->getDoctrine()->getRepository(ShopCategory::class) ;  
        $cats = $repo->findAll() ;

        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shops = $repo->findAll() ;
        // === end navbar
        $user=$this->security->getUser() ;
         
        $post = new Post();
        $form = $this->createForm(PostType::class, $post) ; 
        
        $repo = $this->getDoctrine()->getRepository(Post::class) ;  
        $posts = $repo->findBy(array(), array('createdAt' => 'DESC') , 10) ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($user) ;
            $post->setCreatedAt(new \DateTime('now')) ; 
            $em = $this->getDoctrine()->getManager();
            $em->persist($post) ;
            $em->flush() ; 
            return $this->redirectToRoute('blog') ;
            
        }

        return $this->render('front/blog.html.twig', [ 
            'cats' => $cats,
            'shops' => $shops,
            'user' =>  $user = $this->security->getUser() ,
            'form' => $form->createView(),
            'posts' => $posts
        ]);
    }



    /**
     * @Route("/post/{id}", name="post")
     */
    public function post(Request $request, $id): Response
    {   
        // === navbar
        $repo = $this->getDoctrine()->getRepository(ShopCategory::class) ;  
        $cats = $repo->findAll() ;

        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shops = $repo->findAll() ;
        // === end navbar

        $repo = $this->getDoctrine()->getRepository(Post::class) ; 
        $post = $repo->findOneById($id) ;

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment) ; 
         
        $user=$this->security->getUser() ;

        $repo = $this->getDoctrine()->getRepository(Comment::class) ;  
        $comments = $repo->findBy(array('post'=>$post), array('createdAt' => 'DESC') , 10) ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user) ;
            $comment->setCreatedAt(new \DateTime('now')) ; 
            $comment->setPost($post) ; 
            $post->setNbrComments($post->getNbrComments()+1) ; 
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment) ;
            $em->persist($post) ;

            $em->flush() ; 
            return $this->redirectToRoute('post', ['id' => $id]) ;
            
        }


        return $this->render('front/post.html.twig', [ 
            'cats' => $cats,
            'shops' => $shops,
            'post' => $post,
            'user' =>  $user = $this->security->getUser(),
            'form' => $form->createView(),
            'comments' => $comments


        ]);

    }

    /**
     * @Route("/blog/{id}", name="userBlog")
     */
    public function userBlog($id): Response
    {   
        // === navbar
        $repo = $this->getDoctrine()->getRepository(ShopCategory::class) ;  
        $cats = $repo->findAll() ;

        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shops = $repo->findAll() ;
        // === end navbar
        $repo = $this->getDoctrine()->getRepository(User::class) ; 
        $user = $repo->findBy(array('username' => $id)) ;

        $repo = $this->getDoctrine()->getRepository(Shop::class) ; 
        $shop = $repo->findBy(array('idUser' => $user[0])) ;

        $repo = $this->getDoctrine()->getRepository(Post::class) ; 
        $posts = $repo->findBy(array('user' => $user[0] )) ;
 
                     

        return $this->render('front/user.html.twig', [
             
            'cats' => $cats,
            'shops' => $shops,
            'user' => $user[0],
            'posts' => $posts,
            'shop' => $shop

        ]);

    }


    /**
     * @Route("/DeleteBlog", name="DeleteBlog")
     */
    public function DeleteBlogt(Request $request): Response
    {     
         
        if ($this->security->getUser() == null ) 
        {
            return $this->redirectToRoute('home') ; 

        }  
            $repo = $this->getDoctrine()->getRepository(Post::class) ; 
            $blog = $repo->findBy(array('id' => $request->get('id') , 'user' => $this->security->getUser() )) ;
            
            /*$uploads_directory = $this->getParameter('kernel.project_dir').'/public/uploads/Produit';
            $filesystem = new Filesystem();
            
            if ($this->checkNameProduitDifferentThanDefault($produit->getImage())) {
                $filesystem->remove($uploads_directory.'/'.$produit->getImage());
            }*/
            
            $em = $this->getDoctrine()->getManager() ; 

            foreach($blog[0]->getComments() as $c ) {
                $em->remove($c) ; 
            }

            $em->remove($blog[0]) ; 
            $em->flush() ; 

            return $this->redirectToRoute('userBlog',['id' => $this->security->getUser()->getUsername()]) ; 

         
        //return $this->render('admin/login.html.twig') ;
    }


}


?>