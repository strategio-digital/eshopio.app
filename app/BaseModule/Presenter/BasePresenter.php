<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\BaseModule\Presenter;

use App\BaseModule\Component\AssetLoader\AssetLoader;
use App\BaseModule\Component\Navbar\Navbar;
use App\BaseModule\Component\Notification\Entity\Notification;
use App\UserModule\Database\Entity\User;
use App\UserModule\Database\Repository\UserRepository;
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
     * @var User|null
     */
    public ?User $activeUser = NULL;

    /**
     * Startup
     */
    public function startup() : void
    {
        parent::startup();

        if ($this->netteUser->isLoggedIn()) {
            $this->activeUser = $this->userRepository->findOneById($this->netteUser->getId());
        }
    }

    /**
     * @throws Nette\Application\AbortException
     */
    public function handleLogout() : void
    {
        if ($this->netteUser->isLoggedIn()) {
            $this->notificationFlash(Notification::SUCCESS, 'Odhlášení', 'Právě jste se odhlásili.');
            $this->netteUser->logout(TRUE);
            $this->presenter->redirect('this');
        }
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
}
