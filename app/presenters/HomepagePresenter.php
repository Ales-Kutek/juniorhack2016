<?php

namespace App\Presenters;

use Nette;


class HomepagePresenter extends SecurePresenter
{
        /** @var \Repository\Element $elementRepository */
        private $elementRepository;
        
        public function __construct(\Repository\Element $elementRepository) {
            $this->elementRepository = $elementRepository;
        }

	public function renderDefault()
	{
		$this->template->rooms = $this->elementRepository->getAll(FALSE, $this->user->identity->id);
	}

}
