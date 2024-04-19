<?php
declare(strict_types=1);

//==============Не редактировать
final class DataBase
{
    private $isConnected = false;   // убрал тип так как пишу тестовое на 7.2.34

    public function connect(): string   // здесь поменял тип данных для вывода, так как ретёрн возвращает стринг а не булеан
    {
        sleep(1);
        $this->isConnected = true;
        return 'connected';
    }

    public function random()
    {
        $this->isConnected = rand(0, 3) ? $this->isConnected : false;
    }

    public function fetch($id): string
    {
        $this->random();
        if (!$this->isConnected) {
            throw new Exception('No connection');
        }
        usleep(100000);
        return 'fetched - ' . $id;
    }

    public function insert($data): string
    {
        $this->random();
        if (!$this->isConnected) {
            throw new Exception('No connection');
        }
        usleep(900000);
        return 'inserted - ' . $data;
    }


    public function batchInsert($data): string
    {
        $this->random();
        if (!$this->isConnected) {
            throw new Exception('No connection');
        }
        usleep(900000);
        return 'batch inserted';
    }
}
//==============

class DataBaseHelper
{
    private $_db;   // не указал тип данных, так как сейчас на 7.2

    public function connectAndFetch($id): string
    {
        do {
            try {
                $result = $this->getDb()->fetch($id);
            } catch (Exception $e) {
                // сюда можно добавить запись в log при дисконекте ($e->getMessage())
                $this->getDb()->connect();
            }
        } while (!isset($result));
        return $result;
    }

    public function connectAndInsert($id): string
    {
        do {
            try {
                $result = $this->getDb()->insert($id);
            } catch (Exception $e) {
                // сюда можно добавить запись в log при дисконекте ($e->getMessage())
                $this->getDb()->connect();
            }
        } while (!isset($result));
        return $result;
    }

    public function connectAndBatchInsert(array $data): string
    {
        do {
            try {
                $result = $this->getDb()->batchInsert($data);
            } catch (Exception $e) {
                // сюда можно добавить запись в log при дисконекте ($e->getMessage())
                $this->getDb()->connect();
            }
        } while (!isset($result));
        return $result;
    }

    // вынес в отдельный метод чтоб не пладить объекты
    public function getDb(): DataBase
    {
        if ($this->_db === null) {
            $this->_db = new DataBase();
            // первый коннект выполняем при первом обращении к объекту БД
            $this->_db->connect();
        }
        return $this->_db;
    }
}

function step1($dataToFetch): void
{
    $dataBaseHelper = new DataBaseHelper();
    // вынес в переменную чтоб не считать каждый цикл количество элементов
    $n = count($dataToFetch);

    // фикс старта по фор
    for ($i = 0; $i < $n; $i++) {
        print($dataBaseHelper->connectAndFetch($dataToFetch[$i]));
        print(PHP_EOL);
    }
}

function step2($dataToInsert): void
{
    $dataBaseHelper = new DataBaseHelper();
    // вынес в переменную чтоб не считать каждый цикл количество элементов
    $n = count($dataToInsert);

    // фикс выхода за пределы массива
    for ($i = 0; $i < $n; $i++) {
        // тут бы стоит connectAndInsert на connectAndBatchInsert, так как он будет эффективнее, но в этом случае получим другой ответ в консоли
        print($dataBaseHelper->connectAndInsert($dataToInsert[$i]));
        print(PHP_EOL);
    }
}

//==============Не редактировать
$dataToFetch = [1, 2, 3, 4, 5, 6];
$dataToInsert = [7, 8, 9, 10, 11, 12];

step1($dataToFetch);
step2($dataToInsert);
print("Success");
//==============
// украшательство )) чтоб было как на скрине ))
print(PHP_EOL);
