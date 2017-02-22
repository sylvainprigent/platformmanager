<?php

require_once 'Framework/Controller.php';
require_once 'Framework/Form.php';
require_once 'Modules/core/Controller/CoresecureController.php';
require_once 'Modules/core/Model/CoreStatus.php';
require_once 'Modules/services/Model/ServicesInstall.php';
require_once 'Modules/services/Model/ServicesTranslator.php';

/**
 * 
 * @author sprigent
 * Controller for the home page
 */
class ServicesconfigController extends CoresecureController {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        
        if (!$this->isUserAuthorized(CoreStatus::$USER)) {
            throw new Exception("Error 503: Permission denied");
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see Controller::indexAction()
     */
    public function indexAction($id_space) {

        $this->checkSpaceAdmin($id_space, $_SESSION["id_user"]);
        $lang = $this->getLanguage();
        $modelSpace = new CoreSpace();
        $modelCoreConfig = new CoreConfig();
        
        // maintenance form
        $formMenusactivation = $this->menusactivationForm($id_space, $lang);
        if ($formMenusactivation->check()) {

            $modelSpace->setSpaceMenu($id_space, "services", "services", "glyphicon glyphicon-plus", 
                    $this->request->getParameter("servicesmenustatus"),
                    $this->request->getParameter("displayMenu"));
            
            $this->redirect("servicesconfig/".$id_space);
            
            return;
        }
        
        // color menu form
        $formMenuColor = $this->menuColorForm($modelCoreConfig, $id_space, $lang);
        if ($formMenuColor->check()) {
            $modelCoreConfig->setParam("servicesmenucolor", $this->request->getParameter("servicesmenucolor"), $id_space);
            $modelCoreConfig->setParam("servicesmenucolortxt", $this->request->getParameter("servicesmenucolortxt"), $id_space);
            
            $this->redirect("servicesconfig/".$id_space);
            return;
        }
        
        // period projects
        $formPerodProject = $this->periodProjectForm($modelCoreConfig, $id_space, $lang);
        if($formPerodProject->check()){
            $modelCoreConfig->setParam("projectperiodbegin", CoreTranslator::dateToEn($this->request->getParameter("projectperiodbegin"), $lang) , $id_space);
            $modelCoreConfig->setParam("projectperiodend", CoreTranslator::dateToEn($this->request->getParameter("projectperiodend"), $lang), $id_space);
            
            $this->redirect("servicesconfig/".$id_space);
            return;
        }
        
        // project command form
        $formProjectCommand = $this->projectCommandForm($modelCoreConfig, $id_space, $lang);
        if($formProjectCommand->check()){
            $modelCoreConfig->setParam("servicesuseproject", $this->request->getParameter("servicesuseproject"), $id_space);
            $modelCoreConfig->setParam("servicesusecommand", $this->request->getParameter("servicesusecommand"), $id_space);
            
            $this->redirect("servicesconfig/".$id_space);
            return;
        }

        // view
        $forms = array($formMenusactivation->getHtml($lang), $formMenuColor->getHtml($lang), 
                        $formPerodProject->getHtml($lang), $formProjectCommand->getHtml($lang)
                );
        $this->render(array("id_space" => $id_space, "forms" => $forms, "lang" => $lang));
    }

    protected function menusactivationForm($id_space, $lang) {

        $modelSpace = new CoreSpace();
        $statusUserMenu = $modelSpace->getSpaceMenusRole($id_space, "services");
        $displayMenu = $modelSpace->getSpaceMenusDisplay($id_space, "services");
        
        
        $form = new Form($this->request, "menusactivationForm");
        $form->addSeparator(CoreTranslator::Activate_desactivate_menus($lang));

        $roles = $modelSpace->roles($lang);

        $form->addSelect("servicesmenustatus", CoreTranslator::Users($lang), $roles["names"], $roles["ids"], $statusUserMenu);
        $form->addNumber("displayMenu", CoreTranslator::Display_order($lang), false, $displayMenu);
        
        $form->setValidationButton(CoreTranslator::Save($lang), "servicesconfig/".$id_space);
        $form->setButtonsWidth(2, 9);

        return $form;
    }
    
    public function menuColorForm($modelCoreConfig, $id_space, $lang){
        $ecmenucolor = $modelCoreConfig->getParamSpace("servicesmenucolor", $id_space);
        $ecmenucolortxt = $modelCoreConfig->getParamSpace("servicesmenucolortxt", $id_space);
        
        $form = new Form($this->request, "menuColorForm");
        $form->addSeparator(CoreTranslator::color($lang));
        $form->addColor("servicesmenucolor", CoreTranslator::menu_color($lang), false, $ecmenucolor);
        $form->addColor("servicesmenucolortxt", CoreTranslator::text_color($lang), false, $ecmenucolortxt);
        
        $form->setValidationButton(CoreTranslator::Save($lang), "servicesconfig/".$id_space);
        $form->setButtonsWidth(2, 9);
        
        return $form;
    }
    
    public function periodProjectForm($modelCoreConfig, $id_space, $lang){
        $projectperiodbegin = CoreTranslator::dateFromEn($modelCoreConfig->getParamSpace("projectperiodbegin", $id_space), $lang);
        $projectperiodend = CoreTranslator::dateFromEn($modelCoreConfig->getParamSpace("projectperiodend", $id_space), $lang);
        
        $form = new Form($this->request, "periodProjectForm");
        $form->addSeparator(ServicesTranslator::projectperiod($lang));
        $form->addDate("projectperiodbegin", ServicesTranslator::projectperiodbegin($lang), true, $projectperiodbegin);
        $form->addDate("projectperiodend", ServicesTranslator::projectperiodend($lang), true, $projectperiodend);
        
        $form->setValidationButton(CoreTranslator::Save($lang), "servicesconfig/".$id_space);
        $form->setButtonsWidth(2, 9);
        
        return $form;
    }

    public function projectCommandForm($modelCoreConfig, $id_space, $lang){
        $servicesuseproject = $modelCoreConfig->getParamSpace("servicesuseproject", $id_space);
        $servicesusecommand = $modelCoreConfig->getParamSpace("servicesusecommand", $id_space);
       
        $form = new Form($this->request, "periodCommandForm");
        $form->addSeparator(ServicesTranslator::Project($lang) . " & " . ServicesTranslator::Orders($lang) );
        $form->addSelect("servicesuseproject", ServicesTranslator::UseProject($lang), array(CoreTranslator::yes($lang), CoreTranslator::no($lang)), array(1,0), $servicesuseproject);
        $form->addSelect("servicesusecommand", ServicesTranslator::UseCommand($lang), array(CoreTranslator::yes($lang), CoreTranslator::no($lang)), array(1,0), $servicesusecommand);
        
        $form->setValidationButton(CoreTranslator::Save($lang), "servicesconfig/".$id_space);
        $form->setButtonsWidth(2, 9);
        
        return $form;
    }
}
