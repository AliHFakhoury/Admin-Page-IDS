<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 2/11/2018
 * Time: 3:00 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Categories;
use AppBundle\Entity\CategoryQuestions;
use AppBundle\Entity\Questions;
use AppBundle\Entity\typeOfQuestion;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AssignQuestionCategoriesController extends Controller {
    /**
     * @Route("/Admin/ManageCategoryQuestions/assignQuestionCategories/{questionID}", name="AssignCategoryQuestions")
     */
    public function assingCategoriesAction(Request $request, $questionID){
        $session = new Session();
        
        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        $PAGE_ID = 14;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        if($permissions->getCanAdd() == 0){
            die("You cannot enter this page.");
        }
    
        $locale = $session->get('locale');
        $request->setLocale($locale);
        
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
    
        $form = $this->createFormCategory($questionID);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $formData = $form->getData();
            
            $questionCategoriesRepository = $em->getRepository(CategoryQuestions::class);
            $questionRepository = $em->getRepository(Questions::class);
            
            $categories = $questionCategoriesRepository->findBy([
                'quest_id' => $questionID,
            ]);
            
            $questionRepository->changeTypeOfQuestion($questionID, $formData["type"]);
            
            if (isset($categories)) {
                foreach($categories as $category){
                    $em->remove($category);
                }
            }
    
            try{
                $em->flush();
            }catch ( DBALException  $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'multiple', 'questions_in_category', $e->getMessage(), $this->getIpAddress());
                die($e->getMessage());
            }
    
            $this->addCategories($questionID, $formData["categories"]);
    
            
            
            $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
            $systemLogRepo->addSystemLog($user->getAdminId(), $questionID, 'questions_in_category', 'Edit',$this->getIpAddress());
    
            return $this->redirectToRoute('ManageCategoryQuestions', [
                'userPages' => $systemPages,
                'pageNumber' => 1,
            ]);
        }

        return $this->render('Admin/ManageCategoryQuestions/AddCategoryQuestions.html.twig', [
            'form' => $form->createView(),
            'userPages' => $systemPages,
            'pageNumber' => 1,
        ]);

    }

    public function addCategories($questionID, $data){
        $user = $this->getUser();
        
        $conn = $this->getDoctrine()->getEntityManager()->getConnection();
        for($i = 0; $i < count($data); $i++){
            $sql = "INSERT INTO questions_in_category (id,category_id,quest_id) VALUES (NULL, ".$data[$i].", ".$questionID.");";
            $stmt = $conn->prepare($sql);
    
            try{
                $stmt->execute();
            }catch ( Exception $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $questionID, 'questions_in_category', $e->getMessage(), $this->getIpAddress());
                die($e->getMessage());
            }
    
        }
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

    public function createFormCategory($questionID){
        $repositoryCategory = $this->getDoctrine()->getManager()->getRepository(Categories::class);
        $repositoryQuestionCategory = $this->getDoctrine()->getManager()->getRepository(CategoryQuestions::class);
        $repositoryTypeOfQuestion = $this->getDoctrine()->getManager()->getRepository(typeOfQuestion::class);

        $categories = $repositoryCategory->findNotParentCategories();
        $categoriesDisplay = Array();
        
        $questionTypes = $repositoryTypeOfQuestion->findAll();
        $typeDisplay = Array();
        
        $categoriesData = $repositoryQuestionCategory->getCategoriesOfQuestion($questionID);
        $currentType = $repositoryTypeOfQuestion->findTypeByQuestionId($questionID);
        
        foreach ($categories as $category){
            $categoriesDisplay[$category->getCategoryName()] = $category->getCategoryID();
        }
        
        foreach ($questionTypes as $type){
            $typeDisplay[$type->getQuestionType()] = $type->getTypeQuestionId();
        }

        $form = $this->createFormBuilder()
            ->add('categories', ChoiceType::class,[
                'expanded' => true,
                'multiple' => true,
                'choices' => $categoriesDisplay,
                'data' => $categoriesData,
            ])
            ->add('type', ChoiceType::class, [
                'expanded' => true,
                'choices' => $typeDisplay,
                'data' => $currentType
            ])
            ->add('Submit',SubmitType::class)
            ->add('Clear', ResetType::class)
            ->getForm();

        return $form;
    }
}