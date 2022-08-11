<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/8/2018
 * Time: 6:11 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Categories;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class AddCategoryController extends Controller {
    /**
     * @Route("/Admin/ManageCategories/AddCategory/{pageNumber}",name="AddACategory")
     */
    public function AddCategoryAction(Request $request,$pageNumber)
    {
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        $PAGE_ID = 2;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        if($permissions->getCanAdd() == 0){
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
        $form = $this->createFormCategory();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();

            $category = new Categories();
            $category->setCategoryName($data["categoryName"]);
            $category->setParentID($data["parentCategory"]);
            $category->setImageURL("test"); //until we have the images online
            $category->setIsDeleted(0);

            $em->persist($category);
    
            try{
                $em->flush();
            }catch ( DBALException  $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $category->getCategoryID(), 'permissions', $e->getMessage(), $this->getIpAddress());
                die($e->getMessage());
            }
    
            $categoryId = $category->getCategoryID();
            
            $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
            $systemLogRepo->addSystemLog($user->getAdminId(), $categoryId, 'categories', 'Add',$this->getIpAddress());
    
            return $this->redirectToRoute('ManageCategories',array(
                'language' => $language,
                'pageNumber'=>$pageNumber,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $systemPages,
            ));
        }

        return $this->render('Admin/ManageCategories/AddCategory.html.twig',[
                'form'=> $form->createView(),
                'language' => $language,
                'pageNumber'=>$pageNumber,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $systemPages,
            ]
        );
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

    public function createFormCategory(){
        $repositoryCategory = $this->getDoctrine()->getManager()->getRepository(Categories::class);

        $categories = $repositoryCategory->getParentCategories();
        $categoriesDisplay = Array();

        foreach ($categories as $category){
            $categoriesDisplay[$category->getCategoryName()] = $category->getCategoryID();
        }

        $categoriesDisplay["Is Parent"] = -1;
        $form = $this->createFormBuilder()
            ->add('categoryName', TextType::class)
            ->add('parentCategory', ChoiceType::class,[
                'choices' => $categoriesDisplay,
            ])
            ->add('Submit',SubmitType::class)
            ->add('Clear', ResetType::class)
            ->getForm();


        return $form;
    }

    public function getDate(){
        return new \DateTime('now', (new \DateTimeZone('Asia/Beirut')));
    }
}