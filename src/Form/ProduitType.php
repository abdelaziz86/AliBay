<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Shop;
use App\Entity\ShopCategory;
use App\Entity\CategoryProduit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType ;
use Symfony\Component\Form\Extension\Core\Type\TextareaType ;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User; 
use Symfony\Component\Security\Core\Security;
use App\Controller\CategoryProduitController; 
use Doctrine\ORM\EntityRepository;

class ProduitType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
         $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'attr' => [
                    'class'=>'form-control'
                    
                ]
            ])
            ->add('description', TextAreaType::class,[
                'attr' => [
                    'class'=>'form-control',
                    'required' => false
                ]
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'label' => "Image",
                'attr' => [
                    'class'=>'form-control'
                ]
            ])
            ->add('price', TextType::class,[
                'attr' => [
                    'class'=>'form-control'
                ]
            ])
            ->add('featured', ChoiceType::class, [
                'choices'  => [
                    'Non' => 0 ,
                    'Oui' => 1                  
                ],
            ])
            ->add('disponible', ChoiceType::class, [
                'choices'  => [
                    'Oui' => 1 ,
                    'Non' => 0                  
                ],
            ]) 
            ->add('idCategoryProduit', EntityType::class,[
                'class'=>CategoryProduit::class,
                'query_builder' => function (EntityRepository $er) {
                    $user = $this->security->getUser() ; 
                    
                    return $er->createQueryBuilder('u')
                        ->join('u.IdShop', 'r') 
                        ->join('r.idUser', 's')  
                        ->where('s.id = '.$user->getId()) ; 
                }, 
                 
                'choice_label'=> 'name',
                /*'choice_label'=>function ($idCategoryProduit) {
                   /* $user = $this->security->getUser() ; 
                    $categ = new CategoryProduitController($this->security) ; 
                     

                    $shop = $em->createQuery('SELECT p FROM App\Entity\Shop p WHERE p.idUser LIKE :user') 
                            ->setParameter('user', '%'.$user.'%')  
                            ->getResult() ; 

                    if ($idCategoryProduit->getIdShop() == $shop) {
                        return $idCategoryProduit->getName();
                    }
                },*/
                'attr' => [
                    'class'=>'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
