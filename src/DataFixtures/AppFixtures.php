<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Framework;
use App\Entity\Level;
use App\Entity\Program;
use App\Entity\Ressource;
use App\Entity\TopicFramework;
use App\Entity\TopicProgrammingLanguage;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager) :void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 70; $i++) {
            $user = new User();
            $login = $faker->userName;
            $user->setActive($faker->boolean(75))
                ->setEmail($faker->email)
                ->setLogin($login)
                ->setPassword($this->encoder->encodePassword(
                    $user,
                    'pass_' . $login
                ))
                ->setProfilPic($faker->imageUrl(150, 150));
            $manager->persist($user);
        }


        $authors = ['Fabien Potencier', 'Damien Terro', 'Maxime Renaud'];
        $websites = ['https://symfony.com/', 'https://Damien Terro.com', 'https://Maxime Renaud.com'];
        $levels = ['Débutant', 'Intermédiaire', 'Confirmé'];
        $programs = ['PHP', 'JAVASCRIPT', 'JAVA'];

        for($i = 0; $i < 3; $i++) {
          $author = new Author();
          $author->setName($authors[$i])
                 ->setWebsite($websites[$i]);
          $manager->persist($author);

          $level = new Level();
          $level->setName($levels[$i]);
          $manager->persist($level);

          $program = new Program();
          $program->setName($programs[$i]);
          $manager->persist($program);

          if($i === 0){
              $this->getDataPhp($manager, $program, $author, $level);
          }

          if($i === 1){
              $this->getDataJavascript($manager, $program, $author, $level);
          }

          if($i === 2){
              $this->getDataJava($manager, $program, $author, $level);
          }
      }
    $manager->flush();
  }

    /**
     * @param ObjectManager $manager
     * @param Program $program
     * @param Author $author
     * @param Level $level
     */
    public function getDataPhp(ObjectManager $manager, Program $program, Author $author, Level $level): void
    {
        $symfony = $this->getFramework(
            $manager,
            $program,
            'Symfony',
            'https://symfony.com/doc/current/index.html'
        );

        $phpTopic = $this->getProgrammingTopic($manager, $program);

        $symfonyTopic = $this->getFrameworkTopic($manager, $symfony);

        $this->getResource(
            $manager,
            $author,
            $level,
            $phpTopic,
            'fr',
            'Découvrez les tableaux en PHP',
            'https://symfony.com/doc/current/index.html'
        );

        $this->getResource(
            $manager,
            $author,
            $level,
            $symfonyTopic,
            'fr',
            'Découvrez Symfony',
            'https://symfony.com/doc/current/index.html'
        );
    }

    /**
     * @param ObjectManager $manager
     * @param Program $program
     * @param Author $author
     * @param Level $level
     */
    public function getDataJavascript(ObjectManager $manager, Program $program, Author $author, Level $level): void
    {
        $react = $this->getFramework(
            $manager,
            $program,
            'React',
            'https://react.com/doc/current/index.html'
        );

        $javascriptTopic = $this->getProgrammingTopic($manager, $program);

        $reactTopic = $this->getFrameworkTopic($manager, $react);

        $this->getResource(
            $manager,
            $author,
            $level,
            $javascriptTopic,
            'fr',
            'Découvrez les tableaux en javascript',
            'https://react.com/doc/current/index.html'
        );

        $this->getResource(
            $manager,
            $author,
            $level,
            $reactTopic,
            'fr',
            'Découvrez React',
            'https://react.com/doc/current/index.html'
        );
    }

    /**
     * @param ObjectManager $manager
     * @param Program $program
     * @param Author $author
     * @param Level $level
     */
    public function getDataJava(ObjectManager $manager, Program $program, Author $author, Level $level): void
    {
        $spring = $this->getFramework(
            $manager,
            $program,
            'Spring',
            'https://spring.com/doc/current/index.html'
        );

        $javaTopic = $this->getProgrammingTopic($manager, $program);

        $springTopic = $this->getFrameworkTopic($manager, $spring);

        $this->getResource(
            $manager,
            $author,
            $level,
            $javaTopic,
            'fr',
            'Découvrez les tableaux en Java',
            'https://java.com/doc/current/index.html'
        );

        $this->getResource(
            $manager,
            $author,
            $level,
            $springTopic,
            'fr',
            'Decouvrez Spring',
            'https://spring.com/doc/current/index.html'
        );
    }

    /**
     * @param ObjectManager $manager
     * @param Program $program
     * @return TopicProgrammingLanguage
     */
    public function getProgrammingTopic(ObjectManager $manager, Program $program): TopicProgrammingLanguage
    {
        $topic = new TopicProgrammingLanguage();
        $topic->setProgrammingLanguage($program);
        $manager->persist($topic);
        return $topic;
    }

    /**
     * @param ObjectManager $manager
     * @param Framework $framework
     * @return TopicFramework
     */
    public function getFrameworkTopic(ObjectManager $manager, Framework $framework): TopicFramework
    {
        $frameworkTopic = new TopicFramework();
        $frameworkTopic->setFramework($framework);
        $manager->persist($frameworkTopic);
        return $frameworkTopic;
    }

    /**
     * @param ObjectManager $manager
     * @param Program $program
     * @param $name
     * @param $url
     * @return Framework
     */
    public function getFramework(ObjectManager $manager, Program $program, $name, $url): Framework
    {
        $framework = new Framework();
        $framework->setName($name)
            ->setDocUrl($url)
            ->setProgram($program);
        $manager->persist($framework);
        return $framework;
    }

    /**
     * @param ObjectManager $manager
     * @param Author $author
     * @param Level $level
     * @param TopicProgrammingLanguage $phpTopic
     * @param $language
     * @param $name
     * @param $url
     */
    public function getResource(
        ObjectManager $manager,
        Author $author,
        Level $level,
        TopicProgrammingLanguage $phpTopic,
        $language,
        $name,
        $url
    ): void
    {
        $resource = new Ressource();
        $resource->setAuthor($author)
            ->setLanguage($language)
            ->setLevel($level)
            ->setName($name)
            ->setUrl($url)
            ->setTopic($phpTopic);
        $manager->persist($resource);
    }




}
