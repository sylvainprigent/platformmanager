<?php

require_once 'Framework/Controller.php';
require_once 'Framework/Form.php';
require_once 'Modules/core/Controller/CoresecureController.php';
require_once 'Modules/core/Model/CoreStatus.php';
require_once 'Modules/mailer/Model/MailerInstall.php';
require_once 'Modules/mailer/Model/MailerTranslator.php';

/**
 * 
 * @author sprigent
 * Controller for the home page
 */
class MailerconfigController extends CoresecureController {

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

        // maintenance form
        $formMenusactivation = $this->menusactivationForm($lang, $id_space);
        if ($formMenusactivation->check()) {

           $modelSpace->setSpaceMenu($id_space, "mailer", "mailer", "glyphicon-user", $this->request->getParameter("usermenustatus"));
            
            $this->redirect("mailerconfig/".$id_space);
            return;
        }

        // view
        $forms = array($formMenusactivation->getHtml($lang)
                        );
        $this->render(array("id_space" => $id_space, "forms" => $forms, "lang" => $lang));
    }

    protected function menusactivationForm($lang, $id_space) {
        
        $modelSpace = new CoreSpace();
        $statusUserMenu = $modelSpace->getSpaceMenusRole($id_space, "mailer");
        
        $form = new Form($this->request, "menusactivationForm");
        $form->addSeparator(CoreTranslator::Activate_desactivate_menus($lang));

        $roles = $modelSpace->roles($lang);

        $form->addSelect("usermenustatus", CoreTranslator::Users($lang), $roles["names"], $roles["ids"], $statusUserMenu);
        
        $form->setValidationButton(CoreTranslator::Save($lang), "mailerconfig/".$id_space);
        $form->setButtonsWidth(2, 9);

        return $form;
    }

}