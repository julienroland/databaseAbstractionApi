<?php namespace Burger\Storage\Factory\Adapter;

use Burger\Storage\Contract\StorageInterface;
use Burger\Storage\Exception\StorageNotFoundException;
use Burger\Storage\Exception\UnableToSaveDataStorageException;
use Ramsey\Uuid\Uuid;

class DoctrineAdapter implements StorageInterface
{
    const CONTENTKEY = 'content';
    const REFERENCEKEY = 'reference';
    protected $database;
    protected $connection;
    protected $table;


    /**
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->database = $this->getDatabase($config);
        $this->table = $config['slot'];
        $this->createShema($this->table);
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return $this->database->isConnected();
    }

    /**
     * @param $data
     * @param $reference
     * @return boolean
     */
    public function add($data, $reference = null)
    {
        if (empty($reference)) {
            $reference = $this->generateReference();
        }
        $values = [self::CONTENTKEY => ':content', self::REFERENCEKEY => ':reference'];
        $params = [':content' => $data, ':reference' => $reference];
        $query = $this->database->createQueryBuilder()
            ->insert($this->table)
            ->values($values)
            ->setParameters($params);
        if ($query->execute() > 0) {
            return $reference;
        }
        throw new UnableToSaveDataStorageException($query->getSql());
    }

    /**
     * @param $reference
     * @param $data
     * @return boolean
     */
    public function update($reference, $data)
    {
        try {
            $referenceKey = self::REFERENCEKEY;
            $query = $this->database->createQueryBuilder()
                ->update($this->table)
                ->where("{$referenceKey} = :reference")
                ->setParameter(':reference', $reference)
                ->set(self::CONTENTKEY, ':content')
                ->setParameter(':content', $data);

            return $query->execute() >= 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $reference
     * @return boolean
     */
    public function has($reference)
    {
        try {
            $referenceKey = self::REFERENCEKEY;
            $query = $this->database->createQueryBuilder()
                ->select("COUNT({$referenceKey}) as number")
                ->from($this->table)
                ->where("{$referenceKey} = :reference")
                ->setParameter(':reference', $reference);
            if ($query->execute()->fetch()['number'] > 0) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * @param $content
     * @return boolean
     */
    public function contains($content)
    {
        $contentKey = self::CONTENTKEY;
        try {
            $query = $this->database->createQueryBuilder()
                ->select("COUNT({$contentKey}) as number")
                ->from($this->table)
                ->where("{$contentKey} = :content")
                ->setParameter(':content', $content);
            if ($query->execute()->fetch()['number'] > 0) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $reference
     * @return string
     */
    public function get($reference)
    {
        $referenceKey = self::REFERENCEKEY;
        $query = $this->database->createQueryBuilder()
            ->select(self::CONTENTKEY)
            ->from($this->table)
            ->where("{$referenceKey} = :reference")
            ->setParameter(':reference', $reference)
            ->setMaxResults(1);

        $result = $query->execute()->fetch();
        if (!$result) {
            throw new StorageNotFoundException($reference);
        }

        return $result['content'];
    }

    /**
     * @return string
     */
    public function getFirst()
    {
        $query = $this->database->createQueryBuilder()
            ->select(self::CONTENTKEY)
            ->from($this->table)
            ->setMaxResults(1);

        return $query->execute()->fetch()[self::CONTENTKEY];
    }

    /**
     * @param $reference
     * @return boolean
     */
    public function delete($reference)
    {
        if ($this->has($reference)) {
            $contentKey = self::CONTENTKEY;
            $referenceKey = self::REFERENCEKEY;
            $query = $this->database->createQueryBuilder()
                ->delete($this->table)
                ->where("{$referenceKey} = :reference")
                ->setParameter(':reference', $reference);

            return $query->execute() >= 0;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $query = $this->database->createQueryBuilder()
            ->select(self::REFERENCEKEY, self::CONTENTKEY)
            ->from($this->table);

        $contentKey = self::CONTENTKEY;
        $referenceKey = self::REFERENCEKEY;

        return array_map(function ($item) use ($referenceKey, $contentKey) {
            return ['reference' => $item[$referenceKey], 'content' => $item[$contentKey]];
        }, $query->execute()->fetchAll());
    }


    /**
     * @return bool
     */
    public function clearAll()
    {
        $query = $this->database->createQueryBuilder()
            ->delete($this->table);

        return $query->execute() > 0;
    }


    /**
     * @param $tableName
     */
    private function createShema($tableName)
    {
        $schema = new \Doctrine\DBAL\Schema\Schema();
        $schemaManager = $this->database->getSchemaManager();
        if (!$schemaManager->tablesExist($tableName)) {
            $referenceKey = self::REFERENCEKEY;
            $table = $schema->createTable($tableName);
            $reference = $table->addColumn("{$referenceKey}", "string");
            $table->setPrimaryKey(array("{$referenceKey}"));
            $table->addUniqueIndex(array("{$referenceKey}"));
            $table->addColumn(self::CONTENTKEY, "text");
            $sql = $schema->toSql($this->database->getDatabasePlatform());
            foreach ($sql as $query) {
                $this->database->executeQuery($query);
            }
        }
    }

    /**
     * @param array $config
     * @return \Doctrine\DBAL\Connection
     */
    private function getDatabase(array $config)
    {
        $connectionParams = $config;

        return $this->getConnection($connectionParams);
    }

    /**
     * @param $connectionParams
     * @return \Doctrine\DBAL\Connection
     */
    private function getConnection($connectionParams)
    {
        $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);

        return $connection;
    }

    /**
     * @return string
     */
    private function generateReference()
    {
        $uuid = Uuid::uuid4();

        return $uuid->toString();
    }
}
