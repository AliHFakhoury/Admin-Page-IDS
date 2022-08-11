<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 2/6/2018
 * Time: 8:36 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Categories;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageQuestions extends Controller {
    /**
     * @Route("Admin/ManageCategoryQuestions/{pageNumber}", name="ManageCategoryQuestions")
     */
    public function manageSystemAction(Request $request, $pageNumber){
        $session = new Session();

        $PAGE_ID = 14;
        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        //checking for role
        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();
    
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
    
        $data = $session->get('data');
        
        $categoryQuestionsRepository = $this->getDoctrine()->getRepository('AppBundle:Questions');
        $categoryQuestions = $categoryQuestionsRepository->findAllQuestions($data,10,$pageNumber);

        $numberOfPages = $categoryQuestionsRepository->countPages($data,10);

        $language = $session->get('locale');
        
        $request->setLocale($language);
        
        $form = $this->createSearchForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $categoryQuestions = $categoryQuestionsRepository->findAllQuestions($data,10, $pageNumber);
            $session->set('data', $data);

            if($form->get('reset_form')->isClicked()){
                $session->set('data', null);
                return  $this->redirectToRoute('ManageCategoryQuestions',array(
                    'pageNumber'=>$pageNumber,
                    'canAdd' => $canAdd,
                    'canView' => $canView,
                    'canDelete' => $canDelete,
                    'canEdit' => $canEdit,
                    'questions' => $categoryQuestions,
                    'numPages' => $numberOfPages,
                ));
            }
        }

        return $this->render('Admin/ManageCategoryQuestions/ManageCategoryQuestions.html.twig', [
            'pageNumber' => $pageNumber,
            'userPages' => $systemPages,
            'canAdd' => $canAdd,
            'canView' => $canView,
            'canDelete' => $canDelete,
            'canEdit' => $canEdit,
            'questions' => $categoryQuestions,
            'numPages' => $numberOfPages,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Admin/ManageCategoryQuestions/delete/{id}/{pageNumber}" , name="QuestionDelete")
     */
    public function DeleteQeustion(Request $request,$id,$pageNumber){
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];
        
        if($permissions->getCanDelete() == 0){
            die("You cannot enter this page.");
        }

        $em = $this->getDoctrine()->getManager();

        $DeleteQuestion = $em->getRepository('AppBundle:Questions')->find($id);

        if (!$DeleteQuestion) {
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'unknown', 'questions', "Question not found.", $this->getIpAddress());
            
            return $this->redirectToRoute('ManageCategoryQuestions',array(
                'pageNumber'=>$pageNumber,
            ));
        }
        
        $DeleteQuestion->setIsDeleted(1);
        $em->flush();
        $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $systemLogRepo->addSystemLog($user->getAdminId(), $id, 'questions', 'Delete',$this->getIpAddress());
    
        return $this->redirectToRoute('ManageCategoryQuestions',array(
            'pageNumber'=>$pageNumber,
        ));
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

    public function createSearchForm(){
        $repositoryCategories = $this->getDoctrine()->getManager()->getRepository(Categories::class);
        $categories = $repositoryCategories->findNotParentCategories();

        $categoriesDisplay = array();

        for($i = 0; $i < count($categories); $i++){
            $categoriesDisplay[$categories[$i]->getCategoryName()] = $categories[$i]->getCategoryID();
        }
        $form = $this->createFormBuilder()
            ->add('QuestionName', TextType::class,[
                'required' => false,
            ])
            ->add('Question', TextType::class, [
                'required' => false,
            ])
            ->add('Category', ChoiceType::class, [
                'required' => false,
                'choices' => $categoriesDisplay,
            ])
            ->add('submit', SubmitType::class)
            ->add('reset_form', SubmitType::class)
            ->getForm();

        return $form;
    }
}