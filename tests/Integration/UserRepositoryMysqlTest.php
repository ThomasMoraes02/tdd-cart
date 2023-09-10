<?php 
namespace Cart\Tests\Integration;

use Cart\Infra\EncoderArgon2ID;
use Cart\Infra\Factories\UserFactory;
use Cart\Infra\Persistance\UserRepositoryMysql;
use Cart\Model\Repository\UserRepository;
use Cart\Model\ValueObjects\Phone;
use PDO;
use PHPUnit\Framework\TestCase;

class UserRepositoryMysqlTest extends TestCase
{
    private static PDO $pdo;

    private UserRepository $userRepository;

    private UserFactory $userFactory;

    /**
     * É executando antes de iniciar a bateria de testes
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$pdo = new PDO('sqlite:memory:');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, name TEXT, email TEXT, password LONGTEXT, phone_area INTEGER, phone_number INTEGER);");   
    }

    /**
     * Iniciado a cada teste
     *
     * @return void
     */
    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
        $this->userFactory = new UserFactory(new EncoderArgon2ID());
        $this->userRepository = new UserRepositoryMysql(self::$pdo, $this->userFactory);
    }

    /**
     * Criando um usuário no repositório
     *
     * @return void
     */
    public function testCreateUserInRepository(): void
    {
        $user = $this->userFactory->create('Thomas Moraes', 'thomas@gmail.com', '123456');

        $this->userRepository->save($user);

        $this->userRepository->findByEmail($user->getEmail());

        self::assertEquals('Thomas Moraes', $user->getName());
        self::assertTrue($user->checkPassword('123456'));
        self::assertFalse($user->checkPassword('1234567'));
        self::assertNull($user->getPhone());
    }

    public function testCreateUserWithPhoneInRepository()
    {
        $user = $this->userFactory->create('Bolt Moraes', 'bolt@gmail.com', '654321');
        $user->addPhone(new Phone('11', '987654321'));

        $this->userRepository->save($user);
        $this->userRepository->findByEmail($user->getEmail());

        self::assertEquals('Bolt Moraes', $user->getName());
        self::assertEquals('987654321', $user->getPhone()->getNumber());
        self::assertEquals('11', $user->getPhone()->getAreaCode());
        self::assertEquals('11987654321', $user->getPhone());
    }

    /**
     * Excutado ao final de cada teste
     *
     * @return void
     */
    protected function tearDown(): void
    {
        self::$pdo->rollBack();   
    }
}