<?php
    /**
     * Permission Trait
     */

    namespace Security;

    /**
     * Permission Trait
     */
    trait TPermission
    {
            /**
             * @var Access\ICheckRequirements
             */
            protected $requirementsChecker;
            
            /**
             * @param $element
             * @throws Application\ForbiddenRequestException
             */
            public function checkRequirements($element)
            {
                    if (!$this->user->isLoggedIn()) {
                        $key = $this->storeRequest();
                        $this->redirect("Auth:", $key);
                    } else {
                        $this->template->identity = $this->user->identity;
                        
                        /** nastavení authorizatoru **/
                        $this->user->setAuthorizator(new Authorizator());
                        
                        /** nastavení role **/
                        $this->user->authenticatedRole = $this->user->identity->group->name;

                        $this->requirementsChecker = new \IPub\Permissions\Access\AnnotationChecker($this->user);

                        $allowed = $this->requirementsChecker->isAllowed($element);

                        if (!$allowed) {   
                                throw new \Nette\Application\ForbiddenRequestException;
                        }
                    }
                    
                
                    parent::checkRequirements($element);
            }
    }