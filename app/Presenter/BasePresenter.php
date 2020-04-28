<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Presenter;

use App\Component\AssetLoader\AssetLoader;
use App\Component\BreadCrumb\BreadCrumb;
use App\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\Component\BreadCrumb\IBreadCrumb;
use App\Component\Navbar\Navbar;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\User;
use App\Database\Repository\UserRepository;
use Nette;
use Nette\Security\User as NetteUser;

/**
 * Class BasePresenter
 * @package App\Presenter
 * @property-read \Nette\Bridges\ApplicationLatte\Template|\stdClass $template
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    use TBasePresenter;

    /**
     * @var array<Notification>
     */
    public array $notifications = [];

    /**
     * @var IBreadCrumb
     * @inject
     */
    public IBreadCrumb $IBreadCrumb;

    /**
     * @var NetteUser
     * @inject
     */
    public NetteUser $netteUser;

    /**
     * @var UserRepository
     * @inject
     */
    public UserRepository $userRepository;

    /**
     * @var User
     */
    public User $activeUser;

    /**
     * @var array<BreadCrumbItem>
     */
    protected array $breadCrumb = [];

    /**
     * @throws Nette\Application\AbortException
     * @throws Nette\Application\UI\InvalidLinkException
     */
    protected function startup() : void
    {
        if (!$this->netteUser->isLoggedIn()
            && (!$this->isLinkCurrent('User:login') && !$this->isLinkCurrent('Landing:summary'))) {
            $this->redirect('User:login');
            exit;
        }

        if ($this->netteUser->isLoggedIn() && $this->isLinkCurrent('User:login')) {
            $this->redirect('Project:summary');
            exit;
        }

        // Actual logged User (Entity)
        if ($this->netteUser->getIdentity()) {
            $userId = $this->netteUser->getIdentity()->data['userEntity']->getId();
            $this->activeUser = $this->userRepository->findOneById($userId);
        }

        // BreadCrumb
        $this->breadCrumb[] = new BreadCrumbItem('Contactio', NULL);

        parent::startup();
    }

    /**
     * @throws Nette\Application\AbortException
     */
    public function handleLogout() : void
    {
        $this->user->logout(TRUE);
        $this->notificationFlash(Notification::SUCCESS, 'Odhlášení', 'Odhlášení proběhlo v pořádku.', 3000);
        $this->redirect('User:login');
    }

    /**
     * @return AssetLoader
     * @throws Nette\Utils\JsonException
     */
    protected function createComponentAssetLoader() : AssetLoader
    {
        return new AssetLoader;
    }

    /**
     * @return Navbar
     */
    protected function createComponentNavbar() : Navbar
    {
        return new Navbar;
    }

    /**
     * @return BreadCrumb
     */
    protected function createComponentBreadCrumb() : BreadCrumb
    {
        return $this->IBreadCrumb->create($this->breadCrumb);
    }
}
