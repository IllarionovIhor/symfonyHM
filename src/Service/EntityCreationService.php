<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Driver;
use App\Entity\DriverCar;
use App\Entity\Car;
use App\Entity\Tier;
use App\Entity\FuelUsageType;
use App\Entity\Ride;
use App\Entity\Review;
use App\Entity\Report;
use App\Entity\ReportType;

class EntityCreationService
{
    public function createClient(string $username, string $password): Client
    {
        $client = new Client();
        $client->setUsername($username);
        $client->setPassword($password);
        return $client;
    }

    public function createDriver(string $username, string $password, string $licenseNumber): Driver
    {
        $driver = new Driver();
        $driver->setUsername($username);
        $driver->setPassword($password);
        $driver->setLicenseNumber($licenseNumber);
        return $driver;
    }

    public function createDriverCar(Driver $driver, Car $car, \DateTimeInterface $timeStart, \DateTimeInterface $timeEnd): DriverCar
    {
        $driverCar = new DriverCar();
        $driverCar->setDriver($driver);
        $driverCar->setCar($car);
        $driverCar->setTimeStart($timeStart);
        $driverCar->setTimeEnd($timeEnd);
        return $driverCar;
    }

    public function createCar(string $name, FuelUsageType $fuelUsageType, Tier $tier, string $plateNumber): Car
    {
        $car = new Car();
        $car->setName($name);
        $car->setFuelUsageType($fuelUsageType);
        $car->setTier($tier);
        $car->setPlateNumber($plateNumber);
        return $car;
    }

    public function createTier(string $name, float $priceModifier): Tier
    {
        $tier = new Tier();
        $tier->setName($name);
        $tier->setPriceModifier($priceModifier);
        return $tier;
    }

    public function createFuelUsageType(string $name, int $co2PerKm): FuelUsageType
    {
        $fuelUsageType = new FuelUsageType();
        $fuelUsageType->setName($name);
        $fuelUsageType->setCo2PerKm($co2PerKm);
        return $fuelUsageType;
    }

    public function createRide(string $destination, string $from, ?Review $review, Car $car, Driver $driver, int $totalCost): Ride
    {
        $ride = new Ride();
        $ride->setDestination($destination);
        $ride->setFrom($from);
        $ride->setReview($review);
        $ride->setCar($car);
        $ride->setDriver($driver);
        $ride->setTotalCost($totalCost);
        return $ride;
    }

    public function createReview(int $rating, int $comment, Client $client): Review
    {
        $review = new Review();
        $review->setRating($rating);
        $review->setComment($comment);
        $review->setClient($client);
        return $review;
    }

    public function createReport(ReportType $reportType, int $comment, Client $client): Report
    {
        $report = new Report();
        $report->setReportType($reportType);
        $report->setComment($comment);
        $report->setClient($client);
        return $report;
    }

    public function createReportType(string $name, string $description): ReportType
    {
        $reportType = new ReportType();
        $reportType->setName($name);
        $reportType->setDescription($description);
        return $reportType;
    }
}
