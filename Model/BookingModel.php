<?php

namespace Model;

require_once 'BaseModel.php';

class BookingModel extends BaseModel
{
    const TABLE_NAME = 'Bookings';

    public function getUserBookings($userId)
    {
        return $this->table(self::TABLE_NAME)
            ->select([
                'Bookings.id AS booking_id',
                'Bookings.booking_date',
                'Bookings.due_date',
                'Bookings.status',
                'ChargePoints.address',
                'ChargePoints.price_per_kWh',
                'ChargePoints.image_url'
            ])
            ->join('ChargePoints', 'Bookings.charge_point_id', '=', 'ChargePoints.id')
            ->where('Bookings.user_id', '=', $userId)
            ->get();
    }

    public function cancelBooking($bookingId)
    {
        return $this->table(self::TABLE_NAME)
            ->where('id', '=', $bookingId)
            ->where('status', '=', 'Pending')
            ->update(['status' => 'Canceled']);
    }

}