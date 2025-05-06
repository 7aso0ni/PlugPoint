<?php

namespace Model;

use PDO;
use DateTime;
use DateInterval;

require_once 'BaseModel.php';

class ChargePointModel extends BaseModel
{
    const TABLE_NAME = "ChargePoints";

    /** Count bookings in a given status */
    public function getBookingCountByStatus(string $status): int
    {
        $row = $this->query(
            "SELECT COUNT(*) AS total FROM bookings WHERE status = :status",
            [':status' => $status]
        )->fetch();

        return (int) $row['total'];
    }

    /** Latest N bookings with user & charge-point info */
    public function getRecentBookingsWithDetails(int $limit = 5): array
    {
        $limit = max(1, $limit);

        $sql = "
            SELECT b.*,
                   u.name AS user_name,
                   cp.address
            FROM   bookings b
            JOIN   users         u  ON u.id = b.user_id
            JOIN   charge_points cp ON cp.id = b.charge_point_id
            ORDER  BY b.created_at DESC
            LIMIT  $limit
        ";

        return $this->query($sql)->fetchAll();
    }

    /** Monthly stats for the past N months (oldest → newest) */
    public function getMonthlyStats(int $months = 6): array
    {
        $months = max(1, $months);
        $data = [];

        // Pre-fill with zero rows
        $cursor = new DateTime('first day of this month');
        for ($i = $months - 1; $i >= 0; $i--) {
            $key = $cursor->sub(new DateInterval("P{$i}M"))->format('Y-m');
            $data[$key] = ['bookings' => 0, 'revenue' => 0.0];
            $cursor->add(new DateInterval("P{$i}M"));
        }

        $sql = "
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym,
                   COUNT(*)                       AS bookings,
                   SUM(kwh * cp.price_per_kWh)    AS revenue
            FROM   bookings b
            JOIN   charge_points cp ON cp.id = b.charge_point_id
            WHERE  created_at >= DATE_SUB(
                       DATE_FORMAT(NOW(), '%Y-%m-01'),
                       INTERVAL :months MONTH
                   )
            GROUP  BY ym
        ";

        foreach ($this->query($sql, [':months' => $months])->fetchAll() as $row) {
            $data[$row['ym']] = [
                'bookings' => (int) $row['bookings'],
                'revenue' => (float) $row['revenue'],
            ];
        }

        ksort($data);
        return $data;
    }

    /**
     * Get all charge points
     * 
     * @return array All charge points
     */
    public function getAllChargePoints(): array
    {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->get(PDO::FETCH_ASSOC);
    }

    /**
     * Get charge point by ID
     * 
     * @param int $id Charge point ID
     * @return array|null Charge point or null if not found
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
     * 
     * @param int $limit Maximum number of results
     * @param int $offset Pagination offset
     * @return array Charge points with owner details
     */
    public function getAllChargePointsWithOwners($limit = 8, $offset = 0): array
    {
        return $this->table(self::TABLE_NAME . ' AS cp')
            ->select('cp.*, u.name as owner_name')
            ->join('Users AS u', 'cp.owner_id', '=', 'u.id')
            ->limit($limit, $offset)
            ->get();
    }

    /**
     * Get count of available charge points
     * 
     * @return int Number of available charge points
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
     * Get total number of charge points
     * 
     * @return int Total number of charge points
     */
    public function getTotalChargePoints(): int
    {
        $result = $this->table(self::TABLE_NAME)
            ->select('COUNT(*) as count')
            ->first();

        return $result['count'] ?? 0;
    }

    /**
     * Get filtered charge points for listings page
     * 
     * @param string $search Search term
     * @param float $maxPrice Maximum price filter
     * @param int|null $available Availability filter
     * @param int $limit Maximum results per page
     * @param int $offset Pagination offset
     * @return array Filtered charge points
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
     * 
     * @param string $search Search term
     * @param float $maxPrice Maximum price filter
     * @param int|null $available Availability filter
     * @return int Count of filtered charge points
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
     * 
     * @param int $id Charge point ID
     * @return array|null Charge point with owner details or null if not found
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
     * 
     * @param int $ownerId Owner user ID
     * @return array Charge points owned by the user
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
     * 
     * @param array $data Charge point data
     * @return mixed Result of insert operation
     */
    public function createChargePoint($data)
    {
        return $this->table(self::TABLE_NAME)
            ->insert($data);
    }

    /**
     * Update a charge point
     * 
     * @param int $id Charge point ID
     * @param array $data Updated charge point data
     * @return mixed Result of update operation
     */
    public function updateChargePoint($id, $data)
    {
        return $this->table(self::TABLE_NAME)
            ->where('id', '=', $id)
            ->update($data);
    }
public function getChargerById($id): ?array {
    return $this->table(self::TABLE_NAME)->select('*')->where('id', '=', $id)->first();
}

public function updateCharger($id, $address, $price, $availability, $image_url)
{
    return $this->table(self::TABLE_NAME)
        ->where('id', '=', $id)
        ->update([
            'address' => $address,
            'price_per_kWh' => $price,
            'availability' => $availability,
            'image_url' => $image_url,
        ]);
}



public function deleteCharger($id): void {
    $this->table(self::TABLE_NAME)->where('id', '=', $id)->delete();
}

    /**
     * Update availability status
     * 
     * @param int $id Charge point ID
     * @param int $availability New availability status
     * @return mixed Result of update operation
     */
    public function updateAvailability($id, $availability)
    {
        return $this->table(self::TABLE_NAME)
            ->where('id', '=', $id)
            ->update(['availability' => $availability]);
    }

    /**
     * Check if a charge point exists with the given ID
     * 
     * @param int $id Charge point ID
     * @return bool True if charge point exists
     */
    public function chargerExists($id): bool
    {
        return $this->table(self::TABLE_NAME)
            ->select('id')
            ->where('id', '=', $id)
            ->exists();
    }

    /**
     * Search charge points by address or location
     * 
     * @param string $searchTerm Search term
     * @param int $limit Maximum results
     * @return array Search results
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
     * Search charge points by address or other fields
     * 
     * @param string $search Search term
     * @param int $limit Maximum results per page
     * @param int $offset Pagination offset
     * @return array Search results
     */
    public function searchChargePoints($search, $limit = 10, $offset = 0): array
    {
        return $this->table(self::TABLE_NAME . ' AS cp')
            ->select('cp.*, u.name as owner_name')
            ->join('Users AS u', 'cp.owner_id', '=', 'u.id')
            ->whereOr(function ($q) use ($search) {
                $q->where('cp.address', 'LIKE', "%$search%")
                    ->where('u.name', 'LIKE', "%$search%");

                if (is_numeric($search)) {
                    $q->where('cp.id', '=', (int) $search);
                }
            })
            ->orderBy('cp.id', 'ASC')
            ->limit($limit, $offset)
            ->get();
    }

    /**
     * Get count of search results
     * 
     * @param string $search Search term
     * @return int Total count of search results
     */
    public function getTotalSearchResults($search): int
    {
        $result = $this->table(self::TABLE_NAME . ' AS cp')
            ->select('COUNT(cp.id) as count')
            ->join('Users AS u', 'cp.owner_id', '=', 'u.id')
            ->whereOr(function ($q) use ($search) {
                $q->where('cp.address', 'LIKE', "%$search%")
                    ->where('u.name', 'LIKE', "%$search%");

                if (is_numeric($search)) {
                    $q->where('cp.id', '=', (int) $search);
                }
            })
            ->first();

        return $result['count'] ?? 0;
    }

    /**
     * Delete a charge point
     * 
     * @param int $id Charge point ID
     * @return mixed Result of delete operation
     */
    public function deleteChargePoint($id)
    {
        return $this->table(self::TABLE_NAME)
            ->where('id', '=', $id)
            ->delete();
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

    /**
     * Enhanced version of getNearbyChargePoints that calculates distance using Haversine formula
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
}