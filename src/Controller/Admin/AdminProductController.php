<?php 
namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProductController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    private $repository;
    public function __construct(ProductRepository $repository, EntityManagerInterface $em)
    {
        $this->repository=$repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.product.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PaginatorInterface $paginator,Request $request)
    {
        $products = $paginator->paginate(
            $this->repository->findAll(),
            $request->query->getInt('page', 1),
             10); 
        return $this->render('admin/product/index.html.twig',compact('products'));
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
        if($form->isSubmitted() && $form->isValid())
        {

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
        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'produit modifie avec succes');
            return $this->redirectToRoute('admin.product.index');
        }
        return $this->render('admin/product/edit.html.twig',[
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/product/{id}", name="admin.product.delete", methods="DELETE")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Product $product, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(),$request->get('_token')))
        {
            $this->em->remove($product);
            $this->em->flush();
            $this->addFlash('success', 'produit supprime avec succes');
        }
        
        return $this->redirectToRoute('admin.product.index');
    }

    /**
     * @param File $file
     * @param object $object
     * 
     * @return object
     */
    public function uploadFile(File $file, object $object): object
    {
        $filename = bin2hex(random_bytes(6)) . '.' . $file->guessExtension();
        $file->move($this->getParameter('uploads'), $filename);
        $object->setImage($filename);

        return $object;
    }
}