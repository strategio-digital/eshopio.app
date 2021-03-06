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
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Security\User as NetteUser;
use stdClass;

/**
 * Class BasePresenter
 * @package App\Presenter
 * @property-read Template|stdClass $template
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
    public array $notifications = [];

    /**
     * Startup
     */
    public function startup() : void
    {
        parent::startup();

        $this->setLayout(__DIR__ . '/templates/@backend.latte');

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