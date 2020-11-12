<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\ProductSearch;
use App\Form\ProductSearchType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    public function __construct(ProductRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }
    /**
     *@Route("/biens", name="product.index")
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request) : Response
    {
        $search = new ProductSearch();
        $form = $this->createForm(ProductSearchType::class, $search);
        $form->handleRequest($request);

        $products = $paginator->paginate(
           $this->repository->findAllVisibleQuery($search),
           $request->query->getInt('page', 1),
            4);  
        return $this->render('product/index.html.twig',[
            'products' => $products,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/biens/{slug}-{id}", name="product.show", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function show(Product $product, string $slug) : Response
    {
        if ($product->getSlug() !== $slug )
        {
            return $this->redirectToRoute('product.show',[
                'id' => $product->getId(),
                'slug' => $product->getSlug()
            ], 301);
        }
        return $this->render('product/show.html.twig',[
            'product' => $product,
            'current_menu' => 'products'
        ]);
    }
    /**
     * @Route("/admin/product/create", name="admin.product.new")
     * $param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            $this->uploadFile($file, $product);

            $this->em->persist($product);
            $this->em->flush();

            $this->addFlash('success', "produit ajoute avec succes");
            return $this->redirectToRoute('admin.product.index');
        }
        return $this->render('admin/product/new.html.twig',[
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/product/{id}", name="admin.product.edit", methods="post|get")
     * @param Product $product
     * $param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Product $product, Request $request)
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            $this->uploadFile($file, $product);
            
            $this->em->flush();
            $this->addFlash('success', 'produit modifie avec succes');
            return $this->redirectToRoute('admin.product.index');
        }
        return $this->render('admin/product/edit.html.twig',[
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    public function uploadFile(File $file, object $object)
    {
        $filename = bin2hex(random_bytes(6)) . '.' . $file->guessExtension();
        $file->move($this->getParameter('uploads'), $filename);
        $object->setImage($filename);

        return $object;
    }
    
}
