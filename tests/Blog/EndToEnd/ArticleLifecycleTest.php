<?php

namespace App\Tests\Blog\EndToEnd;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

final class ArticleLifecycleTest extends PantherTestCase
{
    public function testCreatePublishAndDeleteAnArticleAndItsCategory(): void
    {
        $client = self::createPantherClient();
        $this->resetDatabase();

        $crawler = $client->request('GET', '/dashboard/blog/category/new');
        self::assertSelectorTextContains('h1', 'Ajouter une catégorie');
        $client->submit($crawler->selectButton('Enregistrer')->form([
            'category[name]' => 'Développement web',
            'category[slug]' => 'developpement-web',
            'category[description]' => 'Articles consacrés au développement web.',
        ]));
        $client->waitForElementToContain('body', 'La catégorie a été ajoutée.');
        self::assertSelectorTextContains('body', 'La catégorie a été ajoutée.');
        $client->getCrawler()->selectButton('Catégories')->click();
        $client->waitFor('dialog[open]');
        self::assertSelectorTextContains('[data-category-id]', 'Développement web');

        $client->request('GET', '/dashboard/blog/article/new');
        self::assertSelectorTextContains('h1', 'Ajouter un article');
        $client->waitFor('#article_title');
        $client->getWebDriver()->findElement(WebDriverBy::id('article_title'))->sendKeys('Créer un site rapide');
        $client->getWebDriver()->findElement(WebDriverBy::id('article_categoryName'))->sendKeys('Développement web');
        $client->getWebDriver()->findElement(WebDriverBy::id('article_summary'))->sendKeys('Les étapes essentielles pour concevoir un site web rapide, accessible et agréable à utiliser.');
        $client->getWebDriver()->executeScript(<<<'JS'
            const textarea = document.getElementById('article_content');
            textarea.value = '<p>Voici le contenu complet de notre article end-to-end.</p>';
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
            textarea.dispatchEvent(new Event('change', { bubbles: true }));
            JS);
        $image = realpath(__DIR__.'/../../../assets/images/about-me.webp');
        self::assertNotFalse($image, 'L’image utilisée par le test doit exister.');
        $client->getWebDriver()->findElement(WebDriverBy::id('article_coverImageFile'))->sendKeys($image);
        $saveButton = $client->getWebDriver()->findElement(WebDriverBy::xpath("//button[.//span[normalize-space()='Enregistrer']]"));
        $client->getWebDriver()->executeScript('arguments[0].scrollIntoView({block: "center"}); arguments[0].click();', [$saveButton]);

        $client->waitFor('[data-article-id]');
        self::assertSelectorTextContains('body', 'L’article a été ajouté.');
        self::assertSelectorTextContains('[data-article-id]', 'Créer un site rapide');
        self::assertSelectorTextContains('[data-article-id]', 'Brouillon');

        $this->clickArticleAction($client, 'Envoyer en relecture', 'En relecture');
        self::assertSelectorTextContains('[data-article-id]', 'En relecture');

        $this->clickArticleAction($client, 'Publier', 'Publié');
        self::assertSelectorTextContains('[data-article-id]', 'Publié');

        $client->request('GET', '/blog?q=Créer&category=developpement-web');
        self::assertSelectorTextContains('.blog-card', 'Créer un site rapide');

        $client->request('GET', '/blog/creer-un-site-rapide');
        self::assertSelectorTextContains('h1', 'Créer un site rapide');
        self::assertSelectorTextContains('body', 'Voici le contenu complet de notre article end-to-end.');
        self::assertSelectorExists('img');

        $client->request('GET', '/dashboard/blog');
        $this->clickArticleAction($client, 'Archiver', 'Archivé', true);
        self::assertSelectorTextContains('[data-article-id]', 'Archivé');

        $client->request('GET', '/blog/creer-un-site-rapide');
        self::assertSelectorTextContains('body', 'Cet article n’est pas disponible.');

        $client->request('GET', '/dashboard/blog');
        $this->clickCategoryAction($client, 'Supprimer');
        self::assertSelectorTextContains('body', 'La catégorie a été supprimée.');
        self::assertSelectorNotExists('[data-category-id]');
    }

    private function resetDatabase(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    private function clickArticleAction(Client $client, string $label, string $expectedStatus, bool $confirm = false): void
    {
        $client->getWebDriver()->executeScript("document.querySelector('[data-article-id] details').open = true;");
        $button = $client->getCrawler()->filter('[data-article-id]')->selectButton($label);
        $client->getWebDriver()->executeScript('arguments[0].click();', [$button->getElement(0)]);

        if ($confirm) {
            $client->getWebDriver()->switchTo()->alert()->accept();
        }

        $client->waitForElementToContain('[data-article-id]', $expectedStatus);
    }

    private function clickCategoryAction(Client $client, string $label): void
    {
        $client->getCrawler()->selectButton('Catégories')->click();
        $client->waitFor('dialog[open]');
        $client->getCrawler()->filter('[data-category-id]')->selectButton($label)->click();
        $client->getWebDriver()->switchTo()->alert()->accept();
        $client->waitForElementToContain('body', 'La catégorie a été supprimée.');
    }
}
