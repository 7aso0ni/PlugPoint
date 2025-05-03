<?php

namespace Model;

use PDO;

require_once 'BaseModel.php';

class ChargePointModel extends BaseModel
{
    const TABLE_NAME = "ChargePoints";

    /**
     * Get all charge points
     */
    public function getAllChargePoints(): array
    {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->get(PDO::FETCH_ASSOC);
    }

    /**
     * Get charge point by ID
     */
    public function getChargePointById($id)
    {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->where('id', '=', $id)
            ->first();
    }

    /**
     * Get charge points with owner information and pagination
     */
    public function getChargePointsWithOwners($limit = 8, $offset = 0): array
    {
        return $this->table(self::TABLE_NAME . ' AS cp')
            ->select('cp.*, u.name as owner_name')
            ->join('Users AS u', 'cp.owner_id', '=', 'u.id')
            ->where('cp.availability', '=', 1)
            ->limit($limit, $offset)
            ->get();
    }

    /**
     * Get count of available charge points
     */
    public function getAvailableChargePointsCount(): int
    {
        $result = $this->table(self::TABLE_NAME)
            ->select('COUNT(*) as count')
            ->where('availability', '=', 1)
            ->first();

        return $result['count'] ?? 0;
    }

    /**
     * Get filtered charge points for listings page
     */
    public function getFilteredChargePoints($search = '', $maxPrice = 0.50, $available = null, $limit = 8, $offset = 0): array
    {
        $query = $this->table(self::TABLE_NAME . ' AS cp')
            ->select('cp.*, u.name as owner_name')
            ->join('Users AS u', 'cp.owner_id', '=', 'u.id');

        // Apply filters
        if (!empty($search)) {
            $query->where('cp.address', 'LIKE', "%$search%");
        }

        if ($maxPrice > 0) {
            $query->where('cp.price_per_kWh', '<=', $maxPrice);
        }

        if ($available !== null) {
            $query->where('cp.availability', '=', $available);
        }

        // Apply limit and offset for pagination
        return $query->limit($limit, $offset)->get();
    }

    /**
     * Get count of filtered charge points
     */
    public function getFilteredChargePointsCount($search = '', $maxPrice = 0.50, $available = null): int
    {
        $query = $this->table(self::TABLE_NAME)
            ->select('COUNT(*) as count');

        // Apply filters
        if (!empty($search)) {
            $query->where('address', 'LIKE', "%$search%");
        }

        if ($maxPrice > 0) {
            $query->where('price_per_kWh', '<=', $maxPrice);
        }

        if ($available !== null) {
            $query->where('availability', '=', $available);
        }

        // Get count
        $result = $query->first();
        return $result['count'] ?? 0;
    }

    /**
     * Get charge point with owner details
     */
    public function getChargePointWithOwner($id)
    {
        return $this->table(self::TABLE_NAME . ' AS cp')
            ->select('cp.*, u.name as owner_name, u.email as owner_email')
            ->join('Users AS u', 'cp.owner_id', '=', 'u.id')
            ->where('cp.id', '=', $id)
            ->first();
    }

    /**
     * Get charge points owned by a specific user
     */
    public function getChargePointsByOwner($ownerId): array
    {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->where('owner_id', '=', $ownerId)
            ->get();
    }

    /**
     * Create a new charge point
     */
    public function createChargePoint($data)
    {
        return $this->table(self::TABLE_NAME)
            ->insert($data);
    }

    /**
     * Update a charge point
     */
    public function updateChargePoint($id, $data)
    {
        return $this->table(self::TABLE_NAME)
            ->where('id', '=', $id)
            ->update($data);
    }

    /**
     * Update availability status
     */
    public function updateAvailability($id, $availability)
    {
        return $this->table(self::TABLE_NAME)
            ->where('id', '=', $id)
            ->update(['availability' => $availability]);
    }

    /**
     * Check if a charge point exists with the given ID
     */
    public function chargerExists($id): bool
    {
        return $this->table(self::TABLE_NAME)
            ->select('id')
            ->where('id', '=', $id)
            ->exists();
    }

    /**
     * Search charge points by address or city
     */
    public function searchByLocation($searchTerm, $limit = 10): array
    {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->where('address', 'LIKE', "%$searchTerm%")
            ->limit($limit)
            ->get();
    }

    /**
     * Enhanced version of getNearbyChargePoints that uses the ORM patterns
     * and calculates actual distance using Haversine formula
     * 
     * @param float $latitude Central latitude
     * @param float $longitude Central longitude
     * @param float $radius Search radius in kilometers
     * @param int $limit Maximum number of results
     * @return array Array of charge points with distance information
     */
    public function getNearbyChargePoints($latitude, $longitude, $radius = 10, $limit = 5): array
    {
        // This will require a different approach with the ORM

        // 1. First, get all charge points
        $allPoints = $this->table(self::TABLE_NAME . ' AS cp')
            ->select('cp.*, u.name as owner_name')
            ->join('Users AS u', 'cp.owner_id', '=', 'u.id')
            ->get();

        // 2. Calculate distance for each and filter
        $nearbyPoints = [];

        foreach ($allPoints as $point) {
            $distance = $this->calculateHaversineDistance(
                $latitude,
                $longitude,
                $point['latitude'],
                $point['longitude']
            );

            // Only include points within the radius
            if ($distance <= $radius) {
                $point['distance'] = $distance;
                $nearbyPoints[] = $point;
            }
        }

        // 3. Sort by distance
        usort($nearbyPoints, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        // 4. Apply limit
        return array_slice($nearbyPoints, 0, $limit);
    }

    /**
     * Calculate distance between two points using Haversine formula
     * 
     * @param float $lat1 First point latitude
     * @param float $lon1 First point longitude
     * @param float $lat2 Second point latitude
     * @param float $lon2 Second point longitude
     * @return float Distance in kilometers
     */
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        // Earth's radius in kilometers
        $earthRadius = 6371;

        // Convert degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formula
        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($lat1) * cos($lat2) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

}