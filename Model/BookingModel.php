<?php

namespace Model;

use PDO;

require_once 'BaseModel.php';

class BookingModel extends BaseModel
{
    private const TABLE = 'Bookings';

    /* ─────────────────────────── CORE BOOKING OPERATIONS ────────────────────────── */

    /**
     * Reserve a time slot (throws if overlapping) and return the new booking
     * 
     * @param array $data Booking data
     * @return array New booking data
     * @throws \RuntimeException If time slot is already booked
     */
    public function reserve(array $data): array
    {
        if (!$this->isSlotAvailable($data['charge_point_id'], $data['booking_date'], $data['due_date'])) {
            throw new \RuntimeException('Selected time slot is already booked.');
        }

        /* perform insert */
        $this->table(self::TABLE)->insert($data);

        /* fetch the auto-increment id from the PDO connection */
        $id = (int) $this->db->lastInsertId();

        return $this->getBookingById($id);
    }

    /**
     * Check if the [start,end) slot is free for this charge point
     * 
     * @param int $cpId Charge point ID
     * @param string $start Start datetime
     * @param string $end End datetime
     * @return bool True if slot is available
     */
    public function isSlotAvailable(int $cpId, string $start, string $end): bool
    {
        $row = $this->table(self::TABLE)
            ->select('id')
            ->where('charge_point_id', '=', $cpId)
            ->where('status', '!=', 'Canceled')
            ->where('status', '!=', 'Declined')
            // overlap: existing.start < new.end AND existing.end > new.start
            ->where('booking_date', '<', $end)
            ->where('due_date', '>', $start)
            ->first();

        return empty($row);
    }

    /**
     * Get booking by ID
     * 
     * @param int $id Booking ID
     * @return array|null Booking details or null if not found
     */
    public function getBookingById($id)
    {
        return $this->table(self::TABLE)
            ->select()
            ->where('id', '=', $id)
            ->first();
    }

    /**
     * Get booking with detailed information including user and charge point data
     * 
     * @param int $id Booking ID
     * @return array|null Booking with details or null if not found
     */
    public function getBookingWithDetails($id)
    {
        return $this->table(self::TABLE . ' AS b')
            ->select('b.*, u.name AS user_name, u.email AS user_email, u.phone AS user_phone,
                     cp.address, cp.price_per_kWh, cp.latitude, cp.longitude,
                     o.name AS owner_name, o.email AS owner_email')
            ->join('Users AS u', 'b.user_id', '=', 'u.id')
            ->join('ChargePoints AS cp', 'b.charge_point_id', '=', 'cp.id')
            ->join('Users AS o', 'cp.owner_id', '=', 'o.id')
            ->where('b.id', '=', $id)
            ->first();
    }

    /**
     * Get bookings for a specific user
     * 
     * @param int $userId User ID
     * @return array List of bookings
     */
    public function getBookingsByUser($userId): array
    {
        return $this->table(self::TABLE . ' AS b')
            ->select('b.id AS booking_id, b.booking_date, b.due_date, b.status, b.created_at,
                      cp.id AS charge_point_id, cp.address, cp.price_per_kWh, cp.image_url')
            ->join('ChargePoints AS cp', 'b.charge_point_id', '=', 'cp.id')
            ->where('b.user_id', '=', $userId)
            ->get();
    }

    /**
     * Update booking status
     * 
     * @param int $id Booking ID
     * @param string $status New status
     * @return int Number of affected rows
     */
    public function updateBookingStatus(int $id, string $status): int
    {
        $stmt = $this->table(self::TABLE)
            ->where('id', '=', $id)
            ->update(['status' => $status]);

        return $stmt->rowCount(); // number of rows affected
    }

    /**
     * Cancel a booking (shorthand for updateBookingStatus with 'Canceled')
     * 
     * @param int $id Booking ID
     * @return int Number of affected rows
     */
    public function cancelBooking(int $id): int
    {
        return $this->updateBookingStatus($id, 'Canceled');
    }

    /**
     * Add a note to a booking
     * 
     * @param int $bookingId Booking ID
     * @param string $note Note content
     * @param int $userId User ID who added the note
     * @return bool True if successful
     */
    public function addBookingNote($bookingId, $note, $userId): bool
    {
        // Assuming there's a BookingNotes table
        $result = $this->table('BookingNotes')->insert([
            'booking_id' => $bookingId,
            'note' => $note,
            'user_id' => $userId,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $result !== false;
    }

    /* ─────────────────────────── TIME SLOT MANAGEMENT ────────────────────────── */

    /**
     * Get booked slots for a charge point in a date range
     * 
     * @param int $cpId Charge point ID
     * @param string $start Start date
     * @param string $end End date
     * @return array List of booked slots
     */
    public function getBookedSlots($cpId, $start, $end): array
    {
        return $this->table(self::TABLE)
            ->select('booking_date, due_date, status')
            ->where('charge_point_id', '=', $cpId)
            ->where('booking_date', '<', $end . ' 23:59:59')
            ->where('due_date', '>', $start . ' 00:00:00')
            ->where('status', '!=', 'Canceled')
            ->where('status', '!=', 'Declined')
            ->get();
    }

    /* ─────────────────────────── ADMIN DASHBOARD & REPORTING ────────────────────────── */

    /**
     * Get total number of bookings with a specific status
     * 
     * @param string $status Status to count
     * @return int Number of bookings
     */
    public function getBookingCountByStatus($status): int
    {
        return $this->table(self::TABLE)
            ->where('status', '=', $status)
            ->count('id', 'count') ?? 0;
    }

    /**
     * Get estimated monthly revenue
     * 
     * @return float Estimated revenue for current month
     */
    public function getEstimatedMonthlyRevenue(): float
    {
        $currentMonth = date('Y-m-01');
        $nextMonth = date('Y-m-01', strtotime('+1 month'));

        $sql = "SELECT SUM(
            TIMESTAMPDIFF(HOUR, b.booking_date, b.due_date) * cp.price_per_kWh
        ) as revenue
        FROM " . self::TABLE . " b
        JOIN ChargePoints cp ON b.charge_point_id = cp.id
        WHERE b.status IN ('Completed', 'Confirmed')
        AND b.booking_date >= ?
        AND b.booking_date < ?";

        $stmt = $this->query($sql, [$currentMonth, $nextMonth]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (float) ($result['revenue'] ?? 0.0);
    }

    /**
     * Get recent bookings with details for dashboard
     * 
     * @param int $limit Maximum number of bookings
     * @return array List of recent bookings
     */
    public function getRecentBookingsWithDetails($limit = 5): array
    {
        return $this->table(self::TABLE . ' AS b')
            ->select('b.*, u.name AS user_name, cp.address, cp.price_per_kWh')
            ->join('Users AS u', 'b.user_id', '=', 'u.id')
            ->join('ChargePoints AS cp', 'b.charge_point_id', '=', 'cp.id')
            ->orderBy('b.booking_date', 'DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Get monthly statistics for bookings
     * 
     * @param int $months Number of months to include
     * @return array Monthly statistics (bookings and revenue)
     */
    public function getMonthlyStats($months = 6): array
    {
        $stats = [];
        $currentMonth = date('Y-m');

        for ($i = 0; $i < $months; $i++) {
            $monthDate = date('Y-m', strtotime("-$i months"));
            $monthStart = $monthDate . '-01 00:00:00';
            $monthEnd = date('Y-m-t 23:59:59', strtotime($monthStart));

            // Get total bookings for this month
            $bookingCount = $this->table(self::TABLE)
                ->where('booking_date', '>=', $monthStart)
                ->where('booking_date', '<=', $monthEnd)
                ->count('id', 'count') ?? 0;

            // Get revenue for this month - we need custom SQL for the calculation
            $revenueSql = "SELECT SUM(
                TIMESTAMPDIFF(HOUR, b.booking_date, b.due_date) * cp.price_per_kWh
            ) as revenue
            FROM " . self::TABLE . " b
            JOIN ChargePoints cp ON b.charge_point_id = cp.id
            WHERE b.status IN ('Completed', 'Confirmed')
            AND b.booking_date >= ?
            AND b.booking_date <= ?";

            $revenueStmt = $this->query($revenueSql, [$monthStart, $monthEnd]);
            $revenueResult = $revenueStmt->fetch(PDO::FETCH_ASSOC);

            $stats[] = [
                'month' => date('M Y', strtotime($monthDate)),
                'bookings' => $bookingCount,
                'revenue' => $revenueResult['revenue'] ?? 0
            ];
        }

        // Reverse to get chronological order
        return array_reverse($stats);
    }

    /**
     * Get top performing charge points
     * 
     * @param int $limit Maximum number of charge points
     * @return array Top charge points with booking statistics
     */
    public function getTopChargePoints($limit = 5): array
    {
        $pastMonth = date('Y-m-d H:i:s', strtotime('-1 month'));

        $sql = "SELECT 
            cp.id, 
            cp.address, 
            COUNT(b.id) as total_bookings,
            SUM(TIMESTAMPDIFF(HOUR, b.booking_date, b.due_date)) as total_hours,
            SUM(TIMESTAMPDIFF(HOUR, b.booking_date, b.due_date) * cp.price_per_kWh) as revenue
        FROM ChargePoints cp
        JOIN " . self::TABLE . " b ON cp.id = b.charge_point_id
        WHERE b.status IN ('Completed', 'Confirmed')
        AND b.booking_date >= ?
        GROUP BY cp.id, cp.address
        ORDER BY total_bookings DESC
        LIMIT ?";

        $stmt = $this->query($sql, [$pastMonth, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ─────────────────────────── ADMIN LISTING & FILTERING ────────────────────────── */

    /**
     * Get filtered bookings with details for admin listings
     * 
     * @param string $status Status filter (optional)
     * @param string $search Search term (optional)
     * @param int $limit Results per page
     * @param int $offset Pagination offset
     * @return array Filtered bookings
     */
    public function getFilteredBookingsWithDetails($status = '', $search = '', $limit = 10, $offset = 0): array
    {
        $query = $this->table(self::TABLE . ' AS b')
            ->select('b.*, u.name AS user_name, cp.address')
            ->join('Users AS u', 'b.user_id', '=', 'u.id')
            ->join('ChargePoints AS cp', 'b.charge_point_id', '=', 'cp.id');

        if (!empty($status)) {
            $query = $query->where('b.status', '=', $status);
        }

        if (!empty($search)) {
            $query = $query->whereOr(function ($q) use ($search) {
                $q->where('u.name', 'LIKE', "%$search%")
                    ->where('cp.address', 'LIKE', "%$search%");

                if (is_numeric($search)) {
                    $q->where('b.id', '=', (int) $search);
                }
            });
        }

        return $query->orderBy('b.booking_date', 'DESC')
            ->limit($limit, $offset)
            ->get();
    }

    /**
     * Get total count of filtered bookings for pagination
     * 
     * @param string $status Status filter (optional)
     * @param string $search Search term (optional)
     * @return int Total number of filtered bookings
     */
    public function getTotalFilteredBookings($status = '', $search = ''): int
    {
        $query = $this->table(self::TABLE . ' AS b')
            ->select('COUNT(b.id) as count')
            ->join('Users AS u', 'b.user_id', '=', 'u.id')
            ->join('ChargePoints AS cp', 'b.charge_point_id', '=', 'cp.id');

        if (!empty($status)) {
            $query = $query->where('b.status', '=', $status);
        }

        if (!empty($search)) {
            $query = $query->whereOr(function ($q) use ($search) {
                $q->where('u.name', 'LIKE', "%$search%")
                    ->where('cp.address', 'LIKE', "%$search%");

                if (is_numeric($search)) {
                    $q->where('b.id', '=', (int) $search);
                }
            });
        }

        $result = $query->first();
        return (int) ($result['count'] ?? 0);
    }

    /* ─────────────────────────── CHARGE POINT VALIDATION ────────────────────────── */

    /**
     * Check if a charge point has any bookings
     * 
     * @param int $chargePointId Charge point ID
     * @return bool True if charge point has bookings
     */
    public function hasBookingsByChargePoint($chargePointId): bool
    {
        return $this->table(self::TABLE)
            ->select('id')
            ->where('charge_point_id', '=', $chargePointId)
            ->exists();
    }
}