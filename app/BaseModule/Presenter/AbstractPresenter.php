<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\BaseModule\Presenter;

use App\BaseModule\Component\AssetLoader\AssetLoader;
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
abstract class AbstractPresenter extends Nette\Application\UI\Presenter
{
    use TAbstractPresenter;

    /**
     * @var User|null
     */
    public ?User $activeUser = NULL;

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
     * @var array<Notification>
     */
    protected array $notifications = [];

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
     */
    protected function createComponentAssetLoader() : AssetLoader
    {
        return new AssetLoader;
    }
}