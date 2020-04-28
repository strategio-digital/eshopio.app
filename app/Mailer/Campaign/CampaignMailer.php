<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Mailer\Campaign;

use App\Database\Entity\Contact;
use App\Database\Entity\EmailCampaign;
use App\Database\Entity\SentEmail;
use App\Database\Entity\User;
use App\Database\Manager\SmtpSettingManager;
use App\Mailer\AbstractBaseMailer;
use Latte\Engine;
use Nette\Application\LinkGenerator;
use Symfony\Component\DomCrawler\Crawler;
use Tracy\Debugger;

class CampaignMailer extends AbstractBaseMailer
{
    /**
     * Message templates
     */
    const
        TEMPLATE_PATH_OUTER_HTML = __DIR__ . '/templates/@layout-html.latte',
        TEMPLATE_PATH_INNER_HTML_TINYMCE = __DIR__ . '/templates/body-html/tinymce.latte';

    /**
     * @var Engine
     */
    protected Engine $latte;

    /**
     * @var LinkGenerator
     */
    protected LinkGenerator $linkGenerator;

    /**
     * CampaignMailer constructor.
     * @param SmtpSettingManager $smtpSettingManager
     * @param LinkGenerator $linkGenerator
     */
    public function __construct(SmtpSettingManager $smtpSettingManager, LinkGenerator $linkGenerator)
    {
        parent::__construct($smtpSettingManager);
        $this->linkGenerator = $linkGenerator;
        $this->latte = new Engine;
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @param SentEmail $sentEmail
     * @return string
     * @throws \Nette\Application\UI\InvalidLinkException
     */
    public function createHtmlMessage(EmailCampaign $emailCampaign, SentEmail $sentEmail) : string
    {
        $linkParams = [
            'emailCampaignId' => $emailCampaign->getId(),
            'sentEmailId' => $sentEmail->getId(),
            'emailCampaignSecretKey' => $emailCampaign->getSecretKey(),
            'sentEmailSecretKey' => $sentEmail->getSecretKey()
        ];

        $linkOpenRate = $this->linkGenerator->link('Analytics:openRate', $linkParams);

        $params = [
            'emailCampaign' => $emailCampaign,
            'sentEmail' => $sentEmail,
            'linkOpenRate' => $linkOpenRate,
        ];

        $bodyHtml = $this->latte->renderToString(self::TEMPLATE_PATH_INNER_HTML_TINYMCE, $params);

        $crawler = new Crawler($bodyHtml);

        $crawler->filter('a')->each(function (Crawler $node) use ($linkParams) {
            $href = $node->attr('href');
            if ($href) {
                $clickRateLink = $this->linkGenerator->link('Analytics:clickRate', $linkParams + ['targetUrl' => $href]);
                $node->getNode(0)->setAttribute('href', $clickRateLink);
            }
        });

        $body = $crawler->filter('body')->html();
        $html = $this->latte->renderToString(self::TEMPLATE_PATH_OUTER_HTML, $params + ['body' => $body]);

        return $html;
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @param SentEmail $sentEmail
     * @throws \Exception
     */
    public function send(EmailCampaign $emailCampaign, SentEmail $sentEmail) : void
    {
        $html = $this->createHtmlMessage($emailCampaign, $sentEmail);

        $this->message->addTo($sentEmail->getContact()->getEmail());
        $this->message->setSubject($emailCampaign->getSubject());
        $this->message->setHtmlBody($html);
        //$this->message->setPriority(1);

        $this->smtpMailer->send($this->message);
        $this->smtpSettingManager->increaseLimits($this->smtpSetting);
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @param string $email
     * @throws \Exception
     */
    public function sendTest(EmailCampaign $emailCampaign, string $email) : void
    {
        $contact = new Contact;
        $contact->setEmail($email);

        $sentEmail = new SentEmail;
        $sentEmail->setId(0);
        $sentEmail->setContact($contact);
        $sentEmail->setSecretKey();

        $this->send($emailCampaign, $sentEmail);
    }
}
