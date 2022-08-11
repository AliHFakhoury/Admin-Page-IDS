<?php

namespace AppBundle\Controller;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ManageCategoriesController extends Controller
{

    /**
     * @Route("/Admin/ManageCategories/{pageNumber}",name="ManageCategories")
     */
    public function ManageCategoriesAction(Request $request, $pageNumber){
        $session = new Session();

        $PAGE_ID = 2;

        $user = $this->getUser();
        $userID = $user->getAdminID();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        if(!$this->checkRoles($PAGE_ID, $userID)){
            die("You can't access this page.");
        }

        $session->set('data', null);

        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();
        $userRole = $session->get('userRole');

        $language = $session->get('locale');
        $request->setLocale($language);

        $form = $this->formCreate();

        $repo = $this->getDoctrine()->getRepository('AppBundle:Categories');

        $form->handleRequest($request);
        $category = $session->get('categories');
        $values["category"] = $category;
        $categories=$this->getDoctrine()->getRepository('AppBundle:Categories')->findAllCriteria($values, $pageNumber, 10);

        $countPages = $repo->countPages(10, $values);
    
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
    
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData(); //Form data from the user
            $values["categoryName"] = $data["categoryName"];
            $countPages = $repo->countPages(10, $values);

            $categories = $repo->findAllCriteria($values,$pageNumber, 10);
            $session->set('data', $data);

            if($form->get('reset_form')->isClicked()){
                $session->set('data', null);
                return  $this->redirectToRoute('ManageCategories',array(
                    'pageNumber'=>$pageNumber,
                    'language'=>$language,
                    'canAdd' => $canAdd,
                    'canView' => $canView,
                    'canDelete' => $canDelete,
                    'canEdit' => $canEdit,
                    'userRole' => $userRole,
                ));
            }
        }

        return $this->render('Admin/ManageCategories/ManageCategories.html.twig',
            [
                'form'=> $form->createView(),
                'Categories' => $categories,
                'numPages' =>$countPages,
                'pageNumber'=>$pageNumber,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userRole' => $userRole,
                'userPages' => $systemPages,
            ]);
    }

    /**
     * @Route("/Admin/ManageCategories/delete/{id}/{pageNumber}" , name="CategoryDelete")
     */
    public function DeleteCategory(Request $request,$id,$pageNumber){
        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        if($permissions->getCanDelete() == 0){
            die("You cannot enter this page.");
        }

        $em = $this->getDoctrine()->getManager();
        $DeleteCategory = $em->getRepository('AppBundle:Categories')->find($id);
        
        if (!$DeleteCategory) {
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'multiple', 'categories', "Category not found.", $this->getIpAddress());
            
            return $this->redirectToRoute('ManageCategories',array(
                'pageNumber'=>$pageNumber,
            ));
        }

        $DeleteCategory->setIsDeleted(1);

        $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $systemLogRepo->addSystemLog($user->getAdminId(), $id, 'categories', 'Delete',$this->getIpAddress());
    
        return $this->redirectToRoute('ManageCategories',array(
            'pageNumber'=>$pageNumber,
        ));
    }

    public function formCreate(){
        $session = new Session();

        if($session->get('locale') === 'ar'){
            $labelButtonReset = "إخلاء";
            $labelButtonSearch = "بحث";
        }else{
            $labelButtonReset = "Reset";
            $labelButtonSearch = "Search";
        }
        
        $form = $this->createFormBuilder()
            ->add('categoryName', TextType::class, [
                'required'  => false,
            ])
            ->add('Search', SubmitType::class, [
                'label' => $labelButtonSearch,
            ])
            ->add('reset_form',SubmitType::class, [
                'label' => $labelButtonReset,
            ])
            ->getForm();

        return $form;
    }

    public function checkRoles($pageId, $userID){
        $repository = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $repositoryRoles = $this->getDoctrine()->getRepository('AppBundle:UsersRoles');
        $repositoryPermissions = $this->getDoctrine()->getRepository('AppBundle:Permission');

        $roleOfPage = $repository->findRolesByPageId($pageId);

        $rolesOfUser = $repositoryRoles->getRolesById($userID);

        return in_array($roleOfPage['role_id'], $rolesOfUser, false) && $repositoryPermissions->findByUserRoleId($userID, $roleOfPage);
    }
}