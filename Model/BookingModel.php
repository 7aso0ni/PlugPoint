<?php
namespace Model;

use PDO;

require_once 'BaseModel.php';

class BookingModel extends BaseModel
{
    private const TABLE = 'Bookings';

    /* ─────────────────────────── PUBLIC API ────────────────────────── */

    /** Reserve a slot (throws if overlapping) and return the new row */
    public function reserve(array $data): array
    {
        if (
            !$this->isSlotAvailable(
                $data['charge_point_id'],
                $data['booking_date'],
                $data['due_date']
            )
        ) {
            throw new \RuntimeException('Selected time slot is already booked.');
        }

        /* perform insert */
        $this->table(self::TABLE)->insert($data);

        /* fetch the auto-increment id from the PDO connection */
        $id = (int) $this->db->lastInsertId();

        return $this->getBookingById($id);
    }

    /** Check if the [start,end) slot is free for this charge point */
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

    /* ─────────────────────────── HELPERS ─────────────────────────── */

    public function getBookingById($id)
    {
        return $this->table(self::TABLE)
            ->select()
            ->where('id', '=', $id)
            ->first();
    }

    public function getBookingsByUser($userId): array
    {
        return $this->table(self::TABLE . ' AS b')
            ->select('b.id AS booking_id, b.booking_date, b.due_date, b.status, b.created_at,
                      cp.id AS charge_point_id, cp.address, cp.price_per_kWh, cp.image_url')
            ->join('ChargePoints AS cp', 'b.charge_point_id', '=', 'cp.id')
            ->where('b.user_id', '=', $userId)
            ->get();
    }

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

    public function updateBookingStatus(int $id, string $status): int
    {
        $stmt = $this->table(self::TABLE)
            ->where('id', '=', $id)
            ->update(['status' => $status]);

        return $stmt->rowCount();   // number of rows affected
    }

    public function cancelBooking(int $id): int
    {
        return $this->updateBookingStatus($id, 'Canceled');
    }

}
