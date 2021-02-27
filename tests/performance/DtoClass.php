<?php

namespace DTO;

class DtoClass
{
    private string $name;
    private string $address;
    private int $count;
    private int $age;
    private int $price;

    // emulate not required fields

    private string $field1;
    private string $field2;
    private string $field3;

    private int $field4;
    private int $field5;


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        if ($count < 0 || $count > 100) {
            throw new \InvalidArgumentException('count');
        }
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age): void
    {
        if ($age < 0 || $age > 100) {
            throw new \InvalidArgumentException('count');
        }
        $this->age = $age;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price): void
    {
        if ($price < 100 || $price > 10000) {
            throw new \InvalidArgumentException('count');
        }
        $this->price = $price;
    }


    /**
     * @return string
     */
    public function getField1(): string
    {
        return $this->field1;
    }

    /**
     * @param string $field1
     */
    public function setField1(string $field1): void
    {
        $this->field1 = $field1;
    }

    /**
     * @return string
     */
    public function getField2(): string
    {
        return $this->field2;
    }

    /**
     * @param string $field2
     */
    public function setField2(string $field2): void
    {
        $this->field2 = $field2;
    }

    /**
     * @return string
     */
    public function getField3(): string
    {
        return $this->field3;
    }

    /**
     * @param string $field3
     */
    public function setField3(string $field3): void
    {
        $this->field3 = $field3;
    }

    /**
     * @return int
     */
    public function getField4(): int
    {
        return $this->field4;
    }

    /**
     * @param int $field4
     */
    public function setField4(int $field4): void
    {
        $this->field4 = $field4;
    }

    /**
     * @return int
     */
    public function getField5(): int
    {
        return $this->field5;
    }

    /**
     * @param int $field5
     */
    public function setField5(int $field5): void
    {
        $this->field5 = $field5;
    }
}


