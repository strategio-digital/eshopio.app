<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Presenter;

use App\ComponentFront\ContactModal\ContactModal;
use App\ComponentFront\ContactModal\IContactModal;

final class LandingPresenter extends BasePresenter
{
    /**
     * @var IContactModal
     * @inject
     */
    public IContactModal $IContactModal;

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Application\UI\InvalidLinkException
     */
    public function startup() : void
    {
        parent::startup();
        $this->setLayout(__DIR__ . '/templates/@layout-landing.latte');
    }

    /**
     * @return ContactModal
     */
    protected function createComponentContactModal() : ContactModal
    {
        return $this->IContactModal->create();
    }
}
