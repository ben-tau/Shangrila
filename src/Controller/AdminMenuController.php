<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\AdminMenuType;
use App\Form\AdminMenuEditType;
use App\Repository\MenuRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminMenuController extends AbstractController
{
    /**
     * @Route("/admin/menus", name="admin_menus")
     */
    public function index(MenuRepository $menuRepo,CommentRepository $commentRepo): Response
    {
        $menus = $menuRepo->findAll();
        $comments = $commentRepo->findAll();

        return $this->render('admin/menus/index.html.twig', [
            'menus' => $menus,
            'comments' => $comments
        ]);
    }

    /**
     * Permet de créer un menu dans la partie admin
     * @Route("admin/menus/create",name="admin_menus_create")
     * @param Menu $menu
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function create(Request $request,EntityManagerInterface $entityManager,SluggerInterface $slugger)
    {
        $menu = new Menu();
        $form = $this->createForm(AdminMenuType::class,$menu);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $img = $form->get('img')->getData();

            if($img)
            {
                $imgOriginalFilename = pathinfo($img->getClientOriginalName(),PATHINFO_FILENAME);
                $imgSafeFilename = $slugger->slug($imgOriginalFilename);
                $imgNewFilename = $imgSafeFilename.'-'.uniqid().'.'.$img->guessExtension();

                try{
                    $img->move(
                        $this->getParameter('img_menus_directory'),
                        $imgNewFilename
                    );
                }
                catch(FileException $e)
                {
                    $this->addFlash('warning',"Un problème est survenu sur le téléchargement de votre image, vérifiez la taille (10Mo max.) ou l'extension de votre fichier. Veuillez télécharger un document avec un format valide (.jpeg, .jpg, .png)");
                }

                $menu->setImg($imgNewFilename);

            }

            $entityManager->persist($menu);
            $entityManager->flush();

            $this->addFlash('success',"Votre menu a bien été créé !");
        }

        return $this->render('admin/menus/create.html.twig',[
            'menu'=>$menu,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Permet d'éditer un commentaire via l'admin
     * @Route("/admin/menus/{id}/edit",name="admin_menus_edit")
     * @param Menu $menu
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Menu $menu,Request $request,EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(AdminMenuEditType::class,$menu);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($menu);
            $entityManager->flush();

            $this->addFlash("success","La modification du menu a bien été effectuée");
        } 

        return $this->render('admin/menus/edit.html.twig',[
            'menu' => $menu,
            'form' => $form->createView()
        ]);
    }

    /**
     * Suppression d'un menu par l'admin
     * @Route("/admin/menus/{id}/delete",name="admin_menu_delete")
     * @param Menu $menu
     * @param EntityManagerInterface $entityManager
     * @return void
     */
    public function delete(Menu $menu,EntityManagerInterface $entityManager)
    {
        $id = $menu->getId();

        $entityManager->remove($menu);
        $entityManager->flush();

        $this->addFlash("success","Le menu $id a bien été supprimé !");
        return $this->redirectToRoute('admin_menus');
    }
}
