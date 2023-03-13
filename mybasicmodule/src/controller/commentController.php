<?php
    
namespace Mybasicmodule\Controller;

use Mybasicmodule\Entity\CommentTest;
use Mybasicmodule\Form\CommentType;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $form = $this->createForm(CommentType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            // Build the object
            $commentTest = new CommentTest();

            $commentTest->setName($form->get('name')->getData());
            $commentTest->setDescription($form->get('description')->getData());
            $commentTest->setPrice($form->get('price')->getData());

            //persist the data
            $em->persist($commentTest);
            $em->flush();
            $this->addFlash("success", "The form has been submitted correctly");
            $this->addFlash("error", "ERROR === The form has been submitted correctly");
            $this->addFlash("error500", "500 === The form has been submitted correctly");
        }

        return $this->render("@Modules/mybasicmodule/views/templates/admin/comment.html.twig",[
            "test" => 123,
            "form" => $form->createView()
            
        ]);
    }

    public function listAction()
    {
        // get em
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(CommentTest::class)->findAll();
        //$form = $this->createForm(CommentType::class, $data);
        return $this->render("@Modules/mybasicmodule/views/templates/admin/listing.html.twig",[
            "data" => $data,
        ]);
    }

    public function updateAction(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(CommentTest::class)->find($id);
        $form = $this->createForm(CommentType::class, $data);
        // Handle the submision
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash("success", "The form has been submitted correctly");
        }

        return $this->render("@Modules/mybasicmodule/views/templates/admin/update.html.twig",[
            "form" => $form->createView()
        ]);
    }
}

?>