<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 2/1/2018
 * Time: 11:51 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Categories;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class UpdateCategory extends Controller{
    /**
        @Route("/Admin/ManageCategories/UpdateCategories/{id}/{pageNumber}",name="UpdateCategories")
     */
    public function updateAction(Request $request, $id, $pageNumber){
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        $PAGE_ID = 2;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        if($permissions->getCanEdit() == 0) {
            die("You cannot enter this page.");
        }
    
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
    
        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();

        $language = $session->get('locale');
        $request->setLocale($language);

        $em = $this->getDoctrine()->getManager();

        $categoryRepo = $em->getRepository('AppBundle:Categories');
        
        $Category = $categoryRepo->findByID($id);
        
        $data = [
            "category" => $Category->getCategoryName(),
            "parent" => $Category->getParentID(),
        ];
        
        $form = $this->createFormEdit($data);
        $form->handleRequest($request);
    
        if(!$Category){
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'unknown', 'users', "User not found.", $this->getIpAddress());
        
            return $this->render('Admin/ManageCategories/UpdateCategories.html.twig',[
                'form'=> $form->createView(),
                'pageNumber'=>$pageNumber,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'Category' => $Category,
                'userPages' => $systemPages,
            ]);
        }
        
        if($form->isSubmitted() && $form->isValid()){
            $formData = $form->getData();

            $Category->setCategoryName($formData['Category']);
            $Category->setParentID($formData['parentCategory']);
            
            $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
            $systemLogRepo->addSystemLog($user->getAdminId(), $id, 'categories', 'Update',$this->getIpAddress());
            
            return $this->redirectToRoute('ManageCategories', [
                'pageNumber'=> $pageNumber,
                'canDelete' => $canDelete,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canEdit' => $canEdit,
                'userPages' => $systemPages,
            ]);
        }
        
        return $this->render('Admin/ManageCategories/UpdateCategories.html.twig',[
            'form'=> $form->createView(),
            'pageNumber'=>$pageNumber,
            'canAdd' => $canAdd,
            'canView' => $canView,
            'canDelete' => $canDelete,
            'canEdit' => $canEdit,
            'Category' => $Category,
            'userPages' => $systemPages,
        ]);
    }

    public function checkRoles($pageId){
        $repository = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $repositoryRoles = $this->getDoctrine()->getRepository('AppBundle:UsersRoles');
        $repositoryPermissions = $this->getDoctrine()->getRepository('AppBundle:Permission');

        $userId = $this->getUser()->getAdminId();

        $roleOfPage = $repository->findRolesByPageId($pageId);
        $rolesOfUser = $repositoryRoles->getRolesById($userId);

        return in_array($roleOfPage['role_id'], $rolesOfUser, false) && $repositoryPermissions->findByUserRoleId($userId, $roleOfPage);
    }

    public function createFormEdit($data){
        $repositoryCategory = $this->getDoctrine()->getManager()->getRepository(Categories::class);

        $categories = $repositoryCategory->getParentCategories();

        $categoriesDisplay = Array();

        foreach ($categories as $category){
            $categoriesDisplay[$category->getCategoryName()] = $category->getCategoryID();
        }

        $categoriesDisplay["Parent"] = -1;

        $form = $this->createFormBuilder()
            ->add('Category', TextType::class,[
                'data' => $data["category"],
            ])
            ->add('parentCategory', ChoiceType::class,[
                'data' => $data["parent"],
                'choices' => $categoriesDisplay,
            ])
            ->add('Submit',SubmitType::class)
            ->getForm();

        return $form;
    }
}