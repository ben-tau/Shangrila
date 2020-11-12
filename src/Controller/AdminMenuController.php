<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\AdminMenuType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function index(MenuRepository $repo): Response
    {
        $menus = $repo->findAll();

        return $this->render('admin/menus/index.html.twig', [
            'menus' => $menus
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
}
